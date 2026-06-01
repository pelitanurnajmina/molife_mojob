@extends('layouts.app')
@section('title', __('Hari Ini'))
@section('page-title', __('Hari Ini'))
@section('breadcrumb', __('Today'))

@section('content')
@php
    $moodColors = [
        '',
        'border-red-400 bg-red-50 text-red-500',
        'border-orange-400 bg-orange-50 text-orange-500',
        'border-yellow-400 bg-yellow-50 text-yellow-600',
        'border-green-400 bg-green-50 text-green-600',
        'border-emerald-400 bg-emerald-50 text-emerald-600',
    ];
    $priDot   = ['high' => 'bg-red-500', 'medium' => 'bg-orange-400', 'low' => 'bg-gray-300'];
    $priBadge = [
        'high'   => 'bg-red-50 text-red-500 border border-red-200',
        'medium' => 'bg-orange-50 text-orange-500 border border-orange-200',
        'low'    => 'bg-gray-100 text-gray-400 border border-gray-200',
    ];
    $priLabel = ['high' => __('Tinggi'), 'medium' => __('Sedang'), 'low' => __('Rendah')];
    $f = fn($k) => $features[$k] ?? true;
@endphp

<div class="space-y-4 md:space-y-6">

    {{-- ── Date + Life Score ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex flex-col sm:flex-row items-center gap-6">

            {{-- Life Score Ring --}}
            <div class="relative flex-shrink-0">
                <svg class="w-28 h-28 -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#f3f4f6" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9155" fill="none"
                        stroke="{{ $lifeScore['overall'] >= 80 ? '#10b981' : ($lifeScore['overall'] >= 50 ? '#f59e0b' : '#ef4444') }}"
                        stroke-width="3"
                        stroke-dasharray="{{ $lifeScore['overall'] }} 100"
                        stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold leading-none">{{ $lifeScore['overall'] }}</span>
                    <span class="text-[9px] text-gray-400 font-bold">Score</span>
                </div>
            </div>

            {{-- Info + Date --}}
            <div class="flex-1 text-center sm:text-left">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Hari Ini') }}</p>
                <h2 class="text-xl md:text-2xl font-bold">{{ $dateLabel }}</h2>
                <div class="flex flex-wrap gap-2 mt-3 justify-center sm:justify-start">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-600">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        {{ $lifeScore['spiritual'] }}%
                    </span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        {{ $lifeScore['health'] }}%
                    </span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-violet-50 text-violet-600">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $lifeScore['hasMood'] ? $lifeScore['mental'].'%' : '—' }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-orange-50 text-orange-600">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        {{ $lifeScore['hasTasks'] ? $lifeScore['productivity'].'%' : '—' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Mood Check-in ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base font-bold mb-1">{{ __('Mood & Energi') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ __('Bagaimana perasaanmu hari ini?') }}</p>

        <form method="POST" action="{{ route('mental.mood') }}" id="moodForm">
            @csrf
            <input type="hidden" name="date" value="{{ $today }}">
            <input type="hidden" name="score" id="moodScoreInput" value="{{ $todayMood['score'] ?: 3 }}">
            <input type="hidden" name="energy" id="energyScoreInput" value="{{ $todayMood['energy'] ?: 3 }}">

            {{-- Mood Selector --}}
            @php
                $moodFacePaths = [
                    1 => 'M9.172 14.828a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    2 => 'M10 14a2 2 0 014 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    3 => 'M8 12h8M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    4 => 'M14 14a2 2 0 01-4 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    5 => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                ];
            @endphp
            <div class="mb-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-2">{{ __('Mood') }}</p>
                <div class="flex gap-2">
                    @foreach($moodFacePaths as $score => $facePath)
                    <button type="button" onclick="selectMood({{ $score }})"
                        id="moodBtn{{ $score }}"
                        class="mood-emoji-btn flex-1 h-12 rounded-2xl border-2 transition-all flex items-center justify-center
                            {{ ($todayMood['score'] == $score) ? 'border-gray-900 bg-gray-900 scale-105 shadow-lg text-white' : 'border-gray-100 bg-gray-50 hover:border-gray-300 text-gray-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $facePath }}"/>
                        </svg>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Energy Selector --}}
            <div class="mb-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-2">{{ __('Energi') }}</p>
                <div class="flex gap-1.5">
                    @foreach(range(1, 5) as $e)
                    <button type="button" onclick="selectEnergy({{ $e }})"
                        id="energyBtn{{ $e }}"
                        class="energy-btn flex-1 h-9 rounded-xl border-2 font-bold text-xs transition-all
                            {{ ($todayMood['energy'] == $e) ? 'border-violet-500 bg-violet-500 text-white' : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-violet-300' }}">
                        {{ $e }}
                    </button>
                    @endforeach
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-[9px] text-gray-400">{{ __('Sangat Rendah') }}</span>
                    <span class="text-[9px] text-gray-400">{{ __('Sangat Tinggi') }}</span>
                </div>
            </div>

            {{-- Note --}}
            <div class="mb-4">
                <input type="text" name="note" value="{{ $todayMood['note'] }}"
                    placeholder="{{ __('Catatan singkat (opsional)...') }}"
                    class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300 transition-all">
            </div>

            <button type="submit" class="w-full py-3 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ $todayMood['score'] > 0 ? __('Perbarui Mood') : __('Simpan Mood') }}
            </button>
        </form>
    </div>

    {{-- ── Activity Status Grid ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base font-bold mb-4">{{ __('Aktivitas Hari Ini') }}</h3>
        <div class="grid grid-cols-2 gap-3">

            {{-- Sholat --}}
            @if($f('sholat'))
            <a href="{{ route('sholat') }}"
                class="p-4 rounded-2xl border transition-all hover:shadow-sm {{ $todayStats['wajib'] >= 5 ? 'bg-green-500 border-transparent' : 'bg-gray-50 border-gray-100' }}">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 {{ $todayStats['wajib'] >= 5 ? 'text-white' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    @if($todayStats['wajib'] >= 5)
                    <span class="text-[9px] font-bold text-white bg-white/20 px-2 py-0.5 rounded-full">✓ {{ __('Lengkap') }}</span>
                    @endif
                </div>
                <p class="font-bold text-xl {{ $todayStats['wajib'] >= 5 ? 'text-white' : '' }}">{{ $todayStats['wajib'] }}/5</p>
                <p class="text-[10px] font-bold {{ $todayStats['wajib'] >= 5 ? 'text-white/80' : 'text-gray-400' }}">{{ __('Sholat Wajib') }}</p>
            </a>
            @endif

            {{-- Gym --}}
            @if($f('gym'))
            <a href="{{ route('gym') }}"
                class="p-4 rounded-2xl border transition-all hover:shadow-sm {{ $gymToday['done'] ? 'bg-blue-500 border-transparent' : 'bg-gray-50 border-gray-100' }}">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 {{ $gymToday['done'] ? 'text-white' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    @if($gymToday['done'])
                    <span class="text-[9px] font-bold text-white bg-white/20 px-2 py-0.5 rounded-full">✓ Done</span>
                    @endif
                </div>
                <p class="font-bold text-xl {{ $gymToday['done'] ? 'text-white' : '' }}">{{ $gymToday['done'] ? '✓' : '—' }}</p>
                <p class="text-[10px] font-bold {{ $gymToday['done'] ? 'text-white/80' : 'text-gray-400' }}">Gym</p>
            </a>
            @endif

            {{-- Run --}}
            @if($f('run'))
            <a href="{{ route('run') }}"
                class="p-4 rounded-2xl border transition-all hover:shadow-sm {{ $runToday['done'] ? 'bg-emerald-500 border-transparent' : 'bg-gray-50 border-gray-100' }}">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 {{ $runToday['done'] ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    @if($runToday['done'])
                    <span class="text-[9px] font-bold text-white bg-white/20 px-2 py-0.5 rounded-full">{{ $runToday['distance'] }} km</span>
                    @endif
                </div>
                <p class="font-bold text-xl {{ $runToday['done'] ? 'text-white' : '' }}">{{ $runToday['done'] ? $runToday['distance'].' km' : '—' }}</p>
                <p class="text-[10px] font-bold {{ $runToday['done'] ? 'text-white/80' : 'text-gray-400' }}">Run</p>
            </a>
            @endif

            {{-- Intimasi --}}
            @if($f('intimasi'))
            <a href="{{ route('intimasi') }}"
                class="p-4 rounded-2xl border transition-all hover:shadow-sm {{ $intimacyToday > 0 ? 'bg-pink-500 border-transparent' : 'bg-gray-50 border-gray-100' }}">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 {{ $intimacyToday > 0 ? 'text-white' : 'text-pink-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <p class="font-bold text-xl {{ $intimacyToday > 0 ? 'text-white' : '' }}">{{ $intimacyToday }}×</p>
                <p class="text-[10px] font-bold {{ $intimacyToday > 0 ? 'text-white/80' : 'text-gray-400' }}">{{ __('Intimasi') }}</p>
            </a>
            @endif
        </div>
    </div>

    {{-- ── Daily Tasks ── --}}
    @if($f('tasks'))
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold">{{ __('Task Harian') }}</h3>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">{{ count(array_filter($dailyTodos, fn($t)=>$t['done'])) }}/{{ count($dailyTodos) }}</span>
                <a href="{{ route('tasks') }}" class="text-xs font-bold text-gray-400 hover:text-black transition-all">{{ __('Kelola') }} →</a>
            </div>
        </div>

        @if(count($dailyTodos) === 0)
        <div class="py-8 text-center">
            <p class="text-sm text-gray-400">{{ __('Belum ada task hari ini.') }}</p>
            <a href="{{ route('tasks') }}" class="mt-2 inline-block text-sm font-bold text-black hover:underline">{{ __('+ Tambah Task') }}</a>
        </div>
        @else
        <div class="space-y-2">
            @foreach($dailyTodos as $todo)
            @php $pri = $todo['priority'] ?? 'medium'; @endphp
            <form method="POST" action="{{ route('tasks.daily.toggle', $todo['id']) }}" class="group">
                @csrf
                <input type="hidden" name="date" value="{{ $today }}">
                <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-all text-left">
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all
                        {{ $todo['done'] ? 'border-gray-300 bg-gray-200' : 'border-gray-300' }}">
                        @if($todo['done'])
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>
                    <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $priDot[$pri] }}"></span>
                    <span class="flex-1 text-sm {{ $todo['done'] ? 'line-through text-gray-400' : 'text-gray-700' }}">{{ $todo['text'] }}</span>
                    @if(!$todo['done'])
                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full {{ $priBadge[$pri] }}">{{ $priLabel[$pri] }}</span>
                    @endif
                </button>
            </form>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- ── Reflection ── --}}
    @if($f('tasks'))
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base font-bold mb-4">{{ __('Refleksi Harian') }}</h3>
        <form method="POST" action="{{ route('tasks.reflection.update') }}" x-data="{ changed: false }" @submit="changed=false">
            @csrf
            <input type="hidden" name="date" value="{{ $today }}">
            <div class="space-y-3">
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-gray-400 mb-1.5">
                        <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Apa yang berjalan baik hari ini?') }}
                    </label>
                    <textarea name="good" rows="2" @input="changed=true"
                        placeholder="{{ __('Satu hal positif, kecil pun tidak apa...') }}"
                        class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-gray-300 resize-none transition-all">{{ $todayReflection['good'] ?? '' }}</textarea>
                </div>
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-gray-400 mb-1.5">
                        <svg class="w-3.5 h-3.5 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        {{ __('Apa yang bisa diperbaiki besok?') }}
                    </label>
                    <textarea name="improve" rows="2" @input="changed=true"
                        placeholder="{{ __('Satu kebiasaan / sikap yang ingin kamu naikkan...') }}"
                        class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-gray-300 resize-none transition-all">{{ $todayReflection['improve'] ?? '' }}</textarea>
                </div>
            </div>
            <button type="submit" x-show="changed" x-transition
                class="mt-3 w-full py-2.5 bg-black text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all">
                {{ __('Simpan Refleksi') }}
            </button>
        </form>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
// Mood selection
const moodScoreInput  = document.getElementById('moodScoreInput');
const energyScoreInput = document.getElementById('energyScoreInput');

function selectMood(score) {
    moodScoreInput.value = score;
    [1,2,3,4,5].forEach(s => {
        const btn = document.getElementById('moodBtn' + s);
        if (s === score) {
            btn.className = 'mood-emoji-btn flex-1 h-12 rounded-2xl border-2 transition-all flex items-center justify-center border-gray-900 bg-gray-900 scale-105 shadow-lg text-white';
        } else {
            btn.className = 'mood-emoji-btn flex-1 h-12 rounded-2xl border-2 transition-all flex items-center justify-center border-gray-100 bg-gray-50 hover:border-gray-300 text-gray-400';
        }
    });
}

function selectEnergy(e) {
    energyScoreInput.value = e;
    [1,2,3,4,5].forEach(n => {
        const btn = document.getElementById('energyBtn' + n);
        if (n === e) {
            btn.className = 'energy-btn flex-1 h-9 rounded-xl border-2 font-bold text-xs transition-all border-violet-500 bg-violet-500 text-white';
        } else {
            btn.className = 'energy-btn flex-1 h-9 rounded-xl border-2 font-bold text-xs transition-all border-gray-100 bg-gray-50 text-gray-400 hover:border-violet-300';
        }
    });
}

// Alpine-like simple directive for reflection form
document.querySelectorAll('[x-data]').forEach(el => {
    let changed = false;
    const btn = el.querySelector('[x-show]');
    if (btn) btn.style.display = 'none';

    el.querySelectorAll('textarea, input').forEach(input => {
        input.addEventListener('input', () => {
            if (!changed) { changed = true; if (btn) btn.style.display = ''; }
        });
    });
    el.addEventListener('submit', () => {
        changed = false; if (btn) btn.style.display = 'none';
    });
});
</script>
@endpush
