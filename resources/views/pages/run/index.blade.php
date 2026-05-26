@extends('layouts.app')
@section('title', 'Run / Lari')
@section('page-title', 'Run / Lari')
@section('breadcrumb', 'Run')

@section('content')
@php
    $typeLabels = [
        'easy'     => 'Easy Run',
        'tempo'    => 'Tempo',
        'interval' => 'Interval',
        'long_run' => 'Long Run',
        'race'     => 'Lomba',
    ];
    $typeBadge = [
        'easy'     => 'bg-emerald-100 text-emerald-700',
        'tempo'    => 'bg-orange-100 text-orange-700',
        'interval' => 'bg-red-100 text-red-700',
        'long_run' => 'bg-blue-100 text-blue-700',
        'race'     => 'bg-purple-100 text-purple-700',
    ];
    $typePill = [
        'easy'     => 'hover:border-emerald-400 data-active:border-emerald-500 data-active:bg-emerald-500',
        'tempo'    => 'hover:border-orange-400',
        'interval' => 'hover:border-red-400',
        'long_run' => 'hover:border-blue-400',
        'race'     => 'hover:border-purple-400',
    ];
    $idDays   = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    $idMonths = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
@endphp

<div class="space-y-6">

    {{-- ===== ROW 1: Input + Stats ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">

        {{-- INPUT CARD --}}
        <div class="lg:col-span-2 bg-white rounded-2xl md:rounded-3xl p-5 md:p-6 border border-gray-50">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-base">Lari Hari Ini</h2>
                        <p class="text-xs text-gray-400">{{ date('l, j F Y') }}</p>
                    </div>
                </div>
                @if($todayRun['done'])
                <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Sudah Lari
                </div>
                @endif
            </div>

            {{-- FORM --}}
            <form action="{{ route('run.update') }}" method="POST" id="runForm">
                @csrf
                <input type="hidden" name="date" value="{{ $today }}">

                {{-- Type Selector --}}
                <div class="mb-5">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 block">Tipe Lari</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($typeLabels as $val => $label)
                        <button type="button"
                            class="type-pill px-4 py-2 rounded-xl text-sm font-semibold border-2 transition-all
                                {{ ($todayRun['type'] ?? 'easy') === $val
                                    ? 'border-black bg-black text-white'
                                    : 'border-gray-200 text-gray-500 hover:border-gray-400' }}"
                            data-value="{{ $val }}"
                            onclick="selectType('{{ $val }}')">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="type" id="typeInput" value="{{ $todayRun['type'] ?? 'easy' }}">
                </div>

                {{-- Jarak · Durasi · Pace --}}
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Jarak</label>
                        <div class="relative">
                            <input type="number" name="distance" id="distInput"
                                value="{{ $todayRun['distance'] > 0 ? $todayRun['distance'] : '' }}"
                                placeholder="0.0" step="0.1" min="0" max="500"
                                class="w-full bg-gray-50 rounded-xl px-3 py-3 text-xl font-bold focus:ring-2 focus:ring-black outline-none border-0 pr-8"
                                oninput="calcPace()">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 font-bold">km</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Durasi</label>
                        <div class="relative">
                            <input type="number" name="duration" id="durInput"
                                value="{{ $todayRun['duration'] > 0 ? $todayRun['duration'] : '' }}"
                                placeholder="0" step="1" min="0" max="1440"
                                class="w-full bg-gray-50 rounded-xl px-3 py-3 text-xl font-bold focus:ring-2 focus:ring-black outline-none border-0 pr-10"
                                oninput="calcPace()">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 font-bold">mnt</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Pace</label>
                        <div class="bg-emerald-50 rounded-xl px-3 py-3 flex items-center justify-between h-[52px]">
                            <span id="paceDisplay" class="text-xl font-bold text-emerald-700">
                                @php
                                    $d0 = (float)($todayRun['distance'] ?? 0);
                                    $t0 = (int)($todayRun['duration'] ?? 0);
                                    if ($d0 > 0 && $t0 > 0) {
                                        $p = $t0 / $d0;
                                        echo floor($p) . ':' . str_pad(round(fmod($p, 1) * 60), 2, '0', STR_PAD_LEFT);
                                    } else {
                                        echo '--:--';
                                    }
                                @endphp
                            </span>
                            <span class="text-[10px] text-emerald-500 font-bold">/km</span>
                        </div>
                    </div>
                </div>

                {{-- Kalori + Catatan --}}
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Kalori <span class="normal-case font-normal">(opsional)</span></label>
                        <div class="relative">
                            <input type="number" name="calories"
                                value="{{ ($todayRun['calories'] ?? 0) > 0 ? $todayRun['calories'] : '' }}"
                                placeholder="0" step="1" min="0" max="9999"
                                class="w-full bg-gray-50 rounded-xl px-3 py-3 font-bold focus:ring-2 focus:ring-black outline-none border-0 pr-12">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 font-bold">kcal</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Catatan <span class="normal-case font-normal">(opsional)</span></label>
                        <input type="text" name="notes"
                            value="{{ $todayRun['notes'] ?? '' }}"
                            placeholder="Rute, kondisi, perasaan..."
                            class="w-full bg-gray-50 rounded-xl px-3 py-3 font-medium focus:ring-2 focus:ring-black outline-none border-0">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="flex-1 bg-black text-white py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                        {{ $todayRun['done'] ? 'Perbarui Data Lari' : '🏃 Catat Lari Hari Ini' }}
                    </button>
                    @if($todayRun['done'])
                    <form action="{{ route('run.toggle') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <input type="hidden" name="date" value="{{ $today }}">
                        <button type="submit"
                            class="px-4 py-3 rounded-xl border-2 border-red-200 text-red-500 text-sm font-bold hover:bg-red-50 transition-all whitespace-nowrap">
                            Batalkan
                        </button>
                    </form>
                    @endif
                </div>
            </form>
        </div>

        {{-- STATS PANEL --}}
        <div class="flex flex-col gap-4">

            {{-- Weekly --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-50">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Minggu Ini</p>
                <div class="flex items-end gap-2 mt-2 mb-1">
                    <span class="text-3xl font-bold">{{ number_format($weeklyDist, 1) }}</span>
                    <span class="text-sm text-gray-400 mb-0.5">km</span>
                    <span class="ml-auto text-xs text-gray-500 mb-0.5">{{ $weeklyCount }}× lari</span>
                </div>
                {{-- 7-day bar --}}
                <div class="flex gap-1 mt-3">
                    @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $i => $day)
                    @php $wr = $weekRuns[$i] ?? []; $ran = $wr['done'] ?? false; $km = $wr['distance'] ?? 0; @endphp
                    <div class="flex-1 flex flex-col items-center gap-1" title="{{ $ran ? number_format($km,1).' km' : 'Tidak lari' }}">
                        <div class="relative w-full">
                            <div class="w-full h-8 rounded-lg bg-gray-100 overflow-hidden flex items-end">
                                @if($ran && $km > 0 && $weeklyDist > 0)
                                <div class="w-full bg-emerald-400 rounded-lg transition-all"
                                    style="height: {{ max(20, round(($km / max($weeklyDist, 1)) * 100)) }}%"></div>
                                @elseif($ran)
                                <div class="w-full bg-emerald-200 rounded-lg h-2"></div>
                                @endif
                            </div>
                        </div>
                        <span class="text-[8px] text-gray-400 font-medium">{{ $day }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Monthly --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-50">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Bulan Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <span class="text-3xl font-bold">{{ number_format($monthlyDist, 1) }}</span>
                    <span class="text-sm text-gray-400 mb-0.5">km</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $monthlyCount }} sesi lari</p>
            </div>

            {{-- Personal Best --}}
            <div class="bg-white rounded-2xl p-5 border border-gray-50">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Personal Best</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-yellow-50 rounded-lg flex items-center justify-center">
                                <span class="text-xs">🏅</span>
                            </div>
                            <span class="text-xs text-gray-500">Jarak Terjauh</span>
                        </div>
                        <span class="font-bold text-sm">
                            {{ $pbs['distance'] > 0 ? number_format($pbs['distance'], 1).' km' : '—' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-yellow-50 rounded-lg flex items-center justify-center">
                                <span class="text-xs">⚡</span>
                            </div>
                            <span class="text-xs text-gray-500">Pace Terbaik</span>
                        </div>
                        <span class="font-bold text-sm">
                            @if($pbs['pace'] > 0)
                                @php $pm = floor($pbs['pace']); $ps = round(fmod($pbs['pace'], 1) * 60); @endphp
                                {{ $pm }}:{{ str_pad($ps, 2, '0', STR_PAD_LEFT) }} /km
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== ROW 2: Riwayat ===== --}}
    @if(count($history) > 0)
    <div class="bg-white rounded-2xl md:rounded-3xl p-5 md:p-6 border border-gray-50">
        <h3 class="font-bold mb-5">Riwayat Lari</h3>
        <div class="space-y-1">
            @foreach($history as $run)
            @php
                $d       = new DateTime($run['date']);
                $dist    = (float)($run['distance'] ?? 0);
                $dur     = (int)($run['duration'] ?? 0);
                $paceMin = ($dist > 0 && $dur > 0) ? floor($dur / $dist) : 0;
                $paceSec = ($dist > 0 && $dur > 0) ? round(fmod($dur / $dist, 1) * 60) : 0;
                $isToday = $run['date'] === $today;
            @endphp
            <div class="flex items-center gap-3 md:gap-5 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">

                {{-- Date tile --}}
                <div class="flex-shrink-0 w-12 text-center">
                    <p class="text-[9px] font-bold text-gray-400 uppercase">{{ $idDays[$d->format('w')] }}</p>
                    <p class="text-lg font-bold leading-tight">{{ $d->format('j') }}</p>
                    <p class="text-[9px] text-gray-400">{{ $idMonths[(int)$d->format('n')] }}</p>
                </div>

                {{-- Type badge --}}
                <span class="flex-shrink-0 text-[10px] font-bold px-2.5 py-1 rounded-lg {{ $typeBadge[$run['type']] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $typeLabels[$run['type']] ?? $run['type'] }}
                </span>

                {{-- Metrics --}}
                <div class="flex-1 flex items-center gap-4 md:gap-8 min-w-0">
                    <div class="text-center">
                        <p class="text-base md:text-lg font-bold leading-tight">{{ $dist > 0 ? number_format($dist, 1) : '—' }}</p>
                        <p class="text-[9px] text-gray-400">km</p>
                    </div>
                    @if($paceMin > 0)
                    <div class="text-center">
                        <p class="text-base md:text-lg font-bold leading-tight">{{ $paceMin }}:{{ str_pad($paceSec, 2, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-[9px] text-gray-400">/km</p>
                    </div>
                    @endif
                    <div class="text-center">
                        <p class="text-base md:text-lg font-bold leading-tight">{{ $dur > 0 ? $dur : '—' }}</p>
                        <p class="text-[9px] text-gray-400">mnt</p>
                    </div>
                    @if(($run['calories'] ?? 0) > 0)
                    <div class="text-center hidden sm:block">
                        <p class="text-base md:text-lg font-bold leading-tight">{{ $run['calories'] }}</p>
                        <p class="text-[9px] text-gray-400">kcal</p>
                    </div>
                    @endif
                    @if(!empty($run['notes']))
                    <p class="text-xs text-gray-400 truncate hidden md:block flex-1">{{ $run['notes'] }}</p>
                    @endif
                </div>

                {{-- Today badge --}}
                @if($isToday)
                <span class="flex-shrink-0 text-[10px] font-bold bg-gray-100 text-black px-2.5 py-1 rounded-lg">Hari ini</span>
                @endif

            </div>
            @endforeach
        </div>
    </div>
    @else
    {{-- Empty state --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-10 md:p-16 border border-gray-50 text-center">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
        </div>
        <p class="font-bold text-gray-700">Belum ada catatan lari</p>
        <p class="text-sm text-gray-400 mt-1">Catat sesi lari pertamamu di atas!</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function selectType(val) {
    document.getElementById('typeInput').value = val;
    document.querySelectorAll('.type-pill').forEach(btn => {
        if (btn.dataset.value === val) {
            btn.classList.remove('border-gray-200', 'text-gray-500', 'hover:border-gray-400');
            btn.classList.add('border-black', 'bg-black', 'text-white');
        } else {
            btn.classList.remove('border-black', 'bg-black', 'text-white');
            btn.classList.add('border-gray-200', 'text-gray-500', 'hover:border-gray-400');
        }
    });
}

function calcPace() {
    const dist = parseFloat(document.getElementById('distInput').value) || 0;
    const dur  = parseInt(document.getElementById('durInput').value)   || 0;
    const el   = document.getElementById('paceDisplay');
    if (dist > 0 && dur > 0) {
        const pace = dur / dist;
        const min  = Math.floor(pace);
        const sec  = Math.round((pace - min) * 60);
        el.textContent = min + ':' + String(sec).padStart(2, '0');
        el.classList.remove('text-gray-400');
        el.classList.add('text-emerald-700');
    } else {
        el.textContent = '--:--';
        el.classList.remove('text-emerald-700');
        el.classList.add('text-gray-400');
    }
}

// Init pace on load if data exists
calcPace();
</script>
@endpush
