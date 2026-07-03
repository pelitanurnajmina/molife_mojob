<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    /** Step 1: form "lupa password" (masukkan email). */
    public function request()
    {
        return view('pages.forgot-password');
    }

    /** Step 2: kirim link reset ke email. */
    public function email(Request $request)
    {
        $request->validate(
            ['email' => 'required|email'],
            ['email.required' => __('Email wajib diisi.'), 'email.email' => __('Format email tidak valid.')]
        );

        $status = Password::sendResetLink($request->only('email'));

        // Jangan bocorkan apakah email terdaftar — tampilkan pesan yang sama,
        // kecuali saat throttled (terlalu sering minta link).
        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => __('Terlalu sering meminta link. Tunggu sebentar lalu coba lagi.')])
                ->withInput($request->only('email'));
        }

        return back()->with('status', __('Jika email terdaftar, link reset password sudah kami kirim. Cek inbox (dan folder spam).'));
    }

    /** Step 3: form set password baru (dari link email). */
    public function reset(Request $request, string $token)
    {
        return view('pages.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /** Step 4: simpan password baru. */
    public function update(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ], [
            'email.required'     => __('Email wajib diisi.'),
            'email.email'        => __('Format email tidak valid.'),
            'password.required'  => __('Password wajib diisi.'),
            'password.confirmed' => __('Konfirmasi password tidak cocok.'),
            'password.min'       => __('Password minimal 6 karakter.'),
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => __('Link reset tidak valid atau sudah kedaluwarsa. Minta link baru.'),
            ])->withInput($request->only('email'));
        }

        return redirect()->route('login')->with('status', __('Password berhasil diubah! Silakan masuk dengan password barumu.'));
    }
}
