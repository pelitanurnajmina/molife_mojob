<?php

namespace App\Http\Controllers;

use App\Support\Profile;
use App\Support\Features;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        if (Profile::model()->setup_done) {
            return redirect()->route('dashboard');
        }
        return view('pages.onboarding');
    }

    public function store(Request $request)
    {
        $request->validate([
            'display_name'      => 'required|string|max:100',
            'gender'            => 'required|in:male,female',
            'sports'            => 'nullable|array',
            'custom_sport_name' => 'nullable|string|max:50',
            'features'          => 'nullable|array',
            'features.*'        => 'string',
        ]);

        $userId   = auth()->id();
        $sports   = $request->sports ?? [];
        $features = $request->features ?? [];

        // Save profile (Molife is Islam-focused — religion fixed to 'islam').
        $profile = Profile::model($userId);
        $profile->fill([
            'setup_done'        => true,
            'display_name'      => trim($request->display_name),
            'gender'            => $request->gender,
            'religion'          => 'islam',
            'custom_sport_name' => trim($request->custom_sport_name ?? ''),
        ])->save();

        // Spiritual feature: sholat tracker on for everyone.
        Features::set($userId, 'sholat', true);
        Features::set($userId, 'spiritual', false);

        // Sport features
        foreach (['gym', 'run', 'cycling', 'swimming', 'racket', 'custom_sport'] as $sport) {
            Features::set($userId, $sport, in_array($sport, $sports));
        }

        // Other opt-in features chosen in step 4
        foreach (['tasks', 'pomodoro', 'mental', 'motivasi', 'finance', 'lamaran', 'intimasi', 'porn', 'sosmed'] as $feat) {
            Features::set($userId, $feat, in_array($feat, $features));
        }

        // User baru yang datang dari link undangan kolaborasi: terima undangannya
        // dan antar langsung ke workspace proyek (tanpa mampir halaman langganan).
        if ($token = session('collab_invite_token')) {
            [$collab, $error] = \App\Services\CollabService::acceptByToken($token, auth()->user());
            session()->forget(['collab_invite_token', 'collab_invite_email', 'collab_invite_info', 'url.intended']);
            if ($collab) {
                return redirect()->route('kolaborasi.workspace', $collab->business_product_id)
                    ->with('toast', __('Selamat datang, :name! Kamu resmi jadi kolaborator proyek ini.', ['name' => $request->display_name]));
            }
        }

        return redirect()->route('dashboard')
            ->with('toast', __('Selamat datang, :name! Profile kamu sudah siap.', ['name' => $request->display_name]));
    }
}
