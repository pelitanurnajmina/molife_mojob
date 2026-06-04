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
            'religion'          => 'required|string|in:islam,kristen,hindu,buddha,lainnya,none',
            'sports'            => 'nullable|array',
            'custom_sport_name' => 'nullable|string|max:50',
        ]);

        $userId   = auth()->id();
        $religion = $request->religion;
        $sports   = $request->sports ?? [];

        // Save profile
        $profile = Profile::model($userId);
        $profile->fill([
            'setup_done'        => true,
            'display_name'      => trim($request->display_name),
            'religion'          => $religion,
            'custom_sport_name' => trim($request->custom_sport_name ?? ''),
        ])->save();

        // Spiritual features from religion
        if ($religion === 'islam') {
            Features::set($userId, 'sholat', true);
            Features::set($userId, 'spiritual', false);
        } elseif ($religion === 'none') {
            Features::set($userId, 'sholat', false);
            Features::set($userId, 'spiritual', false);
        } else {
            Features::set($userId, 'sholat', false);
            Features::set($userId, 'spiritual', true);
        }

        // Sport features
        foreach (['gym', 'run', 'cycling', 'swimming', 'racket', 'custom_sport'] as $sport) {
            Features::set($userId, $sport, in_array($sport, $sports));
        }

        return redirect()->route('dashboard')
            ->with('toast', __('Selamat datang, :name! Profile kamu sudah siap.', ['name' => $request->display_name]));
    }
}
