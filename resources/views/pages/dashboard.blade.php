@extends('layouts.app')
@section('title', 'Home')
@section('page-title', $greeting . ', ' . (\App\Models\UserStorage::fromSession()->getProfile()['display_name'] ?: auth()->user()->username ?? 'User') . '!')
@section('breadcrumb', 'Home')

@section('content')
@php
    $moodLabel     = ['', __('Buruk'), __('Kurang'), __('Biasa'), __('Baik'), __('Luar Biasa')];
    $moodBg        = ['', 'bg-red-50 text-red-600', 'bg-orange-50 text-orange-600', 'bg-yellow-50 text-yellow-700', 'bg-green-50 text-green-600', 'bg-emerald-50 text-emerald-600'];
    $moodFacePaths = [
        1 => 'M9.172 14.828a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        2 => 'M10 14a2 2 0 014 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        3 => 'M8 12h8M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        4 => 'M14 14a2 2 0 01-4 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        5 => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
    $priBadge = ['high' => 'bg-red-50 text-red-500', 'medium' => 'bg-orange-50 text-orange-500', 'low' => 'bg-gray-100 text-gray-400'];
    $doneTasks   = count(array_filter($dailyTodos, fn($t) => $t['done']));
    $totalTasks  = count($dailyTodos);
    $visibleTodos = array_slice($dailyTodos, 0, 5);
@endphp

<div class="space-y-4 md:space-y-6">

    {{-- ── Today Snapshot Cards ── --}}
    @php
        $_featsD = \App\Models\UserStorage::fromSession()->getFeatures();
        // Count how many snapshot cards will render so the grid fits exactly
        $_cardCount = 1; // Streak always shown
        if (($_featsD['sholat'] ?? true) || ($_featsD['spiritual'] ?? false)) $_cardCount++;
        if ($_featsD['gym'] ?? true) $_cardCount++;
        if ($_featsD['run'] ?? true) $_cardCount++;
        $_gridCols = [
            1 => 'grid-cols-1',
            2 => 'grid-cols-2',
            3 => 'grid-cols-2 sm:grid-cols-3',
            4 => 'grid-cols-2 sm:grid-cols-4',
            5 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-5',
            6 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-6',
        ][$_cardCount] ?? 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4';
    @endphp
    <div class="grid {{ $_gridCols }} gap-3">
        {{-- Spiritual --}}
        @if($_featsD['sholat'] ?? true)
        <a href="{{ route('sholat') }}" class="bg-white rounded-2xl p-4 flex flex-col gap-2 hover:shadow-sm transition-all border border-gray-50">
            <div class="w-9 h-9 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $todayStats['wajib'] }}<span class="text-base text-gray-400">/5</span></p>
                <p class="text-[10px] text-gray-400 font-bold">{{ __('Sholat Wajib') }}</p>
            </div>
            <div class="flex gap-0.5">
                @for($i = 0; $i < 5; $i++)
                <div class="flex-1 h-1.5 rounded-full {{ $i < $todayStats['wajib'] ? 'bg-green-500' : 'bg-gray-100' }}"></div>
                @endfor
            </div>
        </a>
        @elseif($_featsD['spiritual'] ?? false)
        @php
            $spirLabel = match($religion) {
                'kristen'        => __('Ibadah'),
                'hindu','buddha' => __('Sembahyang'),
                default          => __('Spiritual'),
            };
            $allSpiritualDone = $spiritualPracticeTotal > 0 && $spiritualDoneToday >= $spiritualPracticeTotal;
        @endphp
        <a href="{{ route('spiritual') }}" class="bg-white rounded-2xl p-4 flex flex-col gap-2 hover:shadow-sm transition-all border border-gray-50">
            <div class="w-9 h-9 {{ $allSpiritualDone ? 'bg-violet-500 text-white' : 'bg-violet-50 text-violet-600' }} rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $spiritualDoneToday }}<span class="text-base text-gray-400">/{{ $spiritualPracticeTotal }}</span></p>
                <p class="text-[10px] text-gray-400 font-bold">{{ $spirLabel }} {{ __('Hari Ini') }}</p>
            </div>
            <div class="flex gap-0.5">
                @for($i = 0; $i < $spiritualPracticeTotal; $i++)
                <div class="flex-1 h-1.5 rounded-full {{ $i < $spiritualDoneToday ? 'bg-violet-500' : 'bg-gray-100' }}"></div>
                @endfor
            </div>
        </a>
        @endif

        {{-- Gym --}}
        @if($_featsD['gym'] ?? true)
        <a href="{{ route('gym') }}" class="bg-white rounded-2xl p-4 flex flex-col gap-2 hover:shadow-sm transition-all border border-gray-50">
            <div class="w-9 h-9 {{ $isGymToday ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-600' }} rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $gymWeekly }}<span class="text-base text-gray-400">/4</span></p>
                <p class="text-[10px] text-gray-400 font-bold">{{ __('Gym Minggu Ini') }}</p>
            </div>
            <div class="flex gap-0.5 mb-0.5">
                @for($i = 0; $i < 4; $i++)
                <div class="flex-1 h-1.5 rounded-full {{ $i < $gymWeekly ? 'bg-blue-500' : 'bg-gray-100' }}"></div>
                @endfor
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full w-fit {{ $isGymToday ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $isGymToday ? '✓ Done' : ($gymWeekly === 0 ? __('Belum mulai') : __('Rest Day')) }}
            </span>
        </a>
        @endif

        {{-- Run --}}
        @if($_featsD['run'] ?? true)
        <a href="{{ route('run') }}" class="bg-white rounded-2xl p-4 flex flex-col gap-2 hover:shadow-sm transition-all border border-gray-50">
            <div class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $runWeeklyCount }}<span class="text-base text-gray-400">×</span></p>
                <p class="text-[10px] text-gray-400 font-bold">{{ __('Lari Minggu Ini') }}</p>
            </div>
            @if($runMonthlyDist > 0)
            <p class="text-[10px] text-emerald-600 font-bold">{{ number_format($runMonthlyDist, 1) }} km {{ __('bulan ini') }}</p>
            @else
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full w-fit bg-gray-100 text-gray-400">
                {{ __('Belum ada lari') }}
            </span>
            @endif
        </a>
        @endif

        {{-- Streak --}}
        <a href="{{ route('statistik') }}" class="bg-white rounded-2xl p-4 flex flex-col gap-2 hover:shadow-sm transition-all border border-gray-50">
            <div class="w-9 h-9 {{ $streak > 0 ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-400' }} rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold {{ $streak === 0 ? 'text-gray-300' : '' }}">{{ $streak }}</p>
                <p class="text-[10px] text-gray-400 font-bold">{{ __('Streak') }}</p>
            </div>
            <p class="text-[10px] font-bold {{ $streak > 0 ? 'text-orange-500' : 'text-gray-300' }}">
                {{ $streak > 0 ? __('hari berturut-turut') : __('Mulai hari ini!') }}
            </p>
        </a>
    </div>

    {{-- ── Life Score + Mood Selector ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex flex-col lg:flex-row items-stretch gap-6 md:gap-8">

            {{-- Life Score Ring + Score Bars --}}
            <div class="flex flex-col sm:flex-row items-center gap-6 flex-1">
                <div class="relative flex-shrink-0">
                    <svg class="w-32 h-32 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#f3f4f6" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9155" fill="none"
                            stroke="{{ $lifeScore['overall'] >= 80 ? '#10b981' : ($lifeScore['overall'] >= 50 ? '#f59e0b' : '#ef4444') }}"
                            stroke-width="3"
                            stroke-dasharray="{{ $lifeScore['overall'] }} 100"
                            stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold leading-none">{{ $lifeScore['overall'] }}</span>
                        <span class="text-[10px] text-gray-400 font-bold mt-0.5">Life Score</span>
                    </div>
                </div>

                <div class="flex-1 w-full space-y-3">
                    @php
                        $scoreDims = [
                            ['label' => __('Spiritual'),     'key' => 'spiritual',    'color' => 'bg-green-500',  'ic' => 'text-green-600',  'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['label' => __('Kesehatan'),     'key' => 'health',       'color' => 'bg-blue-500',   'ic' => 'text-blue-600',   'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            ['label' => __('Mental'),        'key' => 'mental',       'color' => 'bg-violet-500', 'ic' => 'text-violet-600', 'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['label' => __('Produktivitas'), 'key' => 'productivity', 'color' => 'bg-orange-400', 'ic' => 'text-orange-500', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ];
                    @endphp
                    @foreach($scoreDims as $dim)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="flex items-center gap-1.5 text-xs font-bold text-gray-600">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 {{ $dim['ic'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $dim['icon'] }}"/>
                                </svg>
                                {{ $dim['label'] }}
                            </span>
                            <span class="text-xs font-bold text-gray-400">{{ $lifeScore[$dim['key']] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="{{ $dim['color'] }} h-full rounded-full transition-all" style="width:{{ $lifeScore[$dim['key']] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Divider --}}
            <div class="hidden lg:block w-px bg-gray-100 self-stretch"></div>
            <div class="lg:hidden h-px bg-gray-100 w-full"></div>

            {{-- Mood Selector --}}
            <div class="lg:w-52 flex-shrink-0">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">{{ __('Mood Hari Ini') }}</p>
                <form method="POST" action="{{ route('mental.mood') }}" id="dashMoodForm">
                    @csrf
                    <input type="hidden" name="date" value="{{ $today }}">
                    <input type="hidden" name="score"  id="dashMoodScore"  value="{{ $todayMood['score'] ?: 3 }}">
                    <input type="hidden" name="energy" id="dashMoodEnergy" value="{{ $todayMood['energy'] ?: 3 }}">
                    <div class="flex gap-1.5 mb-3">
                        @foreach($moodFacePaths as $s => $facePath)
                        <button type="button" onclick="dashSetMood({{ $s }})" id="dashMoodBtn{{ $s }}"
                            class="flex-1 py-2.5 rounded-xl border-2 transition-all flex items-center justify-center
                            {{ $todayMood['score'] == $s ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-gray-300' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $facePath }}"/>
                            </svg>
                        </button>
                        @endforeach
                    </div>
                    @if($todayMood['score'] > 0)
                    <p class="text-center text-xs font-bold {{ str_replace('bg-', 'text-', explode(' ', $moodBg[$todayMood['score']])[1]) }} mb-3">
                        {{ $moodLabel[$todayMood['score']] }}
                    </p>
                    @endif
                    <button type="submit" class="w-full py-2.5 bg-black text-white rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                        {{ $todayMood['score'] > 0 ? __('Update Mood') : __('Simpan Mood') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Daily Tasks ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold">{{ __('Tasks Hari Ini') }}</h3>
                @if($totalTasks > 0)
                <p class="text-xs text-gray-400 mt-0.5">{{ $doneTasks }}/{{ $totalTasks }} {{ __('selesai') }}</p>
                @endif
            </div>
            <a href="{{ route('tasks') }}" class="text-xs text-gray-400 hover:text-black font-bold transition-all flex items-center gap-1">
                {{ __('Kelola') }}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($totalTasks === 0)
        <div class="text-center py-6">
            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <p class="text-sm text-gray-400 font-medium">{{ __('Belum ada task hari ini') }}</p>
            <a href="{{ route('tasks') }}" class="text-xs font-bold text-black hover:underline mt-1 inline-block">{{ __('+ Tambah task') }}</a>
        </div>
        @else
        <div class="space-y-2">
            @foreach($visibleTodos as $todo)
            <form method="POST" action="{{ route('tasks.daily.toggle', $todo['id']) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-all group">
                @csrf
                <button type="submit" class="w-5 h-5 rounded-md border-2 flex items-center justify-center flex-shrink-0 transition-all
                    {{ $todo['done'] ? 'bg-black border-black text-white' : 'border-gray-300 group-hover:border-gray-400' }}">
                    @if($todo['done'])
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </button>
                <span class="flex-1 text-sm font-medium {{ $todo['done'] ? 'line-through text-gray-300' : 'text-gray-800' }}">{{ $todo['text'] }}</span>
                @php $pri = $todo['priority'] ?? 'medium'; @endphp
                @if(!$todo['done'] && $pri === 'high')
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-red-50 text-red-500 flex-shrink-0">!</span>
                @endif
            </form>
            @endforeach

            @if($totalTasks > 5)
            <a href="{{ route('tasks') }}" class="block text-center text-xs text-gray-400 hover:text-black font-bold py-2 transition-all">
                +{{ $totalTasks - 5 }} task lainnya
            </a>
            @endif
        </div>

        {{-- Progress bar --}}
        @if($totalTasks > 0)
        <div class="mt-4 w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
            <div class="bg-black h-full rounded-full transition-all" style="width:{{ round(($doneTasks / $totalTasks) * 100) }}%"></div>
        </div>
        @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function dashSetMood(s) {
    document.getElementById('dashMoodScore').value = s;
    [1,2,3,4,5].forEach(function(n) {
        var btn = document.getElementById('dashMoodBtn' + n);
        btn.className = 'flex-1 py-2.5 rounded-xl border-2 transition-all flex items-center justify-center ' +
            (n === s
                ? 'border-gray-900 bg-gray-900 text-white'
                : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-gray-300');
    });
}
</script>
@endpush
