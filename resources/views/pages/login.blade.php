<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Molife — Login</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}?v=2">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#F8F9FA]">
    {{-- Language switcher --}}
    <div class="fixed top-4 right-4">
        <div class="flex items-center bg-white rounded-full border border-gray-200 p-0.5 shadow-sm">
            <a href="{{ route('lang.switch', 'id') }}"
               class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'id' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">ID</a>
            <a href="{{ route('lang.switch', 'en') }}"
               class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">EN</a>
        </div>
    </div>

    <div class="w-full max-w-md p-6">
        <div class="bg-white rounded-3xl p-8 md:p-10 border border-gray-100">
            <div class="flex flex-col items-center mb-8">
                {{-- <svg class="h-14 w-auto mb-3" viewBox="0 0 260 85" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="mjGradLogin" x1="0" y1="1" x2="1" y2="0" gradientUnits="objectBoundingBox">
                            <stop offset="0%" stop-color="#c94b00"/>
                            <stop offset="50%" stop-color="#ef7015"/>
                            <stop offset="100%" stop-color="#f9b418"/>
                        </linearGradient>
                    </defs>
                    <!-- Swoosh -->
                    <path d="M 8 58 C 3 74 22 83 48 74 C 64 68 75 55 70 46 C 65 37 50 44 36 54 C 22 64 10 62 8 58 Z" fill="url(#mjGradLogin)"/>
                    <!-- Upper bean -->
                    <ellipse cx="37" cy="25" rx="15" ry="23" transform="rotate(-10 37 25)" fill="#111111"/>
                    <!-- Lower bean -->
                    <ellipse cx="55" cy="56" rx="11" ry="16" transform="rotate(8 55 56)" fill="#111111"/>
                    <!-- Mojob text -->
                    <text x="90" y="60" font-family="'Plus Jakarta Sans', 'Arial Black', sans-serif" font-weight="800" font-size="50" fill="#111111">Mojob</text>
                </svg> --}}
                <img src="{{ asset('images/logo.png') }}" class="h-14 w-auto mb-3">
                <p class="text-sm text-gray-500">Track your spiritual, physical &amp; personal life</p>
            </div>

            {{-- Error Message --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm font-medium">
                {{ $errors->first('username') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-bold text-gray-400 block mb-2 uppercase tracking-wide">Username</label>
                    <input type="text" name="username"
                        value="{{ old('username') }}"
                        placeholder="{{ __('Masukkan username') }}"
                        class="w-full p-3 bg-gray-50 border {{ $errors->has('username') ? 'border-red-300' : 'border-gray-200' }} rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autofocus required>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 block mb-2 uppercase tracking-wide">Password</label>
                    <input type="password" name="password"
                        placeholder="{{ __('Masukkan password') }}"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        required>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" checked
                        class="w-4 h-4 rounded border-gray-300 accent-black cursor-pointer">
                    <label for="remember" class="text-sm text-gray-500 cursor-pointer">{{ __('Tetap masuk di perangkat ini') }}</label>
                </div>

                <button type="submit"
                    class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition-all mt-2">
                    {{ __('Masuk') }}
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-5">
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400">{{ __('atau') }}</span>
                <div class="flex-1 h-px bg-gray-100"></div>
            </div>

            {{-- Login with Google --}}
            <a href="{{ route('auth.google') }}"
                class="w-full flex items-center justify-center gap-2.5 border border-gray-200 py-3 rounded-xl font-bold text-sm text-gray-700 hover:bg-gray-50 transition-all">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0012 23z"/><path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 010-4.2V7.06H2.18a11 11 0 000 9.88l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 002.18 7.06l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38z"/></svg>
                {{ __('Masuk dengan Google') }}
            </a>

            {{-- Sign up link --}}
            <p class="text-sm text-center text-gray-500 mt-6">
                {{ __('Belum punya akun?') }}
                <a href="{{ route('register') }}" class="font-bold text-black hover:underline">{{ __('Daftar sekarang') }}</a>
            </p>
        </div>

        <p class="text-xs text-center text-gray-400 mt-5 flex items-center justify-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            {{ __('Data tersimpan di database lokal') }}
        </p>
    </div>
</body>
</html>
