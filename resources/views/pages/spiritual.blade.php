@extends('layouts.app')
@php
    $religionLabel = [
        'kristen' => 'Ibadah Harian',
        'hindu'   => 'Sembahyang',
        'buddha'  => 'Sembahyang',
        'lainnya' => 'Spiritual',
    ][$profile['religion'] ?? 'lainnya'] ?? 'Spiritual';

    $colorMap = [
        'orange' => ['bg'=>'bg-orange-500',  'light'=>'bg-orange-50 text-orange-600',  'ring'=>'ring-orange-400'],
        'indigo' => ['bg'=>'bg-indigo-500',  'light'=>'bg-indigo-50 text-indigo-600',  'ring'=>'ring-indigo-400'],
        'blue'   => ['bg'=>'bg-blue-500',    'light'=>'bg-blue-50 text-blue-600',      'ring'=>'ring-blue-400'],
        'purple' => ['bg'=>'bg-purple-500',  'light'=>'bg-purple-50 text-purple-600',  'ring'=>'ring-purple-400'],
        'violet' => ['bg'=>'bg-violet-500',  'light'=>'bg-violet-50 text-violet-600',  'ring'=>'ring-violet-400'],
        'teal'   => ['bg'=>'bg-teal-500',    'light'=>'bg-teal-50 text-teal-600',      'ring'=>'ring-teal-400'],
        'green'  => ['bg'=>'bg-green-500',   'light'=>'bg-green-50 text-green-600',    'ring'=>'ring-green-400'],
    ];
    $doneTodayCount = count(array_filter(array_keys($practices), fn($t) => !empty($todayData[$t])));
    $totalCount     = count($practices);
@endphp
@section('title', $religionLabel)
@section('page-title', $religionLabel)
@section('breadcrumb', $religionLabel)

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- ── Today card ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold">{{ date('l, j F Y') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Hari ini') }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">{{ $doneTodayCount }}<span class="text-gray-300">/{{ $totalCount }}</span></p>
                <p class="text-[10px] text-gray-400">{{ __('selesai') }}</p>
            </div>
        </div>

        <div class="space-y-2.5">
            @foreach($practices as $type => $p)
            @php
                $done   = !empty($todayData[$type]);
                $colors = $colorMap[$p['color']] ?? $colorMap['teal'];
            @endphp
            <form method="POST" action="{{ route('spiritual.toggle') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $today }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <button type="submit" class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all {{ $done ? 'bg-gray-900 text-white' : 'bg-gray-50 hover:bg-gray-100' }}">
                    <div class="w-11 h-11 {{ $done ? 'bg-white/10' : $colors['light'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $p['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="font-bold text-sm flex-1 text-left">{{ $p['label'] }}</span>
                    @if($done)
                    <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                    @endif
                </button>
            </form>
            @endforeach
        </div>
    </div>

    {{-- ── Streak + Week ── --}}
    <div class="grid grid-cols-2 gap-3 md:gap-4">
        <div class="bg-white rounded-2xl p-4 md:p-6 text-center">
            <p class="text-3xl font-bold">{{ $streak }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('Hari Streak') }}</p>
            <div class="flex items-center justify-center gap-1 mt-2">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                </svg>
                <span class="text-xs font-bold text-orange-400">streak</span>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-6">
            <p class="text-xs font-bold text-gray-400 mb-3">{{ __('7 Hari Terakhir') }}</p>
            <div class="flex items-end gap-1.5 justify-between">
                @foreach($week as $w)
                @php
                    $pct = $totalCount > 0 ? ($w['done'] / $totalCount) : 0;
                    $bgBar = $pct >= 1 ? 'bg-green-500' : ($pct > 0 ? 'bg-yellow-400' : 'bg-gray-100');
                @endphp
                <div class="flex flex-col items-center gap-1 flex-1">
                    <div class="w-full rounded-md" style="height: 36px; display:flex; align-items:flex-end;">
                        <div class="{{ $bgBar }} w-full rounded-md transition-all" style="height: {{ max(4, (int)($pct * 36)) }}px"></div>
                    </div>
                    <span class="text-[9px] text-gray-400">{{ date('D', strtotime($w['date']))[0] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Encouragement ── --}}
    @if($doneTodayCount === $totalCount && $totalCount > 0)
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-sm text-green-800">{{ __('Sempurna hari ini!') }}</p>
            <p class="text-xs text-green-600">{{ __('Semua praktik spiritual hari ini sudah selesai.') }}</p>
        </div>
    </div>
    @elseif($doneTodayCount === 0)
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-sm text-blue-800">{{ __('Belum ada yang dimulai') }}</p>
            <p class="text-xs text-blue-600">{{ __('Tap pada item di atas untuk mulai mencatat.') }}</p>
        </div>
    </div>
    @endif

</div>
@endsection
