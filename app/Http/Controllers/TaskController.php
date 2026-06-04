<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Todo;
use App\Services\ReflectionService;
use App\Support\Dates;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $userId  = auth()->id();
        $today   = date('Y-m-d');
        $weekKey = Dates::weekKey();

        $dailyTodos  = self::todos($userId, 'daily', $today);
        $weeklyTodos = self::todos($userId, 'weekly', $weekKey);
        $note        = Note::where('user_id', $userId)->whereDate('date', $today)->value('content') ?? '';
        $reflection  = ReflectionService::get($userId, $today);
        $reflectionStreak = ReflectionService::streak($userId);

        return view('pages.tasks', compact('today', 'weekKey', 'dailyTodos', 'weeklyTodos', 'note', 'reflection', 'reflectionStreak'));
    }

    /** Array list (priority-sorted, incomplete first) for a scope+period. */
    public static function todos(int $userId, string $scope, string $periodKey): array
    {
        $pri = ['high' => 0, 'medium' => 1, 'low' => 2];
        return Todo::where('user_id', $userId)->where('scope', $scope)->where('period_key', $periodKey)
            ->get()
            ->sortBy(fn($t) => [$t->done ? 1 : 0, $pri[$t->priority] ?? 1])
            ->map(fn($t) => ['id' => $t->id, 'text' => $t->text, 'priority' => $t->priority, 'done' => (bool) $t->done])
            ->values()->toArray();
    }

    public function addDaily(Request $request)
    {
        $request->validate(['text' => 'required|string|max:255', 'priority' => 'in:high,medium,low']);
        Todo::create([
            'user_id' => auth()->id(), 'scope' => 'daily', 'period_key' => date('Y-m-d'),
            'text' => $request->text, 'priority' => $request->priority ?? 'medium', 'done' => false,
        ]);
        return redirect()->back();
    }

    public function addWeekly(Request $request)
    {
        $request->validate(['text' => 'required|string|max:255', 'priority' => 'in:high,medium,low']);
        Todo::create([
            'user_id' => auth()->id(), 'scope' => 'weekly', 'period_key' => Dates::weekKey(),
            'text' => $request->text, 'priority' => $request->priority ?? 'medium', 'done' => false,
        ]);
        return redirect()->back();
    }

    public function toggleDaily(Request $request, string $id) { return $this->toggle($id); }
    public function toggleWeekly(Request $request, string $id) { return $this->toggle($id); }

    private function toggle(string $id)
    {
        $t = Todo::where('user_id', auth()->id())->find($id);
        if ($t) { $t->done = !$t->done; $t->save(); }
        return redirect()->back();
    }

    public function deleteDaily(string $id)  { return $this->remove($id); }
    public function deleteWeekly(string $id) { return $this->remove($id); }

    private function remove(string $id)
    {
        Todo::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->back();
    }

    public function updateNote(Request $request)
    {
        Note::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => date('Y-m-d')],
            ['content' => $request->note ?? '']
        );
        if ($request->wantsJson()) return response()->json(['ok' => true]);
        return redirect()->back()->with('toast', 'Catatan tersimpan.');
    }

    public function updateReflection(Request $request)
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');
        $cur    = ReflectionService::get($userId, $today);
        $good    = $request->has('good')    ? ($request->good ?? '')    : $cur['good'];
        $improve = $request->has('improve') ? ($request->improve ?? '') : $cur['improve'];
        ReflectionService::update($userId, $today, $good, $improve);
        return redirect()->back()->with('toast', 'Refleksi tersimpan.');
    }
}
