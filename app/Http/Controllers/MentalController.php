<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class MentalController extends Controller
{
    public function index()
    {
        $storage  = UserStorage::fromSession();
        $today    = date('Y-m-d');

        $todayMood      = $storage->getMood($today);
        $moodHistory    = $storage->getMoodHistory(30);
        $moodAvg7       = $storage->getMoodAvg(7);
        $moodAvg30      = $storage->getMoodAvg(30);
        $energyAvg7     = $storage->getEnergyAvg(7);
        $todayReflection = $storage->getReflection($today);
        $todayNote      = $storage->getNote($today);

        $daysLogged = count(array_filter($moodHistory, fn($d) => $d['score'] > 0));

        // Reflection streak (last 7 days)
        $reflectionStreak = 0;
        for ($i = 1; $i <= 7; $i++) {
            $d = new \DateTime();
            $d->modify("-$i days");
            $r = $storage->getReflection($d->format('Y-m-d'));
            if (!empty($r['good']) || !empty($r['improve'])) $reflectionStreak++;
        }

        // Full reflection history (newest first), today included & flagged
        $reflectionHistory = array_map(function ($entry) use ($today) {
            $entry['label']   = (new \DateTime($entry['date']))->format('l, j F Y');
            $entry['isToday'] = $entry['date'] === $today;
            return $entry;
        }, $storage->getAllReflections());

        return view('pages.mental', compact(
            'today', 'todayMood', 'moodHistory',
            'moodAvg7', 'moodAvg30', 'energyAvg7',
            'todayReflection', 'todayNote', 'daysLogged',
            'reflectionStreak', 'reflectionHistory'
        ));
    }

    public function deleteReflection(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $storage = UserStorage::fromSession();
        $storage->deleteReflection($request->date);
        $storage->save();
        return redirect()->back()->with('toast', __('Refleksi dihapus.'));
    }

    public function storeMood(Request $request)
    {
        $validated = $request->validate([
            'date'   => 'required|date',
            'score'  => 'required|integer|min:1|max:5',
            'energy' => 'required|integer|min:1|max:5',
            'note'   => 'nullable|string|max:500',
        ]);

        $storage = UserStorage::fromSession();
        $storage->saveMood(
            $validated['date'],
            (int) $validated['score'],
            (int) $validated['energy'],
            $validated['note'] ?? ''
        );
        $storage->save();

        return redirect()->back()->with('toast', __('Mood tercatat!'));
    }
}
