<?php

namespace App\Http\Controllers;

use App\Models\SwimmingLog;
use App\Support\Dates;
use Illuminate\Http\Request;

class SwimmingController extends Controller
{
    public function index(Request $request)
    {
        $userId    = auth()->id();
        $today     = date('Y-m-d');
        $all       = SwimmingLog::where('user_id', $userId)->get()->keyBy(fn($r) => $r->date->format('Y-m-d'));
        $todayData = $this->day($all, $today);

        $weekDates = [];
        for ($i = 6; $i >= 0; $i--) $weekDates[] = date('Y-m-d', strtotime("-$i days"));
        $weekData = array_map(fn($d) => $this->day($all, $d), $weekDates);
        $weekDone = count(array_filter($weekData, fn($d) => $d['done']));
        $bestLaps = (int) ($all->max('laps') ?? 0);

        $range  = $request->query('range', 'month');
        $months = Dates::rangeToMonths($range) ?? 1;
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $result = Dates::buildStripRows($months, function ($d) use ($all, $monthShort) {
            $row = $this->day($all, $d);
            $dt  = new \DateTime($d);
            return ['active' => $row['done'], 'value' => $row['done'] ? 1 : 0,
                    'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . ($row['done'] ? $row['laps'].' lap' : 'Rest')];
        });
        $stripRows = $result['rows']; $rangeActive = $result['activeDays']; $rangeTitle = $result['title'];

        return view('pages.sports.swimming', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'bestLaps',
            'range', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    private function day($all, string $date): array
    {
        $r = $all[$date] ?? null;
        return ['done' => (bool) ($r->done ?? false), 'laps' => (int) ($r->laps ?? 0), 'duration' => (int) ($r->duration ?? 0)];
    }

    public function update(Request $request)
    {
        $r = $request->validate([
            'date' => 'required|date', 'laps' => 'required|integer|min:0|max:1000', 'duration' => 'required|integer|min:0|max:600',
        ]);
        SwimmingLog::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $r['date']],
            ['done' => true, 'laps' => (int) $r['laps'], 'duration' => (int) $r['duration']]
        );
        return redirect()->back()->with('toast', __('Data renang tersimpan!'));
    }

    public function reset(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        SwimmingLog::where('user_id', auth()->id())->whereDate('date', $request->date)->delete();
        return redirect()->back()->with('toast', __('Data dihapus.'));
    }
}
