<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class CyclingController extends Controller
{
    public function index()
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

        return view('pages.sports.cycling', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'weekKm', 'bestKm'
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
