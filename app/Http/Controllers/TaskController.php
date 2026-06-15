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

        // Whether any past task data exists (to show the History button)
        $hasHistory = Todo::where('user_id', $userId)
            ->where(fn($q) => $q->where(fn($d) => $d->where('scope', 'daily')->where('period_key', '<', $today))
                                 ->orWhere(fn($w) => $w->where('scope', 'weekly')->where('period_key', '<', $weekKey)))
            ->exists();

        return view('pages.tasks', compact(
            'today', 'weekKey', 'dailyTodos', 'weeklyTodos', 'note', 'reflection', 'reflectionStreak',
            'hasHistory'
        ));
    }

    /** Dedicated history page: full daily + weekly task history. */
    public function history()
    {
        $userId  = auth()->id();
        $today   = date('Y-m-d');
        $weekKey = Dates::weekKey();

        $dailyHistory  = self::dailyHistory($userId, $today, 30);
        $weeklyHistory = self::weeklyHistory($userId, $weekKey, 12);

        return view('pages.tasks-history', compact('dailyHistory', 'weeklyHistory'));
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

    /** Last N days (excluding today) of daily-task completion summaries. */
    public static function dailyHistory(int $userId, string $today, int $days = 7): array
    {
        $rows = Todo::where('user_id', $userId)->where('scope', 'daily')
            ->where('period_key', '<', $today)
            ->where('period_key', '>=', date('Y-m-d', strtotime($today . " -{$days} days")))
            ->get()->groupBy('period_key');

        $out = [];
        foreach ($rows as $key => $items) {
            $total = $items->count();
            $done  = $items->where('done', true)->count();
            $out[] = [
                'key'   => $key,
                'label' => date('l, j F Y', strtotime($key)),
                'total' => $total,
                'done'  => $done,
                'tasks' => $items->map(fn($t) => ['text' => $t->text, 'done' => (bool) $t->done])->values()->toArray(),
            ];
        }
        usort($out, fn($a, $b) => strcmp($b['key'], $a['key']));
        return $out;
    }

    /** Past N weeks (excluding current) of weekly-task completion summaries. */
    public static function weeklyHistory(int $userId, string $weekKey, int $weeks = 8): array
    {
        $rows = Todo::where('user_id', $userId)->where('scope', 'weekly')
            ->where('period_key', '<', $weekKey)
            ->where('period_key', '>=', Dates::weekKey(date('Y-m-d', strtotime($weekKey . ' -' . ($weeks * 7) . ' days'))))
            ->get()->groupBy('period_key');

        $out = [];
        foreach ($rows as $key => $items) {
            $start = strtotime($key);
            $end   = strtotime($key . ' +6 days');
            $out[] = [
                'key'   => $key,
                'label' => date('j M', $start) . ' – ' . date('j M Y', $end),
                'total' => $items->count(),
                'done'  => $items->where('done', true)->count(),
                'tasks' => $items->map(fn($t) => ['text' => $t->text, 'done' => (bool) $t->done])->values()->toArray(),
            ];
        }
        usort($out, fn($a, $b) => strcmp($b['key'], $a['key']));
        return $out;
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
