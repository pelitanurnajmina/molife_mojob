@extends('layouts.app')
@section('title', __('Time Blocking'))
@section('page-title', __('Time Blocking'))
@section('breadcrumb', __('Life') . ' › ' . __('Time Blocking'))

@section('content')
@php
    use Carbon\Carbon;
    $rangeLabel = Carbon::parse($weekStart)->translatedFormat('j M') . ' – ' . Carbon::parse($weekEnd)->translatedFormat('j M Y');
    $gridCols = 'grid-template-columns:52px repeat(7,minmax(0,1fr))';
@endphp
<div class="space-y-4">

    {{-- ── Toolbar ── --}}
    <div class="flex items-center justify-between gap-3 flex-wrap">
        {{-- Kiri: panah mengapit judul minggu (jelas = navigasi minggu yang ditampilkan) --}}
        <div class="flex items-center gap-1">
            <a href="{{ route('timeblock', ['date' => $prevDate]) }}" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all" title="{{ __('Minggu sebelumnya') }}" aria-label="{{ __('Minggu sebelumnya') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="px-1 flex items-baseline gap-2 whitespace-nowrap">
                <h3 class="font-bold text-base sm:text-lg">{{ Carbon::parse($weekStart)->translatedFormat('F Y') }}</h3>
                <span class="text-[11px] text-gray-400">{{ $rangeLabel }}</span>
            </div>
            <a href="{{ route('timeblock', ['date' => $nextDate]) }}" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all" title="{{ __('Minggu berikutnya') }}" aria-label="{{ __('Minggu berikutnya') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        {{-- Kanan: lompat ke tanggal (lebih berguna) + hari ini + tambah blok --}}
        <div class="flex items-center gap-2">
            {{-- Tanpa w-full: flatpickr otomatis meringkas field (size=11) jadi teks & ikon berdekatan --}}
            <div class="relative inline-block">
                <input type="date" value="{{ $weekStart }}" onchange="tbJumpTo(this.value)" title="{{ __('Lompat ke tanggal') }}"
                    class="pl-3 pr-9 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 outline-none focus:border-black transition-all">
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <a href="{{ route('timeblock') }}" class="px-3 h-9 inline-flex items-center rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-all whitespace-nowrap">{{ __('Hari ini') }}</a>
            <button type="button" onclick="tbAdd('{{ $selectedDate }}', 540)"
                class="inline-flex items-center gap-1.5 px-4 h-9 rounded-xl bg-black text-white text-xs font-bold hover:bg-gray-800 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('Blok') }}
            </button>
        </div>
    </div>

    @if($prayerOn || $tasksOn)
    <p class="text-[11px] text-gray-400 -mt-1">
        {{ __('Klik slot kosong untuk membuat blok.') }}
        @if($prayerOn) {{ __('Jadwal sholat ditampilkan otomatis.') }}@endif
        @if($tasksOn) {{ __('Tugas harian muncul sebagai item all-day.') }}@endif
    </p>
    @endif

    {{-- ════════ WEEK VIEW (desktop) ════════ --}}
    <div class="hidden lg:block bg-white rounded-2xl md:rounded-3xl border border-gray-50 overflow-hidden">
        {{-- Header hari --}}
        <div class="grid border-b border-gray-100" style="{{ $gridCols }}">
            <div></div>
            @foreach($days as $d)
            @php $c = Carbon::parse($d); $isToday = $d === $today; @endphp
            <div class="text-center py-2.5 border-l border-gray-100 {{ $isToday ? 'bg-amber-50/40' : '' }}">
                <p class="text-[10px] font-bold uppercase tracking-wide {{ $isToday ? 'text-amber-600' : 'text-gray-400' }}">{{ $c->translatedFormat('D') }}</p>
                <p class="text-lg font-bold {{ $isToday ? 'text-amber-600' : 'text-gray-800' }}">{{ $c->format('j') }}</p>
            </div>
            @endforeach
        </div>

        {{-- Baris all-day (tugas harian) --}}
        @if($tasksOn)
        <div class="grid border-b border-gray-100 bg-gray-50/40" style="{{ $gridCols }}">
            <div class="flex items-center justify-end pr-2 py-1.5"><span class="text-[9px] font-bold text-gray-400 uppercase">{{ __('All-day') }}</span></div>
            @foreach($days as $d)
            <div class="border-l border-gray-100 p-1 space-y-1 min-h-[32px]">
                @foreach($eventsByDate[$d]['allday'] as $t)
                <div class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $t['done'] ? 'bg-green-50 text-green-600 line-through' : 'bg-gray-100 text-gray-600' }} truncate" title="{{ $t['title'] }}">{{ $t['title'] }}</div>
                @endforeach
            </div>
            @endforeach
        </div>
        @endif

        {{-- Grid waktu (scroll) --}}
        <div class="overflow-y-auto tb-scroll" style="max-height:64vh">
            <div class="grid" style="{{ $gridCols }}">
                {{-- Gutter jam --}}
                <div class="relative" style="height:1152px">
                    @for($h = 0; $h < 24; $h++)
                    <div class="absolute right-1.5 -translate-y-1/2 text-[9px] font-bold text-gray-300" style="top:{{ $h * 48 }}px">{{ sprintf('%02d:00', $h) }}</div>
                    @endfor
                </div>
                @foreach($days as $d)
                @include('pages._tb_column', ['date' => $d, 'data' => $eventsByDate[$d], 'today' => $today])
                @endforeach
            </div>
        </div>
    </div>

    {{-- ════════ DAY VIEW (mobile) ════════ --}}
    <div class="lg:hidden bg-white rounded-2xl border border-gray-50 overflow-hidden">
        {{-- Strip pemilih hari --}}
        <div class="grid grid-cols-7 border-b border-gray-100">
            @foreach($days as $d)
            @php $c = Carbon::parse($d); $isToday = $d === $today; @endphp
            <button type="button" onclick="tbSelectDay('{{ $d }}')" data-daybtn="{{ $d }}"
                class="tb-daybtn py-2 text-center border-l border-gray-100 first:border-l-0 transition-all {{ $d === $selectedDate ? 'bg-black text-white' : ($isToday ? 'text-amber-600' : 'text-gray-500') }}">
                <span class="block text-[9px] font-bold uppercase">{{ $c->translatedFormat('D') }}</span>
                <span class="block text-sm font-bold">{{ $c->format('j') }}</span>
            </button>
            @endforeach
        </div>

        {{-- All-day mobile --}}
        @if($tasksOn)
        @foreach($days as $d)
        <div class="tb-mallday border-b border-gray-100 bg-gray-50/40 px-3 py-1.5 flex flex-wrap gap-1 {{ $d === $selectedDate ? '' : 'hidden' }}" data-mallday="{{ $d }}">
            <span class="text-[9px] font-bold text-gray-400 uppercase mr-1 self-center">{{ __('All-day') }}</span>
            @forelse($eventsByDate[$d]['allday'] as $t)
            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $t['done'] ? 'bg-green-50 text-green-600 line-through' : 'bg-gray-100 text-gray-600' }}">{{ $t['title'] }}</span>
            @empty
            <span class="text-[9px] text-gray-300 self-center">—</span>
            @endforelse
        </div>
        @endforeach
        @endif

        {{-- Grid waktu mobile --}}
        <div class="overflow-y-auto tb-scroll" style="max-height:62vh">
            <div class="grid" style="grid-template-columns:52px minmax(0,1fr)">
                <div class="relative" style="height:1152px">
                    @for($h = 0; $h < 24; $h++)
                    <div class="absolute right-1.5 -translate-y-1/2 text-[9px] font-bold text-gray-300" style="top:{{ $h * 48 }}px">{{ sprintf('%02d:00', $h) }}</div>
                    @endfor
                </div>
                <div class="relative">
                    @foreach($days as $d)
                    <div class="tb-mday {{ $d === $selectedDate ? '' : 'hidden' }}" data-mday="{{ $d }}">
                        @include('pages._tb_column', ['date' => $d, 'data' => $eventsByDate[$d], 'today' => $today])
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Tooltip detail blok (hover), dirender di body via JS agar tidak terpotong ── --}}
<div id="tbTip" class="hidden fixed z-[60] max-w-xs px-3 py-2 bg-gray-900 text-white rounded-xl shadow-xl pointer-events-none">
    <p id="tbTipTitle" class="text-xs font-bold leading-snug"></p>
    <p id="tbTipTime" class="text-[11px] text-gray-300 mt-0.5"></p>
    <p id="tbTipNote" class="hidden text-[11px] text-gray-400 mt-1 whitespace-pre-line leading-relaxed"></p>
</div>

{{-- ── Modal blok waktu ── --}}
<div id="tbModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)tbClose()">
    <div class="bg-white rounded-3xl w-full max-w-md my-8">
        <div class="flex items-start justify-between gap-3 px-6 pt-6 pb-4 border-b border-gray-50">
            <h2 class="font-bold text-lg" id="tbModalTitle">{{ __('Blok Waktu Baru') }}</h2>
            <button type="button" onclick="tbClose()" class="w-8 h-8 -mr-1.5 -mt-1 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="" id="tbForm" class="px-6 pt-5 pb-6" onsubmit="return tbPrepare(this)">
            @csrf
            <input type="hidden" name="start_min" id="tbStartMin">
            <input type="hidden" name="end_min" id="tbEndMin">
            <input type="hidden" name="color" id="tbColor" value="blue">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Judul') }} <span class="text-red-400">*</span></label>
                    <input type="text" name="title" id="tbTitle" maxlength="200" required placeholder="{{ __('cth: Deep work, Meeting tim, Olahraga') }}"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tanggal') }}</label>
                    <div class="relative">
                        <input type="date" name="date" id="tbDate" required
                            class="w-full pl-3 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                @php
                    // Opsi waktu per 15 menit: value = menit dari tengah malam, label = HH:MM.
                    $timeOpts = '';
                    for ($t = 0; $t < 1440; $t += 15) $timeOpts .= '<option value="' . $t . '">' . sprintf('%02d:%02d', intdiv($t, 60), $t % 60) . '</option>';
                @endphp
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Mulai') }}</label>
                        <select id="tbStart">{!! $timeOpts !!}</select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Selesai') }}</label>
                        <select id="tbEnd">{!! $timeOpts !!}</select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Warna') }}</label>
                    <div class="flex items-center gap-2" id="tbColors">
                        @php $swatch = ['blue'=>'bg-blue-400','green'=>'bg-green-400','violet'=>'bg-violet-400','amber'=>'bg-amber-400','rose'=>'bg-rose-400','gray'=>'bg-gray-400']; @endphp
                        @foreach($colors as $c)
                        <button type="button" data-color="{{ $c }}" onclick="tbPickColor('{{ $c }}')"
                            class="tb-swatch w-7 h-7 rounded-full {{ $swatch[$c] }} ring-offset-2 transition-all"></button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Catatan') }}</label>
                    <textarea name="note" id="tbNote" rows="2" maxlength="1000" placeholder="{{ __('Opsional...') }}"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-black focus:bg-white resize-none transition-all"></textarea>
                </div>
            </div>
            <button type="submit" class="w-full mt-6 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all" id="tbSubmit">{{ __('Simpan') }}</button>
        </form>
        <form method="POST" action="" id="tbDeleteForm" class="hidden px-6 pb-5 -mt-2 text-center">
            @csrf @method('DELETE')
            <button type="button" onclick="askDelete(this, '{{ __('Hapus blok waktu ini?') }}')"
                class="text-[11px] font-bold text-gray-400 hover:text-red-500 transition-all">{{ __('Hapus blok ini') }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
var TB_URL = '{{ url('time-blocking') }}';

/* Set dropdown waktu dari menit; dibulatkan ke kelipatan 15 (sesuai opsi). */
function tbSetTime(id, min) {
    min = Math.max(0, Math.min(1425, Math.round(min / 15) * 15));
    var el = document.getElementById(id);
    el.value = String(min);
    if (window.refreshSelect) refreshSelect(el);
}
function tbGetMin(id) {
    return parseInt(document.getElementById(id).value, 10) || 0;
}

function tbJumpTo(v) { if (v) location.href = TB_URL + '?date=' + v; }

/* Set field tanggal (flatpickr) di modal */
function tbSetDate(date) {
    var el = document.getElementById('tbDate');
    if (el._flatpickr) el._flatpickr.setDate(date, false); else el.value = date;
}

/* Tooltip detail saat hover blok (untuk judul/jam yang terpotong) */
(function () {
    var t = document.getElementById('tbTip');
    if (t && t.parentElement !== document.body) document.body.appendChild(t);
})();
function tbShowTip(el) {
    var tip = document.getElementById('tbTip');
    document.getElementById('tbTipTitle').textContent = el.dataset.tipTitle || '';
    document.getElementById('tbTipTime').textContent = el.dataset.tipTime || '';
    var noteEl = document.getElementById('tbTipNote');
    if (el.dataset.tipNote) { noteEl.textContent = el.dataset.tipNote; noteEl.classList.remove('hidden'); }
    else { noteEl.classList.add('hidden'); }

    tip.classList.remove('hidden');
    var r = el.getBoundingClientRect(), tw = tip.offsetWidth, th = tip.offsetHeight, gap = 8;
    var left = r.right + gap;
    if (left + tw > window.innerWidth - gap) left = r.left - tw - gap; // balik ke kiri
    if (left < gap) left = gap;
    var top = r.top;
    if (top + th > window.innerHeight - gap) top = window.innerHeight - th - gap;
    if (top < gap) top = gap;
    tip.style.left = left + 'px';
    tip.style.top = top + 'px';
}
function tbHideTip() { document.getElementById('tbTip').classList.add('hidden'); }

function tbClose() { document.getElementById('tbModal').classList.add('hidden'); document.body.style.overflow=''; }
function tbOpen() { document.getElementById('tbModal').classList.remove('hidden'); document.body.style.overflow='hidden'; }

function tbPickColor(c) {
    document.getElementById('tbColor').value = c;
    document.querySelectorAll('#tbColors .tb-swatch').forEach(b => {
        b.classList.toggle('ring-2', b.dataset.color === c);
        b.classList.toggle('ring-gray-900', b.dataset.color === c);
    });
}

/* Buka modal untuk blok baru */
function tbAdd(date, startMin) {
    const f = document.getElementById('tbForm');
    f.action = TB_URL;
    document.getElementById('tbModalTitle').textContent = '{{ __('Blok Waktu Baru') }}';
    document.getElementById('tbSubmit').textContent = '{{ __('Simpan') }}';
    tbSetDate(date);
    document.getElementById('tbTitle').value = '';
    document.getElementById('tbNote').value = '';
    tbSetTime('tbStart', startMin);
    tbSetTime('tbEnd', Math.min(1439, startMin + 60));
    tbPickColor('blue');
    document.getElementById('tbDeleteForm').classList.add('hidden');
    tbOpen();
}

/* Buka modal untuk edit blok */
function tbEditBlock(e) {
    const f = document.getElementById('tbForm');
    f.action = TB_URL + '/' + e.id;
    document.getElementById('tbModalTitle').textContent = '{{ __('Ubah Blok Waktu') }}';
    document.getElementById('tbSubmit').textContent = '{{ __('Perbarui') }}';
    tbSetDate(e.date);
    document.getElementById('tbTitle').value = e.title || '';
    document.getElementById('tbNote').value = e.note || '';
    tbSetTime('tbStart', e.start_min);
    tbSetTime('tbEnd', e.end_min);
    tbPickColor(e.color || 'blue');
    const del = document.getElementById('tbDeleteForm');
    del.action = TB_URL + '/' + e.id;
    del.classList.remove('hidden');
    tbOpen();
}

/* Klik slot kosong → buat blok pada jam terdekat (dibulatkan 30 menit) */
function tbSlotClick(evt, col) {
    const rect = col.getBoundingClientRect();
    const y = evt.clientY - rect.top;
    let min = Math.round((y / rect.height) * 1440 / 30) * 30;
    min = Math.max(0, Math.min(1410, min));
    tbAdd(col.dataset.date, min);
}

/* Siapkan menit dari input jam sebelum submit */
function tbPrepare(form) {
    const s = tbGetMin('tbStart');
    let en = tbGetMin('tbEnd');
    if (en <= s) en = Math.min(1440, s + 15);
    document.getElementById('tbStartMin').value = s;
    document.getElementById('tbEndMin').value = en;
    return true;
}

/* Ganti hari (tampilan mobile) */
function tbSelectDay(date) {
    document.querySelectorAll('[data-mday]').forEach(el => el.classList.toggle('hidden', el.dataset.mday !== date));
    document.querySelectorAll('[data-mallday]').forEach(el => el.classList.toggle('hidden', el.dataset.mallday !== date));
    document.querySelectorAll('[data-daybtn]').forEach(b => {
        const on = b.dataset.daybtn === date;
        b.classList.toggle('bg-black', on);
        b.classList.toggle('text-white', on);
    });
}

/* Garis "sekarang" + auto-scroll ke jam aktif */
(function () {
    const now = new Date();
    const min = now.getHours() * 60 + now.getMinutes();
    document.querySelectorAll('.tb-now').forEach(el => { el.style.top = (min / 1440 * 100) + '%'; });
    // scroll semua grid ke sekitar jam sekarang (atau 07:00 kalau dini hari)
    const target = Math.max(0, (Math.max(min, 420) - 180) / 1440 * 1152);
    document.querySelectorAll('.tb-scroll').forEach(sc => { sc.scrollTop = target; });
})();
</script>
@endpush
