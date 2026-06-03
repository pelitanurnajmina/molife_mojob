<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class IntimacyController extends Controller
{
    public function index(Request $request)
    {
        $storage   = UserStorage::fromSession();
        $today     = date('Y-m-d');
        $date      = $request->query('date', $today);

        $count        = $storage->getIntimacy($date);
        $todayCount   = $storage->getIntimacy($today);
        $monthlyCount = $storage->getIntimacyMonthlyCount();
        $monthDates   = UserStorage::getMonthDates();
        $intimacyAll  = $storage->toArray()['intimacy'];

        // ── Range filter (month | 3m | 6m | 12m) ──
        $range  = $request->query('range', 'month');
        $months = UserStorage::rangeToMonths($range);

        $stripRows  = [];
        $rangeTotal = 0;
        $activeDays = 0;
        $rangeTitle = '';
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        if ($months !== null) {
            $result = UserStorage::buildStripRows($months, function ($d) use ($intimacyAll, $monthShort) {
                $c  = $intimacyAll[$d] ?? 0;
                $dt = new \DateTime($d);
                return [
                    'active' => $c > 0,
                    'value'  => $c,
                    'title'  => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . $c . 'x',
                ];
            });
            $stripRows  = $result['rows'];
            $rangeTotal = $result['total'];
            $activeDays = $result['activeDays'];
            $rangeTitle = $result['title'];
        }

        return view('pages.intimasi', compact(
            'date', 'today', 'count', 'todayCount', 'monthlyCount',
            'monthDates', 'intimacyAll', 'range', 'months',
            'stripRows', 'rangeTotal', 'activeDays', 'rangeTitle'
        ));
    }

    public function change(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->changeIntimacy($request->date, (int)$request->delta);
        $storage->save();
        return redirect()->back();
    }
}
