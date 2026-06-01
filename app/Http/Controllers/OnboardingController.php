<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        $profile = UserStorage::fromSession()->getProfile();
        if ($profile['setup_done']) {
            return redirect()->route('dashboard');
        }
        return view('pages.onboarding');
    }

    public function store(Request $request)
    {
        $request->validate([
            'display_name'     => 'required|string|max:100',
            'religion'         => 'required|string|in:islam,kristen,hindu,buddha,lainnya,none',
            'sports'           => 'nullable|array',
            'custom_sport_name'=> 'nullable|string|max:50',
        ]);

        $storage  = UserStorage::fromSession();
        $religion = $request->religion;
        $sports   = $request->sports ?? [];

        // Save profile
        $storage->updateProfile([
            'setup_done'        => true,
            'display_name'      => trim($request->display_name),
            'religion'          => $religion,
            'sports'            => $sports,
            'custom_sport_name' => trim($request->custom_sport_name ?? ''),
        ]);

        // Sync feature flags from religion choice
        if ($religion === 'islam') {
            $storage->setFeature('sholat',    true);
            $storage->setFeature('spiritual', false);
        } elseif ($religion === 'none') {
            $storage->setFeature('sholat',    false);
            $storage->setFeature('spiritual', false);
        } else {
            $storage->setFeature('sholat',    false);
            $storage->setFeature('spiritual', true);
        }

        // Sync feature flags from sports choices
        $allSports = ['gym', 'run', 'cycling', 'swimming', 'racket', 'custom_sport'];
        foreach ($allSports as $sport) {
            $storage->setFeature($sport, in_array($sport, $sports));
        }

        $storage->save();

        $name = $request->display_name;
        return redirect()->route('dashboard')
            ->with('toast', __('Selamat datang, :name! Profile kamu sudah siap.', ['name' => $name]));
    }
}
