@extends('layouts.app')
@section('title', 'Goals & Reminder')
@section('page-title', 'Goals & Reminder')
@section('breadcrumb', 'Goals')

@section('content')
@php
    $monthLabel = (new DateTime())->format('F Y');

    /* ── Accent colours per goal key ── */
    $accentMap = [
        'sholat'   => ['bg' => 'bg-green-500',   'text' => 'text-green-600',   'bgLight' => 'bg-green-50',   'bar' => 'bg-green-500'],
        'gym'      => ['bg' => 'bg-blue-500',    'text' => 'text-blue-600',    'bgLight' => 'bg-blue-50',    'bar' => 'bg-blue-500'],
        'run'      => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'bgLight' => 'bg-emerald-50', 'bar' => 'bg-emerald-500'],
        'intimacy' => ['bg' => 'bg-pink-500',    'text' => 'text-pink-600',    'bgLight' => 'bg-pink-50',    'bar' => 'bg-pink-500'],
    ];

    /* ── All possible goals — shown only when the matching feature is enabled ── */
    $allGoals = [
        [
            'key'      => 'sholat',
            'feature'  => 'sholat',
            'label'    => __('Sholat 5 Wajib'),
            'unit'     => __('hari'),
            'achieved' => $daysSholatComplete,
            'default'  => 25,
            'max'      => count($monthDates),
            'sub'      => null,
        ],
        [
            'key'      => 'gym',
            'feature'  => 'gym',
            'label'    => __('Sesi Gym'),
            'unit'     => __('sesi'),
            'achieved' => $gymMonthly,
            'default'  => 16,
            'max'      => count($monthDates),
            'sub'      => null,
        ],
        [
            'key'      => 'run',
            'feature'  => 'run',
            'label'    => __('Sesi Lari'),
            'unit'     => __('sesi'),
            'achieved' => $runMonthlyCount,
            'default'  => 12,
            'max'      => count($monthDates),
            'sub'      => number_format($runMonthlyDist, 1) . ' ' . __('km total bulan ini'),
        ],
        [
            'key'      => 'intimacy',
            'feature'  => 'intimasi',
            'label'    => __('Intimasi'),
            'unit'     => __('kali'),
            'achieved' => $intimacyMonthly,
            'default'  => 12,
            'max'      => 60,
            'sub'      => null,
        ],
    ];

    /* Filter to only enabled features */
    $goalsConfig = array_values(array_filter($allGoals, fn($g) => $features[$g['feature']] ?? true));
    $goalCount   = count($goalsConfig);

    /* Responsive grid class */
    $gridClass = match(true) {
        $goalCount <= 1 => 'grid-cols-1',
        $goalCount === 2 => 'grid-cols-1 sm:grid-cols-2',
        $goalCount === 3 => 'grid-cols-1 md:grid-cols-3',
        default          => 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4',
    };

    /* ── Reminders — grouped by feature ── */
    $allReminderGroups = [
        'sholat'  => [
            'Subuh'   => __('Sholat Subuh'),
            'Dzuhur'  => __('Sholat Dzuhur'),
            'Ashar'   => __('Sholat Ashar'),
            'Maghrib' => __('Sholat Maghrib'),
            'Isya'    => __('Sholat Isya'),
            'Tahajud' => 'Tahajud',
            'Dhuha'   => __('Sholat Dhuha'),
        ],
        'gym'   => ['Gym'      => __('Sesi Gym')],
        'run'   => ['Run'      => __('Sesi Lari')],
        'tasks' => ['Refleksi' => __('Refleksi Harian')],
    ];

    $reminderItems = [];   // key => label
    foreach ($allReminderGroups as $feat => $items) {
        if ($features[$feat] ?? true) {
            $reminderItems = array_merge($reminderItems, $items);
        }
    }
@endphp

<div class="space-y-4 md:space-y-6">

    {{-- ── Monthly Goals ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-start justify-between mb-4 md:mb-6 flex-wrap gap-2">
            <div>
                <h3 class="text-base md:text-lg font-bold">{{ __('Target Bulan Ini') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $monthLabel }}</p>
            </div>
            <span class="text-[10px] font-bold text-gray-500 bg-gray-50 px-3 py-1.5 rounded-full">{{ __('Tetap realistis & bertahap') }}</span>
        </div>

        @if($goalCount === 0)
        {{-- Empty state when all life features are disabled --}}
        <div class="py-12 text-center">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-400">{{ __('Belum ada fitur yang aktif') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('Aktifkan fitur di') }} <a href="{{ route('settings') }}" class="underline">{{ __('Pengaturan') }}</a> {{ __('untuk mulai menetapkan target.') }}</p>
        </div>
        @else
        <div class="grid {{ $gridClass }} gap-4">
            @foreach($goalsConfig as $g)
            @php
                $target  = $goals[$g['key']] ?? $g['default'];
                $pct     = $target > 0 ? min(100, ($g['achieved'] / $target) * 100) : 0;
                $onTrack = $g['achieved'] >= $target && $target > 0;
                $a       = $accentMap[$g['key']];
            @endphp
            <div class="p-5 rounded-2xl border {{ $onTrack ? 'border-transparent' : 'border-gray-100' }} {{ $a['bgLight'] }}">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold {{ $a['text'] }}">{{ $g['label'] }}</p>
                    @if($onTrack)
                    <span class="text-[9px] font-bold text-white bg-green-500 px-2 py-0.5 rounded-full">{{ __('TERCAPAI') }}</span>
                    @endif
                </div>

                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-3xl font-bold">{{ $g['achieved'] }}</span>
                    <span class="text-sm text-gray-500">/ {{ $target }} {{ $g['unit'] }}</span>
                </div>

                <div class="w-full bg-white h-2 rounded-full mb-3 overflow-hidden">
                    <div class="{{ $a['bar'] }} h-full rounded-full transition-all" style="width:{{ $pct }}%"></div>
                </div>

                @if($g['sub'])
                <p class="text-[10px] {{ $a['text'] }} mb-3">{{ $g['sub'] }}</p>
                @endif

                <form method="POST" action="{{ route('goals.update') }}">
                    @csrf
                    <input type="hidden" name="field" value="{{ $g['key'] }}">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">Target</span>
                        <input type="number" name="value" min="0" max="{{ $g['max'] }}" value="{{ $target }}"
                            onchange="this.form.submit()"
                            class="w-16 px-2 py-1 bg-white border border-gray-200 rounded-lg text-sm font-bold text-center outline-none focus:border-gray-400 transition-all">
                        <span class="text-[10px] text-gray-400">{{ $g['unit'] }}</span>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── Reminders ── --}}
    @if(count($reminderItems) > 0)
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-start justify-between mb-4 md:mb-6 flex-wrap gap-2">
            <div>
                <h3 class="text-base md:text-lg font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    {{ __('Waktu Pengingat') }}
                </h3>
                <p class="text-xs text-gray-400 mt-1">{{ __('Set jam pengingat untuk setiap aktivitas.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($reminderItems as $key => $label)
            @php $time = $reminders[$key] ?? ''; @endphp
            <div class="p-4 rounded-2xl flex items-center justify-between gap-3 transition-all {{ $time ? 'bg-gray-900 text-white' : 'bg-gray-50' }}">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold truncate">{{ $label }}</p>
                    <p class="text-[10px] {{ $time ? 'text-gray-400' : 'text-gray-400' }}">
                        {{ $time ? __('Pengingat') . ' ' . $time : __('Belum diatur') }}
                    </p>
                </div>
                <form method="POST" action="{{ route('reminders.update') }}" class="flex items-center gap-1">
                    @csrf
                    <input type="hidden" name="key" value="{{ $key }}">
                    <input type="time" name="time" value="{{ $time }}"
                        onchange="this.form.submit()"
                        class="px-2 py-1.5 rounded-lg text-sm font-bold outline-none
                            {{ $time ? 'bg-white/10 text-white border border-white/20' : 'bg-white border border-gray-200' }}">
                    @if($time)
                    <button type="submit" name="time" value=""
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:bg-white/10 hover:text-white"
                        title="{{ __('Hapus pengingat') }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif
                </form>
            </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 bg-yellow-50 rounded-2xl flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"/>
            </svg>
            <p class="text-xs text-yellow-900 leading-relaxed">
                <span class="font-bold">Tips:</span> {{ __('Set pengingat 5–10 menit sebelum waktu sholat. Pengingat ini bersifat manual sesuai jam yang kamu set dan akan ditampilkan saat kamu membuka aplikasi.') }}
            </p>
        </div>
    </div>
    @endif

</div>
@endsection
