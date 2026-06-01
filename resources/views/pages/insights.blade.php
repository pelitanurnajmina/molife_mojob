@extends('layouts.app')
@section('title', __('Insights'))
@section('page-title', __('Insights'))
@section('breadcrumb', __('Insights'))

@section('content')
@php
    $monthLabel = (new DateTime())->format('F Y');
    $sholatPct  = count($stats30) > 0 ? round(($sholatDaysMonth / count($stats30)) * 100) : 0;

    // Mood label helper
    $moodLabel = ['', __('Buruk'), __('Kurang'), __('Biasa'), __('Baik'), __('Luar Biasa')];
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Life Score Today ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-2 mb-6">
            <h3 class="text-base font-bold">{{ __('Life Score Hari Ini') }}</h3>
            <span class="text-xs text-gray-400">{{ date('j F Y') }}</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $dims = [
                    ['label' => __('Spiritual'),     'val' => $lifeScore['spiritual'],    'color' => 'text-green-600',  'bg' => 'bg-green-500',  'light' => 'bg-green-50',  'svgPath' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['label' => __('Kesehatan'),     'val' => $lifeScore['health'],       'color' => 'text-blue-600',   'bg' => 'bg-blue-500',   'light' => 'bg-blue-50',   'svgPath' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['label' => __('Mental'),        'val' => $lifeScore['mental'],       'color' => 'text-violet-600', 'bg' => 'bg-violet-500', 'light' => 'bg-violet-50', 'svgPath' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'empty' => !$lifeScore['hasMood']],
                    ['label' => __('Produktivitas'), 'val' => $lifeScore['productivity'], 'color' => 'text-orange-600', 'bg' => 'bg-orange-400', 'light' => 'bg-orange-50', 'svgPath' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'empty' => !$lifeScore['hasTasks']],
                ];
            @endphp
            @foreach($dims as $dim)
            <div class="text-center p-4 {{ $dim['light'] }} rounded-2xl">
                <div class="relative w-16 h-16 mx-auto mb-2">
                    <svg class="w-16 h-16 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9155" fill="none" stroke="white" stroke-width="3.5"/>
                        <circle cx="18" cy="18" r="15.9155" fill="none"
                            stroke="{{ ['bg-green-500'=>'#10b981','bg-blue-500'=>'#3b82f6','bg-violet-500'=>'#8b5cf6','bg-orange-400'=>'#fb923c'][$dim['bg']] }}"
                            stroke-width="3.5"
                            stroke-dasharray="{{ ($dim['empty'] ?? false) ? 0 : $dim['val'] }} 100"
                            stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $dim['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $dim['svgPath'] }}"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl font-bold {{ $dim['color'] }}">{{ ($dim['empty'] ?? false) ? '—' : $dim['val'] }}</p>
                <p class="text-[10px] font-bold text-gray-500">{{ $dim['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Overall --}}
        <div class="mt-6 p-4 bg-gray-900 text-white rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ __('Overall Life Score') }}</p>
                <p class="text-3xl font-bold mt-1">{{ $lifeScore['overall'] }}</p>
            </div>
            <div class="relative">
                <svg class="w-20 h-20 -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9155" fill="none"
                        stroke="{{ $lifeScore['overall'] >= 80 ? '#10b981' : ($lifeScore['overall'] >= 50 ? '#f59e0b' : '#ef4444') }}"
                        stroke-width="3"
                        stroke-dasharray="{{ $lifeScore['overall'] }} 100"
                        stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center text-white font-bold text-lg">
                    {{ $lifeScore['overall'] }}%
                </div>
            </div>
        </div>
    </div>

    {{-- ── 7-Day Trend ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base font-bold mb-6">{{ __('Trend 7 Hari Terakhir') }}</h3>
        <div class="h-64">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- ── Insights Cards ── --}}
    @php
        $insightIconMap = [
            'streak'     => ['path' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                                                                                                                     'cls' => 'text-orange-500 bg-orange-100'],
            'prayer'     => ['path' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',                             'cls' => 'text-green-600 bg-green-100'],
            'warning'    => ['path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',                                                                                                         'cls' => 'text-yellow-600 bg-yellow-100'],
            'gym'        => ['path' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                                                                                                                     'cls' => 'text-blue-600 bg-blue-100'],
            'run'        => ['path' => 'M22 12h-4l-3 9L9 3l-3 9H2',                                                                                                                                                                                                                      'cls' => 'text-emerald-600 bg-emerald-100'],
            'mood-good'  => ['path' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                          'cls' => 'text-violet-600 bg-violet-100'],
            'mood-bad'   => ['path' => 'M9.172 14.828a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                            'cls' => 'text-red-500 bg-red-100'],
            'mood-ok'    => ['path' => 'M8 12h8M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                  'cls' => 'text-yellow-600 bg-yellow-100'],
            'tasks-done' => ['path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                                 'cls' => 'text-green-600 bg-green-100'],
            'tasks'      => ['path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',                                                                                               'cls' => 'text-orange-600 bg-orange-100'],
            'career'     => ['path' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',                                                               'cls' => 'text-indigo-600 bg-indigo-100'],
            'interview'  => ['path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'cls' => 'text-violet-600 bg-violet-100'],
            'intro'      => ['path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                     'cls' => 'text-gray-500 bg-gray-100'],
        ];
    @endphp
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base font-bold mb-4">{{ __('Insights & Pola') }}</h3>
        <div class="space-y-2">
            @foreach($insights as $ins)
            @php
                $insightStyle = match($ins['type']) {
                    'success' => 'bg-green-50 border border-green-100',
                    'warning' => 'bg-yellow-50 border border-yellow-100',
                    default   => 'bg-gray-50 border border-gray-100',
                };
                $textStyle = match($ins['type']) {
                    'success' => 'text-green-800',
                    'warning' => 'text-yellow-800',
                    default   => 'text-gray-700',
                };
                $ic = $insightIconMap[$ins['icon']] ?? $insightIconMap['intro'];
            @endphp
            <div class="flex items-center gap-3 p-3 rounded-xl {{ $insightStyle }}">
                <div class="w-8 h-8 flex-shrink-0 rounded-lg flex items-center justify-center {{ $ic['cls'] }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ic['path'] }}"/>
                    </svg>
                </div>
                <p class="text-sm {{ $textStyle }} leading-relaxed">{{ $ins['text'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Monthly Summary ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base font-bold mb-2">{{ __('Ringkasan Bulan Ini') }}</h3>
        <p class="text-xs text-gray-400 mb-6">{{ $monthLabel }}</p>

        <div class="space-y-4">
            {{-- Spiritual --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="flex items-center gap-1.5 text-sm font-bold">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        {{ __('Spiritual') }}
                    </span>
                    <span class="text-xs text-gray-500 font-bold">{{ $sholatDaysMonth }} {{ __('hari') }} <span class="text-gray-300">/ {{ count($stats30) }}</span></span>
                </div>
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-green-500 h-full rounded-full" style="width:{{ $sholatPct }}%"></div>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <span class="flex items-center gap-1 text-[10px] text-gray-400">
                        <svg class="w-3 h-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Streak: {{ $streak }} {{ __('hari') }}
                    </span>
                </div>
            </div>

            {{-- Health --}}
            @php
                $gymPct = count($stats30) > 0 ? round(($gymMonthly / count($stats30)) * 100) : 0;
                $runPct = count($stats30) > 0 ? round(($runMonthly / count($stats30)) * 100) : 0;
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="flex items-center gap-1.5 text-sm font-bold">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ __('Gym') }}
                    </span>
                    <span class="text-xs text-gray-500 font-bold">{{ $gymMonthly }} {{ __('sesi') }}</span>
                </div>
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full" style="width:{{ $gymPct }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="flex items-center gap-1.5 text-sm font-bold">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        {{ __('Run') }}
                    </span>
                    <span class="text-xs text-gray-500 font-bold">{{ $runMonthly }} {{ __('sesi') }} · {{ number_format($runDist, 1) }} km</span>
                </div>
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" style="width:{{ $runPct }}%"></div>
                </div>
            </div>

            {{-- Mental --}}
            @if($moodAvg30 > 0)
            @php $mentalPct = round(($moodAvg30 / 5) * 100); @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="flex items-center gap-1.5 text-sm font-bold">
                        <svg class="w-4 h-4 text-violet-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Mental') }}
                    </span>
                    <span class="text-xs text-gray-500 font-bold">{{ __('Avg') }} {{ $moodAvg30 }}/5</span>
                </div>
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-violet-500 h-full rounded-full" style="width:{{ $mentalPct }}%"></div>
                </div>
                @if($moodAvg7 > 0)
                <p class="text-[10px] text-gray-400 mt-1">7 hari terakhir: {{ $moodAvg7 }}/5 · Energi: {{ $energyAvg7 > 0 ? $energyAvg7.'/5' : '—' }}</p>
                @endif
            </div>
            @endif

            {{-- Intimacy --}}
            @if($intimacyMonthly > 0)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="flex items-center gap-1.5 text-sm font-bold">
                        <svg class="w-4 h-4 text-pink-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        {{ __('Intimasi') }}
                    </span>
                    <span class="text-xs text-gray-500 font-bold">{{ $intimacyMonthly }}×</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Mood Heatmap (30 days) ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold">{{ __('Kalender Mood 30 Hari') }}</h3>
            <div class="flex items-center gap-2 text-[10px]">
                <div class="flex items-center gap-1"><div class="w-3 h-3 bg-gray-100 rounded"></div><span class="text-gray-500">{{ __('Belum') }}</span></div>
                <div class="flex items-center gap-1"><div class="w-3 h-3 bg-emerald-500 rounded"></div><span class="text-gray-500">{{ __('Baik') }}</span></div>
            </div>
        </div>
        <div class="grid grid-cols-10 sm:grid-cols-[repeat(15,1fr)] md:grid-cols-[repeat(30,1fr)] gap-0.5 md:gap-1 h-6 md:h-8">
            @foreach($moodHistory as $day)
            @php
                $cls = match(true) {
                    $day['score'] >= 4 => 'bg-emerald-500',
                    $day['score'] === 3 => 'bg-yellow-400',
                    $day['score'] >= 1 => 'bg-red-400',
                    default            => 'bg-gray-100',
                };
            @endphp
            <div class="rounded {{ $cls }}" title="{{ $day['date'] }}: {{ $day['score'] > 0 ? ($moodLabel[$day['score']] ?? '—') : '—' }}"></div>
            @endforeach
        </div>
    </div>

    {{-- ── Weekly Activity Chart (moved from Dashboard) ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold">{{ __('Aktivitas Minggu Ini') }}</h3>
            <span class="text-xs bg-gray-50 rounded-xl px-3 py-1 font-bold text-gray-500">{{ __('7 hari terakhir') }}</span>
        </div>
        <div style="height:200px">
            <canvas id="weekActivityChart"></canvas>
        </div>
    </div>

    {{-- ── Monthly Summary ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4">{{ __('Ringkasan Bulan Ini') }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="p-4 bg-green-50 rounded-2xl">
                <p class="text-2xl font-bold text-green-700">{{ $sholatDaysMonth }}</p>
                <p class="text-xs text-green-600 font-bold mt-1">{{ __('hari sholat penuh') }}</p>
                <p class="text-[10px] text-green-500 mt-0.5">{{ $todayStats['takbir'] }}/5 takbir {{ __('hari ini') }}</p>
            </div>
            <div class="p-4 bg-blue-50 rounded-2xl">
                <p class="text-2xl font-bold text-blue-700">{{ $gymMonthly }}</p>
                <p class="text-xs text-blue-600 font-bold mt-1">{{ __('sesi gym') }}</p>
                <p class="text-[10px] text-blue-500 mt-0.5">{{ $caloriesWeek }} cal {{ __('minggu ini') }}</p>
            </div>
            <div class="p-4 bg-emerald-50 rounded-2xl">
                <p class="text-2xl font-bold text-emerald-700">{{ number_format($runMonthlyDist, 1) }}</p>
                <p class="text-xs text-emerald-600 font-bold mt-1">km {{ __('lari') }}</p>
                <p class="text-[10px] text-emerald-500 mt-0.5">{{ $runWeeklyCount }}× {{ __('minggu ini') }}</p>
            </div>
            <div class="p-4 bg-violet-50 rounded-2xl">
                <p class="text-2xl font-bold text-violet-700">{{ $moodAvg30 > 0 ? $moodAvg30 : '—' }}</p>
                <p class="text-xs text-violet-600 font-bold mt-1">{{ __('avg mood') }}</p>
                <p class="text-[10px] text-violet-500 mt-0.5">{{ __('30 hari terakhir') }}</p>
            </div>
            <div class="p-4 bg-orange-50 rounded-2xl">
                <p class="text-2xl font-bold text-orange-700">{{ $streak }}</p>
                <p class="text-xs text-orange-600 font-bold mt-1">{{ __('hari streak') }}</p>
                <p class="text-[10px] text-orange-500 mt-0.5">{{ __('berturut-turut') }}</p>
            </div>
            <div class="p-4 bg-pink-50 rounded-2xl">
                <p class="text-2xl font-bold text-pink-700">{{ $intimacyMonthly }}</p>
                <p class="text-xs text-pink-600 font-bold mt-1">{{ __('intimasi') }}</p>
                <p class="text-[10px] text-pink-500 mt-0.5">{{ __('bulan ini') }}</p>
            </div>
        </div>
    </div>

    {{-- ── Smart Insights ── --}}
    @if(count($insights) > 0)
    @php
        $insightIconMap = [
            'streak'     => ['path' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                                                                                   'cls' => 'text-orange-500 bg-orange-100'],
            'prayer'     => ['path' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'cls' => 'text-green-600 bg-green-100'],
            'warning'    => ['path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',                                                                         'cls' => 'text-yellow-600 bg-yellow-100'],
            'gym'        => ['path' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                                                                                                   'cls' => 'text-blue-600 bg-blue-100'],
            'run'        => ['path' => 'M22 12h-4l-3 9L9 3l-3 9H2',                                                                                                                                                                                    'cls' => 'text-emerald-600 bg-emerald-100'],
            'mood-good'  => ['path' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                        'cls' => 'text-violet-600 bg-violet-100'],
            'mood-bad'   => ['path' => 'M9.172 14.828a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                          'cls' => 'text-red-500 bg-red-100'],
            'mood-ok'    => ['path' => 'M8 12h8M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                'cls' => 'text-yellow-600 bg-yellow-100'],
            'tasks-done' => ['path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                               'cls' => 'text-green-600 bg-green-100'],
            'tasks'      => ['path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',                                                             'cls' => 'text-orange-600 bg-orange-100'],
            'career'     => ['path' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',                             'cls' => 'text-indigo-600 bg-indigo-100'],
            'interview'  => ['path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'cls' => 'text-violet-600 bg-violet-100'],
            'intro'      => ['path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                   'cls' => 'text-gray-500 bg-gray-100'],
        ];
    @endphp
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4">{{ __('Smart Insights') }}</h3>
        <div class="space-y-2">
            @foreach($insights as $ins)
            @php
                $insightBg = match($ins['type']) {
                    'success' => 'bg-green-50 border-green-100',
                    'warning' => 'bg-yellow-50 border-yellow-100',
                    default   => 'bg-gray-50 border-gray-100',
                };
                $ic = $insightIconMap[$ins['icon']] ?? $insightIconMap['intro'];
            @endphp
            <div class="flex items-center gap-3 p-3 rounded-xl border {{ $insightBg }}">
                <div class="w-8 h-8 flex-shrink-0 rounded-lg flex items-center justify-center {{ $ic['cls'] }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ic['path'] }}"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-700">{{ $ins['text'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
/* Weekly Activity Chart */
new Chart(document.getElementById('weekActivityChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['{{ __('Sen') }}','{{ __('Sel') }}','{{ __('Rab') }}','{{ __('Kam') }}','{{ __('Jum') }}','{{ __('Sab') }}','{{ __('Min') }}'],
        datasets: [
            { label: '{{ __('Sholat') }} (0–5)', data: @json($weekSpiritualData), borderColor: '#10B981', backgroundColor: 'rgba(16,185,129,0.06)', tension: 0.4, borderWidth: 2.5, pointRadius: 4, pointBackgroundColor: '#10B981', fill: true, spanGaps: false },
            { label: '{{ __('Mood') }} (0–5)',   data: @json($weekMoodData),      borderColor: '#8B5CF6', backgroundColor: 'rgba(139,92,246,0.06)',  tension: 0.4, borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#8B5CF6', fill: false, spanGaps: false },
            { label: 'Gym',                        data: @json($weekFitnessData),   borderColor: '#3B82F6', backgroundColor: 'transparent', tension: 0.3, borderWidth: 2, pointRadius: 5, pointBackgroundColor: '#3B82F6', pointStyle: 'rectRounded', fill: false },
            { label: '{{ __('Lari') }}',           data: @json($weekRunData),       borderColor: '#34D399', backgroundColor: 'transparent', tension: 0.3, borderWidth: 2, pointRadius: 5, pointBackgroundColor: '#34D399', pointStyle: 'triangle', fill: false, borderDash: [4,3] },
        ],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: true, position: 'bottom', labels: { usePointStyle: true, padding: 12, font: { size: 10, weight: '600' }, boxWidth: 8 } },
            tooltip: { callbacks: { label: ctx => { const v = ctx.raw; if (v === null || v === undefined) return ctx.dataset.label + ': —'; if (ctx.datasetIndex >= 2) return ctx.dataset.label + ': ' + (v ? '✓' : '✗'); return ctx.dataset.label + ': ' + v; } } }
        },
        scales: {
            y: { min: 0, max: 5, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f9fafb' } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        },
    }
});

/* Life Score Trend Chart */
const trendCtx = document.getElementById('trendChart').getContext('2d');
const weekScores = @json($weekScores);

new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: weekScores.map(d => d.date),
        datasets: [
            {
                label: '{{ __("Spiritual") }}',
                data: weekScores.map(d => d.spiritual),
                borderColor: '#10b981', tension: 0.4, borderWidth: 2.5, pointRadius: 4,
                pointBackgroundColor: '#10b981', fill: false,
            },
            {
                label: '{{ __("Kesehatan") }}',
                data: weekScores.map(d => d.health),
                borderColor: '#3b82f6', tension: 0.4, borderWidth: 2.5, pointRadius: 4,
                pointBackgroundColor: '#3b82f6', fill: false,
            },
            {
                label: '{{ __("Mental") }}',
                data: weekScores.map(d => d.mental),
                borderColor: '#8b5cf6', tension: 0.4, borderWidth: 2, pointRadius: 4,
                pointBackgroundColor: '#8b5cf6', fill: false,
            },
            {
                label: '{{ __("Produktivitas") }}',
                data: weekScores.map(d => d.productivity),
                borderColor: '#fb923c', tension: 0.4, borderWidth: 2, pointRadius: 4,
                pointBackgroundColor: '#fb923c', fill: false,
            },
        ],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true, padding: 15 } }
        },
        scales: {
            y: { min: 0, max: 100, ticks: { stepSize: 25, callback: v => v + '%' }, grid: { color: '#f3f4f6' } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        },
    },
});
</script>
@endpush
