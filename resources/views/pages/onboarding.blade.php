<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" style="background-color:#F8F9FA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
    <title>Molife — Setup Profil</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=9">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @view-transition { navigation: auto; }
        ::view-transition-old(root), ::view-transition-new(root) { animation-duration: .18s; }
        .option-card { transition: all .18s; cursor: pointer; }
        .option-card.selected { border-color: #111827 !important; background: #111827 !important; color: #fff !important; }
        .option-card.selected .opt-icon { color: #fff !important; }
        .option-card.selected .opt-label { color: #fff !important; }
        .option-card.selected .opt-sub { color: #9ca3af !important; }
        .sport-card, .feat-card { transition: all .18s; cursor: pointer; }
        .sport-card.selected, .feat-card.selected { border-color: #111827 !important; background: #111827 !important; color: #fff !important; }
        .feat-card.selected .fc-sub { color: #9ca3af !important; }
        .gender-card { transition: all .18s; cursor: pointer; }
        .gender-card.selected { border-color: #111827 !important; background: #111827 !important; color: #fff !important; }
        .gender-card.selected svg { color: #fff !important; }
        .step-panel { display: none; }
        .step-panel.active { display: block; }
        @keyframes slideIn { from { opacity:0; transform:translateX(24px); } to { opacity:1; transform:translateX(0); } }
        .step-panel.active { animation: slideIn .3s ease; }
    </style>
</head>
<body class="min-h-screen bg-[#F8F9FA] flex flex-col items-center justify-center p-4">

<div class="w-full max-w-lg">

    {{-- Logo --}}
    <div class="flex justify-center mb-7">
        <img src="{{ asset('images/logo.png') }}" class="h-12 w-auto" alt="Molife">
    </div>

    {{-- Progress bar --}}
    <div class="flex items-center gap-2 mb-7 px-2">
        <div id="prog1" class="h-1.5 flex-1 rounded-full bg-black transition-all duration-300"></div>
        <div id="prog2" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
        <div id="prog3" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}" id="onboardingForm">
        @csrf
        <input type="hidden" name="gender"            id="hiddenGender">
        <input type="hidden" name="custom_sport_name" id="hiddenCustomName" value="">

        {{-- ── Step 1: Name ── --}}
        <div class="step-panel active bg-white rounded-3xl p-6 md:p-8 shadow-sm" id="step1">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Langkah 1 dari 3</p>
            <h2 class="text-2xl font-bold mb-1">Selamat datang di Molife 👋</h2>
            <p class="text-sm text-gray-500 mb-6">Yuk kenalan dulu — siapa namamu?</p>

            <input type="text" name="display_name" id="displayName"
                autocomplete="off"
                placeholder="Nama atau panggilanmu..."
                class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-base font-semibold outline-none focus:border-black transition-all"
                required>

            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-6 mb-2">Jenis Kelamin</p>
            <div class="grid grid-cols-2 gap-2.5">
                <div class="gender-card flex items-center justify-center gap-2 p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-gray-300" onclick="selectGender('male', this)">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="10" cy="14" r="6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.5 9.5L20 4m0 0h-5m5 0v5"/></svg>
                    <span class="font-bold text-sm">Laki-laki</span>
                </div>
                <div class="gender-card flex items-center justify-center gap-2 p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-gray-300" onclick="selectGender('female', this)">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="9" r="6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v6m-3-3h6"/></svg>
                    <span class="font-bold text-sm">Perempuan</span>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">Dipakai untuk menyesuaikan fitur (mis. penanganan hari uzur di tracker sholat).</p>

            <button type="button" onclick="goStep(2)"
                class="w-full mt-6 py-4 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all">
                Lanjut
            </button>
        </div>

        {{-- ── Step 2: Sports ── --}}
        <div class="step-panel bg-white rounded-3xl p-6 md:p-8 shadow-sm" id="step2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Langkah 2 dari 3</p>
            <h2 class="text-2xl font-bold mb-1">Olahraga apa saja?</h2>
            <p class="text-sm text-gray-500 mb-6">Pilih yang kamu lakukan. Bisa lebih dari satu, atau lewati jika tidak ada.</p>

            @php
            $sportOptions = [
                ['value'=>'gym',         'label'=>'Gym / Fitness',       'sub'=>'Latihan beban, kardio',    'icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
                ['value'=>'run',         'label'=>'Lari / Running',      'sub'=>'Outdoor, treadmill',       'icon'=>'M22 12h-4l-3 9L9 3l-3 9H2'],
                ['value'=>'cycling',     'label'=>'Bersepeda',           'sub'=>'Road bike, MTB, sepeda santai', 'icon'=>'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
                ['value'=>'swimming',    'label'=>'Renang',              'sub'=>'Kolam renang, open water', 'icon'=>'M7 16.5c2 1 4 1 6 0s4-1 6 0M7 11.5c2 1 4 1 6 0s4-1 6 0M3 7.5c2 1 4 1 6 0s4-1 6 0m-9 11V7.5'],
                ['value'=>'racket',      'label'=>'Tenis / Badminton',   'sub'=>'Padel, squash, dan lainnya','icon'=>'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z'],
                ['value'=>'custom_sport','label'=>'Olahraga Lainnya',   'sub'=>'Voli, basket, sepak bola, dll.', 'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
            ];
            @endphp

            <div class="grid grid-cols-2 gap-2.5 mb-4">
                @foreach($sportOptions as $sp)
                <div class="sport-card flex flex-col items-start gap-2 p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-gray-300"
                     onclick="toggleSport('{{ $sp['value'] }}', this)">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sp['icon'] }}"/>
                    </svg>
                    <div>
                        <p class="font-bold text-xs leading-tight">{{ $sp['label'] }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 leading-tight">{{ $sp['sub'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Custom sport name input (shows when 'custom_sport' is selected) --}}
            <div id="customSportInput" class="hidden mb-4">
                <input type="text" id="customSportName" placeholder="Nama olahraga kamu (mis: Voli, Padel...)"
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-semibold outline-none focus:border-black transition-all"
                    oninput="document.getElementById('hiddenCustomName').value = this.value">
            </div>

            <div class="flex gap-3 mt-2">
                <button type="button" onclick="goStep(1)" class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">Kembali</button>
                <button type="button" onclick="goStep(3)" class="flex-[2] py-4 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all">Lanjut</button>
            </div>
        </div>

        {{-- ── Step 3: Other features ── --}}
        <div class="step-panel bg-white rounded-3xl p-6 md:p-8 shadow-sm" id="step3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Langkah 3 dari 3</p>
            <h2 class="text-2xl font-bold mb-1">Fitur yang mau kamu pakai</h2>
            <p class="text-sm text-gray-500 mb-6">Aktifkan yang relevan. Semua bisa diubah lagi di Pengaturan.</p>

            @php
            $featOptions = [
                ['value'=>'tasks',    'label'=>'Tasks & Notes', 'sub'=>'To-do harian & catatan',        'on'=>true,  'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['value'=>'pomodoro', 'label'=>'Pomodoro',      'sub'=>'Timer fokus produktif',          'on'=>true,  'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['value'=>'mental',   'label'=>'Mental',        'sub'=>'Mood & refleksi harian',         'on'=>true,  'icon'=>'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['value'=>'motivasi', 'label'=>'Motivasi',      'sub'=>'Quote & dampak konsistensi',     'on'=>true,  'icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z'],
                ['value'=>'finance',  'label'=>'Finance',       'sub'=>'Transaksi, anggaran, tabungan',  'on'=>true,  'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['value'=>'lamaran',  'label'=>'Karir & Lamaran','sub'=>'Lacak lamaran kerja',           'on'=>true,  'icon'=>'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['value'=>'intimasi', 'label'=>'Intimasi',      'sub'=>'Tracker bersama pasangan',       'on'=>false, 'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                ['value'=>'porn',     'label'=>'Stop Porn',     'sub'=>'Streak bebas pornografi',        'on'=>false, 'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['value'=>'sosmed',   'label'=>'Kurangi Sosmed','sub'=>'Disiplin waktu media sosial',    'on'=>false, 'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
            ];
            @endphp

            <div class="grid grid-cols-2 gap-2.5 mb-2 max-h-[42vh] overflow-y-auto pr-1">
                @foreach($featOptions as $f)
                <div class="feat-card flex flex-col items-start gap-2 p-4 rounded-2xl border-2 {{ $f['on'] ? 'selected border-gray-900 bg-gray-900 text-white' : 'border-gray-100 bg-gray-50' }} hover:border-gray-300"
                     data-feat="{{ $f['value'] }}" data-on="{{ $f['on'] ? '1' : '0' }}" onclick="toggleFeat(this)">
                    <svg class="w-5 h-5 {{ $f['on'] ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                    </svg>
                    <div>
                        <p class="font-bold text-xs leading-tight">{{ $f['label'] }}</p>
                        <p class="text-[10px] mt-0.5 leading-tight fc-sub {{ $f['on'] ? 'text-gray-400' : 'text-gray-400' }}">{{ $f['sub'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex gap-3 mt-5">
                <button type="button" onclick="goStep(2)" class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">Kembali</button>
                <button type="submit" id="btnFinish"
                    class="flex-[2] py-4 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all">
                    Mulai Sekarang
                </button>
            </div>
            <p class="text-center text-xs text-gray-400 mt-3">Bisa diubah kapan saja di Pengaturan.</p>
        </div>

    </form>
</div>

<script>
let currentStep = 1;
const MAX_STEP = 3;
let selectedGender = '';
let selectedSports = new Set();

function selectGender(value, card) {
    document.querySelectorAll('.gender-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    selectedGender = value;
    document.getElementById('hiddenGender').value = value;
}

function goStep(n) {
    if (n > 1) {
        const name = document.getElementById('displayName').value.trim();
        if (!name) {
            document.getElementById('displayName').focus();
            document.getElementById('displayName').style.borderColor = '#ef4444';
            return;
        }
        document.getElementById('displayName').style.borderColor = '';
        if (!selectedGender) {
            document.querySelectorAll('.gender-card').forEach(c => c.style.borderColor = '#ef4444');
            return;
        }
        document.querySelectorAll('.gender-card').forEach(c => c.style.borderColor = '');
    }

    document.getElementById('step' + currentStep).classList.remove('active');
    document.getElementById('step' + n).classList.add('active');
    currentStep = n;

    for (let i = 1; i <= MAX_STEP; i++) {
        document.getElementById('prog' + i).style.background = (i <= n) ? '#111827' : '#e5e7eb';
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function toggleSport(value, card) {
    if (selectedSports.has(value)) { selectedSports.delete(value); card.classList.remove('selected'); }
    else { selectedSports.add(value); card.classList.add('selected'); }
    document.getElementById('customSportInput').style.display = selectedSports.has('custom_sport') ? 'block' : 'none';
    document.querySelectorAll('.hidden-sport-input').forEach(el => el.remove());
    selectedSports.forEach(sport => {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = 'sports[]'; input.value = sport; input.className = 'hidden-sport-input';
        document.getElementById('onboardingForm').appendChild(input);
    });
}

function toggleFeat(card) {
    const on = card.dataset.on !== '1';
    card.dataset.on = on ? '1' : '0';
    card.classList.toggle('selected', on);
    const svg = card.querySelector('svg');
    if (svg) svg.classList.toggle('text-white', on), svg.classList.toggle('text-gray-500', !on);
    syncFeats();
}

function syncFeats() {
    document.querySelectorAll('.hidden-feat-input').forEach(el => el.remove());
    document.querySelectorAll('.feat-card').forEach(card => {
        if (card.dataset.on === '1') {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'features[]'; input.value = card.dataset.feat; input.className = 'hidden-feat-input';
            document.getElementById('onboardingForm').appendChild(input);
        }
    });
}
syncFeats(); // seed defaults

document.getElementById('displayName').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); goStep(2); }
});
</script>
</body>
</html>
