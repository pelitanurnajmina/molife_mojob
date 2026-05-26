@extends('layouts.app')
@section('title', 'Sholat')
@section('page-title', 'Spiritual Tracking')
@section('breadcrumb', 'Sholat')

@section('content')
@php
    $sholatWajib  = ['Subuh','Dzuhur','Ashar','Maghrib','Isya'];
    $sholatSunnah = ['Tahajud','Dhuha','Qiyamul'];
    $wajibData    = $sholatData['wajib'] ?? [];
    $sunnahData   = $sholatData['sunnah'] ?? [];
@endphp

<div class="space-y-4 md:space-y-6">
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex flex-col lg:flex-row flex-wrap gap-6 md:gap-8">

            {{-- Left: Sholat Wajib --}}
            <div class="flex-1 w-full lg:min-w-[300px]">
                <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6">Sholat Wajib</h3>

                {{-- Date picker --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4 md:mb-6">
                    <form method="GET" action="{{ route('sholat') }}" class="flex items-center gap-2">
                        <input type="date" name="date" value="{{ $date }}" max="{{ $today }}"
                            onchange="this.form.submit()"
                            class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-green-400 transition-all">
                    </form>
                    @if($date !== $today)
                    <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-full self-start">Mengisi data tanggal lampau</span>
                    @endif
                </div>

                <div class="space-y-2">
                    @foreach($sholatWajib as $name)
                    @php
                        $detail  = $wajibData[$name] ?? null;
                        $done    = $detail['done'] ?? false;
                        $takbir  = $detail['takbirPertama'] ?? false;
                        $rawatib = $detail['rawatib'] ?? false;
                    @endphp
                    <div class="flex items-center justify-between p-3 md:p-4 bg-gray-50 rounded-xl md:rounded-2xl hover:bg-gray-100 transition-all">
                        <div class="flex items-center gap-4 flex-1">
                            <form method="POST" action="{{ route('sholat.toggle-wajib') }}">
                                @csrf
                                <input type="hidden" name="date" value="{{ $date }}">
                                <input type="hidden" name="name" value="{{ $name }}">
                                <button type="submit" class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-all {{ $done ? 'bg-green-500 text-white' : 'bg-white border-2 border-gray-200 hover:border-green-300' }}">
                                    @if($done)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            </form>
                            <span class="font-bold text-sm">{{ $name }}</span>
                        </div>
                        <div class="flex items-center gap-2 md:gap-3">
                            <form method="POST" action="{{ route('sholat.toggle-takbir') }}">
                                @csrf
                                <input type="hidden" name="date" value="{{ $date }}">
                                <input type="hidden" name="name" value="{{ $name }}">
                                <button type="submit" @if(!$done) disabled @endif
                                    class="px-3 md:px-4 py-2 rounded-lg md:rounded-xl text-[10px] md:text-xs font-bold transition-all {{ !$done ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : ($takbir ? 'bg-yellow-400 text-black' : 'bg-white border-2 border-gray-200 hover:border-yellow-300') }}">
                                    Takbir
                                </button>
                            </form>
                            <form method="POST" action="{{ route('sholat.toggle-rawatib') }}">
                                @csrf
                                <input type="hidden" name="date" value="{{ $date }}">
                                <input type="hidden" name="name" value="{{ $name }}">
                                <button type="submit" @if(!$done) disabled @endif
                                    class="px-3 md:px-4 py-2 rounded-lg md:rounded-xl text-[10px] md:text-xs font-bold transition-all {{ !$done ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : ($rawatib ? 'bg-green-400 text-white' : 'bg-white border-2 border-gray-200 hover:border-green-300') }}">
                                    Rawatib
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Sholat Sunnah + Stats --}}
            <div class="w-full lg:w-72">
                <div class="bg-green-50/50 rounded-2xl p-4 md:p-6 mb-4">
                    <h3 class="font-bold text-sm mb-4">Sholat Sunnah</h3>
                    <div class="space-y-2">
                        @foreach($sholatSunnah as $name)
                        @php $doneSunnah = in_array($name, $sunnahData); @endphp
                        <form method="POST" action="{{ route('sholat.toggle-sunnah') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="name" value="{{ $name }}">
                            <button type="submit" class="w-full flex items-center justify-between p-3 rounded-xl transition-all {{ $doneSunnah ? 'bg-green-500 text-white' : 'bg-white hover:bg-green-50 text-gray-700' }}">
                                <span class="font-medium text-sm">{{ $name }}</span>
                                @if($doneSunnah)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-4">
                    <h3 class="font-bold text-sm mb-4">Statistik Hari Ini</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Wajib</span>
                            <span class="text-xs font-bold text-green-600">{{ $sholatStats['wajib'] }}/5</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Takbir Pertama</span>
                            <span class="text-xs font-bold text-yellow-600">{{ $sholatStats['takbir'] }}/5</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Rawatib</span>
                            <span class="text-xs font-bold text-green-600">{{ $sholatStats['rawatib'] }}/5</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Sunnah</span>
                            <span class="text-xs font-bold text-blue-600">{{ $sholatStats['sunnah'] }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-xs font-bold">Streak Sholat</span>
                            <span class="text-sm font-bold">{{ $streak }} hari 🔥</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold">Streak Takbir</span>
                            <span class="text-sm font-bold">{{ $takbirStreak }} hari ⭐</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Month calendar --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-6">Kalender Sholat Bulan Ini</h3>
        @php
            $firstDow = (new DateTime($monthDates[0]))->format('N'); // 1=Mon
            $offset   = $firstDow - 1;
        @endphp
        <div class="grid grid-cols-7 gap-1.5 md:gap-2 mb-2">
            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d)
            <div class="text-center text-[10px] font-bold text-gray-400 pb-1">{{ $d }}</div>
            @endforeach
            @for($i = 0; $i < $offset; $i++)<div></div>@endfor
            @foreach($monthDates as $md)
            @php
                $s = \App\Models\UserStorage::fromSession()->getSholatStats($md);
                $day = (int)explode('-', $md)[2];
                $isToday = $md === $today;
                $bg = $s['wajib'] >= 5 ? 'bg-green-500 text-white' : ($s['wajib'] > 0 ? 'bg-yellow-400' : 'bg-gray-100');
            @endphp
            <a href="{{ route('sholat', ['date' => $md]) }}"
               class="aspect-square rounded-lg flex flex-col items-center justify-center text-xs font-bold transition-all {{ $bg }} {{ $isToday ? 'ring-2 ring-green-400 ring-offset-1' : '' }}">
                <span class="text-[10px]">{{ $day }}</span>
                @if($s['wajib'] > 0 && $s['wajib'] < 5)
                <span class="text-[8px]">{{ $s['wajib'] }}/5</span>
                @endif
            </a>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-gray-100 rounded"></div><span>Kosong</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-yellow-400 rounded"></div><span>Sebagian</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-green-500 rounded"></div><span>Lengkap 5 Wajib</span></div>
        </div>
    </div>
</div>
@endsection
