<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class RunController extends Controller
{
    public function index(Request $request)
    {
        $storage = UserStorage::fromSession();
        $today   = date('Y-m-d');

        $todayRun     = $storage->getRun($today);
        $weeklyCount  = $storage->getRunWeeklyCount();
        $weeklyDist   = $storage->getRunWeeklyDistance();
        $monthlyCount = $storage->getRunMonthlyCount();
        $monthlyDist  = $storage->getRunMonthlyDistance();
        $pbs          = $storage->getRunPersonalBests();
        $history      = $storage->getRunHistory(10);
        $weekRuns     = array_map(
            fn($d) => $storage->getRun($d),
            UserStorage::getWeekDates()
        );

        // ── Range filter ──
        $range      = $request->query('range', 'month');
        $months     = UserStorage::rangeToMonths($range);
        $monthDates = UserStorage::getMonthDates();
        $runAll     = $storage->toArray()['run'] ?? [];

        $stripRows = []; $rangeActive = 0; $rangeTitle = '';
        if ($months !== null) {
            $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $result = UserStorage::buildStripRows($months, function ($d) use ($storage, $monthShort) {
                $run  = $storage->getRun($d);
                $done = !empty($run['done']);
                $dist = $run['distance'] ?? 0;
                $dt   = new \DateTime($d);
                return [
                    'active' => $done,
                    'value'  => $done ? 1 : 0,
                    'title'  => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': '
                              . ($done ? ($dist > 0 ? $dist . ' km' : 'Lari') : 'Rest'),
                ];
            });
            $stripRows   = $result['rows'];
            $rangeActive = $result['activeDays'];
            $rangeTitle  = $result['title'];
        }

        return view('pages.run.index', compact(
            'today', 'todayRun',
            'weeklyCount', 'weeklyDist',
            'monthlyCount', 'monthlyDist',
            'pbs', 'history', 'weekRuns',
            'range', 'months', 'stripRows', 'rangeActive', 'rangeTitle',
            'monthDates', 'runAll'
        ));
    }

    public function toggle(Request $request)
    {
        $storage = UserStorage::fromSession();
        $date    = $request->input('date', date('Y-m-d'));
        $storage->toggleRun($date);
        $storage->save();
        return back()->with('toast', 'Status lari diperbarui.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'date'     => 'required|date',
            'distance' => 'required|numeric|min:0|max:500',
            'duration' => 'required|integer|min:0|max:1440',
            'type'     => 'required|in:easy,tempo,interval,long_run,race',
            'calories' => 'nullable|integer|min:0|max:9999',
            'notes'    => 'nullable|string|max:500',
        ]);

        $storage = UserStorage::fromSession();
        $storage->updateRun($validated['date'], [
            'done'     => true,
            'distance' => (float) $validated['distance'],
            'duration' => (int)   $validated['duration'],
            'type'     => $validated['type'],
            'calories' => (int)  ($validated['calories'] ?? 0),
            'notes'    => $validated['notes'] ?? '',
        ]);
        $storage->save();

        return back()->with('toast', 'Data lari tersimpan!');
    }
}
