<?php

namespace App\Services;

use App\Models\FinanceBudget;
use App\Models\FinanceSavingsGoal;
use App\Models\FinanceTransaction;
use App\Models\JobApplication;

/**
 * Aggregates summary numbers + smart-insight cards for the global dashboard,
 * for the Career and Finance domains (Life insights come from InsightService).
 */
class DashboardInsightService
{
    /* ── Career ── */
    public static function careerSummary(int $userId): array
    {
        $counts = InsightService::applicationCounts($userId);
        $active = ($counts['applied'] ?? 0) + ($counts['review'] ?? 0) + ($counts['interview'] ?? 0);
        $total  = array_sum($counts);
        return [
            'total'     => $total,
            'active'    => $active,
            'interview' => $counts['interview'] ?? 0,
            'offer'     => ($counts['offer'] ?? 0) + ($counts['hired'] ?? 0),
            'wishlist'  => $counts['wishlist'] ?? 0,
        ];
    }

    public static function careerInsights(int $userId): array
    {
        $s = self::careerSummary($userId);
        $out = [];
        if ($s['total'] === 0) {
            $out[] = ['type'=>'info','icon'=>'career','text'=>__('Belum ada lamaran. Mulai catat lamaran kerjamu di menu Karir.')];
            return $out;
        }
        if ($s['active'] > 0) {
            $out[] = ['type'=>'info','icon'=>'career','text'=>__(':n lamaran aktif sedang menunggu respon.', ['n' => $s['active']])];
        }
        if ($s['interview'] > 0) {
            $out[] = ['type'=>'success','icon'=>'interview','text'=>__(':n lamaran sampai tahap interview!', ['n' => $s['interview']])];
        }
        if ($s['offer'] > 0) {
            $out[] = ['type'=>'success','icon'=>'career','text'=>__(':n tawaran/diterima. Selamat! 🎉', ['n' => $s['offer']])];
        }
        return $out;
    }

    /* ── Finance ── */
    public static function financeSummary(int $userId): array
    {
        $monthKey = date('Y-m');
        $txs = FinanceTransaction::where('user_id', $userId)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$monthKey])->get();

        $income  = (int) $txs->where('type', 'income')->sum('amount');
        $expense = (int) $txs->where('type', 'expense')->sum('amount');

        $goals      = FinanceSavingsGoal::where('user_id', $userId)->get();
        $goalTarget = (int) $goals->sum('target');
        $goalSaved  = (int) $goals->sum('current');

        return [
            'income'      => $income,
            'expense'     => $expense,
            'balance'     => $income - $expense,
            'goalCount'   => $goals->count(),
            'goalTarget'  => $goalTarget,
            'goalSaved'   => $goalSaved,
            'goalPct'     => $goalTarget > 0 ? min(100, round($goalSaved / $goalTarget * 100)) : 0,
        ];
    }

    public static function financeInsights(int $userId): array
    {
        $monthKey = date('Y-m');
        $s = self::financeSummary($userId);
        $out = [];

        if ($s['income'] === 0 && $s['expense'] === 0) {
            $out[] = ['type'=>'info','icon'=>'finance','text'=>__('Belum ada transaksi bulan ini. Catat pemasukan & pengeluaranmu.')];
            return $out;
        }

        if ($s['balance'] >= 0) {
            $out[] = ['type'=>'success','icon'=>'finance','text'=>__('Saldo bulan ini positif. Keuangan terjaga!')];
        } else {
            $out[] = ['type'=>'warning','icon'=>'warning','text'=>__('Pengeluaran melebihi pemasukan bulan ini. Perlu evaluasi.')];
        }

        // Budget warnings ≥90%
        $budgets = FinanceBudget::where('user_id', $userId)->where('month_key', $monthKey)->get();
        foreach ($budgets as $b) {
            if ($b->amount <= 0) continue;
            $spent = (int) FinanceTransaction::where('user_id', $userId)->where('type', 'expense')
                ->where('category', $b->category)
                ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$monthKey])->sum('amount');
            $pct = $spent / $b->amount * 100;
            if ($pct >= 90) {
                $out[] = ['type'=>'warning','icon'=>'warning',
                    'text'=>__('Anggaran :cat sudah :p% terpakai.', ['cat' => $b->category, 'p' => (int) $pct])];
            }
        }

        if ($s['goalCount'] > 0 && $s['goalPct'] >= 50) {
            $out[] = ['type'=>'success','icon'=>'finance',
                'text'=>__('Tabungan sudah :p% dari total target. Lanjutkan!', ['p' => $s['goalPct']])];
        }

        return $out;
    }
}
