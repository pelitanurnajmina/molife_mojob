<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class CustomSportController extends Controller
{
    public function index()
    {
        $storage   = UserStorage::fromSession();
        $profile   = $storage->getProfile();
        $today     = date('Y-m-d');
        $todayData = $storage->getCustomSport($today);

        $weekDates = [];
        for ($i = 6; $i >= 0; $i--) {
            $weekDates[] = date('Y-m-d', strtotime("-$i days"));
        }
        $weekData   = array_map(fn($d) => $storage->getCustomSport($d), $weekDates);
        $weekDone   = count(array_filter($weekData, fn($d) => $d['done']));
        $monthTotal = 0;
        for ($i = 0; $i < 30; $i++) {
            $d = date('Y-m-d', strtotime("-$i days"));
            if ($storage->getCustomSport($d)['done']) $monthTotal++;
        }

        return view('pages.sports.custom', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'monthTotal', 'profile'
        ));
    }

    public function update(Request $request)
    {
        $r = $request->validate([
            'date'     => 'required|date',
            'duration' => 'required|integer|min:1|max:600',
        ]);
        $storage = UserStorage::fromSession();
        $storage->saveCustomSport($r['date'], (int)$r['duration']);
        $storage->save();
        return redirect()->back()->with('toast', __('Sesi tercatat!'));
    }

    public function reset(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $storage = UserStorage::fromSession();
        $storage->resetCustomSport($request->date);
        $storage->save();
        return redirect()->back()->with('toast', __('Data dihapus.'));
    }
}
