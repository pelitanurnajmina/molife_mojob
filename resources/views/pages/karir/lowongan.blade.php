@extends('layouts.app')
@section('title', __('Lowongan Kerja'))
@section('page-title', __('Lowongan Kerja'))
@section('breadcrumb', 'Karir › Lowongan')

@section('content')
<div class="space-y-4 md:space-y-6">

    @if(!$hasPremium)
    {{-- ── Locked: premium plans only ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-8 md:p-12 text-center">
        <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h3 class="text-lg font-bold">{{ __('Fitur Premium') }}</h3>
        <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto leading-relaxed">
            {{ __('Rekomendasi lowongan kerja dari berbagai perusahaan di seluruh dunia, disesuaikan dengan target kariermu. Tersedia untuk paket 6 Bulan dan 1 Tahun.') }}
        </p>
        <a href="{{ route('settings.langganan') }}" class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-gray-900 text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
            {{ __('Upgrade Paket') }}
        </a>
    </div>
    @else

    {{-- ── Search ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6">
        <form method="GET" action="{{ route('karir.lowongan') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $keyword }}" maxlength="80"
                    placeholder="{{ __('Cari posisi, misal: product designer, laravel, data analyst...') }}"
                    class="w-full pl-10 pr-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-black focus:bg-white transition-all">
            </div>
            <button type="submit" class="px-6 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Cari Lowongan') }}
            </button>
        </form>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3">
            @if($targetRole)
            <p class="text-[11px] text-gray-400">{{ __('Target kariermu:') }} <button type="button" onclick="location.href='{{ route('karir.lowongan') }}'" class="font-bold text-violet-600 hover:underline">{{ $targetRole }}</button></p>
            @else
            <p class="text-[11px] text-gray-400">{{ __('Tips: isi Target Karir di halaman Career Hub agar pencarian otomatis mengikuti target role-mu.') }}</p>
            @endif
            <p class="text-[11px] text-gray-400">{{ __('Sumber: Remotive & Arbeitnow · diperbarui berkala') }}</p>
        </div>
    </div>

    {{-- ── Results ── --}}
    @if(trim($keyword) === '')
    <div class="bg-white rounded-2xl md:rounded-3xl p-8 md:p-12 text-center">
        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-sm font-bold text-gray-600">{{ __('Mulai cari lowongan') }}</p>
        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">{{ __('Ketik posisi yang kamu incar di kolom pencarian, atau isi Target Karir di Career Hub agar rekomendasi muncul otomatis.') }}</p>
    </div>
    @elseif(empty($jobs))
    <div class="bg-white rounded-2xl md:rounded-3xl p-8 md:p-12 text-center">
        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-sm font-bold text-gray-600">{{ __('Tidak ada lowongan untuk ":q"', ['q' => $keyword]) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ __('Coba kata kunci lain yang lebih umum, misalnya dalam bahasa Inggris (developer, designer, marketing).') }}</p>
    </div>
    @else
    <div class="flex items-center justify-between px-1">
        <p class="text-xs font-bold text-gray-500">{{ count($jobs) }} {{ __('lowongan untuk') }} "{{ $keyword }}"</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-4">
        @foreach($jobs as $job)
        <div class="bg-white rounded-2xl p-5 flex flex-col border border-gray-50 hover:shadow-md transition-all">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="font-bold text-sm leading-snug">{{ $job['title'] }}</h3>
                    <p class="text-xs text-gray-500 mt-1 font-medium">{{ $job['company'] }}</p>
                </div>
                <span class="flex-shrink-0 text-[9px] font-bold px-2 py-1 rounded-full {{ $job['source'] === 'Remotive' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $job['source'] }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2.5 text-[11px] text-gray-400">
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $job['location'] }}
                </span>
                @if($job['salary'])
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 9v1"/></svg>
                    {{ $job['salary'] }}
                </span>
                @endif
                @if($job['posted_at'])
                <span>{{ \Carbon\Carbon::parse($job['posted_at'])->locale('id')->diffForHumans() }}</span>
                @endif
            </div>

            @if($job['excerpt'])
            <p class="text-xs text-gray-500 leading-relaxed mt-2.5 flex-1">{{ $job['excerpt'] }}</p>
            @endif

            @if(!empty($job['tags']))
            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach($job['tags'] as $tag)
                <span class="text-[10px] font-bold bg-gray-50 text-gray-500 px-2 py-0.5 rounded-full">{{ $tag }}</span>
                @endforeach
            </div>
            @endif

            <a href="{{ $job['url'] }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 mt-4 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition-all">
                {{ __('Lihat & Lamar di Sumber') }}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
        @endforeach
    </div>
    <p class="text-[11px] text-gray-400 text-center pb-2">{{ __('Kamu akan diarahkan ke situs sumber untuk melihat detail lengkap dan melamar.') }}</p>
    @endif

    @endif
</div>
@endsection
