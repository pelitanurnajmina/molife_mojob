<?php

namespace App\Http\Controllers;

use App\Models\PomodoroSession;
use App\Support\Dates;
use Illuminate\Http\Request;

class PomodoroController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');

        $todaySessions = PomodoroSession::where('user_id', $userId)->whereDate('date', $today)->get();
        $todayCount    = $todaySessions->count();
        $todayMinutes  = (int) $todaySessions->sum('focus_minutes');

        // This week
        $weekDates    = Dates::weekDates();
        $weekSessions = PomodoroSession::where('user_id', $userId)->whereIn('date', $weekDates)->get();
        $weekCount    = $weekSessions->count();
        $weekMinutes  = (int) $weekSessions->sum('focus_minutes');

        // All-time
        $totalCount   = PomodoroSession::where('user_id', $userId)->count();
        $totalMinutes = (int) PomodoroSession::where('user_id', $userId)->sum('focus_minutes');

        // Per-day counts for the week chart
        $weekData = array_map(
            fn($d) => $weekSessions->filter(fn($s) => $s->date->format('Y-m-d') === $d)->count(),
            $weekDates
        );

        // Recent history (last 20)
        $history = PomodoroSession::where('user_id', $userId)
            ->orderByDesc('created_at')->limit(20)->get()
            ->map(fn($s) => [
                'label'   => $s->label,
                'minutes' => (int) $s->focus_minutes,
                'when'    => $s->created_at->format('d M Y · H:i'),
            ])->toArray();

        return view('pages.pomodoro', compact(
            'todayCount', 'todayMinutes', 'weekCount', 'weekMinutes',
            'totalCount', 'totalMinutes', 'weekData', 'weekDates', 'history'
        ));
    }

    public function store(Request $request)
    {
        $r = $request->validate([
            'focus_minutes' => 'required|integer|min:1|max:180',
            'label'         => 'nullable|string|max:120',
        ]);

        PomodoroSession::create([
            'user_id'       => auth()->id(),
            'date'          => date('Y-m-d'),
            'focus_minutes' => $r['focus_minutes'],
            'label'         => $r['label'] ?? null,
        ]);

        if ($request->wantsJson()) return response()->json(['ok' => true]);
        return redirect()->back()->with('toast', __('Sesi fokus tercatat!'));
    }
}
