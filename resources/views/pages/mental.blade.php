@extends('layouts.app')
@section('title', __('Mental'))
@section('page-title', __('Mental'))
@section('breadcrumb', __('Mental'))

@section('content')
@php
    $moodLabel = ['', __('Buruk'), __('Kurang'), __('Biasa'), __('Baik'), __('Luar Biasa')];
    $moodFacePaths = [
        1 => 'M9.172 14.828a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        2 => 'M10 14a2 2 0 014 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        3 => 'M8 12h8M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        4 => 'M14 14a2 2 0 01-4 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        5 => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
    $moodColors = [
        0 => 'bg-gray-100 text-gray-400',
        1 => 'bg-red-500 text-white',
        2 => 'bg-orange-400 text-white',
        3 => 'bg-yellow-400 text-white',
        4 => 'bg-green-500 text-white',
        5 => 'bg-emerald-500 text-white',
    ];
    $moodBorderActive = [
        1 => 'border-red-400 bg-red-50',
        2 => 'border-orange-400 bg-orange-50',
        3 => 'border-yellow-400 bg-yellow-50',
        4 => 'border-green-400 bg-green-50',
        5 => 'border-emerald-400 bg-emerald-50',
    ];
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Stats Row ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold {{ $moodAvg7 >= 4 ? 'text-green-600' : ($moodAvg7 >= 3 ? 'text-yellow-600' : ($moodAvg7 > 0 ? 'text-red-500' : 'text-gray-300')) }}">
                {{ $moodAvg7 > 0 ? $moodAvg7 : '—' }}
            </p>
            <p class="text-[10px] font-bold text-gray-400 mt-1">{{ __('Avg Mood (7 Hari)') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold {{ $moodAvg30 >= 4 ? 'text-green-600' : ($moodAvg30 >= 3 ? 'text-yellow-600' : ($moodAvg30 > 0 ? 'text-red-500' : 'text-gray-300')) }}">
                {{ $moodAvg30 > 0 ? $moodAvg30 : '—' }}
            </p>
            <p class="text-[10px] font-bold text-gray-400 mt-1">{{ __('Avg Mood (30 Hari)') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold {{ $energyAvg7 >= 4 ? 'text-violet-600' : ($energyAvg7 >= 3 ? 'text-blue-500' : ($energyAvg7 > 0 ? 'text-gray-500' : 'text-gray-300')) }}">
                {{ $energyAvg7 > 0 ? $energyAvg7 : '—' }}
            </p>
            <p class="text-[10px] font-bold text-gray-400 mt-1">{{ __('Avg Energi (7 Hari)') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-gray-700">{{ $daysLogged }}</p>
            <p class="text-[10px] font-bold text-gray-400 mt-1">{{ __('Hari Dicatat') }} <span class="text-gray-300">/30</span></p>
        </div>
    </div>

    {{-- ── Mood Log ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold">{{ __('Log Mood Hari Ini') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ date('l, j F Y') }}</p>
            </div>
            @if($todayMood['score'] > 0)
            <div class="w-12 h-12 {{ $moodColors[$todayMood['score']] }} rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $moodFacePaths[$todayMood['score']] }}"/>
                </svg>
            </div>
            @endif
        </div>

        <form method="POST" action="{{ route('mental.mood') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $today }}">
            <input type="hidden" name="score"  id="moodScoreInput"  value="{{ $todayMood['score'] ?: 3 }}">
            <input type="hidden" name="energy" id="energyScoreInput" value="{{ $todayMood['energy'] ?: 3 }}">

            {{-- Mood --}}
            <div class="mb-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Perasaan') }}</p>
                <div class="flex gap-2">
                    @foreach($moodFacePaths as $s => $facePath)
                    <button type="button" onclick="selectMood({{ $s }})" id="moodBtn{{ $s }}"
                        class="mood-btn flex-1 py-3 rounded-2xl border-2 transition-all flex flex-col items-center gap-1
                            {{ $todayMood['score'] == $s ? 'border-gray-900 bg-gray-900 shadow text-white' : 'border-gray-100 bg-gray-50 hover:border-gray-300 text-gray-500' }}">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $facePath }}"/>
                        </svg>
                        <span class="text-[9px] font-bold {{ $todayMood['score'] == $s ? 'text-gray-300' : 'text-gray-400' }} hidden sm:block">{{ $moodLabel[$s] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Energy --}}
            <div class="mb-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Tingkat Energi') }}</p>
                <div class="flex gap-2">
                    @foreach(range(1,5) as $e)
                    <button type="button" onclick="selectEnergy({{ $e }})" id="energyBtn{{ $e }}"
                        class="energy-btn flex-1 py-2.5 rounded-xl border-2 font-bold text-sm transition-all
                            {{ $todayMood['energy'] == $e ? 'border-violet-500 bg-violet-500 text-white' : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-violet-300' }}">
                        {{ $e }}
                    </button>
                    @endforeach
                </div>
                <div class="flex justify-between mt-1.5">
                    <span class="text-[9px] text-gray-400">{{ __('Sangat Rendah') }}</span>
                    <span class="text-[9px] text-gray-400">{{ __('Sangat Tinggi') }}</span>
                </div>
            </div>

            {{-- Note --}}
            <div class="mb-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Catatan') }}</p>
                <input type="text" name="note" value="{{ $todayMood['note'] }}"
                    placeholder="{{ __('Apa yang kamu rasakan hari ini?') }}"
                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300 transition-all">
            </div>

            <button type="submit" class="w-full py-3 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ $todayMood['score'] > 0 ? __('Perbarui') : __('Simpan Mood') }}
            </button>
        </form>
    </div>

    {{-- ── 30-Day Mood Chart ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base font-bold mb-6">{{ __('Trend Mood 30 Hari') }}</h3>
        <div class="h-56">
            <canvas id="moodChart"></canvas>
        </div>
    </div>

    {{-- ── Mood Heatmap ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold">{{ __('Kalender Mood') }}</h3>
            <div class="flex items-center gap-2 text-[10px] text-gray-400">
                <div class="flex items-center gap-1"><div class="w-3 h-3 bg-gray-100 rounded"></div> {{ __('Belum') }}</div>
                <div class="flex items-center gap-1"><div class="w-3 h-3 bg-yellow-400 rounded"></div> {{ __('Biasa') }}</div>
                <div class="flex items-center gap-1"><div class="w-3 h-3 bg-emerald-500 rounded"></div> {{ __('Baik') }}</div>
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

    {{-- ── Reflection ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-base font-bold">{{ __('Refleksi Harian') }}</h3>
            @if($reflectionStreak > 0)
            <span class="text-xs font-bold bg-violet-50 text-violet-600 px-3 py-1 rounded-full">
                {{ $reflectionStreak }}/7 {{ __('hari terakhir') }}
            </span>
            @endif
        </div>
        <p class="text-xs text-gray-400 mb-5">{{ __('Untuk hari ini') }} · {{ date('l, j F Y') }}</p>

        {{-- Form --}}
        <form method="POST" action="{{ route('tasks.reflection.update') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $today }}">
            <div class="space-y-3 mb-4">
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-gray-400 mb-1.5">
                        <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Apa yang berjalan baik hari ini?') }}
                    </label>
                    <textarea name="good" rows="3"
                        placeholder="{{ __('Satu hal positif, kecil pun tidak apa...') }}"
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-gray-300 resize-none transition-all">{{ $todayReflection['good'] ?? '' }}</textarea>
                </div>
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-gray-400 mb-1.5">
                        <svg class="w-3.5 h-3.5 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        {{ __('Apa yang bisa diperbaiki besok?') }}
                    </label>
                    <textarea name="improve" rows="3"
                        placeholder="{{ __('Satu kebiasaan / sikap yang ingin kamu naikkan...') }}"
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-gray-300 resize-none transition-all">{{ $todayReflection['improve'] ?? '' }}</textarea>
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Simpan Refleksi') }}
            </button>
            <p class="text-[11px] text-gray-400 text-center mt-2.5 flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('Tersimpan otomatis & bisa dilihat di riwayat bawah') }}
            </p>
        </form>

        {{-- Riwayat Refleksi --}}
        <div class="mt-6 pt-6 border-t border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                {{ __('Riwayat Refleksi') }}
                @if(count($reflectionHistory) > 0)<span class="text-gray-300">· {{ count($reflectionHistory) }}</span>@endif
            </p>

            @if(count($reflectionHistory) === 0)
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">{{ __('Belum ada refleksi tersimpan') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Refleksi yang kamu simpan akan muncul di sini') }}</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($reflectionHistory as $entry)
                <div class="rounded-2xl border border-gray-100 bg-gray-50 overflow-hidden">
                    {{-- Date header --}}
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-white border-b border-gray-100">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-bold text-gray-600">{{ $entry['label'] }}</span>
                        @if($entry['isToday'] ?? false)
                        <span class="text-[10px] font-bold bg-violet-100 text-violet-600 px-2 py-0.5 rounded-full">{{ __('Hari Ini') }}</span>
                        @endif
                        <form method="POST" action="{{ route('mental.reflection.delete') }}" class="ml-auto flex items-center">
                            @csrf @method('DELETE')
                            <input type="hidden" name="date" value="{{ $entry['date'] }}">
                            <button type="button"
                                onclick="askDelete(this, '{{ __('Hapus refleksi tanggal ini?') }}')"
                                class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all"
                                title="{{ __('Hapus') }}">
                                <svg class="w-3.5 h-3.5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                    {{-- Content --}}
                    <div class="p-4 space-y-3">
                        @if($entry['good'])
                        <div class="flex gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <p class="text-xs text-gray-700 leading-relaxed">{{ $entry['good'] }}</p>
                        </div>
                        @endif
                        @if($entry['improve'])
                        <div class="flex gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-yellow-100 flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            </span>
                            <p class="text-xs text-gray-700 leading-relaxed">{{ $entry['improve'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
const moodScoreInput   = document.getElementById('moodScoreInput');
const energyScoreInput = document.getElementById('energyScoreInput');

function selectMood(s) {
    moodScoreInput.value = s;
    [1,2,3,4,5].forEach(n => {
        const btn = document.getElementById('moodBtn' + n);
        btn.className = 'mood-btn flex-1 py-3 rounded-2xl border-2 transition-all flex flex-col items-center gap-1 ' +
            (n === s
                ? 'border-gray-900 bg-gray-900 shadow text-white'
                : 'border-gray-100 bg-gray-50 hover:border-gray-300 text-gray-500');
        const lbl = btn.querySelector('span');
        if (lbl) lbl.className = 'text-[9px] font-bold hidden sm:block ' + (n === s ? 'text-gray-300' : 'text-gray-400');
    });
}

function selectEnergy(e) {
    energyScoreInput.value = e;
    [1,2,3,4,5].forEach(n => {
        const btn = document.getElementById('energyBtn' + n);
        btn.className = 'energy-btn flex-1 py-2.5 rounded-xl border-2 font-bold text-sm transition-all ' +
            (n === e ? 'border-violet-500 bg-violet-500 text-white' : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-violet-300');
    });
}

// Mood Chart
const moodData   = @json(array_column($moodHistory, 'score'));
const energyData = @json(array_column($moodHistory, 'energy'));
const dateLabels = @json(array_map(fn($d) => substr($d['date'], 8), $moodHistory));

const ctx = document.getElementById('moodChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: dateLabels,
        datasets: [
            {
                label: '{{ __('Mood') }}',
                data: moodData.map(v => v === 0 ? null : v),
                borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.08)',
                tension: 0.4, borderWidth: 2.5, pointRadius: 3,
                pointBackgroundColor: '#10b981', fill: true, spanGaps: false,
            },
            {
                label: '{{ __('Energi') }}',
                data: energyData.map(v => v === 0 ? null : v),
                borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.06)',
                tension: 0.4, borderWidth: 2, pointRadius: 3,
                pointBackgroundColor: '#8b5cf6', fill: false, spanGaps: false,
            },
        ],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true } } },
        scales: {
            y: { min: 0, max: 5, ticks: { stepSize: 1, callback: v => ['', 'Buruk','Kurang','Biasa','Baik','Luar Biasa'][v] || v } },
            x: { grid: { display: false }, ticks: { font: { size: 9 }, maxTicksLimit: 10 } },
        },
    },
});
</script>
@endpush
