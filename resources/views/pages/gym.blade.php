@extends('layouts.app')
@section('title', 'Gym')
@section('page-title', 'Physical Performance')
@section('breadcrumb', 'Gym')

@section('content')
@php $idDays = ['Min','Sen','Sel','Rab','Kam','Jum','Sab']; @endphp
<div class="space-y-4 md:space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">

        {{-- Log Gym --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6">Log Gym Session</h3>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
                <form method="GET" action="{{ route('gym') }}">
                    <input type="date" name="date" value="{{ $date }}" max="{{ $today }}"
                        onchange="this.form.submit()"
                        class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-blue-400 transition-all">
                </form>
                @if($date !== $today)
                <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-full">Mengisi data tanggal lampau</span>
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
                <p class="font-bold text-lg mb-1">{{ $gymData['done'] ? 'Workout Selesai!' : 'Belum Gym Hari Ini' }}</p>
                @if($gymData['done'] && $gymData['calories'] > 0)
                <p class="text-sm text-gray-500">{{ $gymData['calories'] }} kalori terbakar</p>
                @endif
            </div>

            <form method="POST" action="{{ route('gym.toggle') }}" class="mb-4">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="calories" id="caloriesInput" value="{{ $gymData['calories'] ?? 0 }}">
                <button type="submit" class="w-full py-3 rounded-xl font-bold transition-all {{ $gymData['done'] ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                    {{ $gymData['done'] ? 'Batalkan Sesi' : 'Tandai Selesai Gym' }}
                </button>
            </form>

            @if($gymData['done'])
            <form method="POST" action="{{ route('gym.calories') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <div class="flex gap-2">
                    <input type="number" name="calories" value="{{ $gymData['calories'] }}" placeholder="Kalori terbakar"
                        class="flex-1 p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-400 text-sm">
                    <button type="submit" class="bg-blue-500 text-white px-4 rounded-xl font-bold hover:bg-blue-600 transition-all text-sm">Update</button>
                </div>
            </form>
            @endif
        </div>

        {{-- Status Target --}}
        <div class="bg-blue-50/30 border border-blue-50 rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-lg font-bold mb-6">Status Target</h3>
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-bold">Progress Mingguan</span>
                        <span class="text-sm font-bold">{{ $gymWeekly }}/4 Sesi</span>
                    </div>
                    <div class="w-full bg-white h-4 rounded-full overflow-hidden border border-gray-100 p-1">
                        <div class="bg-blue-500 h-full rounded-full transition-all" style="width:{{ min(($gymWeekly/4)*100,100) }}%"></div>
                    </div>
                    <p class="text-[10px] text-center text-gray-400 mt-2">
                        @if($gymWeekly >= 4) Target Tercapai! 🏆 @else Butuh {{ 4 - $gymWeekly }} sesi lagi. @endif
                    </p>
                </div>

                <div class="mt-4 p-4 bg-white rounded-2xl">
                    <p class="text-xs font-bold mb-2">Bulan Ini</p>
                    <div class="flex items-end gap-2">
                        <span class="text-2xl font-bold">{{ $gymMonthly }}</span>
                        <span class="text-xs text-gray-500 mb-1">sesi total</span>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-2xl">
                    <p class="text-xs font-bold mb-2">Kalori Minggu Ini</p>
                    <div class="flex items-end gap-2">
                        <span class="text-2xl font-bold">{{ $caloriesWeek }}</span>
                        <span class="text-xs text-gray-500 mb-1">kalori</span>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-2xl">
                    <p class="text-xs text-gray-500 leading-relaxed flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span>Target ideal adalah 4x seminggu untuk pemulihan otot yang optimal. Pastikan ada jeda istirahat yang cukup.</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Weekly activity --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-6">Aktivitas Minggu Ini</h3>
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
                <p class="text-[10px] font-bold {{ $wdGym['done'] ? 'text-blue-100' : 'text-gray-400' }} uppercase">{{ $idDays[$wdDow] }}</p>
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
    </div>
</div>
@endsection
