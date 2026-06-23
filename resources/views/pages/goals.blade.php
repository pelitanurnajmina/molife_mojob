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
        // 5 wajib prayers are handled automatically by location (see block below);
        // only sunnah prayers remain as manual reminders here.
        'sholat'  => [
            'Tahajud' => 'Tahajud',
            'Dhuha'   => __('Sholat Dhuha'),
        ],
        'gym'   => ['Gym'      => __('Sesi Gym')],
        'run'   => ['Run'      => __('Sesi Lari')],
        'tasks' => ['Refleksi' => __('Refleksi Harian')],
        'journal' => ['Journal' => __('Menulis Journal')],
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

    {{-- ── Auto Prayer Times (by location) ── --}}
    @if($features['sholat'] ?? true)
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-start justify-between mb-4 flex-wrap gap-3">
            <div>
                <h3 class="text-base md:text-lg font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('Jadwal Sholat Otomatis') }}
                </h3>
                <p class="text-xs text-gray-400 mt-1">{{ __('Pilih wilayahmu — waktu sholat dihitung otomatis. Aktifkan pengingat per waktu sesuka hati.') }}</p>
            </div>
            <form method="POST" action="{{ route('reminders.prayer.city') }}" class="w-full sm:w-56">
                @csrf
                <select name="city" onchange="this.form.submit()" aria-label="{{ __('Wilayah') }}">
                    <option value="" disabled {{ $prayerCity ? '' : 'selected' }}>{{ __('Pilih wilayah...') }}</option>
                    @foreach($prayerCities as $ckey => $c)
                    <option value="{{ $ckey }}" {{ $prayerCity === $ckey ? 'selected' : '' }}>{{ $c[0] }} · {{ $c[4] }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if(!$prayerCity)
        <div class="text-center py-8 bg-gray-50 rounded-2xl">
            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">{{ __('Belum pilih wilayah') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Pilih kota terdekat untuk melihat jadwal sholat.') }}</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @php $prayerIcons = ['Subuh'=>'M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z','Dzuhur'=>'M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z','Ashar'=>'M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.72 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z','Maghrib'=>'M17 18a5 5 0 00-10 0M12 2v7m0 0l3-3m-3 3L9 6m-6 12h18','Isya'=>'M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z']; @endphp
            @foreach(\App\Services\PrayerTimeService::PRAYERS as $prayer)
            @php $on = in_array($prayer, $prayerEnabled); $ptime = $prayerTimes[$prayer] ?? '--:--'; @endphp
            <div class="p-4 rounded-2xl flex items-center gap-3 transition-all {{ $on ? 'bg-gray-900 text-white' : 'bg-gray-50' }}">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $on ? 'bg-white/10 text-white' : 'bg-white text-gray-400' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $prayerIcons[$prayer] }}"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold">{{ $prayer }}</p>
                    <p class="text-lg font-black leading-tight tabular-nums">{{ $ptime }}</p>
                </div>
                <form method="POST" action="{{ route('reminders.prayer.toggle') }}" class="flex-shrink-0 m-0 leading-none">
                    @csrf
                    <input type="hidden" name="prayer" value="{{ $prayer }}">
                    <button type="submit" role="switch" aria-checked="{{ $on ? 'true' : 'false' }}"
                        title="{{ $on ? __('Matikan pengingat') : __('Aktifkan pengingat') }}"
                        class="relative w-11 h-6 rounded-full transition-all {{ $on ? 'bg-emerald-500' : 'bg-gray-300' }}">
                        <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform {{ $on ? 'translate-x-5' : '' }}"></span>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        <p class="text-[11px] text-gray-400 mt-3 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('Perhitungan metode Kemenag RI untuk') }} {{ \App\Services\PrayerTimeService::cityLabel($prayerCity) }}. {{ __('Waktu bisa berbeda ±1–2 menit dari jadwal masjid setempat.') }}
        </p>
        @endif
    </div>
    @endif

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
            @php [$hh, $mm] = $time ? array_pad(explode(':', $time), 2, '') : ['', '']; @endphp
            <div class="p-4 rounded-2xl transition-all {{ $time ? 'bg-gray-900 text-white' : 'bg-gray-50' }}">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="min-w-0">
                        <p class="text-sm font-bold truncate">{{ $label }}</p>
                        <p class="text-[10px] text-gray-400">
                            {{ $time ? __('Pengingat') . ' ' . $time : __('Belum diatur') }}
                        </p>
                    </div>
                    @if($time)
                    <form method="POST" action="{{ route('reminders.update') }}" class="flex-shrink-0 m-0 leading-none">
                        @csrf
                        <input type="hidden" name="key" value="{{ $key }}">
                        <button type="submit" name="time" value=""
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:bg-white/10 hover:text-white transition-all"
                            title="{{ __('Hapus pengingat') }}">
                            <svg class="w-3.5 h-3.5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
                <form method="POST" action="{{ route('reminders.update') }}" class="flex items-center gap-1.5">
                    @csrf
                    <input type="hidden" name="key" value="{{ $key }}">
                    <input type="hidden" name="time" value="{{ $time }}" class="reminder-time-value">
                    <div class="flex-1">
                        <select class="reminder-hour" onchange="updateReminderTime(this)" aria-label="{{ __('Jam') }}">
                            <option value="">{{ __('Jam') }}</option>
                            @for($h = 0; $h < 24; $h++)
                            @php $hv = sprintf('%02d', $h); @endphp
                            <option value="{{ $hv }}" {{ $hh === $hv ? 'selected' : '' }}>{{ $hv }}</option>
                            @endfor
                        </select>
                    </div>
                    <span class="font-bold {{ $time ? 'text-white' : 'text-gray-400' }}">:</span>
                    <div class="flex-1">
                        <select class="reminder-min" onchange="updateReminderTime(this)" aria-label="{{ __('Menit') }}">
                            <option value="">{{ __('Mnt') }}</option>
                            @for($m = 0; $m < 60; $m++)
                            @php $mv = sprintf('%02d', $m); @endphp
                            <option value="{{ $mv }}" {{ $mm === $mv ? 'selected' : '' }}>{{ $mv }}</option>
                            @endfor
                        </select>
                    </div>
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

@push('scripts')
<script>
function updateReminderTime(el) {
    const form = el.closest('form');
    const h = form.querySelector('.reminder-hour').value;
    const m = form.querySelector('.reminder-min').value;
    if (h === '') return;                       // hour required
    form.querySelector('.reminder-time-value').value = h + ':' + (m === '' ? '00' : m);
    form.submit();
}
</script>
@endpush
@endsection
