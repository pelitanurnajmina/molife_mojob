<?php

namespace App\Http\Controllers;

use App\Services\QuitService;
use Illuminate\Http\Request;

class QuitController extends Controller
{
    private function guard(string $type): void
    {
        abort_unless(in_array($type, QuitService::TYPES), 404);
    }

    public function index(string $type)
    {
        $this->guard($type);
        $userId = auth()->id();
        $meta    = QuitService::meta($type);
        $stats   = QuitService::stats($userId, $type);
        $history = QuitService::history($userId, $type);

        return view('pages.quit.index', compact('type', 'meta', 'stats', 'history'));
    }

    public function relapse(Request $request, string $type)
    {
        $this->guard($type);
        $request->validate(['note' => 'nullable|string|max:200']);
        QuitService::relapse(auth()->id(), $type, $request->note ?? '');
        return redirect()->back()->with('toast', __('Streak di-reset. Mulai lagi, kamu pasti bisa!'));
    }
}
