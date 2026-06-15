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

        // Always "remember" so users stay logged in across sessions (long-lived cookie),
        // even if they leave the checkbox unticked.
        if (auth()->attempt($credentials, true)) {
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

    /* ── Login with Google (Socialite) ── */
    public function redirectToGoogle()
    {
        if (!config('services.google.client_id')) {
            return redirect()->route('login')
                ->withErrors(['username' => __('Login Google belum dikonfigurasi.')]);
        }
        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $gUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['username' => __('Gagal login dengan Google. Coba lagi.')]);
        }

        // Match by google_id first, then by email (link existing account).
        $user = User::where('google_id', $gUser->getId())->first();
        if (!$user && $gUser->getEmail()) {
            $user = User::where('email', $gUser->getEmail())->first();
        }

        if ($user) {
            $user->fill([
                'google_id' => $gUser->getId(),
                'email'     => $user->email ?: $gUser->getEmail(),
                'name'      => $user->name ?: $gUser->getName(),
                'avatar'    => $gUser->getAvatar() ?: $user->avatar,
            ])->save();
        } else {
            $user = User::create([
                'google_id' => $gUser->getId(),
                'email'     => $gUser->getEmail(),
                'name'      => $gUser->getName(),
                'avatar'    => $gUser->getAvatar(),
                'username'  => $this->uniqueUsername($gUser),
                'password'  => null,
            ]);
        }

        auth()->login($user, true);
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    /** Build a unique username from the Google profile (email local-part or name). */
    private function uniqueUsername($gUser): string
    {
        $base = $gUser->getEmail() ? explode('@', $gUser->getEmail())[0] : ($gUser->getName() ?? 'user');
        $base = strtolower(preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $base))) ?: 'user';
        $base = substr($base, 0, 24);
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }
        return $username;
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
