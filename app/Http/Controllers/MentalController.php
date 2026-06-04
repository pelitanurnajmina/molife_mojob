<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Services\MoodService;
use App\Services\ReflectionService;
use Illuminate\Http\Request;

class MentalController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');

        $todayMood   = MoodService::get($userId, $today);
        $moodHistory = MoodService::history($userId, 30);
        $moodAvg7    = MoodService::avgScore($userId, 7);
        $moodAvg30   = MoodService::avgScore($userId, 30);
        $energyAvg7  = MoodService::avgEnergy($userId, 7);

        $todayReflection = ReflectionService::get($userId, $today);
        $todayNote       = Note::where('user_id', $userId)->whereDate('date', $today)->value('content') ?? '';

        $daysLogged = count(array_filter($moodHistory, fn($d) => $d['score'] > 0));

        $reflectionStreak  = ReflectionService::streak($userId);
        $reflectionHistory = array_map(function ($entry) use ($today) {
            $entry['label']   = (new \DateTime($entry['date']))->format('l, j F Y');
            $entry['isToday'] = $entry['date'] === $today;
            return $entry;
        }, ReflectionService::all($userId));

        return view('pages.mental', compact(
            'today', 'todayMood', 'moodHistory',
            'moodAvg7', 'moodAvg30', 'energyAvg7',
            'todayReflection', 'todayNote', 'daysLogged',
            'reflectionStreak', 'reflectionHistory'
        ));
    }

    public function storeMood(Request $request)
    {
        $v = $request->validate([
            'date'   => 'required|date',
            'score'  => 'required|integer|min:1|max:5',
            'energy' => 'required|integer|min:1|max:5',
            'note'   => 'nullable|string|max:500',
        ]);

        MoodService::save(auth()->id(), $v['date'], (int) $v['score'], (int) $v['energy'], $v['note'] ?? '');

        return redirect()->back()->with('toast', __('Mood tercatat!'));
    }

    public function deleteReflection(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        ReflectionService::delete(auth()->id(), $request->date);
        return redirect()->back()->with('toast', __('Refleksi dihapus.'));
    }
}
