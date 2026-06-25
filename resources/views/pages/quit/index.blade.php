@extends('layouts.app')
@section('title', $meta['label'])
@section('page-title', $meta['title'])
@section('breadcrumb', 'Life › ' . $meta['label'])

@section('content')
@php $c = $meta['color']; @endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── How it works (explainer) ── --}}
    <div class="bg-{{ $c }}-50 border border-{{ $c }}-100 rounded-2xl p-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-{{ $c }}-100 text-{{ $c }}-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-xs text-{{ $c }}-800/80 leading-relaxed">
            <span class="font-bold text-{{ $c }}-900">{{ __('Cara kerja:') }}</span>
            {{ __('Hitungan hari bertambah otomatis selama kamu bertahan, kamu tidak perlu input apa pun setiap hari. Cukup tekan tombol di bawah hanya saat kamu') }}
            <span class="font-bold">{{ strtolower($meta['relapse']) }}</span>{{ __(', dan hitungan mulai dari nol lagi.') }}
        </div>
    </div>

    {{-- ── Streak hero ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-6 md:p-10 text-center relative overflow-hidden border border-gray-50">
        <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-{{ $c }}-50 to-transparent"></div>
        <div class="relative">
            {{-- decorative title (not a button) --}}
            <div class="inline-flex items-center gap-2 text-{{ $c }}-600 mb-5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $meta['icon'] }}"/></svg>
                <span class="text-xs font-bold uppercase tracking-widest">{{ $meta['title'] }}</span>
            </div>

            <p class="text-7xl md:text-8xl font-black text-{{ $c }}-500 leading-none">{{ $stats['streak'] }}</p>
            <p class="text-sm font-bold text-gray-500 mt-2">{{ $meta['unit'] }} {{ __('berturut-turut') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('Mulai sejak') }} {{ date('j M Y', strtotime($stats['start_date'])) }}</p>

            @if($stats['next'])
            <div class="max-w-xs mx-auto mt-6">
                <div class="flex justify-between text-[11px] font-bold text-gray-400 mb-1">
                    <span>{{ $stats['streak'] }} {{ __('hari') }}</span>
                    <span>{{ __('Target') }} {{ $stats['next'] }}</span>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-{{ $c }}-500 h-full rounded-full transition-all" style="width:{{ $stats['next'] > 0 ? min(100, round(($stats['streak'] / $stats['next']) * 100)) : 0 }}%"></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5">{{ __('Tinggal :n hari lagi menuju milestone berikutnya', ['n' => $stats['to_next']]) }}</p>
            </div>
            @endif

            {{-- Relapse action with clear helper --}}
            <div class="mt-7 pt-6 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-2">{{ __('Tergelincir hari ini? Jujur pada diri sendiri, catat ceritanya & mulai lagi.') }}</p>
                <button type="button" onclick="openModal('modal-relapse')"
                    class="px-5 py-2.5 rounded-xl border border-{{ $c }}-200 text-sm font-bold text-{{ $c }}-600 hover:bg-{{ $c }}-50 transition-all">
                    {{ $meta['relapse'] }}
                </button>
            </div>
        </div>
    </div>

    {{-- ── Stats row ── --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-{{ $c }}-600">{{ $stats['best'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Rekor Terlama') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['streak'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Streak Saat Ini') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['relapses'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Total Reset') }}</p>
        </div>
    </div>

    {{-- ── Milestones ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold">{{ __('Milestone') }}</h3>
            <span class="text-[10px] text-gray-400">{{ __('Target yang sudah & belum tercapai') }}</span>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
            @foreach($stats['milestones'] as $m)
            @php $reached = $stats['streak'] >= $m; @endphp
            <div class="rounded-xl p-3 text-center {{ $reached ? 'bg-'.$c.'-50 border border-'.$c.'-200' : 'bg-gray-50 border border-transparent' }}">
                <p class="text-lg font-bold {{ $reached ? 'text-'.$c.'-600' : 'text-gray-300' }}">{{ $m }}</p>
                <p class="text-[9px] font-bold {{ $reached ? 'text-'.$c.'-500' : 'text-gray-300' }}">{{ __('hari') }}</p>
                @if($reached)
                <svg class="w-3.5 h-3.5 mx-auto mt-1 text-{{ $c }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Journal / History ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <h3 class="font-bold mb-1">{{ __('Jurnal Refleksi') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ __('Catatan setiap kali kamu tergelincir, pelajari pemicunya agar makin kuat.') }}</p>

        @if(count($history) === 0)
        <div class="text-center py-8">
            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">{{ __('Belum ada catatan') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Semoga tetap kosong, pertahankan!') }}</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($history as $h)
            @php
                $d = (int)($h['streak'] ?? 0);
                if ($d <= 0)        { $dur = __('kurang dari sehari'); }
                elseif ($d < 7)     { $dur = $d . ' ' . __('hari'); }
                elseif ($d < 30)    { $w = intdiv($d, 7); $rd = $d % 7; $dur = $w . ' ' . __('minggu') . ($rd ? ' ' . $rd . ' ' . __('hari') : ''); }
                elseif ($d < 365)   { $mo = intdiv($d, 30); $rd = $d % 30; $dur = $mo . ' ' . __('bulan') . ($rd ? ' ' . $rd . ' ' . __('hari') : ''); }
                else                { $y = intdiv($d, 365); $rd = $d % 365; $dur = $y . ' ' . __('tahun') . ($rd ? ' ' . $rd . ' ' . __('hari') : ''); }
            @endphp
            <div class="rounded-2xl border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-xs font-bold text-gray-600">{{ date('l, j F Y', strtotime($h['date'])) }}</span>
                    <span class="ml-auto inline-flex items-center gap-1 text-[10px] font-bold text-{{ $c }}-600 bg-{{ $c }}-50 px-2 py-0.5 rounded-full flex-shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('bertahan') }} {{ $dur }}
                    </span>
                </div>
                <div class="px-4 py-3">
                    @if($h['note'])
                    <p class="text-sm text-gray-700 leading-relaxed italic">"{{ $h['note'] }}"</p>
                    @else
                    <p class="text-xs text-gray-300 italic">{{ __('Tidak ada catatan.') }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- ── Modal: catat relapse + reset ── --}}
<div id="modal-relapse" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-relapse')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ $meta['relapse'] }}</h2>
            <p class="text-xs text-gray-400 mt-1">{{ __('Tidak apa-apa, ini bagian dari proses. Ceritakan apa yang terjadi supaya kamu bisa belajar.') }}</p>
        </div>
        <form method="POST" action="{{ route('quit.relapse', $type) }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Apa pemicunya? Bagaimana perasaanmu?') }}</label>
                <textarea name="note" rows="4" maxlength="1000"
                    placeholder="{{ __('cth: Bosan dan scroll terlalu lama sebelum tidur. Besok aku taruh HP di luar kamar...') }}"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-{{ $c }}-400 resize-none transition-all"></textarea>
                <p class="text-[10px] text-gray-400 mt-1">{{ __('Opsional, tapi sangat membantu refleksimu.') }}</p>
            </div>
            <div class="bg-{{ $c }}-50 rounded-xl p-3 text-xs text-{{ $c }}-700 leading-relaxed">
                {{ __('Streak kamu saat ini') }} <span class="font-bold">{{ $stats['streak'] }} {{ __('hari') }}</span> {{ __('akan di-reset ke 0 dan dicatat di jurnal. Bangkit lagi, kamu pasti bisa!') }}
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('modal-relapse')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">{{ __('Batal') }}</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-{{ $c }}-500 text-white text-sm font-bold hover:bg-{{ $c }}-600 transition-all">{{ __('Catat & Reset') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(id){ document.getElementById(id).classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeModal(id){ document.getElementById(id).classList.add('hidden'); document.body.style.overflow=''; }
</script>
@endpush
@endsection
