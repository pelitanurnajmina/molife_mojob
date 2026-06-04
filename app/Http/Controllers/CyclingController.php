<?php

namespace App\Http\Controllers;

use App\Models\CyclingLog;
use App\Support\Dates;
use Illuminate\Http\Request;

class CyclingController extends Controller
{
    public function index(Request $request)
    {
        $userId    = auth()->id();
        $today     = date('Y-m-d');
        $all       = CyclingLog::where('user_id', $userId)->get()->keyBy(fn($r) => $r->date->format('Y-m-d'));
        $todayData = $this->day($all, $today);

        $weekDates = [];
        for ($i = 6; $i >= 0; $i--) $weekDates[] = date('Y-m-d', strtotime("-$i days"));
        $weekData = array_map(fn($d) => $this->day($all, $d), $weekDates);
        $weekDone = count(array_filter($weekData, fn($d) => $d['done']));
        $weekKm   = array_sum(array_column($weekData, 'km'));
        $bestKm   = (float) ($all->max('km') ?? 0);

        $range  = $request->query('range', 'month');
        $months = Dates::rangeToMonths($range) ?? 1;
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $result = Dates::buildStripRows($months, function ($d) use ($all, $monthShort) {
            $row  = $this->day($all, $d);
            $dt   = new \DateTime($d);
            return ['active' => $row['done'], 'value' => $row['done'] ? 1 : 0,
                    'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . ($row['done'] ? $row['km'].' km' : 'Rest')];
        });
        $stripRows = $result['rows']; $rangeActive = $result['activeDays']; $rangeTitle = $result['title'];

        return view('pages.sports.cycling', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'weekKm', 'bestKm',
            'range', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    private function day($all, string $date): array
    {
        $r = $all[$date] ?? null;
        return ['done' => (bool) ($r->done ?? false), 'km' => (float) ($r->km ?? 0), 'duration' => (int) ($r->duration ?? 0)];
    }

    public function update(Request $request)
    {
        $r = $request->validate([
            'date' => 'required|date', 'km' => 'required|numeric|min:0|max:500', 'duration' => 'required|integer|min:0|max:600',
        ]);
        CyclingLog::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $r['date']],
            ['done' => true, 'km' => (float) $r['km'], 'duration' => (int) $r['duration']]
        );
        return redirect()->back()->with('toast', __('Data bersepeda tersimpan!'));
    }

    public function reset(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        CyclingLog::where('user_id', auth()->id())->whereDate('date', $request->date)->delete();
        return redirect()->back()->with('toast', __('Data dihapus.'));
    }
}
