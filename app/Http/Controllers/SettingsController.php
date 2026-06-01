<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        return redirect()->route('settings.profil');
    }

    public function profil()
    {
        $storage = UserStorage::fromSession();
        $profile = $storage->getProfile();
        return view('pages.settings.profil', compact('profile'));
    }

    public function tampilan()
    {
        $storage  = UserStorage::fromSession();
        $features = $storage->getFeatures();
        $profile  = $storage->getProfile();
        return view('pages.settings.tampilan', compact('features', 'profile'));
    }

    public function langganan()
    {
        return view('pages.settings.langganan');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'username'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'display_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user->update(['username' => $validated['username']]);

        // Update display_name in profile storage
        if (isset($validated['display_name'])) {
            $storage = UserStorage::fromSession();
            $storage->updateProfile(['display_name' => trim($validated['display_name'])]);
            $storage->save();
        }

        return back()->with('toast', __('Profil berhasil diperbarui.'));
    }

    public function updateOnboarding(Request $request)
    {
        $request->validate([
            'religion'          => 'required|string|in:islam,kristen,hindu,buddha,lainnya,none',
            'sports'            => 'nullable|array',
            'custom_sport_name' => 'nullable|string|max:50',
        ]);

        $storage  = UserStorage::fromSession();
        $religion = $request->religion;
        $sports   = $request->sports ?? [];

        $storage->updateProfile([
            'religion'          => $religion,
            'sports'            => $sports,
            'custom_sport_name' => trim($request->custom_sport_name ?? ''),
        ]);

        // Sync features
        if ($religion === 'islam') {
            $storage->setFeature('sholat', true);
            $storage->setFeature('spiritual', false);
        } elseif ($religion === 'none') {
            $storage->setFeature('sholat', false);
            $storage->setFeature('spiritual', false);
        } else {
            $storage->setFeature('sholat', false);
            $storage->setFeature('spiritual', true);
        }

        $allSports = ['gym', 'run', 'cycling', 'swimming', 'racket', 'custom_sport'];
        foreach ($allSports as $sport) {
            $storage->setFeature($sport, in_array($sport, $sports));
        }

        $storage->save();

        return back()->with('toast', __('Preferensi berhasil disimpan.'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attr, $val, $fail) {
                if (!Hash::check($val, auth()->user()->password)) {
                    $fail('Password saat ini salah.');
                }
            }],
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('pass_toast', 'Password berhasil diubah.');
    }

    public function toggleFeature(Request $request)
    {
        $key     = $request->validate(['feature' => 'required|string'])['feature'];
        $storage = UserStorage::fromSession();
        $enabled = $storage->toggleFeature($key);
        $storage->save();

        return response()->json(['enabled' => $enabled]);
    }
}
