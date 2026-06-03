<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class SwimmingController extends Controller
{
    public function index(Request $request)
    {
        $storage   = UserStorage::fromSession();
        $today     = date('Y-m-d');
        $todayData = $storage->getSwimming($today);

        $weekDates = [];
        for ($i = 6; $i >= 0; $i--) {
            $weekDates[] = date('Y-m-d', strtotime("-$i days"));
        }
        $weekData = array_map(fn($d) => $storage->getSwimming($d), $weekDates);
        $weekDone = count(array_filter($weekData, fn($d) => $d['done']));

        $bestLaps = 0;
        foreach ($storage->getAllSwimming() as $rec) {
            if (($rec['laps'] ?? 0) > $bestLaps) $bestLaps = $rec['laps'];
        }

        // ── Range filter ──
        $range  = $request->query('range', 'month');
        $months = UserStorage::rangeToMonths($range) ?? 1;
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $result = UserStorage::buildStripRows($months, function ($d) use ($storage, $monthShort) {
            $rec  = $storage->getSwimming($d);
            $done = !empty($rec['done']);
            $laps = $rec['laps'] ?? 0;
            $dt   = new \DateTime($d);
            return ['active' => $done, 'value' => $done ? 1 : 0,
                    'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . ($done ? $laps.' lap' : 'Rest')];
        });
        $stripRows = $result['rows']; $rangeActive = $result['activeDays']; $rangeTitle = $result['title'];

        return view('pages.sports.swimming', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'bestLaps',
            'range', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    public function update(Request $request)
    {
        $r = $request->validate([
            'date'     => 'required|date',
            'laps'     => 'required|integer|min:0|max:1000',
            'duration' => 'required|integer|min:0|max:600',
        ]);
        $storage = UserStorage::fromSession();
        $storage->saveSwimming($r['date'], (int)$r['laps'], (int)$r['duration']);
        $storage->save();
        return redirect()->back()->with('toast', __('Data renang tersimpan!'));
    }

    public function reset(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $storage = UserStorage::fromSession();
        $storage->resetSwimming($request->date);
        $storage->save();
        return redirect()->back()->with('toast', __('Data dihapus.'));
    }
}
