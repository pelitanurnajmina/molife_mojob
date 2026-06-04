<?php

namespace App\Http\Controllers;

use App\Models\GymLog;
use App\Support\Dates;
use Illuminate\Http\Request;

class GymController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');
        $date   = $request->query('date', $today);

        $gymData   = $this->dayData($userId, $date);
        $weekDates = Dates::weekDates();
        $monthKey  = date('Y-m');

        // Map of all gym logs keyed by date (for week grid + strip)
        $gymDataAll = GymLog::where('user_id', $userId)->get()
            ->keyBy(fn($g) => $g->date->format('Y-m-d'))
            ->map(fn($g) => ['done' => (bool) $g->done, 'calories' => (int) $g->calories])
            ->toArray();

        $gymWeekly   = count(array_filter($weekDates, fn($d) => $gymDataAll[$d]['done'] ?? false));
        $gymMonthly  = GymLog::where('user_id', $userId)->where('done', true)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$monthKey])->count();
        $caloriesWeek = array_sum(array_map(fn($d) => $gymDataAll[$d]['calories'] ?? 0, $weekDates));

        // ── Range filter ──
        $range  = $request->query('range', 'month');
        $months = Dates::rangeToMonths($range);
        $stripRows = []; $rangeActive = 0; $rangeTitle = '';
        if ($months !== null) {
            $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $result = Dates::buildStripRows($months, function ($d) use ($gymDataAll, $monthShort) {
                $done = $gymDataAll[$d]['done'] ?? false;
                $dt   = new \DateTime($d);
                return ['active' => $done, 'value' => $done ? 1 : 0,
                        'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . ($done ? 'Gym' : 'Rest')];
            });
            $stripRows = $result['rows']; $rangeActive = $result['activeDays']; $rangeTitle = $result['title'];
        }

        return view('pages.gym', compact(
            'date', 'today', 'gymData', 'gymWeekly', 'gymMonthly', 'caloriesWeek',
            'weekDates', 'gymDataAll', 'range', 'months', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    private function dayData(int $userId, string $date): array
    {
        $g = GymLog::where('user_id', $userId)->whereDate('date', $date)->first();
        return ['done' => (bool) ($g->done ?? false), 'calories' => (int) ($g->calories ?? 0)];
    }

    public function toggle(Request $request)
    {
        $userId   = auth()->id();
        $date     = $request->date;
        $calories = (int) ($request->calories ?? 0);
        $g = GymLog::firstOrNew(['user_id' => $userId, 'date' => $date]);
        if ($g->exists && $g->done) {
            $g->done = false; $g->calories = 0;
        } else {
            $g->done = true; $g->calories = $calories;
        }
        $g->save();
        return redirect()->back();
    }

    public function updateCalories(Request $request)
    {
        $g = GymLog::where('user_id', auth()->id())->whereDate('date', $request->date)->first();
        if ($g) { $g->calories = (int) $request->calories; $g->save(); }
        return redirect()->back();
    }
}
