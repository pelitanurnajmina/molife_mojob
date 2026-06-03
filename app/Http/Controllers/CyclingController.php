<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class CyclingController extends Controller
{
    public function index(Request $request)
    {
        $storage   = UserStorage::fromSession();
        $today     = date('Y-m-d');
        $todayData = $storage->getCycling($today);

        // Weekly summary
        $weekDates = [];
        for ($i = 6; $i >= 0; $i--) {
            $weekDates[] = date('Y-m-d', strtotime("-$i days"));
        }
        $weekData  = array_map(fn($d) => $storage->getCycling($d), $weekDates);
        $weekDone  = count(array_filter($weekData, fn($d) => $d['done']));
        $weekKm    = array_sum(array_column($weekData, 'km'));

        // Personal best
        $allDates  = array_keys($storage->getAllCycling());
        $bestKm    = 0;
        foreach ($allDates as $d) {
            $rec = $storage->getCycling($d);
            if ($rec['km'] > $bestKm) $bestKm = $rec['km'];
        }

        // ── Range filter ── (sports have no monthly calendar → "Bulan Ini" = 1-month strip)
        $range  = $request->query('range', 'month');
        $months = UserStorage::rangeToMonths($range) ?? 1;
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $result = UserStorage::buildStripRows($months, function ($d) use ($storage, $monthShort) {
            $rec  = $storage->getCycling($d);
            $done = !empty($rec['done']);
            $km   = $rec['km'] ?? 0;
            $dt   = new \DateTime($d);
            return ['active' => $done, 'value' => $done ? 1 : 0,
                    'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . ($done ? $km.' km' : 'Rest')];
        });
        $stripRows = $result['rows']; $rangeActive = $result['activeDays']; $rangeTitle = $result['title'];

        return view('pages.sports.cycling', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'weekKm', 'bestKm',
            'range', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    public function update(Request $request)
    {
        $r = $request->validate([
            'date'     => 'required|date',
            'km'       => 'required|numeric|min:0|max:500',
            'duration' => 'required|integer|min:0|max:600',
        ]);
        $storage = UserStorage::fromSession();
        $storage->saveCycling($r['date'], (float)$r['km'], (int)$r['duration']);
        $storage->save();
        return redirect()->back()->with('toast', __('Data bersepeda tersimpan!'));
    }

    public function reset(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $storage = UserStorage::fromSession();
        $storage->resetCycling($request->date);
        $storage->save();
        return redirect()->back()->with('toast', __('Data dihapus.'));
    }
}
