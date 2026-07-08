@extends('layouts.app')
@section('title', 'Siklus Haid')
@section('page-title', __('Siklus Haid'))
@section('breadcrumb', 'Life › Siklus Haid')

@section('content')
@php
    $today = \Carbon\Carbon::today();
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Hero status ── --}}
    <div class="rounded-2xl md:rounded-3xl p-6 md:p-8 bg-gradient-to-br from-rose-500 via-rose-500 to-pink-600 text-white relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10"></div>
        <div class="absolute right-16 bottom-0 w-24 h-24 rounded-full bg-white/5"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-widest text-white/60">{{ $today->translatedFormat('l, j F Y') }}</p>
                @if($ongoing)
                <h2 class="text-2xl md:text-3xl font-black mt-1.5">{{ __('Haid hari ke-:n', ['n' => $periodDay]) }}</h2>
                <p class="text-sm text-white/80 mt-1">{{ __('Mulai') }} {{ $ongoing->start_date->translatedFormat('j F') }} · {{ __('rata-rata haidmu :n hari', ['n' => $avgPeriod]) }}</p>
                @elseif($cycleDay)
                <h2 class="text-2xl md:text-3xl font-black mt-1.5">{{ __('Hari ke-:n siklus', ['n' => $cycleDay]) }}</h2>
                <p class="text-sm text-white/80 mt-1">
                    @if($daysToNext === 0)
                    {{ __('Perkiraan haid berikutnya: hari ini') }}
                    @elseif($daysToNext !== null)
                    {{ __('Perkiraan haid berikutnya :n hari lagi (:d)', ['n' => $daysToNext, 'd' => $nextStart->translatedFormat('j F')]) }}
                    @endif
                </p>
                @else
                <h2 class="text-2xl md:text-3xl font-black mt-1.5">{{ __('Mulai catat siklusmu') }}</h2>
                <p class="text-sm text-white/80 mt-1">{{ __('Catat haid pertamamu, prediksi akan makin akurat seiring waktu.') }}</p>
                @endif
            </div>
            <div class="flex-shrink-0">
                @if($ongoing)
                <form method="POST" action="{{ route('haid.finish') }}">
                    @csrf
                    <button type="submit" class="px-5 py-3 rounded-xl bg-white text-rose-600 text-sm font-bold hover:bg-rose-50 transition-all shadow-lg">
                        {{ __('Haid Selesai Hari Ini') }}
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('haid.store') }}">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ $today->toDateString() }}">
                    <button type="submit" class="px-5 py-3 rounded-xl bg-white text-rose-600 text-sm font-bold hover:bg-rose-50 transition-all shadow-lg">
                        {{ __('Mulai Haid Hari Ini') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Statistik ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none">{{ $avgCycle }}<span class="text-base text-gray-400"> {{ __('hari') }}</span></p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Rata-rata Siklus') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ __('normal 21-35 hari') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none">{{ $avgPeriod }}<span class="text-base text-gray-400"> {{ __('hari') }}</span></p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Rata-rata Haid') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ __('normal 2-7 hari') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <p class="text-base md:text-lg font-black text-gray-900 leading-tight">{{ $nextStart ? $nextStart->translatedFormat('j F') : '-' }}</p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Perkiraan Haid Berikutnya') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $daysToNext !== null ? ($daysToNext === 0 ? __('hari ini') : $daysToNext . ' ' . __('hari lagi')) : __('butuh minimal 1 catatan') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            @php $fp = $predictions[0] ?? null; @endphp
            <p class="text-base md:text-lg font-black text-gray-900 leading-tight">
                {{ $fp ? $fp['fertile_start']->translatedFormat('j M') . ' - ' . $fp['fertile_end']->translatedFormat('j M') : '-' }}
            </p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Perkiraan Masa Subur') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $fp ? __('ovulasi sekitar :d', ['d' => $fp['ovulation']->translatedFormat('j M')]) : __('butuh minimal 1 catatan') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">

        {{-- ── Kalender ── --}}
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold">{{ $monthStart->translatedFormat('F Y') }}</h3>
                <div class="flex items-center gap-1">
                    <a href="{{ route('haid', ['bulan' => $monthStart->copy()->subMonth()->format('Y-m')]) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <a href="{{ route('haid', ['bulan' => $monthStart->copy()->addMonth()->format('Y-m')]) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center mb-1">
                @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d)
                <span class="text-[10px] font-bold uppercase tracking-wide text-gray-400 py-1">{{ $d }}</span>
                @endforeach
            </div>
            @php
                $firstDow = (int) $monthStart->copy()->startOfMonth()->isoWeekday(); // 1=Senin
                $daysInMonth = $monthStart->daysInMonth;
            @endphp
            <div class="grid grid-cols-7 gap-1">
                @for($i = 1; $i < $firstDow; $i++)<span></span>@endfor
                @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = $monthStart->copy()->day($day);
                    $key  = $date->toDateString();
                    $type = $calendar[$key] ?? null;
                    $isToday = $date->isToday();
                    $cls = match($type) {
                        'haid'     => 'bg-rose-500 text-white font-bold',
                        'prediksi' => 'bg-rose-100 text-rose-600 font-bold',
                        'ovulasi'  => 'bg-teal-500 text-white font-bold',
                        'subur'    => 'bg-teal-50 text-teal-600 font-bold',
                        default    => 'text-gray-600',
                    };
                @endphp
                <div class="h-9 md:h-10 flex items-center justify-center rounded-xl text-xs transition-all {{ $cls }} {{ $isToday ? 'ring-2 ring-gray-900 ring-offset-1' : '' }}">
                    {{ $day }}
                </div>
                @endfor
            </div>

            {{-- Legenda --}}
            <div class="flex flex-wrap gap-x-4 gap-y-2 mt-5 pt-4 border-t border-gray-50">
                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500"><span class="w-3 h-3 rounded-md bg-rose-500"></span>{{ __('Haid tercatat') }}</span>
                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500"><span class="w-3 h-3 rounded-md bg-rose-100"></span>{{ __('Perkiraan haid') }}</span>
                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500"><span class="w-3 h-3 rounded-md bg-teal-50 border border-teal-200"></span>{{ __('Masa subur') }}</span>
                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500"><span class="w-3 h-3 rounded-md bg-teal-500"></span>{{ __('Ovulasi') }}</span>
            </div>
        </div>

        {{-- ── Riwayat ── --}}
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold">{{ __('Riwayat Periode') }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('Makin banyak catatan, makin akurat prediksinya.') }}</p>
                </div>
                <button type="button" onclick="openHaidModal()"
                    class="px-4 py-2.5 rounded-xl bg-black text-white text-xs font-bold hover:bg-gray-800 transition-all flex-shrink-0">
                    + {{ __('Catat Manual') }}
                </button>
            </div>

            @if(count($cycles) === 0)
            <div class="text-center py-10">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-300 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c0 0-6 6.9-6 11a6 6 0 0012 0c0-4.1-6-11-6-11z"/></svg>
                </div>
                <p class="text-sm text-gray-400">{{ __('Belum ada catatan. Mulai dari tombol di atas.') }}</p>
            </div>
            @else
            <div class="space-y-2">
                @foreach($cycles as $i => $c)
                @php
                    $len = $c->end_date ? $c->start_date->diffInDays($c->end_date) + 1 : null;
                    $next = $cycles[$i - 1] ?? null; // satu lebih baru
                    $cycleLen = $next ? $c->start_date->diffInDays($next->start_date) : null;
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <div class="w-9 h-9 rounded-xl {{ $c->end_date ? 'bg-white border border-gray-100 text-rose-400' : 'bg-rose-500 text-white' }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c0 0-6 6.9-6 11a6 6 0 0012 0c0-4.1-6-11-6-11z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">
                            {{ $c->start_date->translatedFormat('j M') }} - {{ $c->end_date ? $c->end_date->translatedFormat('j M Y') : __('sekarang') }}
                        </p>
                        <p class="text-[11px] text-gray-400">
                            {{ $len ? __(':n hari haid', ['n' => $len]) : __('sedang berlangsung') }}{{ $cycleLen ? ' · ' . __('siklus :n hari', ['n' => $cycleLen]) : '' }}
                        </p>
                    </div>
                    <button type="button" onclick='openHaidModal(@json(['id' => $c->id, 'start' => $c->start_date->toDateString(), 'end' => $c->end_date?->toDateString()]))'
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-black hover:bg-gray-200 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('haid.destroy', $c->id) }}" class="m-0">
                        @csrf @method('DELETE')
                        <button type="button" onclick="askDelete(this, '{{ __('Hapus catatan periode ini? Tanda uzur sholat di rentang ini ikut terhapus.') }}')"
                            class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-gray-200 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── Info ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-2xl bg-green-50 border border-green-100 p-4 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-green-800">{{ __('Tersinkron dengan Sholat') }}</p>
                <p class="text-[11px] text-green-700/80 mt-0.5 leading-relaxed">{{ __('Hari haid otomatis ditandai sebagai hari uzur, jadi streak sholatmu tidak terputus dan hari itu tidak dihitung.') }}</p>
            </div>
        </div>
        <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-700">{{ __('Prediksi adalah perkiraan') }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">{{ __('Dihitung dari rata-rata siklusmu sendiri. Bukan alat kontrasepsi atau pengganti konsultasi medis; siklus yang terus tidak teratur sebaiknya diperiksakan.') }}</p>
            </div>
        </div>
    </div>

</div>

{{-- ── Modal catat/edit manual ── --}}
<div id="modal-haid" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeHaidModal()">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-start justify-between gap-3 px-6 pt-6 pb-4 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg leading-tight" id="haidModalTitle">{{ __('Catat Periode') }}</h2>
                <p class="text-xs text-gray-400 mt-1">{{ __('Untuk periode lampau, isi tanggal mulai dan selesai.') }}</p>
            </div>
            <button type="button" onclick="closeHaidModal()" class="w-8 h-8 -mr-1.5 -mt-1 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('haid.store') }}" id="haidForm" class="px-6 pt-5 pb-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tanggal Mulai') }} <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="date" name="start_date" required max="{{ $today->toDateString() }}" placeholder="{{ __('Pilih tanggal hari pertama haid') }}"
                            class="w-full pl-3 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    @error('start_date')<p class="text-[11px] font-bold text-red-500 mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tanggal Selesai') }}</label>
                    <div class="relative">
                        <input type="date" name="end_date" max="{{ $today->toDateString() }}" placeholder="{{ __('Pilih tanggal hari terakhir haid') }}"
                            class="w-full pl-3 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1.5">{{ __('Kosongkan bila masih berlangsung.') }}</p>
                    @error('end_date')<p class="text-[11px] font-bold text-red-500 mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>
            <button type="submit" class="w-full mt-6 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all" id="haidSubmit">{{ __('Simpan') }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openHaidModal(c){
    const f = document.getElementById('haidForm');
    const isEdit = !!c;
    document.getElementById('haidModalTitle').textContent = isEdit ? '{{ __('Ubah Periode') }}' : '{{ __('Catat Periode') }}';
    document.getElementById('haidSubmit').textContent = isEdit ? '{{ __('Perbarui') }}' : '{{ __('Simpan') }}';
    f.action = isEdit ? '{{ url('haid') }}/' + c.id : '{{ route('haid.store') }}';
    f.querySelector('[name="start_date"]').value = c?.start ?? '';
    f.querySelector('[name="end_date"]').value = c?.end ?? '';
    document.getElementById('modal-haid').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeHaidModal(){
    document.getElementById('modal-haid').classList.add('hidden');
    document.body.style.overflow = '';
}
@if($errors->any())
openHaidModal();
@endif
</script>
@endpush
