@extends('layouts.app')
@section('title', 'Meditasi')
@section('page-title', __('Meditasi'))
@section('breadcrumb', 'Life › Meditasi')

@section('content')
<style>
    @keyframes breatheCircle {
        0%, 100% { transform: scale(0.62); }
        50%      { transform: scale(1); }
    }
    .breathe-anim { animation: breatheCircle 10s ease-in-out infinite; }
</style>

<div class="space-y-4 md:space-y-6">

    {{-- ── Statistik ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none" id="statStreak">{{ $stats['streak'] }}<span class="text-base text-gray-400"> {{ __('hari') }}</span></p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Streak Meditasi') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ __('terbaik :n hari', ['n' => $stats['best']]) }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none"><span id="statToday">{{ $stats['todayMinutes'] }}</span><span class="text-base text-gray-400">/{{ $stats['goal'] }} {{ __('mnt') }}</span></p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Hari Ini') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ __('target :n menit per hari', ['n' => $stats['goal']]) }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none" id="statWeek">{{ $stats['weekMinutes'] }}<span class="text-base text-gray-400"> {{ __('mnt') }}</span></p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Minggu Ini') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ __('dari semua sesi') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none" id="statTotal">{{ $stats['totalMinutes'] }}<span class="text-base text-gray-400"> {{ __('mnt') }}</span></p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Total Meditasi') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5"><span id="statSessions">{{ $stats['totalSessions'] }}</span> {{ __('sesi tercatat') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 md:gap-6">

        {{-- ── Timer ── --}}
        <div class="lg:col-span-3 dash-card bg-white rounded-2xl md:rounded-3xl border border-gray-50 overflow-hidden">

            {{-- Setup --}}
            <div id="medSetup" class="p-5 md:p-8">
                <h3 class="font-bold">{{ __('Mulai Sesi Meditasi') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5 mb-5">{{ __('Duduk nyaman, pejamkan mata, ikuti irama napas. 10 menit sehari sudah terbukti menenangkan pikiran.') }}</p>

                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">{{ __('Durasi') }}</p>
                <div class="flex flex-wrap gap-2 mb-5">
                    @foreach([5, 10, 15, 20, 30] as $m)
                    <button type="button" data-min="{{ $m }}" onclick="pickDur(this)"
                        class="dur-chip text-xs font-bold px-4 py-2.5 rounded-xl transition-all {{ $m === 10 ? 'bg-black text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-300' }}">
                        {{ $m }} {{ __('mnt') }}
                    </button>
                    @endforeach
                    <input type="number" id="durCustom" min="1" max="180" placeholder="{{ __('lainnya') }}"
                        oninput="pickCustom()" class="w-24 px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold outline-none focus:border-black transition-all">
                </div>

                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">{{ __('Suara Latar') }}</p>
                <div class="flex flex-wrap gap-2 mb-5">
                    @foreach([['hening', 'Hening'], ['hujan', 'Hujan'], ['ombak', 'Ombak'], ['angin', 'Angin']] as [$key, $label])
                    <button type="button" data-sound="{{ $key }}" onclick="pickSound(this)"
                        class="snd-chip text-xs font-bold px-4 py-2.5 rounded-xl transition-all {{ $key === 'hujan' ? 'bg-black text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-300' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide flex-shrink-0">{{ __('Volume') }}</p>
                    <input type="range" id="medVol" min="0" max="100" value="60" oninput="setVol(this.value)" class="flex-1 accent-gray-900">
                </div>

                <button type="button" onclick="startMed()"
                    class="w-full py-4 rounded-2xl bg-gray-900 text-white text-sm font-bold hover:bg-gray-800 transition-all">
                    {{ __('Mulai Meditasi') }}
                </button>
            </div>

        </div>

        {{-- ── Riwayat ── --}}
        <div class="lg:col-span-2 dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
            <h3 class="font-bold mb-1">{{ __('Riwayat Sesi') }}</h3>
            <p class="text-xs text-gray-400 mb-4">{{ __('Konsistensi harian lebih berharga daripada durasi panjang.') }}</p>

            @if(count($history) === 0)
            <div class="text-center py-10">
                <div class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </div>
                <p class="text-sm text-gray-400">{{ __('Belum ada sesi. Mulai yang pertama hari ini!') }}</p>
            </div>
            @else
            @php $soundLabel = ['hening' => 'Hening', 'hujan' => 'Hujan', 'ombak' => 'Ombak', 'angin' => 'Angin']; @endphp
            <div class="space-y-2">
                @foreach($history as $s)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-teal-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800">{{ $s->minutes }} {{ __('menit') }}</p>
                        <p class="text-[11px] text-gray-400">{{ $s->date->translatedFormat('l, j M') }}{{ $s->sound ? ' · ' . ($soundLabel[$s->sound] ?? $s->sound) : '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('meditasi.destroy', $s->id) }}" class="m-0">
                        @csrf @method('DELETE')
                        <button type="button" onclick="askDelete(this, '{{ __('Hapus sesi ini dari riwayat?') }}')"
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

</div>

{{-- ── Overlay imersif: sesi berjalan + selesai (layar penuh, latar blur) ── --}}
<div id="medOverlay" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/70 backdrop-blur-lg">

    {{-- Berjalan --}}
    <div id="medRun" class="hidden w-full max-w-xl text-white text-center">
        <div class="relative w-72 h-72 md:w-80 md:h-80 mx-auto">
            {{-- Lingkaran napas --}}
            <div class="absolute inset-8 rounded-full bg-teal-400/15 breathe-anim"></div>
            <div class="absolute inset-14 rounded-full bg-teal-400/20 breathe-anim"></div>
            {{-- Ring progres --}}
            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 256 256">
                <circle cx="128" cy="128" r="118" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="5"/>
                <circle id="medRing" cx="128" cy="128" r="118" fill="none" stroke="#2dd4bf" stroke-width="5"
                    stroke-linecap="round" stroke-dasharray="741.4" stroke-dashoffset="0" style="transition: stroke-dashoffset .3s linear"/>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <p id="medClock" class="text-5xl md:text-6xl font-black tracking-tight">10:00</p>
                <p id="medBreath" class="text-xs font-bold text-teal-300 mt-3 uppercase tracking-widest">{{ __('Tarik napas...') }}</p>
            </div>
        </div>
        <p class="text-sm text-white/50 mt-8 mb-8">{{ __('Fokus pada napasmu. Biarkan pikiran datang dan pergi.') }}</p>
        <button type="button" onclick="stopMed()"
            class="px-7 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white text-xs font-bold hover:bg-white/20 transition-all">
            {{ __('Hentikan Sesi') }}
        </button>
    </div>

    {{-- Selesai --}}
    <div id="medDone" class="hidden w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 md:p-10 text-center">
        <div class="w-16 h-16 rounded-full bg-teal-50 text-teal-500 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="font-black text-xl" id="medDoneTitle">{{ __('Sesi Selesai!') }}</h3>
        <p class="text-sm text-gray-400 mt-1 mb-6" id="medDoneSub"></p>
        <button type="button" onclick="location.reload()"
            class="px-6 py-3 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition-all">
            {{ __('Kembali') }}
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* Overlay harus anak langsung <body>: kontainer konten punya transform dari
   animasi transisi halaman, yang mengurung elemen fixed di dalamnya. */
document.body.appendChild(document.getElementById('medOverlay'));

/* ═══════════ Suara alam via Web Audio (disintesis, tanpa file audio) ═══════════ */
let AC = null, master = null, ambient = [];

function actx() {
    if (!AC) {
        AC = new (window.AudioContext || window.webkitAudioContext)();
        master = AC.createGain();
        master.gain.value = document.getElementById('medVol').value / 100;
        master.connect(AC.destination);
    }
    if (AC.state === 'suspended') AC.resume();
    return AC;
}

function setVol(v) { if (master) master.gain.value = v / 100; }

function noiseSource(ctx) {
    const len = ctx.sampleRate * 2;
    const buf = ctx.createBuffer(1, len, ctx.sampleRate);
    const d = buf.getChannelData(0);
    for (let i = 0; i < len; i++) d[i] = Math.random() * 2 - 1;
    const src = ctx.createBufferSource();
    src.buffer = buf; src.loop = true;
    return src;
}

/* Gong lonceng (partial inharmonis + peluruhan panjang, khas singing bowl) */
function gong() {
    const ctx = actx(), t = ctx.currentTime, base = 196;
    [[1, .5], [2.02, .28], [2.92, .17], [4.16, .09]].forEach(([ratio, amp]) => {
        const o = ctx.createOscillator(), g = ctx.createGain();
        o.type = 'sine'; o.frequency.value = base * ratio;
        g.gain.setValueAtTime(amp, t);
        g.gain.exponentialRampToValueAtTime(0.0001, t + 6);
        o.connect(g); g.connect(master);
        o.start(t); o.stop(t + 6.2);
    });
}

function startAmbient(type) {
    stopAmbient();
    if (type === 'hening') return;
    const ctx = actx();
    const src = noiseSource(ctx);
    const gain = ctx.createGain();

    if (type === 'hujan') {
        const lp = ctx.createBiquadFilter();
        lp.type = 'lowpass'; lp.frequency.value = 950;
        gain.gain.value = .5;
        src.connect(lp); lp.connect(gain);
        ambient = [src, lp, gain];
    } else if (type === 'ombak') {
        const lp = ctx.createBiquadFilter();
        lp.type = 'lowpass'; lp.frequency.value = 520;
        gain.gain.value = .45;
        const lfo = ctx.createOscillator(), lfoG = ctx.createGain();
        lfo.frequency.value = .08; lfoG.gain.value = .3;   // gelombang datang & pergi
        lfo.connect(lfoG); lfoG.connect(gain.gain); lfo.start();
        src.connect(lp); lp.connect(gain);
        ambient = [src, lp, gain, lfo, lfoG];
    } else if (type === 'angin') {
        const bp = ctx.createBiquadFilter();
        bp.type = 'bandpass'; bp.frequency.value = 420; bp.Q.value = .6;
        gain.gain.value = .55;
        const lfo = ctx.createOscillator(), lfoG = ctx.createGain();
        lfo.frequency.value = .06; lfoG.gain.value = 190;  // hembusan naik-turun
        lfo.connect(lfoG); lfoG.connect(bp.frequency); lfo.start();
        src.connect(bp); bp.connect(gain);
        ambient = [src, bp, gain, lfo, lfoG];
    }

    gain.connect(master);
    src.start();
}

function stopAmbient() {
    ambient.forEach(n => { try { if (n.stop) n.stop(); n.disconnect(); } catch (e) {} });
    ambient = [];
}

/* ═══════════ Timer (berbasis timestamp, tahan tab tidak aktif) ═══════════ */
const RING_LEN = 741.4;
let med = { running: false, dur: 10, sound: 'hujan', startTs: 0, endTs: 0, iv: null, breathIv: null };

function pickDur(btn) {
    document.getElementById('durCustom').value = '';
    document.querySelectorAll('.dur-chip').forEach(b => b.className = 'dur-chip text-xs font-bold px-4 py-2.5 rounded-xl transition-all bg-white text-gray-500 border border-gray-200 hover:border-gray-300');
    btn.className = 'dur-chip text-xs font-bold px-4 py-2.5 rounded-xl transition-all bg-black text-white';
    med.dur = parseInt(btn.dataset.min);
}
function pickCustom() {
    const v = parseInt(document.getElementById('durCustom').value);
    if (v >= 1 && v <= 180) {
        document.querySelectorAll('.dur-chip').forEach(b => b.className = 'dur-chip text-xs font-bold px-4 py-2.5 rounded-xl transition-all bg-white text-gray-500 border border-gray-200 hover:border-gray-300');
        med.dur = v;
    }
}
function pickSound(btn) {
    document.querySelectorAll('.snd-chip').forEach(b => b.className = 'snd-chip text-xs font-bold px-4 py-2.5 rounded-xl transition-all bg-white text-gray-500 border border-gray-200 hover:border-gray-300');
    btn.className = 'snd-chip text-xs font-bold px-4 py-2.5 rounded-xl transition-all bg-black text-white';
    med.sound = btn.dataset.sound;
}

function startMed() {
    med.running = true;
    med.startTs = Date.now();
    med.endTs = med.startTs + med.dur * 60000;

    // Mode imersif: overlay layar penuh, latar (termasuk sidebar) diblur.
    document.getElementById('medOverlay').classList.remove('hidden');
    document.getElementById('medRun').classList.remove('hidden');
    document.getElementById('medDone').classList.add('hidden');
    document.body.style.overflow = 'hidden';

    gong();
    startAmbient(med.sound);

    tick();
    med.iv = setInterval(tick, 300);

    // Panduan napas: 10 detik per siklus (5 tarik, 5 hembus), sinkron dengan animasi.
    const label = document.getElementById('medBreath');
    let inhale = true;
    label.textContent = '{{ __('Tarik napas...') }}';
    med.breathIv = setInterval(() => {
        inhale = !inhale;
        label.textContent = inhale ? '{{ __('Tarik napas...') }}' : '{{ __('Hembuskan perlahan...') }}';
    }, 5000);
}

function tick() {
    const remain = Math.max(0, med.endTs - Date.now());
    const totalMs = med.dur * 60000;
    const mm = Math.floor(remain / 60000), ss = Math.floor((remain % 60000) / 1000);
    document.getElementById('medClock').textContent = String(mm).padStart(2, '0') + ':' + String(ss).padStart(2, '0');
    document.getElementById('medRing').style.strokeDashoffset = (RING_LEN * (1 - remain / totalMs)).toFixed(1);
    if (remain <= 0) endSession(true);
}

function stopMed() { if (med.running) endSession(false); }

async function endSession(completed) {
    clearInterval(med.iv); clearInterval(med.breathIv);
    med.running = false;
    stopAmbient();
    gong();

    const elapsedMin = completed ? med.dur : Math.floor((Date.now() - med.startTs) / 60000);

    document.getElementById('medRun').classList.add('hidden');

    if (elapsedMin < 1) {
        // Terlalu singkat, tidak dicatat: tutup overlay, kembali ke pengaturan.
        document.getElementById('medOverlay').classList.add('hidden');
        document.body.style.overflow = '';
        if (window.showMojobToast) showMojobToast('{{ __('Sesi kurang dari 1 menit, tidak dicatat.') }}');
        return;
    }

    document.getElementById('medDone').classList.remove('hidden');
    document.getElementById('medDoneTitle').textContent = completed ? '{{ __('Sesi Selesai!') }}' : '{{ __('Sesi Dihentikan') }}';
    document.getElementById('medDoneSub').textContent = '{{ __('Mencatat sesi...') }}';

    try {
        const res = await fetch('{{ route('meditasi.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ minutes: elapsedMin, sound: med.sound }),
        });
        const d = await res.json();
        if (res.ok && d.ok) {
            const s = d.stats;
            document.getElementById('medDoneSub').textContent = elapsedMin + ' {{ __('menit tercatat. Streak kamu sekarang') }} ' + s.streak + ' {{ __('hari.') }}';
            document.getElementById('statStreak').childNodes[0].textContent = s.streak;
            document.getElementById('statToday').textContent = s.todayMinutes;
            document.getElementById('statWeek').childNodes[0].textContent = s.weekMinutes;
            document.getElementById('statTotal').childNodes[0].textContent = s.totalMinutes;
            document.getElementById('statSessions').textContent = s.totalSessions;
        } else {
            document.getElementById('medDoneSub').textContent = '{{ __('Gagal mencatat sesi. Coba lagi nanti.') }}';
        }
    } catch (e) {
        document.getElementById('medDoneSub').textContent = '{{ __('Gagal terhubung. Sesi tidak tercatat.') }}';
    }
}

// Jaga-jaga: user meninggalkan halaman saat sesi berjalan.
window.addEventListener('beforeunload', function (e) {
    if (med.running) { e.preventDefault(); e.returnValue = ''; }
});
</script>
@endpush
