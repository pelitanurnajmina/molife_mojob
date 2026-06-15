@extends('layouts.app')
@section('title', 'Pomodoro')
@section('page-title', 'Pomodoro')
@section('breadcrumb', 'Life › Pomodoro')

@section('content')
<style>
    @keyframes pomoAurora1 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(40px,-30px) scale(1.25); } }
    @keyframes pomoAurora2 { 0%,100% { transform: translate(0,0) scale(1.1); } 50% { transform: translate(-50px,30px) scale(1); } }
    @keyframes pomoFloat  { 0% { transform: translateY(0); opacity:0; } 10% { opacity:.7; } 90% { opacity:.6; } 100% { transform: translateY(-160px); opacity:0; } }
    @keyframes pomoBreath { 0%,100% { transform: scale(1); opacity:.35; } 50% { transform: scale(1.06); opacity:.7; } }
    @keyframes pomoSpin   { to { transform: rotate(360deg); } }
    .pomo-aurora { position:absolute; border-radius:9999px; filter: blur(60px); opacity:.5; pointer-events:none; }
    .pomo-firefly { position:absolute; bottom:-8px; width:4px; height:4px; border-radius:9999px; background: rgba(167,243,208,.9);
        box-shadow:0 0 8px 2px rgba(110,231,183,.6); animation: pomoFloat linear infinite; pointer-events:none; }
    .pomo-glow { animation: pomoBreath 4s ease-in-out infinite; }
    .pomo-running .pomo-glow { animation: pomoSpin 8s linear infinite; opacity:.6; }
</style>
<div class="space-y-4 md:space-y-6">

    {{-- ── Timer ── --}}
    <div id="pomoHero" class="bg-gradient-to-br from-emerald-950 via-gray-900 to-teal-900 rounded-2xl md:rounded-3xl p-6 md:p-10 text-white relative overflow-hidden">
        {{-- Ambient aurora --}}
        <div class="pomo-aurora" style="width:280px;height:280px;top:-80px;left:-60px;background:radial-gradient(circle,#10b981,transparent 70%);animation:pomoAurora1 14s ease-in-out infinite"></div>
        <div class="pomo-aurora" style="width:240px;height:240px;bottom:-90px;right:-40px;background:radial-gradient(circle,#14b8a6,transparent 70%);animation:pomoAurora2 18s ease-in-out infinite"></div>
        <div class="pomo-aurora" style="width:200px;height:200px;top:40%;left:55%;background:radial-gradient(circle,#34d399,transparent 70%);animation:pomoAurora1 22s ease-in-out infinite reverse"></div>

        {{-- Fireflies --}}
        @for($i = 0; $i < 14; $i++)
        <div class="pomo-firefly" style="left:{{ rand(4, 96) }}%;animation-duration:{{ rand(7, 14) }}s;animation-delay:-{{ rand(0, 12) }}s;opacity:{{ rand(3,8)/10 }}"></div>
        @endfor

        <div class="relative text-center">
            {{-- Mode tabs --}}
            <div class="inline-flex bg-white/10 rounded-full p-1 mb-7">
                <button type="button" id="modeFocus" onclick="setMode('focus')" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-white text-gray-900">{{ __('Fokus') }}</button>
                <button type="button" id="modeBreak" onclick="setMode('break')" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all text-white/70">{{ __('Istirahat') }}</button>
            </div>

            {{-- Ring + time --}}
            <div class="relative w-56 h-56 md:w-64 md:h-64 mx-auto">
                {{-- Breathing glow behind the ring --}}
                <div class="pomo-glow absolute inset-2 rounded-full" style="background:radial-gradient(circle, rgba(52,211,153,.35), transparent 65%)"></div>
                <svg class="w-full h-full -rotate-90 relative" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="2.5"/>
                    <circle id="progressRing" cx="18" cy="18" r="15.9155" fill="none" stroke="#6ee7b7" stroke-width="2.5"
                        stroke-dasharray="100 100" stroke-dashoffset="0" stroke-linecap="round" style="transition:stroke-dashoffset .3s linear"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <p id="timerDisplay" class="text-5xl md:text-6xl font-black tabular-nums">25:00</p>
                    <p id="timerLabel" class="text-xs font-bold uppercase tracking-widest text-white/50 mt-1">{{ __('Fokus') }}</p>
                </div>
            </div>

            {{-- Task label --}}
            <input type="text" id="taskLabel" maxlength="120" placeholder="{{ __('Sedang mengerjakan apa? (opsional)') }}"
                class="w-full max-w-xs mx-auto mt-6 px-4 py-2.5 bg-white/10 border border-white/15 rounded-xl text-sm text-white placeholder-white/40 text-center outline-none focus:border-white/40 transition-all">

            {{-- Controls --}}
            <div class="flex items-center justify-center gap-3 mt-6">
                <button type="button" id="startBtn" onclick="toggleTimer()"
                    class="px-8 py-3 rounded-xl bg-white text-gray-900 text-sm font-bold hover:bg-gray-100 transition-all">{{ __('Mulai') }}</button>
                <button type="button" onclick="resetTimer()"
                    class="px-5 py-3 rounded-xl border border-white/20 text-sm font-bold text-white/80 hover:bg-white/10 transition-all">{{ __('Reset') }}</button>
            </div>

            {{-- Duration presets --}}
            <div class="flex items-center justify-center gap-2 mt-6 text-[11px]">
                <span class="text-white/40 font-bold uppercase tracking-wide">{{ __('Durasi fokus') }}:</span>
                @foreach([15,25,30,45,60] as $m)
                <button type="button" onclick="setFocusDuration({{ $m }})" data-dur="{{ $m }}"
                    class="dur-btn px-2.5 py-1 rounded-full font-bold transition-all {{ $m === 25 ? 'bg-white/20 text-white' : 'text-white/50 hover:bg-white/10' }}">{{ $m }}m</button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-rose-600" id="statTodayCount">{{ $todayCount }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Sesi Hari Ini') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-gray-800" id="statTodayMin">{{ $todayMinutes }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Menit Fokus Hari Ini') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-gray-800">{{ $weekCount }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Sesi Minggu Ini') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-gray-800">{{ $totalCount }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Total Sesi') }}</p>
        </div>
    </div>

    {{-- ── Week chart ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4">{{ __('Sesi Fokus Minggu Ini') }}</h3>
        <div style="height:180px"><canvas id="pomoChart"></canvas></div>
    </div>

    {{-- ── History ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4">{{ __('Riwayat Sesi') }}</h3>
        <div id="pomoHistory" class="space-y-2">
            @forelse($history as $h)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-700 truncate">{{ $h['label'] ?: __('Sesi fokus') }}</p>
                    <p class="text-xs text-gray-400">{{ $h['when'] }}</p>
                </div>
                <span class="text-sm font-bold text-rose-600 flex-shrink-0">{{ $h['minutes'] }}m</span>
            </div>
            @empty
            <p id="pomoEmpty" class="text-center text-gray-400 text-sm py-8">{{ __('Belum ada sesi. Mulai fokus pertamamu!') }}</p>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
/* The timer engine lives globally in the layout (window.MojobPomodoro) so it
   keeps running across page navigation. This page only reflects + controls it. */
const P = window.MojobPomodoro;

const disp     = document.getElementById('timerDisplay');
const ring     = document.getElementById('progressRing');
const label    = document.getElementById('timerLabel');
const startBtn = document.getElementById('startBtn');
const hero     = document.getElementById('pomoHero');
const taskInput = document.getElementById('taskLabel');

function fmt(s) { const m = Math.floor(s/60), x = s%60; return String(m).padStart(2,'0') + ':' + String(x).padStart(2,'0'); }

function syncUI(st) {
    const total = (st.mode === 'focus' ? st.focusMin : st.breakMin) * 60;
    disp.textContent = fmt(st.remaining);
    ring.setAttribute('stroke-dashoffset', (100 - (total > 0 ? (st.remaining/total)*100 : 0)).toFixed(2));
    label.textContent = st.mode === 'focus' ? '{{ __('Fokus') }}' : '{{ __('Istirahat') }}';
    startBtn.textContent = st.running ? '{{ __('Jeda') }}' : '{{ __('Mulai') }}';
    hero.classList.toggle('pomo-running', st.running);

    document.getElementById('modeFocus').className = 'px-4 py-1.5 rounded-full text-xs font-bold transition-all ' + (st.mode === 'focus' ? 'bg-white text-gray-900' : 'text-white/70');
    document.getElementById('modeBreak').className = 'px-4 py-1.5 rounded-full text-xs font-bold transition-all ' + (st.mode === 'break' ? 'bg-white text-gray-900' : 'text-white/70');

    document.querySelectorAll('.dur-btn').forEach(b => {
        b.className = 'dur-btn px-2.5 py-1 rounded-full font-bold transition-all ' + (parseInt(b.dataset.dur) === st.focusMin ? 'bg-white/20 text-white' : 'text-white/50 hover:bg-white/10');
    });
}

window.toggleTimer = function() { P.toggle(); };
window.resetTimer = function() { P.reset(); };
window.setMode = function(m) { P.setMode(m); };
window.setFocusDuration = function(min) { P.setFocus(min); };

taskInput.addEventListener('input', () => P.setLabel(taskInput.value.trim()));

/* Keep UI in sync with the global engine */
document.addEventListener('pomo:tick', e => syncUI(e.detail));
document.addEventListener('pomo:change', e => syncUI(e.detail));

/* When a focus session completes anywhere, update stats + history here */
document.addEventListener('pomo:saved', e => {
    const { minutes, label } = e.detail;
    const tc = document.getElementById('statTodayCount');
    const tm = document.getElementById('statTodayMin');
    if (tc) tc.textContent = parseInt(tc.textContent) + 1;
    if (tm) tm.textContent = parseInt(tm.textContent) + minutes;

    const empty = document.getElementById('pomoEmpty');
    if (empty) empty.remove();
    const now = new Date();
    const when = now.toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) + ' · ' +
                 String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3 p-3 rounded-xl bg-gray-50';
    row.innerHTML = `
        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-700 truncate">${label || '{{ __('Sesi fokus') }}'}</p>
            <p class="text-xs text-gray-400">${when}</p>
        </div>
        <span class="text-sm font-bold text-rose-600 flex-shrink-0">${minutes}m</span>`;
    document.getElementById('pomoHistory').prepend(row);
});

/* Initial paint from the persisted engine state */
syncUI(P.get());
if (P.get().label) taskInput.value = P.get().label;

/* Week chart */
new Chart(document.getElementById('pomoChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['{{ __('Sen') }}','{{ __('Sel') }}','{{ __('Rab') }}','{{ __('Kam') }}','{{ __('Jum') }}','{{ __('Sab') }}','{{ __('Min') }}'],
        datasets: [{ label: '{{ __('Sesi') }}', data: @json($weekData), backgroundColor: 'rgba(244,63,94,0.7)', borderRadius: 6 }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } }, grid: { color: '#f9fafb' } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        },
    },
});
</script>
@endpush
