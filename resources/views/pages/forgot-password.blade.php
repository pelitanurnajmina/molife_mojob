<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" style="background-color:#ffffff">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Molife — {{ __('Lupa Password') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=6">
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

            <h2 class="text-3xl font-extrabold tracking-tight">{{ __('Lupa Password?') }}</h2>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ __('Masukkan email akunmu. Kami akan mengirim link untuk membuat password baru.') }}</p>

            @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mt-6 text-sm font-medium">
                {{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mt-6 text-sm font-medium">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-7 space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-bold text-gray-700 block mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="you@example.com"
                        class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-200' }} rounded-xl outline-none focus:border-black focus:bg-white transition-all"
                        autocomplete="email" autofocus required>
                </div>

                <button type="submit" class="w-full bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition-all">
                    {{ __('Kirim Link Reset') }}
                </button>
            </form>

            <p class="text-sm text-center text-gray-500 mt-7">
                {{ __('Ingat password-mu?') }}
                <a href="{{ route('login') }}" class="font-bold text-black hover:underline">{{ __('Kembali ke login') }}</a>
            </p>
        </div>
    </div>

</div>
</body>
</html>
