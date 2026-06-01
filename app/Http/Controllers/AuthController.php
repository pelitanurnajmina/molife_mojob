<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /* ── Login ── */
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['username' => 'Username atau password salah.'])
            ->withInput($request->only('username'));
    }

    /* ── Register ── */
    public function showRegister()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username'              => 'required|string|min:3|max:30|alpha_dash|unique:users,username',
            'password'              => ['required', 'confirmed', Password::min(6)],
            'terms'                 => 'accepted',
        ], [
            'username.required'  => __('Username wajib diisi.'),
            'username.min'       => __('Username minimal 3 karakter.'),
            'username.alpha_dash'=> __('Username hanya boleh huruf, angka, dash, dan underscore.'),
            'username.unique'    => __('Username sudah digunakan.'),
            'password.required'  => __('Password wajib diisi.'),
            'password.confirmed' => __('Konfirmasi password tidak cocok.'),
            'password.min'       => __('Password minimal 6 karakter.'),
            'terms.accepted'     => __('Kamu harus menyetujui syarat & ketentuan.'),
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'password' => $validated['password'], // auto-hashed via $casts
        ]);

        auth()->login($user);
        $request->session()->regenerate();

        // New user → akan otomatis di-redirect ke /onboarding oleh middleware
        return redirect()->route('dashboard');
    }

    /* ── Logout ── */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
