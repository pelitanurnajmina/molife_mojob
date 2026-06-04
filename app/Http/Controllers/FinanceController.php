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

        return view('pages.finance.transaksi', compact(
            'monthKey', 'summary', 'transactions', 'incomeCats', 'expenseCats',
            'isFreemium', 'daysLimit', 'hiddenCount'
        ));
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
            'month'     => 'required|string|max:7',
            'budgets'   => 'required|array',
            'budgets.*' => 'nullable|integer|min:0',
        ]);
        $userId = auth()->id();
        foreach ($r['budgets'] as $cat => $amount) {
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
