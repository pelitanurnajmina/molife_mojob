<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Profil — Mojob</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .option-card { transition: all .18s; cursor: pointer; }
        .option-card.selected { border-color: #111827 !important; background: #111827 !important; color: #fff !important; }
        .option-card.selected .opt-icon { color: #fff !important; }
        .option-card.selected .opt-label { color: #fff !important; }
        .option-card.selected .opt-sub { color: #9ca3af !important; }
        .sport-card { transition: all .18s; cursor: pointer; }
        .sport-card.selected { border-color: #111827 !important; background: #111827 !important; color: #fff !important; }
        .step-panel { display: none; }
        .step-panel.active { display: block; }
        @keyframes slideIn { from { opacity:0; transform:translateX(24px); } to { opacity:1; transform:translateX(0); } }
        .step-panel.active { animation: slideIn .3s ease; }
    </style>
</head>
<body class="min-h-screen bg-[#F8F9FA] flex flex-col items-center justify-center p-4">

<div class="w-full max-w-lg">

    {{-- Logo --}}
    <div class="flex justify-center mb-8">
        <svg class="h-10 w-auto" viewBox="0 0 260 85" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="mjGrad" x1="0" y1="1" x2="1" y2="0" gradientUnits="objectBoundingBox">
                    <stop offset="0%" stop-color="#c94b00"/>
                    <stop offset="50%" stop-color="#ef7015"/>
                    <stop offset="100%" stop-color="#f9b418"/>
                </linearGradient>
            </defs>
            <path d="M 8 58 C 3 74 22 83 48 74 C 64 68 75 55 70 46 C 65 37 50 44 36 54 C 22 64 10 62 8 58 Z" fill="url(#mjGrad)"/>
            <ellipse cx="37" cy="25" rx="15" ry="23" transform="rotate(-10 37 25)" fill="#111111"/>
            <ellipse cx="55" cy="56" rx="11" ry="16" transform="rotate(8 55 56)" fill="#111111"/>
            <text x="90" y="60" font-family="'Plus Jakarta Sans','Arial Black',sans-serif" font-weight="800" font-size="50" fill="#111111">Mojob</text>
        </svg>
    </div>

    {{-- Progress bar --}}
    <div class="flex items-center gap-2 mb-8 px-2">
        <div id="prog1" class="h-1.5 flex-1 rounded-full bg-black transition-all duration-300"></div>
        <div id="prog2" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
        <div id="prog3" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}" id="onboardingForm">
        @csrf
        <input type="hidden" name="religion"          id="hiddenReligion">
        <input type="hidden" name="sports"             id="hiddenSports" value="">
        <input type="hidden" name="custom_sport_name" id="hiddenCustomName" value="">

        {{-- ── Step 1: Name ── --}}
        <div class="step-panel active bg-white rounded-3xl p-6 md:p-8 shadow-sm" id="step1">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Langkah 1 dari 3</p>
            <h2 class="text-2xl font-bold mb-1">Hai, siapa namamu?</h2>
            <p class="text-sm text-gray-500 mb-6">Ini akan ditampilkan di dalam app.</p>

            <input type="text" name="display_name" id="displayName"
                autocomplete="off"
                placeholder="Nama atau panggilanmu..."
                class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-base font-semibold outline-none focus:border-black transition-all"
                required>

            <button type="button" onclick="goStep(2)"
                class="w-full mt-6 py-4 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all">
                Lanjut →
            </button>
        </div>

        {{-- ── Step 2: Religion ── --}}
        <div class="step-panel bg-white rounded-3xl p-6 md:p-8 shadow-sm" id="step2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Langkah 2 dari 3</p>
            <h2 class="text-2xl font-bold mb-1">Agama / kepercayaan</h2>
            <p class="text-sm text-gray-500 mb-6">Kami akan menyesuaikan fitur spiritual sesuai pilihanmu.</p>

            <div class="space-y-2.5">
                @php
                $religions = [
                    ['value'=>'islam',   'label'=>'Islam',       'sub'=>'Sholat 5 waktu, Rawatib, Takbir',       'icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                    ['value'=>'kristen', 'label'=>'Kristen',     'sub'=>'Doa pagi/malam, Baca Alkitab, Ibadah',  'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['value'=>'hindu',   'label'=>'Hindu',       'sub'=>'Sembahyang pagi/sore, Meditasi',         'icon'=>'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['value'=>'buddha',  'label'=>'Buddha',      'sub'=>'Sembahyang pagi/sore, Meditasi',         'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                    ['value'=>'lainnya', 'label'=>'Lainnya',     'sub'=>'Praktik spiritual & jurnal syukur',      'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                    ['value'=>'none',    'label'=>'Tidak ada',   'sub'=>'Fitur spiritual disembunyikan',          'icon'=>'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21'],
                ];
                @endphp

                @foreach($religions as $rel)
                <div class="option-card flex items-center gap-4 p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-gray-300"
                     onclick="selectReligion('{{ $rel['value'] }}', this)">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0 opt-icon">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $rel['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm opt-label">{{ $rel['label'] }}</p>
                        <p class="text-xs text-gray-400 opt-sub">{{ $rel['sub'] }}</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex-shrink-0 opt-radio"></div>
                </div>
                @endforeach
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="goStep(1)" class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">← Kembali</button>
                <button type="button" onclick="goStep(3)" id="btnStep2Next" disabled
                    class="flex-[2] py-4 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                    Lanjut →
                </button>
            </div>
        </div>

        {{-- ── Step 3: Sports ── --}}
        <div class="step-panel bg-white rounded-3xl p-6 md:p-8 shadow-sm" id="step3">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Langkah 3 dari 3</p>
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
                <button type="button" onclick="goStep(2)" class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">← Kembali</button>
                <button type="submit" id="btnFinish"
                    class="flex-[2] py-4 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition-all">
                    Mulai Sekarang →
                </button>
            </div>
            <p class="text-center text-xs text-gray-400 mt-3">Bisa diubah kapan saja di Pengaturan.</p>
        </div>

    </form>
</div>

<script>
let currentStep = 1;
let selectedReligion = '';
let selectedSports = new Set();

function goStep(n) {
    // Validate step 1
    if (n > 1) {
        const name = document.getElementById('displayName').value.trim();
        if (!name) {
            document.getElementById('displayName').focus();
            document.getElementById('displayName').style.borderColor = '#ef4444';
            return;
        }
        document.getElementById('displayName').style.borderColor = '';
    }
    // Validate step 2
    if (n > 2 && !selectedReligion) return;

    document.getElementById('step' + currentStep).classList.remove('active');
    document.getElementById('step' + n).classList.add('active');
    currentStep = n;

    // Update progress bar
    ['prog1','prog2','prog3'].forEach((id, i) => {
        document.getElementById(id).style.background = (i < n) ? '#111827' : '#e5e7eb';
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function selectReligion(value, card) {
    // Deselect all
    document.querySelectorAll('.option-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.opt-radio').style.background = '';
        c.querySelector('.opt-radio').style.borderColor = '#d1d5db';
    });
    // Select this
    card.classList.add('selected');
    card.querySelector('.opt-radio').style.background = '#fff';
    card.querySelector('.opt-radio').style.borderColor = '#fff';

    selectedReligion = value;
    document.getElementById('hiddenReligion').value = value;
    document.getElementById('btnStep2Next').disabled = false;
}

function toggleSport(value, card) {
    if (selectedSports.has(value)) {
        selectedSports.delete(value);
        card.classList.remove('selected');
    } else {
        selectedSports.add(value);
        card.classList.add('selected');
    }

    // Show/hide custom sport name input
    document.getElementById('customSportInput').style.display =
        selectedSports.has('custom_sport') ? 'block' : 'none';

    // Update hidden input — repeat the field name for array
    updateSportsHidden();
}

function updateSportsHidden() {
    // We'll serialize to a comma-delimited string and let the server split it
    // Actually we use multiple hidden inputs
    const container = document.getElementById('hiddenSports');
    // Remove old sport inputs
    document.querySelectorAll('.hidden-sport-input').forEach(el => el.remove());

    selectedSports.forEach(sport => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'sports[]';
        input.value = sport;
        input.className = 'hidden-sport-input';
        document.getElementById('onboardingForm').appendChild(input);
    });
}

// Validate name on enter
document.getElementById('displayName').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); goStep(2); }
});
</script>
</body>
</html>
