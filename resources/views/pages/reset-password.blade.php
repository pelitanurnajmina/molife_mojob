<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" style="background-color:#ffffff">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
    <title>Molife — {{ __('Password Baru') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=8">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; } @view-transition { navigation: auto; } ::view-transition-old(root),::view-transition-new(root){animation-duration:.18s}</style>
</head>
<body class="min-h-screen bg-white">
<div class="min-h-screen flex">

    {{-- ── Left: brand panel ── --}}
    @include('partials.auth-side')

    {{-- ── Right: form ── --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-10 relative">
        <div class="w-full max-w-sm">
            <img src="{{ asset('images/logo.png') }}" class="h-9 w-auto mb-8 lg:hidden" alt="molife">

            <h2 class="text-3xl font-extrabold tracking-tight">{{ __('Buat Password Baru') }}</h2>
            <p class="text-sm text-gray-500 mt-2">{{ __('Masukkan password baru untuk akunmu.') }}</p>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mt-6 text-sm font-medium">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="mt-7 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="text-sm font-bold text-gray-700 block mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}"
                        placeholder="you@example.com"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autocomplete="email" required {{ $email ? 'readonly' : '' }}>
                </div>

                <div>
                    <label class="text-sm font-bold text-gray-700 block mb-1.5">{{ __('Password Baru') }}</label>
                    <div class="relative">
                        <input type="password" name="password" id="pw1"
                            placeholder="{{ __('Minimal 6 karakter') }}"
                            class="w-full px-4 py-3 pr-11 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                            autocomplete="new-password" required>
                        <button type="button" onclick="togglePw()" tabindex="-1"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition-all">
                            <svg id="pwEye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-bold text-gray-700 block mb-1.5">{{ __('Ulangi Password Baru') }}</label>
                    <input type="password" name="password_confirmation" id="pw2"
                        placeholder="{{ __('Ketik ulang password') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autocomplete="new-password" required>
                </div>

                <button type="submit" class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition-all">
                    {{ __('Simpan Password Baru') }}
                </button>
            </form>

            <p class="text-sm text-center text-gray-500 mt-7">
                <a href="{{ route('login') }}" class="font-bold text-black hover:underline">{{ __('Kembali ke login') }}</a>
            </p>
        </div>
    </div>

</div>

<script>
function togglePw() {
    const i = document.getElementById('pw1');
    i.type = i.type === 'password' ? 'text' : 'password';
    document.getElementById('pw2').type = i.type;
}
</script>
</body>
</html>
