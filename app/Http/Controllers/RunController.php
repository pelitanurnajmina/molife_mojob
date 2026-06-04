<?php

namespace App\Http\Controllers;

use App\Models\RunLog;
use App\Support\Dates;
use Illuminate\Http\Request;

class RunController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');

        $runAll = RunLog::where('user_id', $userId)->get()
            ->keyBy(fn($r) => $r->date->format('Y-m-d'));

        $todayRun = $this->dayData($runAll, $today);

        $weekDates    = Dates::weekDates();
        $monthKey     = date('Y-m');
        $weeklyCount  = count(array_filter($weekDates, fn($d) => ($runAll[$d]->done ?? false)));
        $weeklyDist   = array_sum(array_map(fn($d) => (float) ($runAll[$d]->distance ?? 0), $weekDates));
        $monthRuns    = $runAll->filter(fn($r) => $r->done && $r->date->format('Y-m') === $monthKey);
        $monthlyCount = $monthRuns->count();
        $monthlyDist  = (float) $monthRuns->sum('distance');

        // Personal bests
        $doneRuns = $runAll->filter(fn($r) => $r->done && $r->distance > 0);
        $bestDist = (float) ($doneRuns->max('distance') ?? 0);
        $paces = $doneRuns->filter(fn($r) => $r->distance > 0 && $r->duration > 0)
            ->map(fn($r) => $r->duration / $r->distance);
        $pbs = ['distance' => $bestDist, 'pace' => $paces->isEmpty() ? 0.0 : (float) $paces->min()];

        // History (last 10 done)
        $history = $runAll->filter(fn($r) => $r->done)
            ->sortKeysDesc()->take(10)
            ->map(fn($r) => [
                'date' => $r->date->format('Y-m-d'), 'distance' => (float) $r->distance,
                'duration' => (int) $r->duration, 'type' => $r->type, 'calories' => (int) $r->calories,
                'notes' => $r->notes,
            ])->values()->toArray();

        $weekRuns = array_map(fn($d) => $this->dayData($runAll, $d), $weekDates);

        // ── Range filter ──
        $range      = $request->query('range', 'month');
        $months     = Dates::rangeToMonths($range);
        $monthDates = Dates::monthDates();
        $runAllArr  = $runAll->map(fn($r) => ['done' => (bool) $r->done, 'distance' => (float) $r->distance])->toArray();

        $stripRows = []; $rangeActive = 0; $rangeTitle = '';
        if ($months !== null) {
            $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $result = Dates::buildStripRows($months, function ($d) use ($runAllArr, $monthShort) {
                $done = $runAllArr[$d]['done'] ?? false;
                $km   = $runAllArr[$d]['distance'] ?? 0;
                $dt   = new \DateTime($d);
                return ['active' => $done, 'value' => $done ? 1 : 0,
                        'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': '
                                 . ($done ? ($km > 0 ? $km . ' km' : 'Lari') : 'Rest')];
            });
            $stripRows = $result['rows']; $rangeActive = $result['activeDays']; $rangeTitle = $result['title'];
        }

        return view('pages.run.index', compact(
            'today', 'todayRun', 'weeklyCount', 'weeklyDist', 'monthlyCount', 'monthlyDist',
            'pbs', 'history', 'weekRuns', 'range', 'months', 'stripRows', 'rangeActive', 'rangeTitle',
            'monthDates', 'runAllArr'
        ));
    }

    private function dayData($runAll, string $date): array
    {
        $r = $runAll[$date] ?? null;
        return [
            'done'     => (bool) ($r->done ?? false),
            'distance' => (float) ($r->distance ?? 0),
            'duration' => (int) ($r->duration ?? 0),
            'type'     => $r->type ?? 'easy',
            'calories' => (int) ($r->calories ?? 0),
            'notes'    => $r->notes ?? '',
        ];
    }

    public function toggle(Request $request)
    {
        $userId = auth()->id();
        $date   = $request->input('date', date('Y-m-d'));
        $r = RunLog::firstOrNew(['user_id' => $userId, 'date' => $date]);
        $r->done = !($r->done ?? false);
        $r->save();
        return back()->with('toast', 'Status lari diperbarui.');
    }

    public function update(Request $request)
    {
        $v = $request->validate([
            'date'     => 'required|date',
            'distance' => 'required|numeric|min:0|max:500',
            'duration' => 'required|integer|min:0|max:1440',
            'type'     => 'required|in:easy,tempo,interval,long_run,race',
            'calories' => 'nullable|integer|min:0|max:9999',
            'notes'    => 'nullable|string|max:500',
        ]);

        RunLog::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $v['date']],
            [
                'done'     => true,
                'distance' => (float) $v['distance'],
                'duration' => (int) $v['duration'],
                'type'     => $v['type'],
                'calories' => (int) ($v['calories'] ?? 0),
                'notes'    => $v['notes'] ?? '',
            ]
        );

        return back()->with('toast', 'Data lari tersimpan!');
    }
}
