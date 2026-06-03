<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class RacketController extends Controller
{
    public function index(Request $request)
    {
        $storage   = UserStorage::fromSession();
        $profile   = $storage->getProfile();
        $today     = date('Y-m-d');
        $todayData = $storage->getRacket($today);

        $weekDates = [];
        for ($i = 6; $i >= 0; $i--) {
            $weekDates[] = date('Y-m-d', strtotime("-$i days"));
        }
        $weekData    = array_map(fn($d) => $storage->getRacket($d), $weekDates);
        $weekDone    = count(array_filter($weekData, fn($d) => $d['done']));
        $monthTotal  = 0;
        for ($i = 0; $i < 30; $i++) {
            $d = date('Y-m-d', strtotime("-$i days"));
            if ($storage->getRacket($d)['done']) $monthTotal++;
        }

        // ── Range filter ──
        $range  = $request->query('range', 'month');
        $months = UserStorage::rangeToMonths($range) ?? 1;
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $result = UserStorage::buildStripRows($months, function ($d) use ($storage, $monthShort) {
            $rec  = $storage->getRacket($d);
            $done = !empty($rec['done']);
            $sets = $rec['sets'] ?? 0;
            $dt   = new \DateTime($d);
            return ['active' => $done, 'value' => $done ? 1 : 0,
                    'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . ($done ? $sets.' set' : 'Rest')];
        });
        $stripRows = $result['rows']; $rangeActive = $result['activeDays']; $rangeTitle = $result['title'];

        return view('pages.sports.racket', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'monthTotal', 'profile',
            'range', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    public function update(Request $request)
    {
        $r = $request->validate([
            'date' => 'required|date',
            'sets' => 'required|integer|min:1|max:20',
        ]);
        $storage = UserStorage::fromSession();
        $storage->saveRacket($r['date'], (int)$r['sets']);
        $storage->save();
        return redirect()->back()->with('toast', __('Sesi tercatat!'));
    }

    public function reset(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $storage = UserStorage::fromSession();
        $storage->resetRacket($request->date);
        $storage->save();
        return redirect()->back()->with('toast', __('Data dihapus.'));
    }
}
