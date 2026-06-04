<?php

namespace App\Http\Controllers;

use App\Models\IntimacyLog;
use App\Support\Dates;
use Illuminate\Http\Request;

class IntimacyController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');
        $date   = $request->query('date', $today);

        $intimacyAll = IntimacyLog::where('user_id', $userId)->get()
            ->keyBy(fn($r) => $r->date->format('Y-m-d'))
            ->map(fn($r) => (int) $r->count)->toArray();

        $count        = $intimacyAll[$date] ?? 0;
        $todayCount   = $intimacyAll[$today] ?? 0;
        $monthKey     = date('Y-m');
        $monthlyCount = IntimacyLog::where('user_id', $userId)
            ->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$monthKey])->sum('count');
        $monthDates   = Dates::monthDates();

        // ── Range filter ──
        $range  = $request->query('range', 'month');
        $months = Dates::rangeToMonths($range);
        $stripRows = []; $rangeTotal = 0; $activeDays = 0; $rangeTitle = '';
        if ($months !== null) {
            $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $result = Dates::buildStripRows($months, function ($d) use ($intimacyAll, $monthShort) {
                $c  = $intimacyAll[$d] ?? 0;
                $dt = new \DateTime($d);
                return ['active' => $c > 0, 'value' => $c,
                        'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . $c . 'x'];
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
        $userId = auth()->id();
        $date   = $request->date;
        $delta  = (int) $request->delta;

        $row = IntimacyLog::firstOrNew(['user_id' => $userId, 'date' => $date]);
        $row->count = max(0, (int) ($row->count ?? 0) + $delta);
        if ($row->count === 0 && $row->exists) {
            $row->delete();
        } else {
            $row->save();
        }
        return redirect()->back();
    }
}
