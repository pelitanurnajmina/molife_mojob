<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    const INCOME_CATS  = ['Gaji', 'Freelance', 'Bisnis', 'Investasi', 'Bonus', 'Hadiah', 'Lainnya'];
    const EXPENSE_CATS = ['Makanan', 'Transportasi', 'Belanja', 'Hiburan', 'Kesehatan',
                          'Pendidikan', 'Tagihan', 'Sewa', 'Tabungan', 'Investasi', 'Lainnya'];

    /**
     * Filter transactions by plan's days-limit (Freemium = 7 days).
     * Returns [filteredTxs, isLimited, daysLimit, cutoffDate]
     */
    private function applyPlanLimit(UserStorage $storage, array $txs): array
    {
        $daysLimit = $storage->getFinanceDaysLimit();
        if ($daysLimit === null) {
            return [$txs, false, null, null];
        }
        $cutoff = date('Y-m-d', strtotime("-{$daysLimit} days"));
        $filtered = array_values(array_filter($txs, fn($t) => ($t['date'] ?? '') >= $cutoff));
        return [$filtered, true, $daysLimit, $cutoff];
    }

    /* ── Overview ── */
    public function index()
    {
        $storage    = UserStorage::fromSession();
        $isFreemium = $storage->isFreemium();
        $daysLimit  = $storage->getFinanceDaysLimit();
        $monthKey   = date('Y-m');

        if ($isFreemium) {
            // Freemium — only last N days
            $allMonthTxs = $storage->getTransactions($monthKey);
            [$visibleTxs] = $this->applyPlanLimit($storage, $allMonthTxs);

            // Recompute summary from filtered txs
            $income  = array_sum(array_map(fn($t) => $t['amount'] ?? 0, array_filter($visibleTxs, fn($t) => $t['type'] === 'income')));
            $expense = array_sum(array_map(fn($t) => $t['amount'] ?? 0, array_filter($visibleTxs, fn($t) => $t['type'] === 'expense')));
            $summary = ['income' => $income, 'expense' => $expense, 'balance' => $income - $expense];

            // Spent by category — also filtered
            $spentByCategory = [];
            foreach ($visibleTxs as $tx) {
                if ($tx['type'] === 'expense') {
                    $spentByCategory[$tx['category']] = ($spentByCategory[$tx['category']] ?? 0) + ($tx['amount'] ?? 0);
                }
            }

            $recentTxs = array_slice($visibleTxs, 0, 6);
            $trend     = []; // No 6-month trend on freemium
        } else {
            $summary         = $storage->getFinanceSummary($monthKey);
            $recentTxs       = array_slice($storage->getTransactions($monthKey), 0, 6);
            $spentByCategory = $storage->getSpentByCategory($monthKey);

            $trend = [];
            for ($i = 5; $i >= 0; $i--) {
                $mk = date('Y-m', strtotime("-$i months"));
                $s  = $storage->getFinanceSummary($mk);
                $trend[] = ['month' => date('M', strtotime($mk . '-01')), 'balance' => $s['balance'],
                            'income' => $s['income'], 'expense' => $s['expense']];
            }
        }

        $budget       = $storage->getBudget($monthKey);
        $savingsGoals = $storage->getSavingsGoals();

        return view('pages.finance.index', compact(
            'monthKey', 'summary', 'recentTxs', 'budget', 'spentByCategory', 'savingsGoals', 'trend',
            'isFreemium', 'daysLimit'
        ));
    }

    /* ── Transaksi ── */
    public function transaksi(Request $request)
    {
        $storage    = UserStorage::fromSession();
        $isFreemium = $storage->isFreemium();
        $daysLimit  = $storage->getFinanceDaysLimit();
        $monthKey   = $request->get('month', date('Y-m'));

        $allMonthTxs = $storage->getTransactions($monthKey);
        [$transactions, $isLimited] = $this->applyPlanLimit($storage, $allMonthTxs);

        if ($isFreemium) {
            // Recompute summary from filtered
            $income  = array_sum(array_map(fn($t) => $t['amount'] ?? 0, array_filter($transactions, fn($t) => $t['type'] === 'income')));
            $expense = array_sum(array_map(fn($t) => $t['amount'] ?? 0, array_filter($transactions, fn($t) => $t['type'] === 'expense')));
            $summary = ['income' => $income, 'expense' => $expense, 'balance' => $income - $expense];
        } else {
            $summary = $storage->getFinanceSummary($monthKey);
        }

        $hiddenCount = count($allMonthTxs) - count($transactions);
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

        $storage = UserStorage::fromSession();
        $storage->addTransaction([
            'id'       => uniqid('tx_'),
            'type'     => $r['type'],
            'date'     => $r['date'],
            'category' => $r['category'],
            'amount'   => (int) $r['amount'],
            'note'     => $r['note'] ?? '',
        ]);
        $storage->save();

        return redirect()->back()->with('toast', __('Transaksi ditambahkan!'));
    }

    public function deleteTransaction(string $id)
    {
        $storage = UserStorage::fromSession();
        $storage->deleteTransaction($id);
        $storage->save();
        return redirect()->back()->with('toast', __('Transaksi dihapus.'));
    }

    /* ── Anggaran ── */
    public function anggaran(Request $request)
    {
        $storage  = UserStorage::fromSession();
        $monthKey = $request->get('month', date('Y-m'));

        $budget          = $storage->getBudget($monthKey);
        $spentByCategory = $storage->getSpentByCategory($monthKey);
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
            'month'   => 'required|string|max:7',
            'budgets' => 'required|array',
            'budgets.*' => 'nullable|integer|min:0',
        ]);

        $storage = UserStorage::fromSession();
        foreach ($r['budgets'] as $cat => $amount) {
            if ((int)$amount > 0) {
                $storage->setBudget($r['month'], $cat, (int)$amount);
            }
        }
        $storage->save();

        return redirect()->back()->with('toast', __('Anggaran disimpan!'));
    }

    /* ── Tabungan ── */
    public function tabungan()
    {
        $goals = UserStorage::fromSession()->getSavingsGoals();
        return view('pages.finance.tabungan', compact('goals'));
    }

    public function saveGoal(Request $request)
    {
        $r = $request->validate([
            'id'       => 'nullable|string',
            'name'     => 'required|string|max:100',
            'target'   => 'required|integer|min:1',
            'current'  => 'nullable|integer|min:0',
            'deadline' => 'nullable|date',
            'color'    => 'nullable|string|max:20',
        ]);

        $storage = UserStorage::fromSession();
        $storage->saveSavingsGoal([
            'id'       => $r['id'] ?: uniqid('goal_'),
            'name'     => $r['name'],
            'target'   => (int) $r['target'],
            'current'  => (int) ($r['current'] ?? 0),
            'deadline' => $r['deadline'] ?? null,
            'color'    => $r['color'] ?? 'emerald',
        ]);
        $storage->save();

        return redirect()->back()->with('toast', __('Tujuan tabungan disimpan!'));
    }

    public function deleteGoal(string $id)
    {
        $storage = UserStorage::fromSession();
        $storage->deleteSavingsGoal($id);
        $storage->save();
        return redirect()->back()->with('toast', __('Tujuan dihapus.'));
    }
}
