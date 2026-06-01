@extends('layouts.app')
@section('title', __('Bersepeda'))
@section('page-title', __('Bersepeda'))
@section('breadcrumb', __('Bersepeda'))

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- Stats row --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold {{ $weekDone >= 3 ? 'text-green-600' : 'text-gray-800' }}">{{ $weekDone }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ __('Sesi / Minggu') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ number_format($weekKm, 1) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">km {{ __('minggu ini') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($bestKm, 1) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">km {{ __('terbaik') }}</p>
        </div>
    </div>

    {{-- Log form --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold">{{ __('Log Hari Ini') }}</h3>
            @if($todayData['done'])
            <span class="text-xs font-bold bg-green-50 text-green-600 px-3 py-1 rounded-full">{{ __('Sudah dicatat') }}</span>
            @endif
        </div>

        <form method="POST" action="{{ route('cycling.update') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $today }}">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Jarak (km)') }}</label>
                    <input type="number" name="km" step="0.1" min="0" max="500"
                        value="{{ $todayData['km'] ?: '' }}" placeholder="0.0"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-green-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Durasi (menit)') }}</label>
                    <input type="number" name="duration" min="0" max="600"
                        value="{{ $todayData['duration'] ?: '' }}" placeholder="0"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-green-400 transition-all">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                    {{ $todayData['done'] ? __('Perbarui') : __('Catat Bersepeda Hari Ini') }}
                </button>
                @if($todayData['done'])
                <form method="POST" action="{{ route('cycling.reset') }}" class="flex-shrink-0">
                    @csrf
                    <input type="hidden" name="date" value="{{ $today }}">
                    <button type="submit" class="px-4 py-3 rounded-xl border-2 border-red-200 text-red-500 text-sm font-bold hover:bg-red-50 transition-all">{{ __('Hapus') }}</button>
                </form>
                @endif
            </div>
        </form>
    </div>

    {{-- Weekly chart --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4 text-sm">{{ __('7 Hari Terakhir') }}</h3>
        <div class="flex items-end gap-2">
            @foreach($weekData as $i => $d)
            @php $h = $d['km'] > 0 ? max(8, min(80, (int)($d['km'] / max(1, ...array_column($weekData, 'km')) * 80))) : 8; @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <span class="text-[9px] text-gray-400">{{ $d['km'] > 0 ? number_format($d['km'],1) : '' }}</span>
                <div class="{{ $d['done'] ? 'bg-green-500' : 'bg-gray-100' }} w-full rounded-lg transition-all" style="height:{{ $h }}px"></div>
                <span class="text-[9px] text-gray-400">{{ date('D', strtotime($weekDates[$i]))[0] }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
