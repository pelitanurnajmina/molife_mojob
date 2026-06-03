@extends('layouts.app')
@section('title', 'Gym')
@section('page-title', 'Physical Performance')
@section('breadcrumb', 'Gym')

@section('content')
@php
$days = [__('Min'),__('Sen'),__('Sel'),__('Rab'),__('Kam'),__('Jum'),__('Sab')];
@endphp
<div class="space-y-4 md:space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">

        {{-- Log Gym --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6">Log Gym Session</h3>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
                <style>#gymDateInput::-webkit-calendar-picker-indicator{opacity:0;cursor:pointer;position:absolute;right:0;top:0;width:2.5rem;height:100%}</style>
                <form method="GET" action="{{ route('gym') }}" style="display:inline-flex">
                    <div class="relative">
                        <input id="gymDateInput" type="date" name="date" value="{{ $date }}" max="{{ $today }}"
                            onchange="this.form.submit()"
                            class="px-3 py-2 pr-9 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-blue-400 transition-all">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </form>
                @if($date !== $today)
                <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-full">{{ __('Mengisi data tanggal lampau') }}</span>
                @endif
            </div>

            <div class="text-center p-6 md:p-8 {{ $gymData['done'] ? 'bg-blue-50' : 'bg-gray-50' }} rounded-2xl md:rounded-3xl mb-6">
                <div class="flex items-center justify-center mb-3">
                    @if($gymData['done'])
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @else
                    <div class="w-14 h-14 bg-gray-200 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <p class="font-bold text-lg mb-1">{{ $gymData['done'] ? __('Workout Selesai!') : __('Belum Gym Hari Ini') }}</p>
                @if($gymData['done'] && $gymData['calories'] > 0)
                <p class="text-sm text-gray-500">{{ $gymData['calories'] }} {{ __('kalori terbakar') }}</p>
                @endif
            </div>

            <form method="POST" action="{{ route('gym.toggle') }}" class="mb-4">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="calories" id="caloriesInput" value="{{ $gymData['calories'] ?? 0 }}">
                <button type="submit" class="w-full py-3 rounded-xl font-bold transition-all {{ $gymData['done'] ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                    {{ $gymData['done'] ? __('Batalkan Sesi') : __('Tandai Selesai Gym') }}
                </button>
            </form>

            @if($gymData['done'])
            <form method="POST" action="{{ route('gym.calories') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <div class="flex gap-2">
                    <input type="number" name="calories" value="{{ $gymData['calories'] }}" placeholder="{{ __('Kalori terbakar') }}"
                        class="flex-1 p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-400 text-sm">
                    <button type="submit" class="bg-blue-500 text-white px-4 rounded-xl font-bold hover:bg-blue-600 transition-all text-sm">{{ __('Update') }}</button>
                </div>
            </form>
            @endif
        </div>

        {{-- Status Target --}}
        <div class="bg-blue-50/30 border border-blue-50 rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-lg font-bold mb-6">{{ __('Status Target') }}</h3>
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-bold">{{ __('Progress Mingguan') }}</span>
                        <span class="text-sm font-bold">{{ $gymWeekly }}/4 {{ __('Sesi') }}</span>
                    </div>
                    <div class="w-full bg-white h-4 rounded-full overflow-hidden border border-gray-100 p-1">
                        <div class="bg-blue-500 h-full rounded-full transition-all" style="width:{{ min(($gymWeekly/4)*100,100) }}%"></div>
                    </div>
                    <p class="text-[10px] text-center text-gray-400 mt-2">
                        @if($gymWeekly >= 4)
                        <span class="inline-flex items-center gap-1 text-green-600 font-bold">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            {{ __('Target Tercapai!') }}
                        </span>
                        @else {{ __('Butuh :n sesi lagi.', ['n' => 4 - $gymWeekly]) }} @endif
                    </p>
                </div>

                <div class="mt-4 p-4 bg-white rounded-2xl">
                    <p class="text-xs font-bold mb-2">{{ __('Bulan Ini') }}</p>
                    <div class="flex items-end gap-2">
                        <span class="text-2xl font-bold">{{ $gymMonthly }}</span>
                        <span class="text-xs text-gray-500 mb-1">{{ __('sesi total') }}</span>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-2xl">
                    <p class="text-xs font-bold mb-2">{{ __('Kalori Minggu Ini') }}</p>
                    <div class="flex items-end gap-2">
                        <span class="text-2xl font-bold">{{ $caloriesWeek }}</span>
                        <span class="text-xs text-gray-500 mb-1">{{ __('kalori') }}</span>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-2xl">
                    <p class="text-xs text-gray-500 leading-relaxed flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span>{{ __('Target ideal adalah 4x seminggu untuk pemulihan otot yang optimal. Pastikan ada jeda istirahat yang cukup.') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity: weekly grid / multi-month strip --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-6 gap-3">
            <h3 class="font-bold">{{ $months === null ? __('Aktivitas Minggu Ini') : $rangeTitle }}</h3>
            <x-range-filter :range="$range" route="gym" />
        </div>

        @if($months === null)
        {{-- ── Weekly grid (default) ── --}}
        <div class="grid grid-cols-7 gap-2">
            @foreach($weekDates as $wd)
            @php
                $wdGym = $gymDataAll[$wd] ?? ['done'=>false,'calories'=>0];
                $wdDow = (new DateTime($wd))->format('w');
                $wdDay = (int)explode('-',$wd)[2];
                $isToday = $wd === $today;
            @endphp
            <a href="{{ route('gym', ['date' => $wd]) }}"
               class="flex flex-col items-center p-2 md:p-3 rounded-2xl transition-all {{ $isToday ? 'ring-2 ring-blue-400' : '' }} {{ $wdGym['done'] ? 'bg-blue-500 text-white' : 'bg-gray-50 hover:bg-blue-50' }}">
                <p class="text-[10px] font-bold {{ $wdGym['done'] ? 'text-blue-100' : 'text-gray-400' }} uppercase">{{ $days[$wdDow] }}</p>
                <p class="text-xl font-bold leading-none mt-1">{{ $wdDay }}</p>
                @if($wdGym['done'])
                <svg class="w-4 h-4 mt-1 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @endif
                @if($wdGym['calories'])
                <p class="text-[9px] mt-1 {{ $wdGym['done'] ? 'text-blue-100' : 'text-gray-400' }}">{{ $wdGym['calories'] }} cal</p>
                @endif
            </a>
            @endforeach
        </div>
        @else
        {{-- ── Multi-month strip ── --}}
        <div class="mb-6">
            <p class="text-2xl font-bold text-blue-600">{{ $rangeActive }}</p>
            <p class="text-[10px] text-gray-400 font-bold">{{ __('Total sesi gym') }}</p>
        </div>
        <x-activity-strip :rows="$stripRows" color="blue"
            :legendOff="__('Rest')" :legendOn="__('Gym')" />
        @endif
    </div>
</div>
@endsection
