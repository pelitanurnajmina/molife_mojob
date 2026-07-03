@extends('layouts.app')
@section('title', __('Sholat'))
@section('page-title', 'Spiritual Tracking')
@section('breadcrumb', __('Sholat'))

@section('content')
@php
    $sholatWajib  = ['Subuh','Dzuhur','Ashar','Maghrib','Isya'];
    $sholatSunnah = ['Tahajud','Dhuha','Qiyamul'];
    $wajibData    = $sholatData['wajib'] ?? [];
    $sunnahData   = $sholatData['sunnah'] ?? [];
    $dayHeaders   = [__('Sen'),__('Sel'),__('Rab'),__('Kam'),__('Jum'),__('Sab'),__('Min')];
    $pq           = \App\Support\Profile::prayerQuality();
@endphp

<div class="space-y-4 md:space-y-6">

    {{-- ── Jadwal Sholat hari ini (otomatis by lokasi) ── --}}
    @if(!empty($prayerTimes))
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl md:rounded-3xl p-5 md:p-7 text-white relative overflow-hidden">
        <div class="flex items-center justify-between mb-4 relative">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-white/60">{{ __('Jadwal Sholat') }} · {{ date('j M Y', strtotime($date)) }}</p>
                <p class="text-sm font-bold mt-0.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $prayerCityLabel }}
                </p>
            </div>
            <a href="{{ route('goals') }}" class="text-[10px] font-bold text-white/70 hover:text-white bg-white/10 px-3 py-1.5 rounded-full transition-all">{{ __('Ubah') }}</a>
        </div>
        <div class="grid grid-cols-5 gap-2 relative">
            @foreach($prayerTimes as $name => $t)
            @php $isNext = isset($nextPrayer) && $nextPrayer === $name; @endphp
            <div class="rounded-xl py-3 px-1 text-center transition-all {{ $isNext ? 'bg-white text-emerald-700 shadow-lg' : 'bg-white/10' }}">
                <p class="text-[10px] font-bold {{ $isNext ? 'text-emerald-600' : 'text-white/70' }}">{{ $name }}</p>
                <p class="text-base md:text-lg font-black tabular-nums leading-tight mt-0.5">{{ $t }}</p>
                @if($isNext)<p class="text-[8px] font-bold text-emerald-500 uppercase tracking-wide">{{ __('Berikutnya') }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @else
    <a href="{{ route('goals') }}" class="block bg-emerald-50 border border-emerald-100 rounded-2xl p-4 hover:bg-emerald-100/60 transition-all">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-emerald-900">{{ __('Aktifkan jadwal sholat otomatis') }}</p>
                <p class="text-xs text-emerald-700/70">{{ __('Pilih wilayahmu untuk melihat waktu Subuh–Isya otomatis.') }}</p>
            </div>
            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </a>
    @endif

    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex flex-col lg:flex-row flex-wrap gap-6 md:gap-8">

            {{-- Left: Sholat Wajib --}}
            <div class="flex-1 w-full lg:min-w-[300px]">
                <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6">{{ __('Sholat Wajib') }}</h3>

                {{-- Date picker --}}
                <style>
                #sholatDateInput::-webkit-calendar-picker-indicator{opacity:0;cursor:pointer;position:absolute;right:0;top:0;width:2.5rem;height:100%}
                </style>
                <div class="flex flex-wrap items-center gap-3 mb-4 md:mb-6">
                    <form method="GET" action="{{ route('sholat') }}" style="display:inline-flex">
                        <div class="relative">
                            <input id="sholatDateInput" type="date" name="date" value="{{ $date }}" max="{{ $today }}"
                                onchange="this.form.submit()"
                                class="px-3 py-2 pr-9 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-green-400 transition-all">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </form>
                    @if($date !== $today)
                    <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-full self-start">{{ __('Mengisi data tanggal lampau') }}</span>
                    @endif

                    @if($isFemale)
                    {{-- Uzur (haid) toggle — wajib prayers excused, streak stays safe --}}
                    <form method="POST" data-instant action="{{ route('sholat.toggle-excused') }}" class="self-start">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 text-[10px] font-bold px-3 py-1.5 rounded-full transition-all {{ $isExcused ? 'bg-violet-500 text-white' : 'bg-violet-50 text-violet-600 hover:bg-violet-100' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            {{ $isExcused ? __('Hari uzur ditandai') : __('Tandai hari uzur') }}
                        </button>
                    </form>
                    @endif
                </div>

                @if($isExcused)
                {{-- Excused day: wajib not required, streak protected --}}
                <div class="rounded-2xl bg-violet-50 border border-violet-100 p-5 text-center">
                    <div class="w-11 h-11 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </div>
                    <p class="font-bold text-sm text-violet-900">{{ __('Hari uzur ditandai') }}</p>
                    <p class="text-xs text-violet-700/80 mt-1 leading-relaxed max-w-xs mx-auto">{{ __('Sholat wajib tidak dihitung hari ini, dan streak-mu tetap aman. Batalkan kapan saja lewat tombol di atas.') }}</p>
                </div>
                @else
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
                            <form method="POST" data-instant action="{{ route('sholat.toggle-wajib') }}">
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
                            <form method="POST" data-instant action="{{ route('sholat.toggle-takbir') }}">
                                @csrf
                                <input type="hidden" name="date" value="{{ $date }}">
                                <input type="hidden" name="name" value="{{ $name }}">
                                <button type="submit" @if(!$done) disabled @endif title="{{ $pq['tip'] }}"
                                    class="px-3 md:px-4 py-2 rounded-lg md:rounded-xl text-[10px] md:text-xs font-bold whitespace-nowrap transition-all {{ !$done ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : ($takbir ? 'bg-yellow-400 text-black' : 'bg-white border-2 border-gray-200 hover:border-yellow-300') }}">
                                    {{ $pq['button'] }}
                                </button>
                            </form>
                            <form method="POST" data-instant action="{{ route('sholat.toggle-rawatib') }}">
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
                @endif
            </div>

            {{-- Right: Sholat Sunnah + Stats --}}
            <div class="w-full lg:w-72">
                <div class="bg-green-50/50 rounded-2xl p-4 md:p-6 mb-4">
                    <h3 class="font-bold text-sm mb-4">{{ __('Sholat Sunnah') }}</h3>
                    <div class="space-y-2">
                        @foreach($sholatSunnah as $name)
                        @php $doneSunnah = in_array($name, $sunnahData); @endphp
                        <form method="POST" data-instant action="{{ route('sholat.toggle-sunnah') }}">
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
                    <h3 class="font-bold text-sm mb-4">{{ __('Statistik Hari Ini') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">{{ __('Wajib') }}</span>
                            <span class="text-xs font-bold text-green-600">{{ $sholatStats['wajib'] }}/5</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">{{ $pq['label'] }}</span>
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
                            <span class="text-xs font-bold">{{ __('Streak Sholat') }}</span>
                            <span class="text-sm font-bold flex items-center gap-1">
                                {{ $streak }} {{ __('hari') }}
                                <svg class="w-3.5 h-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold">{{ $pq['streak'] }}</span>
                            <span class="text-sm font-bold flex items-center gap-1">
                                {{ $takbirStreak }} {{ __('hari') }}
                                <svg class="w-3.5 h-3.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar / Strip --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-6 gap-3">
            <h3 class="font-bold">{{ $months === null ? __('Kalender Sholat Bulan Ini') : $rangeTitle }}</h3>
            <x-range-filter :range="$range" route="sholat" />
        </div>

        @if($months === null)
        {{-- ── Monthly calendar (default) ── --}}
        @php
            $firstDow = (new DateTime($monthDates[0]))->format('N');
            $offset   = $firstDow - 1;
        @endphp
        <div class="grid grid-cols-7 gap-1.5 md:gap-2 mb-2">
            @foreach($dayHeaders as $d)
            <div class="text-center text-[10px] font-bold text-gray-400 pb-1">{{ $d }}</div>
            @endforeach
            @for($i = 0; $i < $offset; $i++)<div></div>@endfor
            @foreach($monthDates as $md)
            @php
                $s = $monthStats[$md] ?? ['wajib' => 0];
                $day = (int)explode('-', $md)[2];
                $isToday = $md === $today;
                $excused = $excusedMap[$md] ?? false;
                $bg = $excused ? 'bg-violet-400 text-white'
                    : ($s['wajib'] >= 5 ? 'bg-green-500 text-white' : ($s['wajib'] > 0 ? 'bg-yellow-400' : 'bg-gray-100'));
            @endphp
            <a href="{{ route('sholat', ['date' => $md]) }}"
               class="aspect-square rounded-lg flex flex-col items-center justify-center text-xs font-bold transition-all {{ $bg }} {{ $isToday ? 'ring-2 ring-green-400 ring-offset-1' : '' }}">
                <span class="text-[10px]">{{ $day }}</span>
                @if(!$excused && $s['wajib'] > 0 && $s['wajib'] < 5)
                <span class="text-[8px]">{{ $s['wajib'] }}/5</span>
                @endif
            </a>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-gray-100 rounded"></div><span>{{ __('Kosong') }}</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-yellow-400 rounded"></div><span>{{ __('Sebagian') }}</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-green-500 rounded"></div><span>{{ __('Lengkap 5 Wajib') }}</span></div>
            @if($isFemale)
            <div class="flex items-center gap-2"><div class="w-4 h-4 bg-violet-400 rounded"></div><span>{{ __('Hari uzur') }}</span></div>
            @endif
        </div>
        @else
        {{-- ── Multi-month strip ── --}}
        <div class="mb-6">
            <p class="text-2xl font-bold text-green-600">{{ $rangeActive }}</p>
            <p class="text-[10px] text-gray-400 font-bold">{{ __('Hari lengkap (5 wajib)') }}</p>
        </div>
        <x-activity-strip :rows="$stripRows" color="green"
            :legendOff="__('Belum lengkap')" :legendOn="__('5 wajib lengkap')" />
        @endif
    </div>
</div>
@endsection
