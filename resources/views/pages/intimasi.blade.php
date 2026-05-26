@extends('layouts.app')
@section('title', 'Intimasi')
@section('page-title', 'Intimacy Tracking')
@section('breadcrumb', 'Intimasi')

@section('content')
<div class="space-y-4 md:space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">

        {{-- Tracking --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6">Tracking Intimasi</h3>
            <div class="space-y-4 md:space-y-6">
                <div>
                    <label class="text-xs font-bold text-gray-400 block mb-2 uppercase">Pilih Tanggal</label>
                    <form method="GET" action="{{ route('intimasi') }}">
                        <input type="date" name="date" value="{{ $date }}" max="{{ $today }}"
                            onchange="this.form.submit()"
                            class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-pink-400 transition-all">
                    </form>
                </div>

                <div class="text-center p-6 md:p-8 bg-pink-50 rounded-2xl md:rounded-3xl">
                    <p class="text-xs md:text-sm font-bold text-pink-700 mb-3 md:mb-4">
                        @if($date === $today) Jumlah Hari Ini
                        @else {{ (new DateTime($date))->format('j F') }} @endif
                    </p>
                    <div class="text-5xl md:text-6xl font-bold mb-4 md:mb-6">{{ $count }}</div>
                    <div class="flex gap-3 justify-center">
                        <form method="POST" action="{{ route('intimasi.change') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="delta" value="-1">
                            <button type="submit" class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-2xl font-bold hover:bg-gray-50 transition-all border-2 border-pink-200">−</button>
                        </form>
                        <form method="POST" action="{{ route('intimasi.change') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="delta" value="1">
                            <button type="submit" class="w-14 h-14 bg-pink-500 text-white rounded-2xl flex items-center justify-center text-2xl font-bold hover:bg-pink-600 transition-all">+</button>
                        </form>
                    </div>
                </div>

                <div class="p-4 md:p-6 bg-gray-50 rounded-2xl">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs md:text-sm font-bold">Bulan Ini (Total)</span>
                        <span class="text-xl md:text-2xl font-bold">{{ $monthlyCount }}x</span>
                    </div>
                    <div class="w-full bg-white h-2 rounded-full">
                        <div class="bg-pink-500 h-2 rounded-full transition-all" style="width:{{ min(($monthlyCount/20)*100,100) }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2">Hari ini: {{ $todayCount }}x</p>
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6">Kalender Bulan Ini</h3>
            @php
                $firstDow = (new DateTime($monthDates[0]))->format('N');
                $offset   = $firstDow - 1;
            @endphp
            <div class="grid grid-cols-7 gap-1.5 md:gap-2 mb-4 md:mb-6">
                @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d)
                <div class="text-center text-[10px] font-bold text-gray-400 pb-1">{{ $d }}</div>
                @endforeach
                @for($i=0;$i<$offset;$i++)<div></div>@endfor
                @foreach($monthDates as $md)
                @php
                    $cnt = $intimacyAll[$md] ?? 0;
                    $day = (int)explode('-',$md)[2];
                    $isToday = $md === $today;
                @endphp
                <div class="aspect-square rounded-lg flex flex-col items-center justify-center text-xs font-bold transition-all {{ $cnt > 0 ? 'bg-pink-500 text-white' : 'bg-gray-100' }} {{ $isToday ? 'border-2 border-pink-400' : '' }}">
                    <div class="text-[10px]">{{ $day }}</div>
                    @if($cnt > 0)<div class="text-[8px] mt-0.5">{{ $cnt }}x</div>@endif
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <div class="flex items-center gap-2"><div class="w-4 h-4 bg-gray-100 rounded"></div><span>Tidak ada</span></div>
                <div class="flex items-center gap-2"><div class="w-4 h-4 bg-pink-500 rounded"></div><span>Ada aktivitas</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
