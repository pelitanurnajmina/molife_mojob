@extends('layouts.app')
@section('title', __('Profil & Akun'))
@section('page-title', __('Settings'))
@section('breadcrumb', 'Settings › Profil & Akun')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- ── Profile ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Profil') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Nama tampilan dan username akun') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.profile') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Nama Tampilan') }}</label>
                    <input type="text" name="display_name"
                        value="{{ old('display_name', $profile['display_name'] ?? '') }}"
                        placeholder="{{ __('Nama atau panggilan kamu') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-gray-400 focus:bg-white transition-all">
                    <p class="text-[10px] text-gray-400 mt-1">{{ __('Ditampilkan di sidebar dan greeting') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Username') }}</label>
                    <input type="text" name="username"
                        value="{{ old('username', auth()->user()->username) }}" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-gray-400 focus:bg-white transition-all">
                    <p class="text-[10px] text-gray-400 mt-1">{{ __('Digunakan untuk login') }}</p>
                </div>
            </div>
            @error('username')<p class="text-xs text-red-500 mb-3">{{ $message }}</p>@enderror
            <button type="submit" class="px-6 py-2.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Simpan Profil') }}
            </button>
        </form>
    </div>

    {{-- ── Security ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Keamanan') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Ganti password akun kamu') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.password') }}" class="max-w-md">
            @csrf
            @if($errors->has('current_password') || $errors->has('new_password'))
            <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-xl text-sm space-y-1">
                @foreach($errors->only(['current_password','new_password']) as $err)<p>{{ $err }}</p>@endforeach
            </div>
            @endif
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Password saat ini') }}</label>
                    <input type="password" name="current_password" required placeholder="••••••••"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Password baru') }}</label>
                    <input type="password" name="new_password" required placeholder="{{ __('Min. 6 karakter') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Konfirmasi password baru') }}</label>
                    <input type="password" name="new_password_confirmation" required placeholder="{{ __('Ulangi password baru') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Ganti Password') }}
            </button>
        </form>
    </div>

    {{-- ── Account / Logout ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 border border-red-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gray-900 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(mb_substr($profile['display_name'] ?? auth()->user()->username ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-bold">{{ $profile['display_name'] ?: auth()->user()->username }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()->username }} · {{ __('Akun aktif') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
