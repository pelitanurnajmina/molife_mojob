<?php

namespace App\Http\Controllers;

use App\Models\HaidCycle;
use App\Services\HaidService;
use App\Support\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HaidController extends Controller
{
    /** Fitur khusus akun perempuan. */
    private function gate()
    {
        if (!Profile::isFemale()) {
            return redirect()->route('dashboard')->with('toast', __('Fitur Siklus Haid khusus akun perempuan. Atur gender di Settings > Profil.'));
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($r = $this->gate()) return $r;

        $userId = auth()->id();
        $data   = HaidService::data($userId);

        // Periode berjalan: pastikan hari-harinya tersinkron sebagai uzur sholat.
        if ($data['ongoing']) {
            HaidService::syncExcused($userId, $data['ongoing']->start_date, null);
        }

        // Kalender bulan (navigasi ?bulan=YYYY-MM).
        $month = $request->query('bulan');
        $monthStart = preg_match('/^\d{4}-\d{2}$/', (string) $month)
            ? Carbon::createFromFormat('Y-m-d', $month . '-01')->startOfDay()
            : Carbon::today()->startOfMonth();

        $calendar = HaidService::calendarMap($userId, $monthStart, $data);

        return view('pages.haid', array_merge($data, [
            'monthStart' => $monthStart,
            'calendar'   => $calendar,
        ]));
    }

    /** Catat periode: cepat (mulai hari ini) atau manual (rentang tanggal lampau). */
    public function store(Request $request)
    {
        if ($r = $this->gate()) return $r;

        $v = $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date'   => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
        ]);

        $userId = auth()->id();
        $start  = Carbon::parse($v['start_date']);
        $end    = isset($v['end_date']) && $v['end_date'] ? Carbon::parse($v['end_date']) : null;

        if (!$end && HaidCycle::where('user_id', $userId)->whereNull('end_date')->exists()) {
            return back()->with('toast', __('Masih ada periode yang berjalan. Akhiri dulu sebelum memulai yang baru.'));
        }
        if (HaidService::overlaps($userId, $start, $end)) {
            return back()->with('toast', __('Rentang tanggal ini bertabrakan dengan periode yang sudah tercatat.'));
        }

        HaidCycle::create([
            'user_id'    => $userId,
            'start_date' => $start->toDateString(),
            'end_date'   => $end?->toDateString(),
        ]);
        HaidService::syncExcused($userId, $start, $end);

        return redirect()->route('haid')->with('toast', __('Periode haid dicatat. Hari uzur sholat ikut ditandai otomatis.'));
    }

    /** Akhiri periode yang sedang berjalan (hari ini). */
    public function finish()
    {
        if ($r = $this->gate()) return $r;

        $userId  = auth()->id();
        $ongoing = HaidCycle::where('user_id', $userId)->whereNull('end_date')->first();
        if (!$ongoing) {
            return back()->with('toast', __('Tidak ada periode yang sedang berjalan.'));
        }

        $ongoing->update(['end_date' => now()->toDateString()]);
        HaidService::syncExcused($userId, $ongoing->start_date, Carbon::today());

        return redirect()->route('haid')->with('toast', __('Periode selesai dicatat. Semoga sehat selalu!'));
    }

    public function update(Request $request, string $id)
    {
        if ($r = $this->gate()) return $r;

        $userId = auth()->id();
        $cycle  = HaidCycle::where('user_id', $userId)->findOrFail($id);

        $v = $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date'   => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
        ]);

        $newStart = Carbon::parse($v['start_date']);
        $newEnd   = isset($v['end_date']) && $v['end_date'] ? Carbon::parse($v['end_date']) : null;

        if (HaidService::overlaps($userId, $newStart, $newEnd, $cycle->id)) {
            return back()->with('toast', __('Rentang tanggal ini bertabrakan dengan periode lain.'));
        }

        // Lepas tanda uzur rentang lama, pasang untuk rentang baru.
        HaidService::removeExcused($userId, $cycle->start_date, $cycle->end_date);
        $cycle->update(['start_date' => $newStart->toDateString(), 'end_date' => $newEnd?->toDateString()]);
        HaidService::syncExcused($userId, $newStart, $newEnd);

        return redirect()->route('haid')->with('toast', __('Periode diperbarui.'));
    }

    public function destroy(string $id)
    {
        if ($r = $this->gate()) return $r;

        $userId = auth()->id();
        $cycle  = HaidCycle::where('user_id', $userId)->findOrFail($id);

        HaidService::removeExcused($userId, $cycle->start_date, $cycle->end_date);
        $cycle->delete();

        return redirect()->route('haid')->with('toast', __('Periode dihapus. Tanda uzur di rentang itu ikut dihapus.'));
    }
}
