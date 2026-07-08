<?php

namespace App\Http\Controllers;

use App\Models\MeditationSession;
use App\Services\MeditationService;
use Illuminate\Http\Request;

class MeditasiController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        return view('pages.meditasi', [
            'stats'   => MeditationService::stats($userId),
            'history' => MeditationSession::where('user_id', $userId)
                ->orderByDesc('date')->orderByDesc('id')->limit(14)->get(),
        ]);
    }

    /** Dipanggil via fetch saat sesi selesai (atau dihentikan >= 1 menit). */
    public function store(Request $request)
    {
        $v = $request->validate([
            'minutes' => 'required|integer|min:1|max:180',
            'sound'   => 'nullable|in:hening,hujan,ombak,angin',
            'note'    => 'nullable|string|max:200',
        ]);

        MeditationSession::create([
            'user_id' => auth()->id(),
            'date'    => now()->toDateString(),
            'minutes' => $v['minutes'],
            'sound'   => $v['sound'] ?? null,
            'note'    => $v['note'] ?? null,
        ]);

        return response()->json([
            'ok'    => true,
            'stats' => MeditationService::stats(auth()->id()),
        ]);
    }

    public function destroy(string $id)
    {
        MeditationSession::where('user_id', auth()->id())->findOrFail($id)->delete();
        return redirect()->route('meditasi')->with('toast', __('Sesi dihapus.'));
    }
}
