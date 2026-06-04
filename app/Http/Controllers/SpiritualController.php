<?php

namespace App\Http\Controllers;

use App\Services\SpiritualService;
use App\Support\Profile;
use Illuminate\Http\Request;

class SpiritualController extends Controller
{
    public function index()
    {
        $userId  = auth()->id();
        $profile = Profile::data();
        $today   = date('Y-m-d');

        // Religion-specific practice types
        $practices = $this->getPractices($profile['religion'] ?? 'lainnya');
        $todayData = SpiritualService::day($userId, $today);
        $streak    = SpiritualService::streak($userId, array_keys($practices));

        // Last 7 days for mini-calendar
        $week = [];
        for ($i = 6; $i >= 0; $i--) {
            $d    = date('Y-m-d', strtotime("-$i days"));
            $data = SpiritualService::day($userId, $d);
            $done = count(array_filter(array_keys($practices), fn($t) => !empty($data[$t])));
            $week[] = ['date' => $d, 'done' => $done, 'total' => count($practices)];
        }

        return view('pages.spiritual', compact('profile', 'practices', 'todayData', 'streak', 'week', 'today'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['date' => 'required|date', 'type' => 'required|string|max:50']);
        SpiritualService::toggle(auth()->id(), $request->date, $request->type);
        return redirect()->back();
    }

    /**
     * Return the list of spiritual practices for a given religion.
     * Each item: ['label' => '...', 'icon' => 'svg-path', 'color' => 'tailwind-class']
     */
    private function getPractices(string $religion): array
    {
        return match($religion) {
            'kristen' => [
                'morning_prayer' => ['label' => __('Doa Pagi'),         'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'orange'],
                'evening_prayer' => ['label' => __('Doa Malam'),        'icon' => 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z', 'color' => 'indigo'],
                'bible_read'     => ['label' => __('Baca Alkitab'),     'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'blue'],
                'church'         => ['label' => __('Ibadah Minggu'),    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'color' => 'purple'],
            ],
            'hindu', 'buddha' => [
                'morning'    => ['label' => __('Sembahyang Pagi'),  'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'orange'],
                'evening'    => ['label' => __('Sembahyang Sore'),  'icon' => 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z', 'color' => 'indigo'],
                'meditation' => ['label' => __('Meditasi'),         'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'color' => 'violet'],
            ],
            default => [ // lainnya
                'practice'   => ['label' => __('Praktik Spiritual'), 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'color' => 'teal'],
                'gratitude'  => ['label' => __('Jurnal Syukur'),    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'green'],
            ],
        };
    }
}
