<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mojob — @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .nav-active { background-color: #000; color: #fff !important; }
        summary::-webkit-details-marker { display: none; }
        summary::marker { content: ''; }
        .nav-item { transition: all .15s; }
        input[type=time]::-webkit-calendar-picker-indicator { opacity: .5; cursor: pointer; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade-in { animation: fadeIn .3s ease; }

        /* ── Page transitions ── */
        @keyframes pageIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .page-content {
            animation: pageIn .22s ease-out both;
            will-change: opacity;
        }

        /* Exit state — pure opacity fade before navigation */
        .page-leaving {
            opacity: 0;
            transition: opacity .12s ease-out;
        }

        /* Subtle hover lift for nav items + cards */
        .nav-item { transition: background-color .15s ease, color .15s ease, transform .15s ease; }
        a, button { -webkit-tap-highlight-color: transparent; }

        /* ── Custom orange radio & checkbox ── */
        .form-radio-orange,
        .form-check-orange {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 16px; height: 16px;
            border: 1.5px solid #d1d5db;
            background: #fff;
            cursor: pointer;
            position: relative;
            transition: border-color .15s ease, background-color .15s ease;
            flex-shrink: 0;
        }
        .form-radio-orange { border-radius: 50%; }
        .form-check-orange { border-radius: 4px; }

        .form-radio-orange:hover,
        .form-check-orange:hover { border-color: #9ca3af; }

        /* Checked state — orange */
        .form-radio-orange:checked,
        .form-check-orange:checked {
            border-color: #EF8221;
            background: #EF8221;
        }

        /* Radio inner dot */
        .form-radio-orange:checked::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #fff;
            transform: translate(-50%, -50%);
        }

        /* Checkbox tick */
        .form-check-orange:checked::after {
            content: '';
            position: absolute;
            top: 1px; left: 4px;
            width: 4px; height: 8px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .form-radio-orange:focus-visible,
        .form-check-orange:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(239,130,33,.18);
        }

        /* Icon next to checkbox follows checked state */
        .form-check-orange + .sport-icon { color: #6b7280; transition: color .15s ease; }
        .form-check-orange:checked + .sport-icon { color: #EF8221; }

        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .page-content, .page-leaving { animation: none !important; transition: none !important; }
        }
        /* Status pills */
        .pill-wishlist  { background:#f3e8ff; color:#6d28d9; }
        .pill-applied   { background:#f1f5f9; color:#334155; }
        .pill-review    { background:#fef3c7; color:#92400e; }
        .pill-interview { background:#dbeafe; color:#1d4ed8; }
        .pill-offer     { background:#d1fae5; color:#047857; }
        .pill-hired     { background:#a7f3d0; color:#064e3b; }
        .pill-rejected  { background:#fee2e2; color:#991b1b; }
        /* ── Flatpickr Custom Theme ── */
        .flatpickr-calendar {
            background: #fff;
            border-radius: 20px !important;
            box-shadow: 0 16px 48px rgba(0,0,0,.14), 0 2px 8px rgba(0,0,0,.06) !important;
            border: none !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            overflow: hidden;
            padding: 4px;
        }
        .flatpickr-calendar.arrowTop:before, .flatpickr-calendar.arrowTop:after,
        .flatpickr-calendar.arrowBottom:before, .flatpickr-calendar.arrowBottom:after { display: none; }
        .flatpickr-months { padding: 8px 4px 4px; border-bottom: 1px solid #f3f4f6; }
        .flatpickr-month { height: 38px; }
        .flatpickr-current-month { font-size: 14px; font-weight: 700; color: #111827; }
        .flatpickr-current-month .cur-month { font-weight: 700; }
        .flatpickr-current-month input.cur-year { font-weight: 700; }
        .flatpickr-prev-month, .flatpickr-next-month { padding: 8px 10px; color: #6b7280 !important; }
        .flatpickr-prev-month:hover, .flatpickr-next-month:hover { color: #000 !important; }
        .flatpickr-prev-month svg, .flatpickr-next-month svg { fill: currentColor; }
        .flatpickr-weekdays { background: transparent; padding: 4px 0; }
        .flatpickr-weekday { font-weight: 700; font-size: 11px; color: #9ca3af; background: transparent; }
        .dayContainer { padding: 4px 0; gap: 2px; }
        .flatpickr-day {
            border-radius: 10px !important;
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            border: none !important;
            height: 36px;
            line-height: 36px;
            max-width: 36px;
        }
        .flatpickr-day:hover, .flatpickr-day:focus { background: #f3f4f6 !important; }
        .flatpickr-day.selected, .flatpickr-day.selected:hover, .flatpickr-day.selected:focus {
            background: #111827 !important; color: #fff !important; border: none !important;
        }
        .flatpickr-day.today { border: 2px solid #111827 !important; }
        .flatpickr-day.today:hover { background: #f3f4f6 !important; color: #111827 !important; }
        .flatpickr-day.today.selected { background: #111827 !important; color: #fff !important; }
        .flatpickr-day.flatpickr-disabled, .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay {
            color: #d1d5db !important;
        }
        .flatpickr-day.flatpickr-disabled { cursor: not-allowed; }
        /* Month dropdown — strip browser native styling */
        .flatpickr-monthDropdown-months {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            color: #111827 !important;
            cursor: pointer;
            padding: 0 2px !important;
        }
        .flatpickr-monthDropdown-months:focus { outline: none !important; box-shadow: none !important; }
        .flatpickr-monthDropdown-months option {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            color: #111827;
            background: #fff;
            padding: 8px 12px;
        }

        /* Custom Select Component */
        .cs-wrapper { position: relative; display: block; }
        .cs-trigger {
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
            width: 100%; text-align: left;
            background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 10px 12px; font-size: 14px; font-family: inherit;
            color: #111827; cursor: pointer; transition: border-color .15s, background .15s;
        }
        .cs-trigger:hover { border-color: #9ca3af; }
        .cs-trigger.open, .cs-trigger:focus { border-color: #000; background: #fff; outline: none; box-shadow: 0 0 0 3px rgba(0,0,0,0.06); }
        .cs-trigger-label { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cs-chevron { flex-shrink: 0; width: 14px; height: 14px; color: #9ca3af; transition: transform .2s, color .15s; }
        .cs-trigger.open .cs-chevron { transform: rotate(180deg); color: #111; }
        .cs-dropdown {
            background: #fff; border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0,0,0,.13), 0 2px 8px rgba(0,0,0,.07);
            z-index: 99999; padding: 6px;
        }
        .cs-option {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 10px 14px; border-radius: 10px;
            cursor: pointer; font-size: 14px; font-family: inherit;
            color: #374151; transition: background .1s; white-space: nowrap;
        }
        .cs-option:hover { background: #f9fafb; }
        .cs-option.cs-selected { font-weight: 700; color: #111827; }
        .cs-check { flex-shrink: 0; color: #6366f1; width: 15px; height: 15px; stroke-width: 2.5; }
    </style>
</head>
<body class="bg-[#F8F9FA] min-h-screen pb-20 md:pb-0">

{{-- Toast (passed via session flash) --}}
@if(session('toast'))
<div id="toast" class="fixed top-4 right-4 z-50 bg-gray-900 text-white rounded-2xl px-5 py-4 shadow-2xl flex items-center gap-3 max-w-sm animate-fade-in">
    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    </div>
    <div class="flex-1">
        <p class="text-sm font-bold">{{ session('toast') }}</p>
    </div>
    <button onclick="document.getElementById('toast').remove()" class="text-gray-400 hover:text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
<script>setTimeout(()=>{ const t=document.getElementById('toast'); if(t) t.remove(); }, 5000);</script>
@endif

@php
    $_storage = auth()->check() ? \App\Models\UserStorage::fromSession() : null;
    $_feats   = $_storage ? $_storage->getFeatures() : [];
    $_profile = $_storage ? $_storage->getProfile()  : [];
    $_f       = fn($k) => $_feats[$k] ?? false;
    $_religion = $_profile['religion'] ?? '';
    $_spiritualLabel = match($_religion) {
        'kristen'        => 'Ibadah',
        'hindu','buddha' => 'Sembahyang',
        'lainnya'        => 'Spiritual',
        default          => 'Spiritual',
    };
    $_sportLabel = $_profile['custom_sport_name'] ?? __('Olahraga');
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="hidden md:flex w-64 bg-white border-r border-gray-100 flex-col p-6 fixed top-0 left-0 h-screen z-10 overflow-y-auto">
        <div class="mb-8">
            <img src="{{ asset('images/logo.png') }}" class="h-8">
            {{-- <svg class="h-8 w-auto" viewBox="0 0 260 85" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="mjGradSidebar" x1="0" y1="1" x2="1" y2="0" gradientUnits="objectBoundingBox">
                        <stop offset="0%" stop-color="#c94b00"/>
                        <stop offset="50%" stop-color="#ef7015"/>
                        <stop offset="100%" stop-color="#f9b418"/>
                    </linearGradient>
                </defs>
                <!-- Swoosh -->
                <path d="M 8 58 C 3 74 22 83 48 74 C 64 68 75 55 70 46 C 65 37 50 44 36 54 C 22 64 10 62 8 58 Z" fill="url(#mjGradSidebar)"/>
                <!-- Upper bean -->
                <ellipse cx="37" cy="25" rx="15" ry="23" transform="rotate(-10 37 25)" fill="#111111"/>
                <!-- Lower bean -->
                <ellipse cx="55" cy="56" rx="11" ry="16" transform="rotate(8 55 56)" fill="#111111"/>
                <!-- Mojob text -->
                <text x="90" y="60" font-family="'Plus Jakarta Sans', 'Arial Black', sans-serif" font-weight="800" font-size="50" fill="#111111">Mojob</text>
            </svg> --}}
        </div>

        <nav class="flex-1">
            @php
            /* ── Build all three section nav arrays ── */
            $lifeNav = array_values(array_filter([
                ['route'=>'dashboard',   'label'=>'Home',               'match'=>'dashboard',   'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'show'=>true],
                ['route'=>'sholat',     'label'=>__('Sholat'),          'match'=>'sholat',      'icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'show'=>$_f('sholat')],
                ['route'=>'spiritual',  'label'=>$_spiritualLabel,      'match'=>'spiritual',   'icon'=>'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'show'=>$_f('spiritual')],
                ['route'=>'gym',        'label'=>'Gym',                 'match'=>'gym',         'icon'=>'M13 10V3L4 14h7v7l9-11h-7z',  'show'=>$_f('gym')],
                ['route'=>'run',        'label'=>__('Lari'),            'match'=>'run',         'icon'=>'M22 12h-4l-3 9L9 3l-3 9H2',   'show'=>$_f('run')],
                ['route'=>'cycling',    'label'=>__('Bersepeda'),       'match'=>'cycling',     'icon'=>'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'show'=>$_f('cycling')],
                ['route'=>'swimming',   'label'=>__('Renang'),          'match'=>'swimming',    'icon'=>'M7 16.5c2 1 4 1 6 0s4-1 6 0M7 11.5c2 1 4 1 6 0s4-1 6 0M3 7.5c2 1 4 1 6 0m-9 9V7.5', 'show'=>$_f('swimming')],
                ['route'=>'racket',     'label'=>'Tenis/Badminton',    'match'=>'racket',      'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'show'=>$_f('racket')],
                ['route'=>'custom_sport','label'=>$_sportLabel,         'match'=>'custom_sport','icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'show'=>$_f('custom_sport')],
                ['route'=>'intimasi',   'label'=>__('Intimasi'),        'match'=>'intimasi',    'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'show'=>$_f('intimasi')],
                ['route'=>'mental',     'label'=>__('Mental'),          'match'=>'mental',      'icon'=>'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'show'=>$_f('mental')],
                ['route'=>'tasks',      'label'=>'Tasks & Notes',       'match'=>'tasks',       'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'show'=>$_f('tasks')],
                ['route'=>'statistik',  'label'=>__('Statistik'),       'match'=>'statistik',   'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'show'=>$_f('statistik')],
                ['route'=>'insights',   'label'=>__('Insights'),        'match'=>'insights',    'icon'=>'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'show'=>$_f('insights')],
                ['route'=>'goals',      'label'=>__('Goals & Reminder'),'match'=>'goals',       'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'show'=>$_f('goals')],
            ], fn($i) => $i['show']));

            $careerNav = array_values(array_filter([
                ['route'=>'karir',           'match'=>'karir',       'label'=>__('Overview'),          'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'show'=>$_f('lamaran')],
                ['route'=>'lamaran.index',   'match'=>'lamaran.*',   'label'=>__('Lamaran Kerja'),     'icon'=>'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'show'=>$_f('lamaran')],
                ['route'=>'persiapan.index', 'match'=>'persiapan.*', 'label'=>__('Persiapan Melamar'),'icon'=>'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z', 'show'=>$_f('persiapan')],
            ], fn($i) => $i['show']));

            $financeNav = array_values(array_filter([
                ['route'=>'finance.index',    'match'=>'finance.index',   'label'=>__('Overview'),      'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'show'=>$_f('finance')],
                ['route'=>'finance.transaksi','match'=>'finance.transaksi','label'=>__('Transaksi'),    'icon'=>'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'show'=>$_f('finance')],
                ['route'=>'finance.anggaran', 'match'=>'finance.anggaran', 'label'=>__('Anggaran'),     'icon'=>'M9 7h6m0 0l-3-3m3 3l-3 3M9 17h6m0 0l-3-3m3 3l-3 3M4 12h16', 'show'=>$_f('finance')],
                ['route'=>'finance.tabungan', 'match'=>'finance.tabungan', 'label'=>__('Tabungan'),     'icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'show'=>$_f('finance')],
            ], fn($i) => $i['show']));

            $settingsNav = [
                ['route'=>'settings.profil',    'match'=>'settings.profil',    'label'=>__('Profil & Akun'),    'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['route'=>'settings.tampilan',  'match'=>'settings.tampilan',  'label'=>__('Tampilan & Fitur'), 'icon'=>'M4 6h16M4 12h16M4 18h7'],
                ['route'=>'settings.langganan', 'match'=>'settings.langganan', 'label'=>__('Langganan'),        'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                ['route'=>'settings.referral',  'match'=>'settings.referral',  'label'=>__('Referral'),         'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
            ];

            /* Determine which section is active */
            $activeSection = 'life';
            if (request()->routeIs('karir') || request()->routeIs('lamaran.*') || request()->routeIs('persiapan.*')) $activeSection = 'karir';
            if (request()->routeIs('finance.*'))  $activeSection = 'finance';
            if (request()->routeIs('settings.*') || request()->routeIs('settings')) $activeSection = 'settings';

            $sections = [
                ['id'=>'life',     'label'=>'Life',     'items'=>$lifeNav],
                ['id'=>'karir',    'label'=>'Karir',    'items'=>$careerNav],
                ['id'=>'finance',  'label'=>'Finance',  'items'=>$financeNav],
                ['id'=>'settings', 'label'=>'Settings & Referral', 'items'=>$settingsNav],
            ];
            @endphp

            {{-- Collapsible sections --}}
            <div id="sidebarNav">
            @foreach($sections as $sec)
            @if(count($sec['items']) > 0)
            <div class="sidebar-section mb-1" data-section="{{ $sec['id'] }}">
                {{-- Section header — no icon, just label + chevron --}}
                <button type="button" onclick="toggleSection('{{ $sec['id'] }}')"
                    class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-all group">
                    <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">{{ $sec['label'] }}</span>
                    <svg id="chevron-{{ $sec['id'] }}" class="w-3 h-3 text-gray-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
                {{-- Items --}}
                <div id="section-{{ $sec['id'] }}" class="overflow-hidden transition-all duration-200" style="max-height:0">
                    <div class="space-y-0.5 pt-1 pb-2">
                    @foreach($sec['items'] as $item)
                    <a href="{{ route($item['route']) }}"
                        class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all
                        {{ request()->routeIs($item['match']) ? 'nav-active' : 'text-gray-500 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/>
                        </svg>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                    </div>
                </div>
            </div>
            @if(!$loop->last)<div class="h-px bg-gray-100 my-1.5"></div>@endif
            @endif
            @endforeach
            </div>
        </nav>

    </aside>

    {{-- Main Content --}}
    <main class="md:ml-64 flex-1 p-4 md:p-8 w-full">
        <header class="flex justify-between items-center mb-3 md:mb-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold">@yield('page-title')</h1>
                <p class="text-gray-400 text-xs md:text-sm hidden md:block">Mojob › @yield('breadcrumb')</p>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                {{-- Language switcher --}}
                <div class="flex items-center bg-white rounded-full border border-gray-100 p-0.5">
                    <a href="{{ route('lang.switch', 'id') }}"
                       class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'id' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}"
                       class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">EN</a>
                </div>

                {{-- Notifications --}}
                @php $_notifs = $_storage ? $_storage->getNotifications() : []; @endphp
                <div class="relative" id="notifWrapper">
                    <button type="button" id="notifBtn" onclick="toggleNotifPanel()"
                            class="relative bg-white p-2 rounded-full border border-gray-100 hover:bg-gray-50 transition-all"
                            aria-label="Notifications">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(count($_notifs) > 0)
                        <span id="notifDot" class="absolute top-1 right-1 w-2 h-2 bg-orange-500 rounded-full border-2 border-white"></span>
                        @endif
                    </button>

                    {{-- Dropdown panel --}}
                    <div id="notifPanel" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50">
                            <p class="font-bold text-sm">{{ __('Notifikasi') }}</p>
                            @if(count($_notifs) > 0)
                            <button type="button" onclick="markAllNotifRead()" class="text-[10px] font-bold text-gray-400 hover:text-black transition-all">
                                {{ __('Tandai semua') }}
                            </button>
                            @endif
                        </div>

                        <div class="max-h-96 overflow-y-auto" id="notifList">
                            @if(count($_notifs) === 0)
                            <div class="px-4 py-10 text-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-gray-700">{{ __('Tidak ada notifikasi') }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ __('Semua up to date!') }}</p>
                            </div>
                            @else
                            @php
                            $typeColors = [
                                'success' => ['bg' => 'bg-green-50',  'iconBg' => 'bg-green-100',  'iconText' => 'text-green-600'],
                                'warning' => ['bg' => 'bg-orange-50', 'iconBg' => 'bg-orange-100', 'iconText' => 'text-orange-600'],
                                'info'    => ['bg' => 'bg-blue-50',   'iconBg' => 'bg-blue-100',   'iconText' => 'text-blue-600'],
                            ];
                            @endphp
                            @foreach($_notifs as $n)
                            @php $c = $typeColors[$n['type']] ?? $typeColors['info']; @endphp
                            <a href="{{ $n['link'] }}"
                               data-notif-id="{{ $n['id'] }}"
                               class="notif-item flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-all border-b border-gray-50 last:border-b-0">
                                <div class="w-9 h-9 {{ $c['iconBg'] }} {{ $c['iconText'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $n['icon'] }}"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $n['title'] }}</p>
                                    <p class="text-xs text-gray-500 leading-relaxed">{{ $n['message'] }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $n['time'] }}</p>
                                </div>
                            </a>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white px-3 py-1.5 md:px-4 md:py-2 rounded-full border border-gray-100 flex items-center gap-1 md:gap-2 text-xs md:text-sm">
                    <svg class="w-4 h-4 text-gray-600 hidden md:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ date('j/n') }}</span>
                </div>
                {{-- Settings icon (mobile) --}}
                <a href="{{ route('settings') }}" class="md:hidden bg-white p-2 rounded-full border border-gray-100 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="md:hidden">
                    @csrf
                    <button type="submit" class="bg-white p-2 rounded-full border border-gray-100 hover:bg-gray-50">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </header>

        <div id="pageContent" class="page-content">
            @yield('content')
        </div>
    </main>
</div>

{{-- Mobile Bottom Nav --}}
@php
    $_ico = [
        'dashboard'    => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'today'        => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z',
        'spiritual'    => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
        'gym'          => 'M13 10V3L4 14h7v7l9-11h-7z',
        'run'          => 'M22 12h-4l-3 9L9 3l-3 9H2',
        'cycling'      => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
        'swimming'     => 'M7 16.5c2 1 4 1 6 0s4-1 6 0M7 11.5c2 1 4 1 6 0s4-1 6 0M3 7.5c2 1 4 1 6 0m-9 9V7.5',
        'racket'       => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z',
        'custom_sport' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
        'intimasi'     => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
        'mental'       => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'tasks'        => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'insights'     => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'statistik'    => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'goals'        => 'M13 3l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
        'karir'        => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
        'lamaran'      => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'persiapan'    => 'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z',
        'settings'     => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
    ];
    $mobileNavAll = [
        ['route'=>'dashboard',       'match'=>'dashboard',    'label'=>'Home',                       'icon'=>$_ico['dashboard'],    'show'=>true],
        ['route'=>'sholat',          'match'=>'sholat',       'label'=>'Sholat',                     'icon'=>$_ico['spiritual'],    'show'=>$_f('sholat')],
        ['route'=>'spiritual',       'match'=>'spiritual',    'label'=>mb_substr($_spiritualLabel,0,7),'icon'=>$_ico['spiritual'],   'show'=>$_f('spiritual')],
        ['route'=>'gym',             'match'=>'gym',          'label'=>'Gym',                        'icon'=>$_ico['gym'],          'show'=>$_f('gym')],
        ['route'=>'run',             'match'=>'run',          'label'=>'Lari',                       'icon'=>$_ico['run'],          'show'=>$_f('run')],
        ['route'=>'cycling',         'match'=>'cycling',      'label'=>'Sepeda',                     'icon'=>$_ico['cycling'],      'show'=>$_f('cycling')],
        ['route'=>'swimming',        'match'=>'swimming',     'label'=>'Renang',                     'icon'=>$_ico['swimming'],     'show'=>$_f('swimming')],
        ['route'=>'racket',          'match'=>'racket',       'label'=>'Tenis',                      'icon'=>$_ico['racket'],       'show'=>$_f('racket')],
        ['route'=>'custom_sport',    'match'=>'custom_sport', 'label'=>mb_substr($_sportLabel,0,6),  'icon'=>$_ico['custom_sport'], 'show'=>$_f('custom_sport')],
        ['route'=>'intimasi',        'match'=>'intimasi',     'label'=>'Intim',                      'icon'=>$_ico['intimasi'],     'show'=>$_f('intimasi')],
        ['route'=>'mental',          'match'=>'mental',       'label'=>'Mental',                     'icon'=>$_ico['mental'],       'show'=>$_f('mental')],
        ['route'=>'tasks',           'match'=>'tasks',        'label'=>'Tasks',                      'icon'=>$_ico['tasks'],        'show'=>$_f('tasks')],
        ['route'=>'insights',        'match'=>'insights',     'label'=>'Insights',                   'icon'=>$_ico['insights'],     'show'=>$_f('insights')],
        ['route'=>'statistik',       'match'=>'statistik',    'label'=>'Stats',                      'icon'=>$_ico['statistik'],    'show'=>$_f('statistik')],
        ['route'=>'goals',           'match'=>'goals',        'label'=>'Goals',                      'icon'=>$_ico['goals'],        'show'=>$_f('goals')],
        ['route'=>'karir',            'match'=>'karir',         'label'=>'Karir',     'icon'=>$_ico['karir'],     'show'=>$_f('lamaran')],
        ['route'=>'lamaran.index',   'match'=>'lamaran.*',    'label'=>'Lamaran',   'icon'=>$_ico['lamaran'],   'show'=>$_f('lamaran')],
        ['route'=>'persiapan.index', 'match'=>'persiapan.*', 'label'=>'Persiapan', 'icon'=>$_ico['persiapan'], 'show'=>$_f('persiapan')],
        ['route'=>'finance.index',   'match'=>'finance.*',    'label'=>'Finance',   'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'show'=>$_f('finance')],
        ['route'=>'settings',        'match'=>'settings',     'label'=>'Setting',   'icon'=>$_ico['settings'],  'show'=>true],
    ];
    $mobileNav       = array_values(array_filter($mobileNavAll, fn($i) => $i['show']));
    $mobileCount     = count($mobileNav);
    $mobileGridClass = 'grid-cols-' . min($mobileCount, 10);
@endphp
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 z-20">
    <div class="grid {{ $mobileGridClass }} gap-0.5 p-1.5">
        @foreach($mobileNav as $item)
        <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-0.5 py-1.5 px-0.5 rounded-xl transition-all {{ request()->routeIs($item['match']) ? 'bg-black text-white' : 'text-gray-500' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
            <span class="text-[8px] font-bold leading-none">{{ $item['label'] }}</span>
        </a>
        @endforeach
    </div>
</nav>

@stack('modals')

{{-- Global Confirm Delete Modal --}}
<div id="confirmModal" class="hidden fixed inset-0 z-[200] flex items-end sm:items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeConfirm()"></div>
    <div class="relative bg-white rounded-3xl p-6 shadow-2xl w-full max-w-sm animate-fade-in">
        <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <h3 class="text-center font-bold text-base mb-1">{{ __('Hapus data ini?') }}</h3>
        <p id="confirmMessage" class="text-center text-sm text-gray-500 mb-6 leading-relaxed"></p>
        <div class="flex gap-3">
            <button onclick="closeConfirm()"
                class="flex-1 py-3 bg-gray-100 rounded-2xl font-bold text-sm hover:bg-gray-200 transition-all">
                {{ __('Batalkan') }}
            </button>
            <button id="confirmOkBtn" onclick="doConfirm()"
                class="flex-1 py-3 bg-red-500 text-white rounded-2xl font-bold text-sm hover:bg-red-600 transition-all">
                {{ __('Hapus') }}
            </button>
        </div>
    </div>
</div>

<script>
/* ============================================================
   Custom Select — converts all <select> elements app-wide
   ============================================================ */
(function () {
    var openTrigger = null, openDropdown = null;

    function closeAll() {
        if (openDropdown) { openDropdown.remove(); openDropdown = null; }
        if (openTrigger)  { openTrigger.classList.remove('open'); openTrigger = null; }
    }

    document.addEventListener('click', closeAll);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAll(); });

    function syncLabel(select, trigger) {
        var opt = select.options[select.selectedIndex];
        trigger.querySelector('.cs-trigger-label').textContent = opt ? opt.text : '';
    }

    function initCustomSelects() {
        document.querySelectorAll('select:not([data-cs])').forEach(function (select) {
            select.dataset.cs = '1';

            /* ---- Wrapper ---- */
            var wrapper = document.createElement('div');
            wrapper.className = 'cs-wrapper';

            /* ---- Trigger button ---- */
            var trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'cs-trigger';
            trigger.innerHTML =
                '<span class="cs-trigger-label"></span>' +
                '<svg class="cs-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>';

            syncLabel(select, trigger);

            /* ---- Click: open dropdown ---- */
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                if (openTrigger === trigger) { closeAll(); return; }
                closeAll();

                /* Build dropdown */
                var dd = document.createElement('div');
                dd.className = 'cs-dropdown';
                dd.style.position = 'fixed';

                Array.from(select.options).forEach(function (opt) {
                    var isSelected = opt.value === select.value;
                    var item = document.createElement('div');
                    item.className = 'cs-option' + (isSelected ? ' cs-selected' : '');
                    item.innerHTML =
                        '<span>' + opt.text + '</span>' +
                        '<svg class="cs-check" fill="none" stroke="currentColor" viewBox="0 0 24 24"' +
                        ' style="visibility:' + (isSelected ? 'visible' : 'hidden') + '">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';

                    item.addEventListener('click', function (e2) {
                        e2.stopPropagation();
                        select.value = opt.value;
                        syncLabel(select, trigger);
                        select.dispatchEvent(new Event('change'));
                        closeAll();
                    });
                    dd.appendChild(item);
                });

                /* Position */
                var rect = trigger.getBoundingClientRect();
                dd.style.left  = rect.left + 'px';
                dd.style.width = rect.width + 'px';
                dd.style.top   = (rect.bottom + 6) + 'px';
                document.body.appendChild(dd);

                /* Flip up if overflows viewport */
                requestAnimationFrame(function () {
                    var dh = dd.offsetHeight;
                    if (rect.bottom + 6 + dh > window.innerHeight - 8) {
                        dd.style.top = (rect.top - dh - 6) + 'px';
                    }
                });

                openDropdown = dd;
                openTrigger  = trigger;
                trigger.classList.add('open');
            });

            /* ---- Replace native select in DOM ---- */
            select.style.display = 'none';
            select.parentNode.insertBefore(wrapper, select);
            wrapper.appendChild(trigger);
            wrapper.appendChild(select); /* keep hidden select for form submission */

            /* ---- Expose refresh (for programmatic .value changes) ---- */
            select._csRefresh = function () { syncLabel(select, trigger); };
        });
    }

    /* Global helpers */
    window.initCustomSelects = initCustomSelects;
    window.refreshSelect = function (el) {
        if (typeof el === 'string') el = document.getElementById(el);
        if (el && el._csRefresh) el._csRefresh();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomSelects);
    } else {
        initCustomSelects();
    }
})();
</script>

@stack('scripts')
<script>
/* ── Collapsible Sidebar ── */
(function() {
    var ACTIVE   = '{{ $activeSection ?? "life" }}';
    var SECTIONS = ['life', 'karir', 'finance', 'settings'];

    /* ── Notifications dropdown ── */
    window.toggleNotifPanel = function() {
        var p = document.getElementById('notifPanel');
        if (!p) return;
        p.classList.toggle('hidden');
    };

    window.markAllNotifRead = function() {
        var dot = document.getElementById('notifDot');
        if (dot) dot.remove();
        try {
            var ids = Array.from(document.querySelectorAll('[data-notif-id]')).map(el => el.dataset.notifId);
            localStorage.setItem('notifRead', JSON.stringify({ ids: ids, at: Date.now() }));
        } catch(e) {}
        setTimeout(function() {
            var p = document.getElementById('notifPanel');
            if (p) p.classList.add('hidden');
        }, 200);
    };

    /* Click outside to close panel */
    document.addEventListener('click', function(e) {
        var wrap = document.getElementById('notifWrapper');
        var panel = document.getElementById('notifPanel');
        if (!wrap || !panel || panel.classList.contains('hidden')) return;
        if (!wrap.contains(e.target)) panel.classList.add('hidden');
    });

    /* Hide dot if all current notif IDs are already read */
    (function() {
        try {
            var saved = JSON.parse(localStorage.getItem('notifRead') || '{}');
            var savedIds = saved.ids || [];
            var current = Array.from(document.querySelectorAll('[data-notif-id]')).map(el => el.dataset.notifId);
            if (current.length === 0) return;
            var allRead = current.every(id => savedIds.indexOf(id) !== -1);
            if (allRead) {
                var dot = document.getElementById('notifDot');
                if (dot) dot.remove();
            }
        } catch(e) {}
    })();

    /* ── Smooth page transitions ── */
    (function() {
        // BFCache: re-trigger fade-in when navigating back
        window.addEventListener('pageshow', function(e) {
            var pc = document.getElementById('pageContent');
            if (pc) {
                pc.classList.remove('page-leaving');
                pc.style.opacity = '';
                pc.style.transform = '';
            }
        });

        // Intercept internal navigation links and fade-out before navigation
        document.addEventListener('click', function(e) {
            var a = e.target.closest('a[href]');
            if (!a) return;
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return; // open in new tab/window
            if (a.target && a.target !== '_self') return;
            if (a.hasAttribute('download')) return;

            var url = a.getAttribute('href');
            if (!url || url.startsWith('#') || url.startsWith('javascript:') ||
                url.startsWith('mailto:') || url.startsWith('tel:')) return;

            // Only same-origin
            try {
                var dest = new URL(a.href, window.location.href);
                if (dest.origin !== window.location.origin) return;
                if (dest.pathname === window.location.pathname &&
                    dest.search === window.location.search) return; // same page
            } catch (err) { return; }

            // Respect reduced-motion
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            var pc = document.getElementById('pageContent');
            if (!pc) return;

            e.preventDefault();
            pc.classList.add('page-leaving');
            setTimeout(function() { window.location.href = a.href; }, 110);
        });
    })();

    function getState() {
        try { return JSON.parse(localStorage.getItem('sbState') || '{}'); } catch(e) { return {}; }
    }
    function setState(s, open) {
        var state = getState(); state[s] = open;
        try { localStorage.setItem('sbState', JSON.stringify(state)); } catch(e) {}
    }

    function applySection(id, open, animate) {
        var el  = document.getElementById('section-' + id);
        var chv = document.getElementById('chevron-' + id);
        if (!el) return;

        /* Skip animation: snap to final state instantly */
        if (animate === false) {
            var prev = el.style.transition;
            el.style.transition = 'none';
            if (open) {
                el.style.maxHeight = 'none';
                if (chv) { chv.style.transition = 'none'; chv.style.transform = ''; }
            } else {
                el.style.maxHeight = '0';
                if (chv) { chv.style.transition = 'none'; chv.style.transform = 'rotate(-90deg)'; }
            }
            /* Force reflow then restore transition for future toggles */
            void el.offsetHeight;
            el.style.transition = prev;
            if (chv) chv.style.transition = '';
            return;
        }

        /* Animated toggle */
        if (open) {
            /* If max-height is 'none', set it to 0 first so transition has a starting point */
            if (el.style.maxHeight === 'none' || el.style.maxHeight === '') {
                el.style.maxHeight = '0';
                void el.offsetHeight;
            }
            el.style.maxHeight = el.scrollHeight + 'px';
            if (chv) chv.style.transform = '';
        } else {
            /* If currently 'none', set to actual pixel value first */
            if (el.style.maxHeight === 'none' || el.style.maxHeight === '') {
                el.style.maxHeight = el.scrollHeight + 'px';
                void el.offsetHeight;
            }
            el.style.maxHeight = '0';
            if (chv) chv.style.transform = 'rotate(-90deg)';
        }
    }

    /* Accordion: only ONE section open at a time, always one open */
    window.toggleSection = function(id) {
        var el = document.getElementById('section-' + id);
        if (!el) return;
        var isOpen = !(el.style.maxHeight === '0px' || el.style.maxHeight === '');

        /* Clicking the already-open section keeps it open (always one open) */
        if (isOpen) return;

        /* Close every other section, open this one */
        SECTIONS.forEach(function(other) {
            if (other === id) return;
            var oel = document.getElementById('section-' + other);
            if (oel && !(oel.style.maxHeight === '0px' || oel.style.maxHeight === '')) {
                applySection(other, false, true);
            }
        });
        applySection(id, true, true);

        try { localStorage.setItem('sbOpen', id); } catch(e) {}
    };

    /* Initial state: open exactly ONE section (no animation, before paint) */
    function initSidebar() {
        var savedOpen = null;
        try { savedOpen = localStorage.getItem('sbOpen'); } catch(e) {}

        /* Priority: section with active page > saved > ACTIVE > first */
        var openId = null;
        SECTIONS.forEach(function(id) {
            var el = document.getElementById('section-' + id);
            if (el && el.querySelector('.nav-active')) openId = id;
        });
        if (!openId && savedOpen && document.getElementById('section-' + savedOpen)) openId = savedOpen;
        if (!openId) openId = ACTIVE;
        if (!openId || !document.getElementById('section-' + openId)) {
            /* fallback to first existing section */
            for (var i = 0; i < SECTIONS.length; i++) {
                if (document.getElementById('section-' + SECTIONS[i])) { openId = SECTIONS[i]; break; }
            }
        }

        SECTIONS.forEach(function(id) {
            var el = document.getElementById('section-' + id);
            if (!el) return;
            applySection(id, id === openId, false);  /* animate = false */
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        initSidebar();
    }
})();

/* ── Flatpickr: custom month dropdown ── */
function fpBuildMonthPicker(fp) {
    var select = fp.calendarContainer.querySelector('.flatpickr-monthDropdown-months');
    if (!select || fp.calendarContainer._monthPatched) return;
    fp.calendarContainer._monthPatched = true;

    var months = fp.l10n.months.longhand;
    var chevron = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2.5" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>';

    /* Trigger button — replaces native select inline */
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.style.cssText = 'font-family:"Plus Jakarta Sans",sans-serif;font-weight:700;font-size:14px;color:#111827;background:none;border:none;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:3px;line-height:1;';
    function setLabel() { btn.innerHTML = months[fp.currentMonth] + ' ' + chevron; }
    setLabel();

    /* Panel appended to body so it escapes any overflow/stacking context */
    var panel = document.createElement('div');
    panel.style.cssText = [
        'display:none', 'position:fixed',
        'background:#fff', 'border-radius:16px',
        'box-shadow:0 12px 40px rgba(0,0,0,.13),0 2px 8px rgba(0,0,0,.07)',
        'z-index:999999', 'padding:6px', 'min-width:130px', 'max-height:260px', 'overflow-y:auto'
    ].join(';');
    document.body.appendChild(panel);

    months.forEach(function(name, idx) {
        var item = document.createElement('div');
        item.textContent = name;
        item.style.cssText = 'padding:9px 14px;border-radius:10px;cursor:pointer;font-size:13px;font-family:"Plus Jakarta Sans",sans-serif;font-weight:600;color:#374151;white-space:nowrap;transition:background .1s;';
        item.addEventListener('mouseenter', function() { item.style.background = '#f9fafb'; });
        item.addEventListener('mouseleave', function() { item.style.background = ''; });
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            fp.changeMonth(idx, false);
            panel.style.display = 'none';
        });
        panel.appendChild(item);
    });

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (panel.style.display !== 'none') { panel.style.display = 'none'; return; }
        /* Position panel below the button using viewport coords */
        var r = btn.getBoundingClientRect();
        panel.style.top  = (r.bottom + 6) + 'px';
        panel.style.left = Math.max(8, r.left + r.width / 2 - 65) + 'px';
        panel.style.display = 'block';
    });

    document.addEventListener('click', function() { panel.style.display = 'none'; });

    /* Replace native select with button */
    select.parentNode.replaceChild(btn, select);

    /* Keep label in sync when navigating with prev/next arrows */
    fp.config.onMonthChange.push(setLabel);
}

/* ── Flatpickr Init ── */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="date"]').forEach(function (input) {
        var originalOnchange = input.onchange;
        var maxDate    = input.getAttribute('max')  || null;
        var minDate    = input.getAttribute('min')  || null;
        var currentVal = input.value || null;

        flatpickr(input, {
            dateFormat   : 'Y-m-d',
            maxDate      : maxDate,
            minDate      : minDate,
            defaultDate  : currentVal,
            locale       : 'id',
            disableMobile: true,
            onReady: function(selectedDates, dateStr, instance) {
                fpBuildMonthPicker(instance);
            },
            onChange: function(selectedDates, dateStr) {
                if (originalOnchange) originalOnchange.call(input);
            },
        });
    });
});

/* ── Global Confirm Delete ── */
let _confirmTarget = null;

function askDelete(triggerEl, message) {
    // triggerEl can be: a <form>, or a button inside a form
    _confirmTarget = (triggerEl instanceof HTMLFormElement)
        ? triggerEl
        : triggerEl.closest('form');
    document.getElementById('confirmMessage').textContent = message || '{{ __('Tindakan ini tidak bisa dibatalkan.') }}';
    document.getElementById('confirmModal').classList.remove('hidden');
    document.getElementById('confirmOkBtn').focus();
}

function closeConfirm() {
    _confirmTarget = null;
    document.getElementById('confirmModal').classList.add('hidden');
}

function doConfirm() {
    if (_confirmTarget) {
        // Bypass any onsubmit that returns false (we've already confirmed)
        const form = _confirmTarget;
        closeConfirm();
        form.onsubmit = null;
        form.submit();
    } else {
        closeConfirm();
    }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeConfirm(); });
</script>
</body>
</html>
