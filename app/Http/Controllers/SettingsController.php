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
        $features = UserStorage::fromSession()->getFeatures();
        return view('pages.settings.index', compact('features'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255',
                Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update(['username' => $validated['username']]);

        return back()->with('toast', 'Username berhasil diperbarui.');
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
