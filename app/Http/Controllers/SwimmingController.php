<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class SwimmingController extends Controller
{
    public function index()
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

        return view('pages.sports.swimming', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'bestLaps'
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
