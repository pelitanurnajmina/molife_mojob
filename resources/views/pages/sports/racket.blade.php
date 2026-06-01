@extends('layouts.app')
@php $sportName = $profile['custom_sport_name'] ?: 'Tenis / Badminton'; @endphp
@section('title', $sportName)
@section('page-title', $sportName)
@section('breadcrumb', $sportName)

@section('content')
<div class="space-y-4 md:space-y-6">

    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold {{ $weekDone >= 3 ? 'text-violet-600' : 'text-gray-800' }}">{{ $weekDone }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ __('Sesi / Minggu') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $monthTotal }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ __('Sesi bulan ini') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold">{{ __('Log Hari Ini') }}</h3>
            @if($todayData['done'])
            <span class="text-xs font-bold bg-violet-50 text-violet-600 px-3 py-1 rounded-full">{{ __('Sudah dicatat') }}</span>
            @endif
        </div>

        <form method="POST" action="{{ route('racket.update') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $today }}">
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Jumlah Set') }}</label>
                <input type="number" name="sets" min="1" max="20"
                    value="{{ $todayData['sets'] ?: '' }}" placeholder="3"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-violet-400 transition-all">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                    {{ $todayData['done'] ? __('Perbarui') : __('Catat Sesi Hari Ini') }}
                </button>
                @if($todayData['done'])
                <form method="POST" action="{{ route('racket.reset') }}" class="flex-shrink-0">
                    @csrf
                    <input type="hidden" name="date" value="{{ $today }}">
                    <button type="submit" class="px-4 py-3 rounded-xl border-2 border-red-200 text-red-500 text-sm font-bold hover:bg-red-50 transition-all">{{ __('Hapus') }}</button>
                </form>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4 text-sm">{{ __('7 Hari Terakhir') }}</h3>
        <div class="flex items-center gap-2">
            @foreach($weekData as $i => $d)
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="{{ $d['done'] ? 'bg-violet-500' : 'bg-gray-100' }} w-full rounded-lg h-10 flex items-center justify-center">
                    @if($d['done'])
                    <span class="text-[9px] font-bold text-white">{{ $d['sets'] }}s</span>
                    @endif
                </div>
                <span class="text-[9px] text-gray-400">{{ date('D', strtotime($weekDates[$i]))[0] }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
