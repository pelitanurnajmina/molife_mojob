<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;

class StatistikKarirController extends Controller
{
    public function index()
    {
        $storage      = UserStorage::fromSession();
        $applications = $storage->getApplications();
        $jobCounts    = $storage->getApplicationCounts();
        $jobTotal     = count($applications);

        // KPI rates
        $responded     = $jobTotal - ($jobCounts['applied'] ?? 0);
        $responseRate  = $jobTotal > 0 ? round(($responded / $jobTotal) * 100) : 0;

        $interviewed   = ($jobCounts['interview'] ?? 0) + ($jobCounts['offer'] ?? 0) + ($jobCounts['hired'] ?? 0);
        $interviewRate = $jobTotal > 0 ? round(($interviewed / $jobTotal) * 100) : 0;

        $successful    = ($jobCounts['offer'] ?? 0) + ($jobCounts['hired'] ?? 0);
        $successRate   = $jobTotal > 0 ? round(($successful / $jobTotal) * 100) : 0;

        // This month
        $thisMonth      = date('Y-m');
        $thisMonthCount = count(array_filter(
            $applications,
            fn($a) => str_starts_with($a['applied_date'] ?? '', $thisMonth)
        ));

        // Last month
        $lastMonth      = date('Y-m', strtotime('first day of last month'));
        $lastMonthCount = count(array_filter(
            $applications,
            fn($a) => str_starts_with($a['applied_date'] ?? '', $lastMonth)
        ));

        // Weekly trend (12 weeks) — labels + counts
        $weeklyTrend  = $storage->getWeeklyApplicationTrend();
        $trendLabels  = array_column($weeklyTrend, 'week');
        $trendCounts  = array_column($weeklyTrend, 'count');

        // Pipeline funnel
        $pipeline = [
            ['key' => 'applied',   'label' => 'Dikirim',   'count' => $jobCounts['applied']   ?? 0, 'color' => '#6b7280'],
            ['key' => 'review',    'label' => 'Review',    'count' => $jobCounts['review']    ?? 0, 'color' => '#f59e0b'],
            ['key' => 'interview', 'label' => 'Interview', 'count' => $jobCounts['interview'] ?? 0, 'color' => '#3b82f6'],
            ['key' => 'offer',     'label' => 'Tawaran',   'count' => $jobCounts['offer']     ?? 0, 'color' => '#10b981'],
            ['key' => 'hired',     'label' => 'Diterima',  'count' => $jobCounts['hired']     ?? 0, 'color' => '#059669'],
            ['key' => 'rejected',  'label' => 'Ditolak',   'count' => $jobCounts['rejected']  ?? 0, 'color' => '#ef4444'],
        ];

        // Recent 5 applications (newest first)
        $sorted = $applications;
        usort($sorted, fn($a, $b) => strcmp($b['applied_date'] ?? '', $a['applied_date'] ?? ''));
        $recentApps = array_slice($sorted, 0, 5);

        // Heatmap: last 60 days (applied_date)
        $heatmap = [];
        for ($i = 59; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $count = count(array_filter($applications, fn($a) => ($a['applied_date'] ?? '') === $d));
            $heatmap[] = ['date' => $d, 'count' => $count];
        }

        return view('pages.karir.index', compact(
            'jobTotal', 'jobCounts', 'thisMonthCount', 'lastMonthCount',
            'responseRate', 'interviewRate', 'successRate',
            'pipeline', 'weeklyTrend', 'trendLabels', 'trendCounts',
            'recentApps', 'heatmap'
        ));
    }
}
