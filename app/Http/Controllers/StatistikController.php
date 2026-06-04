<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use App\Services\SholatService;
use App\Support\Profile;
use App\Support\Features;

class StatistikController extends Controller
{
    public function index()
    {
        $userId   = auth()->id();
        $profile  = Profile::data();
        $features = Features::map($userId);
        $stats30  = StatsService::last30Days($userId, 30);

        $religion = $profile['religion'] ?? '';
        $sports   = $profile['sports']   ?? [];

        // Build adaptive heatmap rows based on profile/features
        $heatmapRows = [];

        // Spiritual row
        if ($features['sholat'] ?? false) {
            $heatmapRows[] = [
                'key'    => 'sholat',
                'label'  => 'SHOLAT',
                'icon'   => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                'color'  => 'bg-green-500',
                'alt'    => 'bg-yellow-400',
                'days'   => array_map(fn($d) => $d['sholat']['takbir'] >= 5 ? 'alt' : ($d['sholat']['wajib'] >= 5 ? 'done' : 'empty'), $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . $d['sholat']['wajib'] . '/5 wajib', $stats30),
            ];
        } elseif ($features['spiritual'] ?? false) {
            $spiritualLabel = match($religion) {
                'kristen'        => 'IBADAH',
                'hindu','buddha' => 'SEMBAHYANG',
                default          => 'SPIRITUAL',
            };
            $heatmapRows[] = [
                'key'    => 'spiritual',
                'label'  => $spiritualLabel,
                'icon'   => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z',
                'color'  => 'bg-violet-500',
                'days'   => array_map(fn($d) => count(array_filter($d['spiritual'])) > 0 ? 'done' : 'empty', $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . count(array_filter($d['spiritual'])) . ' ibadah', $stats30),
            ];
        }

        // Sports rows
        if ($features['gym'] ?? false) {
            $heatmapRows[] = [
                'key'    => 'gym',
                'label'  => 'GYM',
                'icon'   => 'M13 10V3L4 14h7v7l9-11h-7z',
                'color'  => 'bg-blue-500',
                'days'   => array_map(fn($d) => $d['gym']['done'] ? 'done' : 'empty', $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . ($d['gym']['done'] ? 'Gym ✓' : 'Rest'), $stats30),
            ];
        }
        if ($features['run'] ?? false) {
            $heatmapRows[] = [
                'key'    => 'run',
                'label'  => 'LARI',
                'icon'   => 'M22 12h-4l-3 9L9 3l-3 9H2',
                'color'  => 'bg-emerald-500',
                'days'   => array_map(fn($d) => ($d['run']['done'] ?? false) ? 'done' : 'empty', $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . (($d['run']['done'] ?? false) ? ($d['run']['distance'] ?? 0) . 'km' : 'Rest'), $stats30),
            ];
        }
        if ($features['cycling'] ?? false) {
            $heatmapRows[] = [
                'key'    => 'cycling',
                'label'  => 'BERSEPEDA',
                'icon'   => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
                'color'  => 'bg-green-400',
                'days'   => array_map(fn($d) => $d['cycling']['done'] ? 'done' : 'empty', $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . ($d['cycling']['done'] ? $d['cycling']['km'] . 'km' : 'Rest'), $stats30),
            ];
        }
        if ($features['swimming'] ?? false) {
            $heatmapRows[] = [
                'key'    => 'swimming',
                'label'  => 'RENANG',
                'icon'   => 'M7 16.5c2 1 4 1 6 0s4-1 6 0M7 11.5c2 1 4 1 6 0s4-1 6 0M3 7.5c2 1 4 1 6 0m-9 9V7.5',
                'color'  => 'bg-blue-400',
                'days'   => array_map(fn($d) => $d['swimming']['done'] ? 'done' : 'empty', $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . ($d['swimming']['done'] ? $d['swimming']['laps'] . ' lap' : 'Rest'), $stats30),
            ];
        }
        if ($features['racket'] ?? false) {
            $heatmapRows[] = [
                'key'    => 'racket',
                'label'  => 'TENIS/BADMINTON',
                'icon'   => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                'color'  => 'bg-violet-400',
                'days'   => array_map(fn($d) => $d['racket']['done'] ? 'done' : 'empty', $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . ($d['racket']['done'] ? 'Sesi ✓' : 'Rest'), $stats30),
            ];
        }
        if ($features['custom_sport'] ?? false) {
            $sportName = $profile['custom_sport_name'] ?? 'Olahraga';
            $heatmapRows[] = [
                'key'    => 'custom',
                'label'  => strtoupper($sportName),
                'icon'   => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
                'color'  => 'bg-orange-400',
                'days'   => array_map(fn($d) => $d['custom']['done'] ? 'done' : 'empty', $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . ($d['custom']['done'] ? 'Sesi ✓' : 'Rest'), $stats30),
            ];
        }
        if ($features['intimasi'] ?? false) {
            $heatmapRows[] = [
                'key'    => 'intimacy',
                'label'  => 'INTIMASI',
                'icon'   => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                'color'  => 'bg-pink-500',
                'days'   => array_map(fn($d) => $d['intimacy'] > 0 ? 'done' : 'empty', $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': ' . $d['intimacy'] . 'x', $stats30),
            ];
        }
        if ($features['mental'] ?? false) {
            $heatmapRows[] = [
                'key'    => 'mood',
                'label'  => 'MOOD',
                'icon'   => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'color'  => 'bg-violet-500',
                'days'   => array_map(fn($d) => $d['mood']['score'] >= 4 ? 'done' : ($d['mood']['score'] > 0 ? 'partial' : 'empty'), $stats30),
                'titles' => array_map(fn($d) => $d['date'] . ': Mood ' . ($d['mood']['score'] > 0 ? $d['mood']['score'] . '/5' : '-'), $stats30),
                'alt'    => 'bg-violet-200',
            ];
        }

        // Doughnut data — only enabled features with data
        $doughnutLabels = [];
        $doughnutData   = [];
        $doughnutColors = [];
        foreach ($heatmapRows as $row) {
            $doneDays = count(array_filter($row['days'], fn($s) => $s !== 'empty'));
            if ($doneDays > 0) {
                $colorMap = [
                    'bg-green-500'  => '#10B981', 'bg-violet-500' => '#8B5CF6',
                    'bg-blue-500'   => '#3B82F6', 'bg-emerald-500'=> '#34D399',
                    'bg-green-400'  => '#4ADE80', 'bg-blue-400'   => '#60A5FA',
                    'bg-violet-400' => '#A78BFA', 'bg-orange-400' => '#FB923C',
                    'bg-pink-500'   => '#EC4899',
                ];
                $doughnutLabels[] = $row['label'];
                $doughnutData[]   = $doneDays;
                $doughnutColors[] = $colorMap[$row['color']] ?? '#9CA3AF';
            }
        }

        // Monthly summary stats
        $streak          = SholatService::streak($userId);
        $gymMonthly      = StatsService::gymMonthlyCount($userId);
        $runMonthly      = StatsService::runMonthlyCount($userId);
        $intimacyMonthly = StatsService::intimacyMonthlyCount($userId);
        $moodAvg30       = \App\Services\MoodService::avgScore($userId, 30);

        return view('pages.statistik', compact(
            'stats30', 'heatmapRows', 'profile', 'features',
            'doughnutLabels', 'doughnutData', 'doughnutColors',
            'streak', 'gymMonthly', 'runMonthly', 'intimacyMonthly', 'moodAvg30'
        ));
    }
}
