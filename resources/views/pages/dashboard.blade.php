@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', $greeting . ', ' . $displayName . '!')
@section('breadcrumb', 'Dashboard')

@section('content')
@php
    $monthLabel = (new DateTime())->format('F Y');
    $sholatPct  = count($stats30) > 0 ? round(($sholatDaysMonth / count($stats30)) * 100) : 0;
    $moodLabel  = ['', __('Buruk'), __('Kurang'), __('Biasa'), __('Baik'), __('Luar Biasa')];
    $rp = fn($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');

    /* Merge insights from all domains, each tagged with its domain */
    $tag = fn($arr, $domain) => array_map(fn($i) => $i + ['domain' => $domain], $arr);
    $allInsights = array_merge(
        $tag($insights, 'Life'),
        $tag($careerInsights ?? [], 'Karir'),
        $tag($financeInsights ?? [], 'Finance'),
    );

    $insightIconMap = [
        'streak'     => ['path' => 'M13 10V3L4 14h7v7l9-11h-7z', 'cls' => 'text-orange-500 bg-orange-100'],
        'prayer'     => ['path' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'cls' => 'text-green-600 bg-green-100'],
        'warning'    => ['path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'cls' => 'text-yellow-600 bg-yellow-100'],
        'gym'        => ['path' => 'M13 10V3L4 14h7v7l9-11h-7z', 'cls' => 'text-blue-600 bg-blue-100'],
        'run'        => ['path' => 'M22 12h-4l-3 9L9 3l-3 9H2', 'cls' => 'text-emerald-600 bg-emerald-100'],
        'mood-good'  => ['path' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'cls' => 'text-violet-600 bg-violet-100'],
        'mood-bad'   => ['path' => 'M9.172 14.828a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'cls' => 'text-red-500 bg-red-100'],
        'mood-ok'    => ['path' => 'M8 12h8M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'cls' => 'text-yellow-600 bg-yellow-100'],
        'tasks-done' => ['path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'cls' => 'text-green-600 bg-green-100'],
        'tasks'      => ['path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'cls' => 'text-orange-600 bg-orange-100'],
        'career'     => ['path' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'cls' => 'text-indigo-600 bg-indigo-100'],
        'interview'  => ['path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'cls' => 'text-violet-600 bg-violet-100'],
        'finance'    => ['path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'cls' => 'text-teal-600 bg-teal-100'],
        'intro'      => ['path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'cls' => 'text-gray-500 bg-gray-100'],
    ];
    $domainPill = ['Life' => 'bg-gray-100 text-gray-500', 'Karir' => 'bg-indigo-50 text-indigo-500', 'Finance' => 'bg-teal-50 text-teal-600'];
@endphp

<style>
    .dash-card { transition: transform .2s ease, box-shadow .2s ease; }
    .dash-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.07); }
    @keyframes kpiRise { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: translateY(0); } }
    .kpi-anim { animation: kpiRise .45s ease both; }
</style>

<div class="space-y-4 md:space-y-6">

    {{-- ── KPI hero row ── --}}
    @php
        // Mini sparkline from 7-day overall life score
        $spk = $scoreSpark; $sn = count($spk);
        $pts = '';
        foreach ($spk as $i => $v) {
            $x = $sn > 1 ? ($i / ($sn - 1)) * 78 + 1 : 40;
            $y = 26 - ($v / 100) * 24;
            $pts .= round($x, 1) . ',' . round($y, 1) . ' ';
        }
        $up = $lifeDelta >= 0;

        $kpis = [
            [
                'label' => __('Life Score'), 'value' => $lifeScore['overall'], 'suffix' => '%',
                'icon'  => 'M13 10V3L4 14h7v7l9-11h-7z',
                'grad'  => 'from-emerald-500 to-teal-500', 'tint' => 'bg-emerald-50', 'text' => 'text-emerald-600',
                'delta' => ($up ? '+' : '') . $lifeDelta, 'deltaUp' => $up, 'sub' => __('vs kemarin'),
                'spark' => true,
            ],
            [
                'label' => __('Streak Sholat'), 'value' => $streak, 'suffix' => ' ' . __('hari'),
                'icon'  => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z',
                'grad'  => 'from-orange-500 to-amber-500', 'tint' => 'bg-orange-50', 'text' => 'text-orange-600',
                'sub'   => __('berturut-turut'),
            ],
            [
                'label' => __('Fokus Minggu Ini'), 'value' => $pomoWeek, 'suffix' => ' ' . __('sesi'),
                'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'grad'  => 'from-rose-500 to-pink-500', 'tint' => 'bg-rose-50', 'text' => 'text-rose-600',
                'sub'   => $pomoToday . ' ' . __('hari ini'),
            ],
            $showFinance ? [
                'label' => __('Saldo Bulan Ini'), 'value' => 'Rp ' . number_format(($financeSummary['balance'] ?? 0), 0, ',', '.'), 'suffix' => '',
                'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'grad'  => 'from-sky-500 to-indigo-500', 'tint' => 'bg-sky-50', 'text' => 'text-sky-600',
                'sub'   => ($financeSummary['balance'] ?? 0) >= 0 ? __('positif') : __('defisit'),
                'small' => true,
            ] : [
                'label' => __('Mood 7 Hari'), 'value' => $moodAvg7 > 0 ? $moodAvg7 : '—', 'suffix' => $moodAvg7 > 0 ? '/5' : '',
                'icon'  => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'grad'  => 'from-violet-500 to-purple-500', 'tint' => 'bg-violet-50', 'text' => 'text-violet-600',
                'sub'   => __('rata-rata'),
            ],
        ];
    @endphp
    <div id="dashKpis" class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        @foreach($kpis as $idx => $k)
        <div class="dash-card kpi-anim relative overflow-hidden bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50" style="animation-delay: {{ $idx * 70 }}ms">
            <div class="absolute -right-6 -top-6 w-20 h-20 rounded-full bg-gradient-to-br {{ $k['grad'] }} opacity-10"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $k['grad'] }} text-white flex items-center justify-center shadow-sm">
                        <svg class="w-4.5 h-4.5" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $k['icon'] }}"/></svg>
                    </div>
                    @if(!empty($k['delta']))
                    <span class="inline-flex items-center gap-0.5 text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $k['deltaUp'] ? 'text-emerald-600 bg-emerald-50' : 'text-red-500 bg-red-50' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $k['deltaUp'] ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                        {{ $k['delta'] }}
                    </span>
                    @endif
                </div>
                <p class="{{ !empty($k['small']) ? 'text-lg md:text-xl' : 'text-2xl md:text-3xl' }} font-black text-gray-900 leading-none">
                    {{ $k['value'] }}<span class="text-sm font-bold text-gray-400">{{ $k['suffix'] }}</span>
                </p>
                <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ $k['label'] }}</p>
                @if(!empty($k['spark']) && $sn > 1)
                <svg class="w-full h-7 mt-2" viewBox="0 0 80 28" preserveAspectRatio="none">
                    <polyline points="{{ trim($pts) }}" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @else
                <p class="text-[10px] text-gray-400 mt-1.5">{{ $k['sub'] }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Smart Insights (all domains) ── --}}
    @if(count($allInsights) > 0)
    <div id="dashInsights" class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-2 mb-4">
            <h3 class="text-base font-bold">{{ __('Insight Hari Ini') }}</h3>
            <span class="text-xs text-gray-400">{{ __('dari semua aktivitasmu') }}</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            @foreach($allInsights as $ins)
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
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ic['path'] }}"/></svg>
                </div>
                <p class="flex-1 text-sm text-gray-700 leading-snug">{{ $ins['text'] }}</p>
                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full flex-shrink-0 {{ $domainPill[$ins['domain']] ?? 'bg-gray-100 text-gray-400' }}">{{ $ins['domain'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Life Score Today ── --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <h3 class="text-base font-bold">{{ __('Life Score Hari Ini') }}</h3>
                <span class="text-xs text-gray-400">{{ date('j F Y') }}</span>
            </div>
            <a href="{{ route('statistik') }}" class="inline-flex items-center gap-1 text-xs font-bold text-gray-400 hover:text-black transition-all">
                {{ __('Statistik') }}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
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
                        <svg class="w-5 h-5 {{ $dim['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $dim['svgPath'] }}"/></svg>
                    </div>
                </div>
                <p class="text-xl font-bold {{ $dim['color'] }}">{{ ($dim['empty'] ?? false) ? '—' : $dim['val'] }}</p>
                <p class="text-[10px] font-bold text-gray-500">{{ $dim['label'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 md:p-5 bg-gradient-to-br from-gray-900 via-gray-900 to-emerald-900 text-white rounded-2xl flex items-center justify-between relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-emerald-500/10"></div>
            <div class="relative">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ __('Overall Life Score') }}</p>
                <p class="text-4xl font-black mt-1">{{ $lifeScore['overall'] }}<span class="text-lg text-gray-500">%</span></p>
            </div>
            <div class="relative">
                <svg class="w-20 h-20 -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9155" fill="none"
                        stroke="{{ $lifeScore['overall'] >= 80 ? '#10b981' : ($lifeScore['overall'] >= 50 ? '#f59e0b' : '#ef4444') }}"
                        stroke-width="3" stroke-dasharray="{{ $lifeScore['overall'] }} 100" stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center text-white font-bold text-lg">{{ $lifeScore['overall'] }}%</div>
            </div>
        </div>
    </div>

    {{-- ── Karir & Finance overview ── --}}
    @if($showCareer || $showFinance)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        @if($showCareer)
        <a href="{{ route('karir') }}" class="block bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 hover:shadow-sm transition-all border border-gray-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                    {{ __('Karir') }}
                </h3>
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div><p class="text-2xl font-bold text-gray-800">{{ $careerSummary['active'] ?? 0 }}</p><p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Lamaran Aktif') }}</p></div>
                <div><p class="text-2xl font-bold text-violet-600">{{ $careerSummary['interview'] ?? 0 }}</p><p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Interview') }}</p></div>
                <div><p class="text-2xl font-bold text-green-600">{{ $careerSummary['offer'] ?? 0 }}</p><p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Offer/Diterima') }}</p></div>
            </div>
        </a>
        @endif

        @if($showFinance)
        <a href="{{ route('finance.index') }}" class="block bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 hover:shadow-sm transition-all border border-gray-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                    {{ __('Finance') }} · {{ $monthLabel }}
                </h3>
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div><p class="text-base md:text-lg font-bold text-green-600 truncate">{{ $rp($financeSummary['income'] ?? 0) }}</p><p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Pemasukan') }}</p></div>
                <div><p class="text-base md:text-lg font-bold text-red-500 truncate">{{ $rp($financeSummary['expense'] ?? 0) }}</p><p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Pengeluaran') }}</p></div>
                <div><p class="text-base md:text-lg font-bold {{ ($financeSummary['balance'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }} truncate">{{ $rp($financeSummary['balance'] ?? 0) }}</p><p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Saldo') }}</p></div>
            </div>
            @if(($financeSummary['goalCount'] ?? 0) > 0)
            <div class="mt-4 pt-3 border-t border-gray-50">
                <div class="flex justify-between text-[11px] font-bold text-gray-400 mb-1">
                    <span>{{ __('Tabungan') }}</span><span>{{ $financeSummary['goalPct'] }}%</span>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden"><div class="bg-teal-500 h-full rounded-full" style="width:{{ $financeSummary['goalPct'] }}%"></div></div>
            </div>
            @endif
        </a>
        @endif
    </div>
    @endif

    {{-- ── 7-Day Trend ── --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base font-bold mb-6">{{ __('Trend 7 Hari Terakhir') }}</h3>
        <div class="h-64"><canvas id="trendChart"></canvas></div>
    </div>

    {{-- ── Monthly Summary (Life) ── --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-1">{{ __('Ringkasan Bulan Ini') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $monthLabel }}</p>
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

    {{-- ── Mood Heatmap (30 days) ── --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
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

    {{-- ── Weekly Activity Chart ── --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold">{{ __('Aktivitas Minggu Ini') }}</h3>
            <span class="text-xs bg-gray-50 rounded-xl px-3 py-1 font-bold text-gray-500">{{ __('7 hari terakhir') }}</span>
        </div>
        <div style="height:200px"><canvas id="weekActivityChart"></canvas></div>
    </div>

</div>

{{-- ── Product tour (first-time users) ── --}}
@if($showTour)
<div id="tourRoot" class="fixed inset-0 z-[300]" style="display:none">
    <div id="tourSpot" class="absolute rounded-2xl" style="box-shadow:0 0 0 9999px rgba(17,24,39,.72); transition:all .3s ease; pointer-events:none"></div>
    <div id="tourCard" class="absolute w-[320px] max-w-[88vw] bg-white rounded-2xl shadow-2xl p-5" style="transition:top .25s ease,left .25s ease">
        <p id="tourStepNo" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1"></p>
        <h3 id="tourTitle" class="font-bold text-lg mb-1"></h3>
        <p id="tourText" class="text-sm text-gray-500 leading-relaxed mb-4"></p>
        <div class="flex items-center justify-between">
            <button id="tourSkip" type="button" class="text-xs font-bold text-gray-400 hover:text-gray-700 transition-all">{{ __('Lewati') }}</button>
            <div class="flex gap-2">
                <button id="tourPrev" type="button" class="px-3 py-2 rounded-xl text-xs font-bold bg-gray-100 hover:bg-gray-200 transition-all">{{ __('Kembali') }}</button>
                <button id="tourNext" type="button" class="px-4 py-2 rounded-xl text-xs font-bold bg-black text-white hover:bg-gray-800 transition-all">{{ __('Lanjut') }}</button>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const DONE_URL = '{{ route('tour.done') }}';
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const isDesktop = window.innerWidth >= 768;

    // r = requires the target to be visible (sidebar items / sections); open = section to expand first
    let steps = [
        { sel: null,            title: '{{ __('Selamat datang di Molife! 👋') }}', text: '{{ __('Tur singkat biar kamu tahu fungsi tiap menu. Bisa dilewati kapan saja.') }}' },
        { sel: '#dashKpis',     title: '{{ __('Ringkasan cepat') }}',            text: '{{ __('Skor hidup, streak sholat, fokus, dan keuangan dalam sekejap.') }}' },
        { sel: '#dashInsights', title: '{{ __('Insight harian') }}',             text: '{{ __('Saran & pola dari semua aktivitasmu muncul di sini setiap hari.') }}' },
        { sel: '[data-tour="dashboard-nav"]', r: true, title: '{{ __('Dashboard') }}', text: '{{ __('Halaman ini — pusat ringkasan semua aktivitasmu.') }}' },
        { sel: '[data-feat="sholat"]',   r: true, open: 'life', title: '{{ __('Sholat') }}',      text: '{{ __('Catat sholat 5 waktu, rawatib & takbir, plus jadwal otomatis sesuai lokasi.') }}' },
        { sel: '[data-feat="pomodoro"]', r: true, open: 'life', title: 'Pomodoro',                text: '{{ __('Timer fokus yang tetap berjalan walau kamu pindah menu.') }}' },
        { sel: '[data-feat="tasks"]',    r: true, open: 'life', title: 'Tasks & Notes',           text: '{{ __('To-do harian & mingguan, catatan, refleksi, dan riwayatnya.') }}' },
        { sel: '[data-feat="goals"]',    r: true, open: 'life', title: '{{ __('Goals & Reminder') }}', text: '{{ __('Target bulanan & pengingat, termasuk atur lokasi jadwal sholat.') }}' },
        { sel: '[data-section="karir"]',   r: true, open: 'karir',   title: 'Karir',   text: '{{ __('Lacak lamaran kerja, tahap interview, dan persiapan melamar.') }}' },
        { sel: '[data-section="finance"]', r: true, open: 'finance', title: 'Finance', text: '{{ __('Catat transaksi, atur anggaran, dan kejar target tabungan.') }}' },
        { sel: '[data-section="settings"]',r: true, open: 'settings',title: '{{ __('Pengaturan') }}', text: '{{ __('Aktif/nonaktifkan fitur & atur profil sesuai kebutuhanmu.') }}' },
        { sel: null,            title: '{{ __('Siap memulai! 🎉') }}',           text: '{{ __('Mulai dari mencatat sholat atau task hari ini. Selamat mencoba!') }}' },
    ];

    // Drop steps whose target is hidden/disabled; on mobile drop sidebar steps (sidebar is off-canvas)
    steps = steps.filter(s => {
        if (!s.r) return true;
        if (!isDesktop) return false;
        const el = document.querySelector(s.sel);
        return el && !el.classList.contains('hidden');
    });

    let i = 0;
    const root = document.getElementById('tourRoot');
    const spot = document.getElementById('tourSpot');
    const card = document.getElementById('tourCard');
    const M = 12, GAP = 14, PAD = 8;

    function placeCard(r) {
        const cw = card.offsetWidth || 320, ch = card.offsetHeight || 200;
        const clampTop  = t => Math.min(Math.max(M, t), window.innerHeight - ch - M);
        const clampLeft = l => Math.min(Math.max(M, l), window.innerWidth - cw - M);
        let pos;
        if (r.right + GAP + cw <= window.innerWidth - M)      pos = { left: r.right + GAP,      top: clampTop(r.top) };           // right
        else if (r.bottom + GAP + ch <= window.innerHeight-M) pos = { left: clampLeft(r.left),  top: r.bottom + GAP };            // below
        else if (r.top - GAP - ch >= M)                       pos = { left: clampLeft(r.left),  top: r.top - GAP - ch };         // above
        else if (r.left - GAP - cw >= M)                      pos = { left: r.left - GAP - cw,  top: clampTop(r.top) };          // left
        else                                                  pos = { left: (window.innerWidth-cw)/2, top: (window.innerHeight-ch)/2 };
        card.style.transform = 'none';
        card.style.left = pos.left + 'px';
        card.style.top  = pos.top + 'px';
    }

    function position(el) {
        const r = el.getBoundingClientRect();
        spot.style.top = (r.top - PAD) + 'px'; spot.style.left = (r.left - PAD) + 'px';
        spot.style.width = (r.width + PAD * 2) + 'px'; spot.style.height = (r.height + PAD * 2) + 'px';
        placeCard(r);
    }

    function centerCard() {
        spot.style.width = '0'; spot.style.height = '0';
        spot.style.top = '50%'; spot.style.left = '50%';
        card.style.top = '50%'; card.style.left = '50%'; card.style.transform = 'translate(-50%,-50%)';
    }

    function render() {
        const s = steps[i];
        document.getElementById('tourStepNo').textContent = '{{ __('Langkah') }} ' + (i + 1) + ' / ' + steps.length;
        document.getElementById('tourTitle').textContent = s.title;
        document.getElementById('tourText').textContent = s.text;
        document.getElementById('tourPrev').style.visibility = i === 0 ? 'hidden' : 'visible';
        document.getElementById('tourNext').textContent = i === steps.length - 1 ? '{{ __('Selesai') }}' : '{{ __('Lanjut') }}';

        if (s.open && typeof window.toggleSection === 'function') window.toggleSection(s.open);

        const el = s.sel ? document.querySelector(s.sel) : null;
        if (el) {
            el.scrollIntoView({ block: 'center', behavior: 'smooth' });
            setTimeout(() => {
                if (el.getClientRects().length > 0) position(el); else centerCard();
            }, s.open ? 360 : 260);
        } else {
            centerCard();
        }
    }
    function finish() {
        root.style.display = 'none';
        fetch(DONE_URL, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } }).catch(() => {});
    }
    document.getElementById('tourNext').onclick = () => { if (i === steps.length - 1) finish(); else { i++; render(); } };
    document.getElementById('tourPrev').onclick = () => { if (i > 0) { i--; render(); } };
    document.getElementById('tourSkip').onclick = finish;
    window.addEventListener('resize', () => { if (root.style.display !== 'none') render(); });

    setTimeout(() => { root.style.display = 'block'; render(); }, 500);
})();
</script>
@endif
@endsection

@push('scripts')
<script>
if (window.Chart) {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.plugins.tooltip.backgroundColor = '#111827';
    Chart.defaults.plugins.tooltip.cornerRadius = 10;
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.displayColors = false;
}

/* ── Life Score Trend → clean bar chart, highest day highlighted ── */
const weekScores = @json($weekScores);
(function () {
    const ctx = document.getElementById('trendChart').getContext('2d');
    const vals = weekScores.map(d => d.score);
    const max = Math.max(...vals);
    const colors = vals.map(v => (v === max && max > 0) ? '#6366f1' : '#e0e7ff');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: weekScores.map(d => d.date),
            datasets: [{ data: vals, backgroundColor: colors, hoverBackgroundColor: '#4f46e5', borderRadius: 8, borderSkipped: false, maxBarThickness: 38 }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => '{{ __('Life Score') }}: ' + c.raw + '%' } } },
            scales: {
                y: { min: 0, max: 100, ticks: { stepSize: 25, callback: v => v + '%', font: { size: 9 }, color: '#9ca3af' }, grid: { color: '#f3f4f6', drawBorder: false } },
                x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#6b7280' } },
            },
        },
    });
})();

/* ── Weekly Activity → colorful stacked bars ── */
(function () {
    const ctx = document.getElementById('weekActivityChart').getContext('2d');
    const z = a => a.map(v => v == null ? 0 : v);
    const base = { stack: 's', borderRadius: 5, borderSkipped: false, maxBarThickness: 30, borderWidth: 2, borderColor: '#fff' };
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['{{ __('Sen') }}','{{ __('Sel') }}','{{ __('Rab') }}','{{ __('Kam') }}','{{ __('Jum') }}','{{ __('Sab') }}','{{ __('Min') }}'],
            datasets: [
                { ...base, label: '{{ __('Sholat') }}', data: @json($weekSpiritualData), backgroundColor: '#10b981' },
                { ...base, label: '{{ __('Mood') }}',   data: z(@json($weekMoodData)),    backgroundColor: '#8b5cf6' },
                { ...base, label: 'Gym',                 data: @json($weekFitnessData),    backgroundColor: '#3b82f6' },
                { ...base, label: '{{ __('Lari') }}',    data: @json($weekRunData),        backgroundColor: '#f59e0b' },
            ],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', padding: 12, font: { size: 10, weight: '600' }, boxWidth: 8 } } },
            scales: {
                x: { stacked: true, grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#6b7280' } },
                y: { stacked: true, beginAtZero: true, ticks: { stepSize: 2, font: { size: 9 }, color: '#9ca3af' }, grid: { color: '#f9fafb', drawBorder: false } },
            },
        },
    });
})();
</script>
@endpush
