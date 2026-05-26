<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $storage   = UserStorage::fromSession();
        $today     = date('Y-m-d');
        $weekKey   = UserStorage::getWeekKey();

        $dailyTodos  = $storage->getDailyTodos($today);
        $weeklyTodos = $storage->getWeeklyTodos($weekKey);
        $note        = $storage->getNote($today);
        $reflection  = $storage->getReflection($today);

        // Count reflection streak last 7 days
        $reflectionStreak = 0;
        for ($i = 0; $i < 7; $i++) {
            $d  = new \DateTime();
            $d->modify("-$i days");
            $ds = $d->format('Y-m-d');
            $r  = $storage->getReflection($ds);
            if ($r['good'] || $r['improve']) $reflectionStreak++;
        }

        return view('pages.tasks', compact('today', 'weekKey', 'dailyTodos', 'weeklyTodos', 'note', 'reflection', 'reflectionStreak'));
    }

    public function addDaily(Request $request)
    {
        $request->validate(['text' => 'required|string|max:255']);
        $storage = UserStorage::fromSession();
        $storage->addDailyTodo(date('Y-m-d'), $request->text);
        $storage->save();
        return redirect()->back();
    }

    public function addWeekly(Request $request)
    {
        $request->validate(['text' => 'required|string|max:255']);
        $storage = UserStorage::fromSession();
        $storage->addWeeklyTodo(UserStorage::getWeekKey(), $request->text);
        $storage->save();
        return redirect()->back();
    }

    public function toggleDaily(Request $request, string $id)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleDailyTodo(date('Y-m-d'), $id);
        $storage->save();
        return redirect()->back();
    }

    public function toggleWeekly(Request $request, string $id)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleWeeklyTodo(UserStorage::getWeekKey(), $id);
        $storage->save();
        return redirect()->back();
    }

    public function deleteDaily(string $id)
    {
        $storage = UserStorage::fromSession();
        $storage->deleteDailyTodo(date('Y-m-d'), $id);
        $storage->save();
        return redirect()->back();
    }

    public function deleteWeekly(string $id)
    {
        $storage = UserStorage::fromSession();
        $storage->deleteWeeklyTodo(UserStorage::getWeekKey(), $id);
        $storage->save();
        return redirect()->back();
    }

    public function updateNote(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->updateNote(date('Y-m-d'), $request->note ?? '');
        $storage->save();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->back()->with('toast', 'Catatan tersimpan.');
    }

    public function updateReflection(Request $request)
    {
        $storage = UserStorage::fromSession();
        $today   = date('Y-m-d');
        if ($request->has('good'))    $storage->updateReflection($today, 'good', $request->good ?? '');
        if ($request->has('improve')) $storage->updateReflection($today, 'improve', $request->improve ?? '');
        $storage->save();
        return redirect()->back()->with('toast', 'Refleksi tersimpan.');
    }
}
