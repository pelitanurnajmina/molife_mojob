<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" style="background-color:#ffffff">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
    <title>Molife — Login</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}?v=2">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=10">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; } @view-transition { navigation: auto; } ::view-transition-old(root),::view-transition-new(root){animation-duration:.18s}</style>
</head>
<body class="min-h-screen bg-white">
<div class="min-h-screen flex">

    {{-- ── Left: brand panel ── --}}
    @include('partials.auth-side')

    {{-- ── Right: form ── --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-10 relative">
        {{-- Language switcher --}}
        <div class="absolute top-5 right-5">
            <div class="flex items-center bg-white rounded-full border border-gray-200 p-0.5 shadow-sm">
                <a href="{{ route('lang.switch', 'id') }}" class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'id' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">ID</a>
                <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">EN</a>
            </div>
        </div>

        <div class="w-full max-w-sm">
            {{-- Mobile logo --}}
            <img src="{{ asset('images/logo.png') }}" class="h-9 w-auto mb-8 lg:hidden" alt="molife">

            <h2 class="text-3xl font-extrabold tracking-tight">{{ __('Selamat Datang Kembali') }}</h2>
            <p class="text-sm text-gray-500 mt-2">{{ __('Masuk untuk melanjutkan ke dashboard-mu.') }}</p>

            @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mt-6 text-sm font-medium">
                {{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mt-6 text-sm font-medium">
                {{ $errors->first('login') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="mt-7 space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-bold text-gray-700 block mb-1.5">Email</label>
                    <input type="text" name="login" value="{{ old('login') }}"
                        placeholder="{{ __('you@example.com') }}"
                        class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('login') ? 'border-red-300' : 'border-gray-200' }} rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autocomplete="username" autofocus required>
                </div>

                <div>
                    <label class="text-sm font-bold text-gray-700 block mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput"
                            placeholder="{{ __('Masukkan password') }}"
                            class="w-full px-4 py-3 pr-11 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                            required>
                        <button type="button" onclick="togglePw()" tabindex="-1"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition-all">
                            <svg id="pwEye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" checked class="w-4 h-4 rounded border-gray-300 accent-black cursor-pointer">
                        <span class="text-sm text-gray-500">{{ __('Tetap masuk') }}</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-bold text-gray-500 hover:text-black transition-all">{{ __('Lupa password?') }}</a>
                </div>

                <button type="submit" class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition-all">
                    {{ __('Masuk') }}
                </button>
            </form>

            <a href="{{ route('auth.google') }}"
                class="mt-3 w-full flex items-center justify-center gap-2.5 border border-gray-200 py-3 rounded-xl font-bold text-sm text-gray-700 hover:bg-gray-50 transition-all">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0012 23z"/><path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 010-4.2V7.06H2.18a11 11 0 000 9.88l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 002.18 7.06l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38z"/></svg>
                {{ __('Masuk dengan Google') }}
            </a>

            <p class="text-sm text-center text-gray-500 mt-7">
                {{ __('Belum punya akun?') }}
                <a href="{{ route('register') }}" class="font-bold text-black hover:underline">{{ __('Daftar sekarang') }}</a>
            </p>
        </div>
    </div>

</div>

<script>
function togglePw() {
    const i = document.getElementById('passwordInput');
    const eye = document.getElementById('pwEye');
    if (i.type === 'password') {
        i.type = 'text';
        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        i.type = 'password';
        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
</body>
</html>
