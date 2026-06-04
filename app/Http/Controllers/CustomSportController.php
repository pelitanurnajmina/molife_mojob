<?php

namespace App\Http\Controllers;

use App\Models\CustomSportLog;
use App\Support\Dates;
use App\Support\Profile;
use Illuminate\Http\Request;

class CustomSportController extends Controller
{
    public function index(Request $request)
    {
        $userId    = auth()->id();
        $profile   = Profile::data();
        $today     = date('Y-m-d');
        $all       = CustomSportLog::where('user_id', $userId)->get()->keyBy(fn($r) => $r->date->format('Y-m-d'));
        $todayData = $this->day($all, $today);

        $weekDates = [];
        for ($i = 6; $i >= 0; $i--) $weekDates[] = date('Y-m-d', strtotime("-$i days"));
        $weekData   = array_map(fn($d) => $this->day($all, $d), $weekDates);
        $weekDone   = count(array_filter($weekData, fn($d) => $d['done']));
        $monthTotal = CustomSportLog::where('user_id', $userId)->where('done', true)
            ->where('date', '>=', date('Y-m-d', strtotime('-30 days')))->count();

        $range  = $request->query('range', 'month');
        $months = Dates::rangeToMonths($range) ?? 1;
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $result = Dates::buildStripRows($months, function ($d) use ($all, $monthShort) {
            $row = $this->day($all, $d);
            $dt  = new \DateTime($d);
            return ['active' => $row['done'], 'value' => $row['done'] ? 1 : 0,
                    'title' => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . ($row['done'] ? $row['duration'].' mnt' : 'Rest')];
        });
        $stripRows = $result['rows']; $rangeActive = $result['activeDays']; $rangeTitle = $result['title'];

        return view('pages.sports.custom', compact(
            'today', 'todayData', 'weekDates', 'weekData', 'weekDone', 'monthTotal', 'profile',
            'range', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
    }

    private function day($all, string $date): array
    {
        $r = $all[$date] ?? null;
        return ['done' => (bool) ($r->done ?? false), 'duration' => (int) ($r->duration ?? 0)];
    }

    public function update(Request $request)
    {
        $r = $request->validate(['date' => 'required|date', 'duration' => 'required|integer|min:1|max:600']);
        CustomSportLog::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $r['date']],
            ['done' => true, 'duration' => (int) $r['duration']]
        );
        return redirect()->back()->with('toast', __('Sesi tercatat!'));
    }

    public function reset(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        CustomSportLog::where('user_id', auth()->id())->whereDate('date', $request->date)->delete();
        return redirect()->back()->with('toast', __('Data dihapus.'));
    }
}
