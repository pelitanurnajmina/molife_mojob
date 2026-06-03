<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class SholatController extends Controller
{
    public function index(Request $request)
    {
        $storage  = UserStorage::fromSession();
        $today    = date('Y-m-d');
        $date     = $request->query('date', $today);

        $sholatData  = $storage->getSholat($date);
        $sholatStats = $storage->getSholatStats($date);
        $streak      = $storage->getSholatStreak();
        $takbirStreak= $storage->getTakbirStreak();
        $monthDates  = UserStorage::getMonthDates();

        // ── Range filter ──
        $range  = $request->query('range', 'month');
        $months = UserStorage::rangeToMonths($range);

        $stripRows = []; $rangeActive = 0; $rangeTitle = '';
        if ($months !== null) {
            $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $result = UserStorage::buildStripRows($months, function ($d) use ($storage, $monthShort) {
                $w  = $storage->getSholatStats($d)['wajib'] ?? 0;
                $dt = new \DateTime($d);
                return [
                    'active' => $w >= 5,
                    'value'  => $w,
                    'title'  => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . $w . '/5 wajib',
                ];
            });
            $stripRows   = $result['rows'];
            $rangeActive = $result['activeDays'];
            $rangeTitle  = $result['title'];
        }

        return view('pages.sholat', compact(
            'date', 'today', 'sholatData', 'sholatStats', 'streak', 'takbirStreak',
            'monthDates', 'range', 'months', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    public function toggleWajib(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleSholatWajib($request->date, $request->name);
        $storage->save();
        return redirect()->back();
    }

    public function toggleTakbir(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleTakbirPertama($request->date, $request->name);
        $storage->save();
        return redirect()->back();
    }

    public function toggleRawatib(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleRawatib($request->date, $request->name);
        $storage->save();
        return redirect()->back();
    }

    public function toggleSunnah(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleSholatSunnah($request->date, $request->name);
        $storage->save();
        return redirect()->back();
    }
}
