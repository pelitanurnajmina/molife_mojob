<?php

namespace App\Http\Controllers;

use App\Models\FinanceTransaction;
use App\Models\FinanceBudget;
use App\Models\FinanceSavingsGoal;
use App\Support\Profile;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    const INCOME_CATS  = ['Gaji', 'Freelance', 'Bisnis', 'Investasi', 'Bonus', 'Hadiah', 'Lainnya'];
    const EXPENSE_CATS = ['Makanan', 'Transportasi', 'Belanja', 'Hiburan', 'Kesehatan',
                          'Pendidikan', 'Tagihan', 'Sewa', 'Tabungan', 'Investasi', 'Lainnya'];

    /** Transactions for a month as arrays, newest first. Optionally limited to last N days. */
    private function monthTxs(int $userId, string $monthKey, ?int $daysLimit = null): array
    {
        $q = FinanceTransaction::where('user_id', $userId)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$monthKey]);
        if ($daysLimit !== null) {
            $q->where('date', '>=', date('Y-m-d', strtotime("-{$daysLimit} days")));
        }
        return $q->orderByDesc('date')->orderByDesc('id')->get()->map(fn($t) => [
            'id' => $t->id, 'type' => $t->type, 'date' => $t->date->format('Y-m-d'),
            'category' => $t->category, 'amount' => (int) $t->amount, 'note' => $t->note,
        ])->toArray();
    }

    private function summarize(array $txs): array
    {
        $income  = array_sum(array_map(fn($t) => $t['amount'], array_filter($txs, fn($t) => $t['type'] === 'income')));
        $expense = array_sum(array_map(fn($t) => $t['amount'], array_filter($txs, fn($t) => $t['type'] === 'expense')));
        return ['income' => $income, 'expense' => $expense, 'balance' => $income - $expense];
    }

    private function spentByCategory(array $txs): array
    {
        $out = [];
        foreach ($txs as $t) {
            if ($t['type'] === 'expense') $out[$t['category']] = ($out[$t['category']] ?? 0) + $t['amount'];
        }
        return $out;
    }

    private function budgetMap(int $userId, string $monthKey): array
    {
        return FinanceBudget::where('user_id', $userId)->where('month_key', $monthKey)
            ->pluck('amount', 'category')->toArray();
    }

    private function goalsList(int $userId): array
    {
        return FinanceSavingsGoal::where('user_id', $userId)->latest()->get()->map(fn($g) => [
            'id' => $g->id, 'name' => $g->name, 'target' => (int) $g->target,
            'current' => (int) $g->current, 'deadline' => optional($g->deadline)->format('Y-m-d'), 'color' => $g->color,
        ])->toArray();
    }

    /* ── Overview ── */
    public function index()
    {
        $userId     = auth()->id();
        $isFreemium = Profile::isFreemium($userId);
        $daysLimit  = Profile::financeDaysLimit($userId);
        $monthKey   = date('Y-m');

        $txs             = $this->monthTxs($userId, $monthKey, $daysLimit);
        $summary         = $this->summarize($txs);
        $spentByCategory = $this->spentByCategory($txs);
        $recentTxs       = array_slice($txs, 0, 6);

        $trend = [];
        if (!$isFreemium) {
            for ($i = 5; $i >= 0; $i--) {
                $mk = date('Y-m', strtotime("-$i months"));
                $s  = $this->summarize($this->monthTxs($userId, $mk));
                $trend[] = ['month' => date('M', strtotime($mk . '-01')), 'balance' => $s['balance'],
                            'income' => $s['income'], 'expense' => $s['expense']];
            }
        }

        $budget       = $this->budgetMap($userId, $monthKey);
        $savingsGoals = $this->goalsList($userId);

        return view('pages.finance.index', compact(
            'monthKey', 'summary', 'recentTxs', 'budget', 'spentByCategory', 'savingsGoals', 'trend',
            'isFreemium', 'daysLimit'
        ));
    }

    /* ── Transaksi ── */
    public function transaksi(Request $request)
    {
        $userId     = auth()->id();
        $isFreemium = Profile::isFreemium($userId);
        $daysLimit  = Profile::financeDaysLimit($userId);
        $monthKey   = $request->get('month', date('Y-m'));

        $allCount     = FinanceTransaction::where('user_id', $userId)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$monthKey])->count();
        $transactions = $this->monthTxs($userId, $monthKey, $daysLimit);
        $summary      = $this->summarize($transactions);
        $hiddenCount  = $allCount - count($transactions);

        $incomeCats  = self::INCOME_CATS;
        $expenseCats = self::EXPENSE_CATS;

        // AI receipt scan (premium plans only)
        $scanPremium    = \App\Services\SubscriptionService::hasPremium($userId);
        $scanConfigured = \App\Services\ReceiptScanService::configured();
        $scanRemaining      = $scanPremium ? \App\Services\ReceiptScanService::remainingToday($userId) : 0;
        $scanRemainingMonth = $scanPremium ? \App\Services\ReceiptScanService::remainingThisMonth($userId) : 0;

        return view('pages.finance.transaksi', compact(
            'monthKey', 'summary', 'transactions', 'incomeCats', 'expenseCats',
            'isFreemium', 'daysLimit', 'hiddenCount',
            'scanPremium', 'scanConfigured', 'scanRemaining', 'scanRemainingMonth'
        ));
    }

    /** AI receipt scan endpoint: photo in, structured transaction out (premium only). */
    public function scanReceipt(Request $request)
    {
        $userId = auth()->id();

        if (!\App\Services\SubscriptionService::hasPremium($userId)) {
            return response()->json([
                'error' => __('Scan struk hanya tersedia di paket 6 Bulan dan 1 Tahun.'),
            ], 403);
        }

        // heic/heif = format asli kamera iPhone; browser sudah mengonversi ke JPEG
        // lebih dulu, ini jaring pengaman bila konversinya gagal (Gemini bisa baca HEIC).
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,webp,heic,heif|max:8192',
        ], [
            'receipt.mimes' => __('Format harus JPG, PNG, WebP, atau HEIC.'),
            'receipt.max'   => __('Ukuran maksimal 8 MB.'),
        ]);

        try {
            $file   = $request->file('receipt');
            $result = \App\Services\ReceiptScanService::scan(
                $userId,
                file_get_contents($file->getRealPath()),
                $file->getMimeType() ?: 'image/jpeg',
            );
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Receipt scan failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => __('Gagal memproses struk. Coba lagi.')], 502);
        }

        return response()->json([
            'ok'              => true,
            'data'            => $result,
            'remaining'       => \App\Services\ReceiptScanService::remainingToday($userId),
            'remaining_month' => \App\Services\ReceiptScanService::remainingThisMonth($userId),
        ]);
    }

    public function addTransaction(Request $request)
    {
        $r = $request->validate([
            'type'     => 'required|in:income,expense',
            'date'     => 'required|date',
            'category' => 'required|string|max:50',
            'amount'   => 'required|integer|min:1',
            'note'     => 'nullable|string|max:200',
        ]);
        $r['user_id'] = auth()->id();
        FinanceTransaction::create($r);
        return redirect()->back()->with('toast', __('Transaksi ditambahkan!'));
    }

    public function deleteTransaction(string $id)
    {
        FinanceTransaction::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->back()->with('toast', __('Transaksi dihapus.'));
    }

    /* ── Anggaran ── */
    public function anggaran(Request $request)
    {
        $userId   = auth()->id();
        $monthKey = $request->get('month', date('Y-m'));

        $budget          = $this->budgetMap($userId, $monthKey);
        $spentByCategory = $this->spentByCategory($this->monthTxs($userId, $monthKey));
        $totalBudget     = array_sum($budget);
        $totalSpent      = array_sum($spentByCategory);
        $expenseCats     = self::EXPENSE_CATS;

        return view('pages.finance.anggaran', compact(
            'monthKey', 'budget', 'spentByCategory', 'totalBudget', 'totalSpent', 'expenseCats'
        ));
    }

    public function setBudget(Request $request)
    {
        $r = $request->validate([
            'month'            => 'required|string|max:7',
            'budgets'          => 'nullable|array',
            'budgets.*'        => 'nullable|integer|min:0',
            'custom_names'     => 'nullable|array',
            'custom_names.*'   => 'nullable|string|max:50',
            'custom_amounts'   => 'nullable|array',
            'custom_amounts.*' => 'nullable|integer|min:0',
        ]);
        $userId  = auth()->id();
        $budgets = $r['budgets'] ?? [];

        // Merge manually-named "Lainnya" categories into the budget map.
        $names   = $r['custom_names'] ?? [];
        $amounts = $r['custom_amounts'] ?? [];
        foreach ($names as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') continue;
            $budgets[$name] = (int) ($amounts[$i] ?? 0);
        }

        // Clear previously-saved custom categories that were removed from the form,
        // so deletions in the UI take effect.
        $keep = array_keys($budgets);
        FinanceBudget::where('user_id', $userId)->where('month_key', $r['month'])
            ->whereNotIn('category', self::EXPENSE_CATS)
            ->when(!empty($keep), fn($q) => $q->whereNotIn('category', $keep))
            ->delete();

        foreach ($budgets as $cat => $amount) {
            if ((int) $amount > 0) {
                FinanceBudget::updateOrCreate(
                    ['user_id' => $userId, 'month_key' => $r['month'], 'category' => $cat],
                    ['amount' => (int) $amount]
                );
            } else {
                FinanceBudget::where('user_id', $userId)->where('month_key', $r['month'])->where('category', $cat)->delete();
            }
        }
        return redirect()->back()->with('toast', __('Anggaran disimpan!'));
    }

    /* ── Tabungan ── */
    public function tabungan()
    {
        $goals = $this->goalsList(auth()->id());
        return view('pages.finance.tabungan', compact('goals'));
    }

    public function saveGoal(Request $request)
    {
        $r = $request->validate([
            'id'       => 'nullable',
            'name'     => 'required|string|max:100',
            'target'   => 'required|integer|min:1',
            'current'  => 'nullable|integer|min:0',
            'deadline' => 'nullable|date',
            'color'    => 'nullable|string|max:20',
        ]);

        $userId = auth()->id();
        $attrs = [
            'name' => $r['name'], 'target' => (int) $r['target'], 'current' => (int) ($r['current'] ?? 0),
            'deadline' => $r['deadline'] ?? null, 'color' => $r['color'] ?? 'emerald',
        ];

        if (!empty($r['id'])) {
            FinanceSavingsGoal::where('user_id', $userId)->where('id', $r['id'])->update($attrs);
        } else {
            $attrs['user_id'] = $userId;
            FinanceSavingsGoal::create($attrs);
        }

        return redirect()->back()->with('toast', __('Tujuan tabungan disimpan!'));
    }

    public function deleteGoal(string $id)
    {
        FinanceSavingsGoal::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->back()->with('toast', __('Tujuan dihapus.'));
    }
}
