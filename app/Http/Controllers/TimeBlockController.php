<?php

namespace App\Http\Controllers;

use App\Models\TimeBlock;
use App\Models\Todo;
use App\Services\PrayerTimeService;
use App\Support\Features;
use App\Support\Profile;
use Illuminate\Http\Request;

class TimeBlockController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $feats  = Features::map($userId);

        // Tanggal acuan → awal minggu (Senin).
        $ref       = self::validDate($request->query('date')) ?? date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($ref)));
        $days      = [];
        for ($i = 0; $i < 7; $i++) $days[] = date('Y-m-d', strtotime("$weekStart +$i days"));
        $weekEnd = $days[6];

        // Blok manual minggu ini (dikelompokkan per tanggal; date di-cast Carbon,
        // jadi kelompokkan pakai string 'Y-m-d' agar cocok saat dipetakan per hari).
        $blocksByDate = TimeBlock::where('user_id', $userId)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get()
            ->groupBy(fn($b) => $b->date->format('Y-m-d'));

        // Jadwal sholat (read-only) per hari, jika fitur aktif & kota diset.
        $prayerCity  = Profile::prayerCity($userId);
        $prayerOn    = ($feats['sholat'] ?? false) && $prayerCity;

        // Tugas harian (read-only, all-day) jika fitur aktif.
        $tasksOn = $feats['tasks'] ?? false;
        $todos   = $tasksOn
            ? Todo::where('user_id', $userId)->where('scope', 'daily')
                ->whereIn('period_key', $days)->orderBy('id')->get()->groupBy('period_key')
            : collect();

        // Bangun peta event per tanggal.
        $eventsByDate = [];
        foreach ($days as $d) {
            $timed  = [];
            $allday = [];

            // Blok manual (bisa diedit).
            foreach (($blocksByDate[$d] ?? []) as $b) {
                $timed[] = [
                    'id'        => $b->id,
                    'source'    => 'block',
                    'editable'  => true,
                    'title'     => $b->title,
                    'note'      => $b->note,
                    'color'     => in_array($b->color, TimeBlock::COLORS, true) ? $b->color : 'blue',
                    'startMin'  => (int) $b->start_min,
                    'endMin'    => (int) $b->end_min,
                ];
            }
            $timed = self::assignLanes($timed);

            // Jadwal sholat sebagai band read-only (di belakang blok).
            $prayers = [];
            if ($prayerOn) {
                foreach (PrayerTimeService::forCity($prayerCity, $d) as $name => $hhmm) {
                    $m = self::hhmmToMin($hhmm);
                    if ($m === null) continue;
                    $prayers[] = [
                        'title'    => $name,
                        'timeLabel'=> $hhmm,
                        'startMin' => $m,
                        'endMin'   => min(1440, $m + 25),
                    ];
                }
            }

            // Tugas harian → chip all-day.
            foreach (($todos[$d] ?? collect()) as $t) {
                $allday[] = ['title' => $t->text, 'done' => (bool) $t->done];
            }

            $eventsByDate[$d] = ['timed' => $timed, 'prayers' => $prayers, 'allday' => $allday];
        }

        // Hari terpilih untuk tampilan mobile (hari ini kalau dalam minggu ini, kalau tidak Senin).
        $today        = date('Y-m-d');
        $selectedDate = in_array($today, $days, true) ? $today : $weekStart;

        return view('pages.timeblock', [
            'weekStart'    => $weekStart,
            'weekEnd'      => $weekEnd,
            'days'         => $days,
            'today'        => $today,
            'selectedDate' => $selectedDate,
            'prevDate'     => date('Y-m-d', strtotime("$weekStart -7 days")),
            'nextDate'     => date('Y-m-d', strtotime("$weekStart +7 days")),
            'eventsByDate' => $eventsByDate,
            'colors'       => TimeBlock::COLORS,
            'prayerOn'     => $prayerOn,
            'tasksOn'      => $tasksOn,
        ]);
    }

    private array $rules = [
        'date'      => 'required|date',
        'start_min' => 'required|integer|min:0|max:1439',
        'end_min'   => 'required|integer|min:1|max:1440',
        'title'     => 'required|string|max:200',
        'color'     => 'nullable|string|max:20',
        'note'      => 'nullable|string|max:1000',
    ];

    public function store(Request $request)
    {
        $data = $this->normalize($request->validate($this->rules));
        $data['user_id'] = auth()->id();
        TimeBlock::create($data);

        return redirect()->route('timeblock', ['date' => $data['date']])->with('toast', __('Blok waktu ditambahkan.'));
    }

    public function update(Request $request, string $id)
    {
        $block = TimeBlock::where('user_id', auth()->id())->findOrFail($id);
        $block->update($this->normalize($request->validate($this->rules)));

        return redirect()->route('timeblock', ['date' => $block->date->format('Y-m-d')])->with('toast', __('Blok waktu diperbarui.'));
    }

    public function destroy(string $id)
    {
        $block = TimeBlock::where('user_id', auth()->id())->findOrFail($id);
        $date  = $block->date->format('Y-m-d');
        $block->delete();

        return redirect()->route('timeblock', ['date' => $date])->with('toast', __('Blok waktu dihapus.'));
    }

    /** Salin blok ke tanggal lain (jam, judul, warna, catatan sama). */
    public function copy(Request $request, string $id)
    {
        $block = TimeBlock::where('user_id', auth()->id())->findOrFail($id);
        $r = $request->validate(['date' => 'required|date']);
        $target = date('Y-m-d', strtotime($r['date']));

        TimeBlock::create([
            'user_id'   => auth()->id(),
            'date'      => $target,
            'start_min' => $block->start_min,
            'end_min'   => $block->end_min,
            'title'     => $block->title,
            'color'     => $block->color,
            'note'      => $block->note,
        ]);

        return redirect()->route('timeblock', ['date' => $target])->with('toast', __('Blok disalin ke :date.', ['date' => $target]));
    }

    /** Pastikan end > start; kalau tidak, beri durasi minimal 15 menit. */
    private function normalize(array $data): array
    {
        if (!in_array($data['color'] ?? null, TimeBlock::COLORS, true)) $data['color'] = 'blue';
        if ($data['end_min'] <= $data['start_min']) {
            $data['end_min'] = min(1440, $data['start_min'] + 15);
        }
        return $data;
    }

    /** Bagi event yang tumpang tindih ke beberapa "lane" berdampingan. */
    private static function assignLanes(array $events): array
    {
        usort($events, fn($a, $b) => ($a['startMin'] <=> $b['startMin']) ?: ($a['endMin'] <=> $b['endMin']));

        $result     = [];
        $cluster    = [];
        $clusterEnd = -1;

        $flush = function (array $cluster) use (&$result) {
            $lanes = []; // laneIndex => lastEndMin
            foreach ($cluster as &$e) {
                $placed = false;
                foreach ($lanes as $i => $end) {
                    if ($e['startMin'] >= $end) { $e['lane'] = $i; $lanes[$i] = $e['endMin']; $placed = true; break; }
                }
                if (!$placed) { $i = count($lanes); $e['lane'] = $i; $lanes[$i] = $e['endMin']; }
            }
            unset($e);
            $count = count($lanes);
            foreach ($cluster as $e) { $e['laneCount'] = $count; $result[] = $e; }
        };

        foreach ($events as $e) {
            if (!empty($cluster) && $e['startMin'] >= $clusterEnd) {
                $flush($cluster);
                $cluster = [];
                $clusterEnd = -1;
            }
            $cluster[]  = $e;
            $clusterEnd = max($clusterEnd, $e['endMin']);
        }
        if (!empty($cluster)) $flush($cluster);

        return $result;
    }

    private static function hhmmToMin(?string $hhmm): ?int
    {
        if (!$hhmm || !preg_match('/^(\d{1,2}):(\d{2})$/', $hhmm, $m)) return null;
        return ((int) $m[1]) * 60 + (int) $m[2];
    }

    private static function validDate(?string $d): ?string
    {
        if (!$d || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return null;
        return date('Y-m-d', strtotime($d)) === $d ? $d : null;
    }
}
