<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mojob — {{ __('Daftar') }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}?v=2">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#F8F9FA] py-8">

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
            <div class="flex flex-col items-center mb-7">
                <img src="{{ asset('images/logo.png') }}" class="h-14 w-auto mb-3" alt="Mojob">
                <h1 class="text-lg font-bold text-gray-900">{{ __('Buat Akun Baru') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('Mulai track kehidupan & karirmu') }}</p>
            </div>

            {{-- General error --}}
            @if($errors->any() && !$errors->hasAny(['username','password','terms']))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm font-medium">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                {{-- Username --}}
                <div>
                    <label class="text-xs font-bold text-gray-400 block mb-2 uppercase tracking-wide">Username</label>
                    <input type="text" name="username"
                        value="{{ old('username') }}"
                        placeholder="{{ __('cth: budi_setiawan') }}"
                        class="w-full p-3 bg-gray-50 border {{ $errors->has('username') ? 'border-red-300' : 'border-gray-200' }} rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autocomplete="username"
                        autofocus required>
                    @error('username')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @else
                    <p class="text-[10px] text-gray-400 mt-1.5">{{ __('Min 3 karakter. Huruf, angka, dash, underscore.') }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="text-xs font-bold text-gray-400 block mb-2 uppercase tracking-wide">Password</label>
                    <input type="password" name="password"
                        placeholder="{{ __('Minimal 6 karakter') }}"
                        class="w-full p-3 bg-gray-50 border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-200' }} rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autocomplete="new-password"
                        required>
                    @error('password')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label class="text-xs font-bold text-gray-400 block mb-2 uppercase tracking-wide">{{ __('Konfirmasi Password') }}</label>
                    <input type="password" name="password_confirmation"
                        placeholder="{{ __('Ulangi password') }}"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autocomplete="new-password"
                        required>
                </div>

                {{-- Terms --}}
                <div class="flex items-start gap-2 pt-1">
                    <input type="checkbox" name="terms" id="terms" value="1"
                        {{ old('terms') ? 'checked' : '' }}
                        class="w-4 h-4 mt-0.5 rounded border-gray-300 accent-black cursor-pointer flex-shrink-0">
                    <label for="terms" class="text-xs text-gray-500 cursor-pointer leading-relaxed">
                        {{ __('Saya setuju dengan') }}
                        <a href="#" class="font-bold text-gray-700 hover:underline">{{ __('Syarat & Ketentuan') }}</a>
                        {{ __('dan') }}
                        <a href="#" class="font-bold text-gray-700 hover:underline">{{ __('Kebijakan Privasi') }}</a>
                    </label>
                </div>
                @error('terms')
                <p class="text-xs text-red-500 -mt-2">{{ $message }}</p>
                @enderror

                {{-- Submit --}}
                <button type="submit"
                    class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition-all mt-2">
                    {{ __('Daftar Sekarang') }}
                </button>
            </form>

            {{-- Sign in link --}}
            <p class="text-sm text-center text-gray-500 mt-6">
                {{ __('Sudah punya akun?') }}
                <a href="{{ route('login') }}" class="font-bold text-black hover:underline">{{ __('Masuk di sini') }}</a>
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
