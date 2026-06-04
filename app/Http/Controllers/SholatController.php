<?php

namespace App\Http\Controllers;

use App\Services\SholatService;
use App\Support\Dates;
use Illuminate\Http\Request;

class SholatController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');
        $date   = $request->query('date', $today);

        $sholatData   = SholatService::day($userId, $date);
        $sholatStats  = SholatService::stats($userId, $date);
        $streak       = SholatService::streak($userId);
        $takbirStreak = SholatService::takbirStreak($userId);
        $monthDates   = Dates::monthDates();

        // Precompute stats per day for the month calendar
        $monthStats = [];
        foreach ($monthDates as $d) {
            $monthStats[$d] = SholatService::stats($userId, $d);
        }

        // ── Range filter ──
        $range  = $request->query('range', 'month');
        $months = Dates::rangeToMonths($range);

        $stripRows = []; $rangeActive = 0; $rangeTitle = '';
        if ($months !== null) {
            $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $result = Dates::buildStripRows($months, function ($d) use ($userId, $monthShort) {
                $w  = SholatService::stats($userId, $d)['wajib'];
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
            'monthDates', 'monthStats', 'range', 'months', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    public function toggleWajib(Request $request)
    {
        SholatService::toggleWajib(auth()->id(), $request->date, $request->name);
        return redirect()->back();
    }

    public function toggleTakbir(Request $request)
    {
        SholatService::toggleTakbir(auth()->id(), $request->date, $request->name);
        return redirect()->back();
    }

    public function toggleRawatib(Request $request)
    {
        SholatService::toggleRawatib(auth()->id(), $request->date, $request->name);
        return redirect()->back();
    }

    public function toggleSunnah(Request $request)
    {
        SholatService::toggleSunnah(auth()->id(), $request->date, $request->name);
        return redirect()->back();
    }
}
