<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" style="background-color:#F8F9FA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.pwa-head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Molife — @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/icon.png') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=10">
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

        /* ── Page transitions ──
           Cross-document View Transitions: browser menahan tampilan halaman lama
           sampai halaman baru siap, lalu crossfade. Tidak ada layar putih/kedip.
           (Chrome/Edge/Safari terbaru; browser lain fallback ke navigasi biasa.) */
        @view-transition { navigation: auto; }
        @keyframes vtOut { to { opacity: 0; } }
        @keyframes vtIn  { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        ::view-transition-old(root) { animation: vtOut .12s ease-out both; }
        ::view-transition-new(root) { animation: vtIn .22s ease-out both; }
        @media (prefers-reduced-motion: reduce) {
            ::view-transition-old(root), ::view-transition-new(root) { animation: none !important; }
        }
        /* Fallback universal (browser tanpa View Transitions): fade-in singkat
           supaya kemunculan halaman tidak "menghentak". */
        @keyframes pageIn { from { opacity: .35; } to { opacity: 1; } }
        .page-content { animation: pageIn .18s ease-out both; }

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
            overflow: visible;             /* never clip the last (Minggu) column */
            padding: 8px 12px 12px;
            width: auto !important;
            margin-top: 8px;               /* breathing room from the field */
        }
        .flatpickr-calendar.arrowTop:before, .flatpickr-calendar.arrowTop:after,
        .flatpickr-calendar.arrowBottom:before, .flatpickr-calendar.arrowBottom:after { display: none; }
        .flatpickr-months { padding: 4px 0 8px; border-bottom: 1px solid #f3f4f6; position: relative; }
        .flatpickr-month { height: 38px; }
        .flatpickr-current-month { font-size: 14px; font-weight: 700; color: #111827; padding-top: 0; height: 38px; display: flex; align-items: center; justify-content: center; }
        .flatpickr-current-month .cur-month { font-weight: 700; }
        .flatpickr-current-month input.cur-year { font-weight: 700; line-height: normal; }
        /* Vertically center the prev/next arrows with the month title */
        .flatpickr-prev-month, .flatpickr-next-month {
            top: 4px !important; height: 38px !important;
            display: flex !important; align-items: center; justify-content: center;
            padding: 0 8px !important; color: #6b7280 !important;
        }
        .flatpickr-prev-month svg, .flatpickr-next-month svg { width: 14px; height: 14px; }
        .flatpickr-prev-month:hover, .flatpickr-next-month:hover { color: #000 !important; }
        .flatpickr-prev-month svg, .flatpickr-next-month svg { fill: currentColor; }
        /* Fixed 7×40px grid so weekdays + days line up perfectly and nothing is cut off */
        .flatpickr-rContainer, .flatpickr-days, .dayContainer,
        .flatpickr-weekdaycontainer, .flatpickr-weekdays {
            width: 280px !important;
            min-width: 280px !important;
            max-width: 280px !important;
        }
        .flatpickr-weekdays { background: transparent; padding: 6px 0 2px; }
        .flatpickr-weekday { font-weight: 700; font-size: 11px; color: #9ca3af; background: transparent; flex: 1; }
        .dayContainer { padding: 2px 0; justify-content: flex-start; }
        .flatpickr-day {
            border-radius: 10px !important;
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            border: none !important;
            height: 36px;
            line-height: 36px;
            width: 40px;
            max-width: 40px;
            flex-basis: 40px;
            margin: 1px 0;
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
        .cs-trigger.cs-white { background: #fff; }
        .cs-trigger.open, .cs-trigger:focus { border-color: #000; background: #fff; outline: none; box-shadow: 0 0 0 3px rgba(0,0,0,0.06); }
        .cs-trigger-label { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cs-chevron { flex-shrink: 0; width: 14px; height: 14px; color: #9ca3af; transition: transform .2s, color .15s; }
        .cs-trigger.open .cs-chevron { transform: rotate(180deg); color: #111; }
        .cs-dropdown {
            background: #fff; border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0,0,0,.13), 0 2px 8px rgba(0,0,0,.07);
            z-index: 99999; padding: 6px;
            max-height: 280px; overflow-y: auto;
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
        /* Hide the browser's native date/month calendar icon so only our custom SVG shows.
           Keep it clickable by overlaying it (transparent) on top of the custom icon area. */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="month"]::-webkit-calendar-picker-indicator {
            position: absolute; right: 0; top: 0;
            width: 2.4rem; height: 100%;
            margin: 0; padding: 0;
            opacity: 0; cursor: pointer;
        }
        input[type="date"], input[type="month"] { position: relative; }
        /* Date field (flatpickr altInput) — clear clickable affordance on hover */
        .mo-datefield { cursor: pointer; transition: border-color .15s, background-color .15s, box-shadow .15s; }
        .mo-datefield:hover { border-color: #9ca3af !important; background-color: #fff !important; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
        .mo-datefield-icon { transition: color .15s; }
        .mo-datefield:hover ~ .mo-datefield-icon { color: #374151; }
        @keyframes pomoPulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.35; transform:scale(.7); } }
    </style>
</head>
<body class="bg-[#F8F9FA] min-h-screen">

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
    $_feats   = auth()->check() ? \App\Support\Features::map() : [];
    $_profile = auth()->check() ? \App\Support\Profile::data() : [];
    $_f       = fn($k) => $_feats[$k] ?? false;
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
            /* Standalone top-level item (outside any section) */
            $homeNav = ['route'=>'dashboard', 'label'=>__('Dashboard'), 'match'=>'dashboard', 'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'];

            $lifeNav = [
                ['route'=>'sholat',     'feat'=>'sholat',       'label'=>__('Sholat'),          'match'=>'sholat',      'icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                ['route'=>'gym',        'feat'=>'gym',          'label'=>'Gym',                 'match'=>'gym',         'icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
                ['route'=>'run',        'feat'=>'run',          'label'=>__('Lari'),            'match'=>'run',         'icon'=>'M22 12h-4l-3 9L9 3l-3 9H2'],
                ['route'=>'cycling',    'feat'=>'cycling',      'label'=>__('Bersepeda'),       'match'=>'cycling',     'icon'=>'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
                ['route'=>'swimming',   'feat'=>'swimming',     'label'=>__('Renang'),          'match'=>'swimming',    'icon'=>'M7 16.5c2 1 4 1 6 0s4-1 6 0M7 11.5c2 1 4 1 6 0s4-1 6 0M3 7.5c2 1 4 1 6 0m-9 9V7.5'],
                ['route'=>'racket',     'feat'=>'racket',       'label'=>'Tenis/Badminton',    'match'=>'racket',      'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                ['route'=>'custom_sport','feat'=>'custom_sport','label'=>$_sportLabel,         'match'=>'custom_sport','icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                ['route'=>'intimasi',   'feat'=>'intimasi',     'label'=>__('Intimasi'),        'match'=>'intimasi',    'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ];
            // Siklus Haid: hanya untuk akun perempuan.
            if (\App\Support\Profile::isFemale()) {
                $lifeNav[] = ['route'=>'haid', 'feat'=>'haid', 'label'=>__('Siklus Haid'), 'match'=>'haid', 'icon'=>'M12 3c0 0-6 6.9-6 11a6 6 0 0012 0c0-4.1-6-11-6-11z'];
            }
            $lifeNav = array_merge($lifeNav, [
                ['route'=>'quit',       'feat'=>'porn',         'label'=>'Stop Porn',           'match'=>'quit',        'routeParams'=>['type'=>'porn'],   'quitType'=>'porn',   'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['route'=>'quit',       'feat'=>'sosmed',       'label'=>__('Kurangi Sosmed'),  'match'=>'quit',        'routeParams'=>['type'=>'sosmed'], 'quitType'=>'sosmed', 'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                ['route'=>'motivasi',   'feat'=>'motivasi',     'label'=>__('Motivasi'),        'match'=>'motivasi',    'icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z'],
                ['route'=>'pomodoro',   'feat'=>'pomodoro',     'label'=>'Pomodoro',            'match'=>'pomodoro',    'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['route'=>'meditasi',   'feat'=>'meditasi',     'label'=>__('Meditasi'),        'match'=>'meditasi',    'icon'=>'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'],
                ['route'=>'mental',     'feat'=>'mental',       'label'=>__('Mental'),          'match'=>'mental',      'icon'=>'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['route'=>'tasks',      'feat'=>'tasks',        'label'=>'Tasks & Notes',       'match'=>'tasks',       'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['route'=>'links',      'feat'=>'links',        'label'=>__('Link Penting'),    'match'=>'links',       'icon'=>'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
                ['route'=>'journal',    'feat'=>'journal',      'label'=>'Journal',             'match'=>'journal',     'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                ['route'=>'statistik',  'feat'=>'statistik',    'label'=>__('Statistik'),       'match'=>'statistik',   'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['route'=>'goals',      'feat'=>'goals',        'label'=>__('Goals & Reminder'),'match'=>'goals',       'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
            ]);

            $careerNav = [
                ['route'=>'karir',           'feat'=>'lamaran',   'match'=>'karir',       'label'=>__('Overview'),          'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                ['route'=>'lamaran.index',   'feat'=>'lamaran',   'match'=>'lamaran.*',   'label'=>__('Lamaran Kerja'),     'icon'=>'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['route'=>'karir.lowongan',  'feat'=>'lamaran',   'match'=>'karir.lowongan', 'label'=>__('Lowongan Kerja'), 'icon'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['route'=>'persiapan.index', 'feat'=>'persiapan', 'match'=>'persiapan.*', 'label'=>__('Persiapan Melamar'),'icon'=>'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z'],
            ];

            $businessNav = [
                ['route'=>'bisnis.index', 'feat'=>'bisnis', 'match'=>'bisnis.index', 'label'=>__('Overview'),        'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                ['route'=>'bisnis.deals', 'feat'=>'bisnis', 'match'=>'bisnis.deals', 'label'=>__('Proposal & Klien'), 'icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['route'=>'bisnis.tugas', 'feat'=>'bisnis', 'match'=>'bisnis.tugas', 'label'=>__('Tugas'), 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['route'=>'bisnis.docs',  'feat'=>'bisnis', 'match'=>'bisnis.docs',  'label'=>__('Dokumen & File'),  'icon'=>'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
            ];
            // Kolaborasi: tampil bila user diundang ke produk orang lain (tanpa perlu fitur bisnis aktif).
            $hasCollab = \App\Services\CollabService::hasAny(auth()->id());
            if ($hasCollab) {
                $businessNav[] = ['route'=>'kolaborasi.index', 'match'=>'kolaborasi.*', 'label'=>__('Kolaborasi'), 'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'];
            }

            $financeNav = [
                ['route'=>'finance.index',    'feat'=>'finance', 'match'=>'finance.index',   'label'=>__('Overview'),      'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['route'=>'finance.transaksi','feat'=>'finance', 'match'=>'finance.transaksi','label'=>__('Transaksi'),    'icon'=>'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                ['route'=>'finance.anggaran', 'feat'=>'finance', 'match'=>'finance.anggaran', 'label'=>__('Anggaran'),     'icon'=>'M9 7h6m0 0l-3-3m3 3l-3 3M9 17h6m0 0l-3-3m3 3l-3 3M4 12h16'],
                ['route'=>'finance.tabungan', 'feat'=>'finance', 'match'=>'finance.tabungan', 'label'=>__('Tabungan'),     'icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            ];

            $settingsNav = [
                ['route'=>'settings.profil',    'match'=>'settings.profil',    'label'=>__('Profil & Akun'),    'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['route'=>'settings.tampilan',  'match'=>'settings.tampilan',  'label'=>__('Tampilan & Fitur'), 'icon'=>'M4 6h16M4 12h16M4 18h7'],
                ['route'=>'settings.langganan', 'match'=>'settings.langganan', 'label'=>__('Langganan'),        'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                ['route'=>'settings.referral',  'match'=>'settings.referral',  'label'=>__('Referral'),         'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
            ];

            /* Determine which section is active */
            $activeSection = 'life';
            if (request()->routeIs('karir') || request()->routeIs('lamaran.*') || request()->routeIs('persiapan.*')) $activeSection = 'karir';
            if (request()->routeIs('bisnis.*') || request()->routeIs('kolaborasi.*')) $activeSection = 'bisnis';
            if (request()->routeIs('finance.*'))  $activeSection = 'finance';
            if (request()->routeIs('settings.*') || request()->routeIs('settings')) $activeSection = 'settings';

            $sections = [
                ['id'=>'life',     'label'=>__('Life'),     'items'=>$lifeNav],
                ['id'=>'karir',    'label'=>__('Karir'),    'items'=>$careerNav],
                ['id'=>'bisnis',   'label'=>__('Bisnis'),   'items'=>$businessNav],
                ['id'=>'finance',  'label'=>__('Finance'),  'items'=>$financeNav],
                ['id'=>'settings', 'label'=>__('Settings & Referral'), 'items'=>$settingsNav],
            ];
            @endphp

            {{-- Standalone top-level: Dashboard (outside sections) --}}
            <a href="{{ route($homeNav['route']) }}" data-tour="dashboard-nav"
                class="w-full flex items-center px-3 py-1.5 mb-1 rounded-lg hover:bg-gray-50 transition-all
                {{ request()->routeIs($homeNav['match']) ? 'text-black' : 'text-gray-400' }}">
                <span class="text-[10px] uppercase font-bold tracking-widest">{{ $homeNav['label'] }}</span>
            </a>
            <div class="sidebar-divider h-px bg-gray-100 my-1.5"></div>

            {{-- Collapsible sections --}}
            <div id="sidebarNav">
            @foreach($sections as $sec)
            @php
                $secHasVisible = collect($sec['items'])->contains(fn($i) => empty($i['feat']) || $_f($i['feat']));
            @endphp
            <div class="sidebar-section mb-1 {{ $secHasVisible ? '' : 'hidden' }}" data-section="{{ $sec['id'] }}">
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
                    @php
                        $itemVisible = empty($item['feat']) || $_f($item['feat']);
                        $href = route($item['route'], $item['routeParams'] ?? []);
                        if (isset($item['quitType'])) {
                            $isActive = request()->routeIs('quit') && request()->route('type') === $item['quitType'];
                        } else {
                            $isActive = request()->routeIs($item['match']);
                        }
                    @endphp
                    <a href="{{ $href }}"
                        @if(!empty($item['feat'])) data-feat="{{ $item['feat'] }}" @endif
                        class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all
                        {{ $isActive ? 'nav-active' : 'text-gray-500 hover:bg-gray-50' }} {{ $itemVisible ? '' : 'hidden' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $item['icon'] }}"/>
                        </svg>
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                    </div>
                </div>
            </div>
            @if(!$loop->last)<div class="sidebar-divider h-px bg-gray-100 my-1.5 {{ $secHasVisible ? '' : 'hidden' }}"></div>@endif
            @endforeach
            </div>
        </nav>

    </aside>

    {{-- Main Content --}}
    <main class="md:ml-64 flex-1 p-4 md:p-8 w-full">
        <header class="flex justify-between items-center gap-3 mb-3 md:mb-4">
            <div class="flex items-center gap-2.5 min-w-0">
                {{-- Hamburger (mobile) — far left --}}
                <button type="button" onclick="openMobileMenu()" aria-label="{{ __('Menu') }}"
                    class="md:hidden flex-shrink-0 w-10 h-10 flex items-center justify-center bg-white rounded-xl border border-gray-100 hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-lg md:text-2xl font-bold truncate">@yield('page-title')</h1>
                    <p class="text-gray-400 text-xs md:text-sm hidden md:block">Molife › @yield('breadcrumb')</p>
                </div>
            </div>
            <div class="flex items-center gap-2 md:gap-4 flex-shrink-0">
                {{-- Language switcher (desktop only; mobile lives in the drawer) --}}
                <div class="hidden md:flex items-center bg-white rounded-full border border-gray-100 p-0.5">
                    <a href="{{ route('lang.switch', 'id') }}"
                       class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'id' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}"
                       class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">EN</a>
                </div>

                {{-- Notifications --}}
                @php $_notifs = auth()->check() ? \App\Support\Notifications::for(auth()->id()) : []; @endphp
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

                {{-- Date (desktop only) --}}
                <div class="hidden md:flex bg-white px-4 py-2 rounded-full border border-gray-100 items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ date('j/n') }}</span>
                </div>
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
        ['route'=>'gym',             'match'=>'gym',          'label'=>'Gym',                        'icon'=>$_ico['gym'],          'show'=>$_f('gym')],
        ['route'=>'run',             'match'=>'run',          'label'=>'Lari',                       'icon'=>$_ico['run'],          'show'=>$_f('run')],
        ['route'=>'cycling',         'match'=>'cycling',      'label'=>'Sepeda',                     'icon'=>$_ico['cycling'],      'show'=>$_f('cycling')],
        ['route'=>'swimming',        'match'=>'swimming',     'label'=>'Renang',                     'icon'=>$_ico['swimming'],     'show'=>$_f('swimming')],
        ['route'=>'racket',          'match'=>'racket',       'label'=>'Tenis',                      'icon'=>$_ico['racket'],       'show'=>$_f('racket')],
        ['route'=>'custom_sport',    'match'=>'custom_sport', 'label'=>mb_substr($_sportLabel,0,6),  'icon'=>$_ico['custom_sport'], 'show'=>$_f('custom_sport')],
        ['route'=>'intimasi',        'match'=>'intimasi',     'label'=>'Intim',                      'icon'=>$_ico['intimasi'],     'show'=>$_f('intimasi')],
        ['route'=>'quit',            'match'=>'quit',         'label'=>'Porn',     'params'=>['type'=>'porn'],   'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'show'=>$_f('porn')],
        ['route'=>'quit',            'match'=>'quit',         'label'=>'Sosmed',   'params'=>['type'=>'sosmed'], 'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'show'=>$_f('sosmed')],
        ['route'=>'motivasi',        'match'=>'motivasi',     'label'=>'Motivasi', 'icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z', 'show'=>$_f('motivasi')],
        ['route'=>'pomodoro',        'match'=>'pomodoro',     'label'=>'Pomodoro', 'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'show'=>$_f('pomodoro')],
        ['route'=>'mental',          'match'=>'mental',       'label'=>'Mental',                     'icon'=>$_ico['mental'],       'show'=>$_f('mental')],
        ['route'=>'tasks',           'match'=>'tasks',        'label'=>'Tasks',                      'icon'=>$_ico['tasks'],        'show'=>$_f('tasks')],
        ['route'=>'journal',         'match'=>'journal',      'label'=>'Journal',  'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'show'=>$_f('journal')],
        ['route'=>'links',           'match'=>'links',        'label'=>'Link',     'icon'=>'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'show'=>$_f('links')],
        ['route'=>'statistik',       'match'=>'statistik',    'label'=>'Stats',                      'icon'=>$_ico['statistik'],    'show'=>$_f('statistik')],
        ['route'=>'goals',           'match'=>'goals',        'label'=>'Goals',                      'icon'=>$_ico['goals'],        'show'=>$_f('goals')],
        ['route'=>'karir',            'match'=>'karir',         'label'=>__('Karir'),     'icon'=>$_ico['karir'],     'show'=>$_f('lamaran')],
        ['route'=>'lamaran.index',   'match'=>'lamaran.*',    'label'=>'Lamaran',   'icon'=>$_ico['lamaran'],   'show'=>$_f('lamaran')],
        ['route'=>'persiapan.index', 'match'=>'persiapan.*', 'label'=>'Persiapan', 'icon'=>$_ico['persiapan'], 'show'=>$_f('persiapan')],
        ['route'=>'bisnis.index',    'match'=>'bisnis.index','label'=>__('Bisnis'),    'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'show'=>$_f('bisnis')],
        ['route'=>'bisnis.deals',    'match'=>'bisnis.deals','label'=>'Proposal',  'icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'show'=>$_f('bisnis')],
        ['route'=>'bisnis.docs',     'match'=>'bisnis.docs', 'label'=>'Dok Bisnis','icon'=>'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'show'=>$_f('bisnis')],
        ['route'=>'finance.index',   'match'=>'finance.*',    'label'=>'Finance',   'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'show'=>$_f('finance')],
        ['route'=>'settings',        'match'=>'settings',     'label'=>'Setting',   'icon'=>$_ico['settings'],  'show'=>true],
    ];
    $mobileNav       = array_values(array_filter($mobileNavAll, fn($i) => $i['show']));
    $mobileCount     = count($mobileNav);
    $mobileGridClass = 'grid-cols-' . min($mobileCount, 10);
@endphp
@php
    /* Active-state helper (handles quit type variants) */
    $mobActive = function($item) {
        $m = request()->routeIs($item['match']);
        if ($m && isset($item['quitType'])) {
            $m = (request('type') ?? request()->route('type')) === $item['quitType'];
        }
        return $m;
    };
    /* Drawer sections (reuse the sidebar nav arrays defined above) */
    $mobSections = [
        [__('Life'),            $lifeNav],
        [__('Karier'),      $careerNav],
        [__('Bisnis'),          $businessNav],
        [__('Finance'),         $financeNav],
        [__('Pengaturan'),  $settingsNav],
    ];
@endphp

{{-- ── Mobile full menu drawer ── --}}
<div id="mobileMenu" class="md:hidden fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeMobileMenu()"></div>
    <div id="mobileMenuPanel" class="absolute top-0 left-0 h-full w-[84%] max-w-[330px] bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <img src="{{ asset('images/logo.png') }}" class="h-7">
            <button onclick="closeMobileMenu()" class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5">
            {{-- Home --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $homeNav['icon'] }}"/></svg>
                <span class="text-sm font-bold">Home</span>
            </a>

            @foreach($mobSections as [$secTitle, $secItems])
                @php $visible = array_filter($secItems, fn($it) => !isset($it['feat']) || $_f($it['feat'])); @endphp
                @if(count($visible))
                <div>
                    <p class="px-3 mb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ $secTitle }}</p>
                    <div class="space-y-0.5">
                        @foreach($visible as $item)
                        <a href="{{ route($item['route'], $item['routeParams'] ?? []) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ $mobActive($item) ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                            <span class="text-sm font-bold">{{ $item['label'] }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach
        </nav>

        {{-- Drawer footer: language + logout --}}
        <div class="border-t border-gray-100 px-4 py-3 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">{{ __('Bahasa') }}</span>
                <div class="flex items-center bg-gray-100 rounded-full p-0.5">
                    <a href="{{ route('lang.switch', 'id') }}" class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'id' ? 'bg-black text-white' : 'text-gray-500' }}">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-black text-white' : 'text-gray-500' }}">EN</a>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ __('Keluar') }}
                </button>
            </form>
        </div>
    </div>
</div>

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
            /* Select toolbar (ditulis bg-white) → trigger ikut putih; select form modal tetap abu. */
            if (select.classList.contains('bg-white')) trigger.classList.add('cs-white');
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

                /* Prefer opening downward; only flip up when below is too tight
                   AND there's clearly more room above. Cap height to available space. */
                requestAnimationFrame(function () {
                    var gap        = 6, margin = 8;
                    var spaceBelow = window.innerHeight - rect.bottom - gap - margin;
                    var spaceAbove = rect.top - gap - margin;
                    var dh         = dd.offsetHeight;

                    if (dh <= spaceBelow || spaceBelow >= spaceAbove) {
                        /* Open downward (default), clamp height to space below */
                        dd.style.top       = (rect.bottom + gap) + 'px';
                        dd.style.maxHeight = Math.min(280, Math.max(120, spaceBelow)) + 'px';
                    } else {
                        /* Not enough below and more room above → flip up */
                        var h = Math.min(280, Math.max(120, spaceAbove));
                        dd.style.maxHeight = h + 'px';
                        dd.style.top       = (rect.top - Math.min(dd.offsetHeight, h) - gap) + 'px';
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

{{-- ── Global Pomodoro engine (persists across pages via localStorage) ── --}}
@auth
<script>
(function () {
    const KEY = 'pomoState';
    const STORE_URL = '{{ route('pomodoro.store') }}';
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const D = { running:false, mode:'focus', endsAt:0, remaining:25*60, focusMin:25, breakMin:5, label:'' };

    function load(){ try { return Object.assign({}, D, JSON.parse(localStorage.getItem(KEY)||'{}')); } catch(e){ return Object.assign({}, D); } }
    function save(){ try { localStorage.setItem(KEY, JSON.stringify(state)); } catch(e){} }
    let state = load();

    function remainingNow(){ return state.running ? Math.max(0, Math.round((state.endsAt - Date.now())/1000)) : state.remaining; }
    function phaseSeconds(m){ return (m === 'focus' ? state.focusMin : state.breakMin) * 60; }

    const api = {
        get(){ return Object.assign({}, state, { remaining: remainingNow() }); },
        start(){
            if (state.running) return;
            let rem = state.remaining > 0 ? state.remaining : phaseSeconds(state.mode);
            state.running = true; state.endsAt = Date.now() + rem*1000; save(); emit();
        },
        pause(){ if (!state.running) return; state.remaining = remainingNow(); state.running = false; save(); emit(); },
        reset(){ state.running = false; state.remaining = phaseSeconds(state.mode); save(); emit(); },
        setMode(m){ state.running = false; state.mode = m; state.remaining = phaseSeconds(m); save(); emit(); },
        setFocus(min){ state.focusMin = min; if (state.mode === 'focus' && !state.running) state.remaining = min*60; save(); emit(); },
        setLabel(t){ state.label = t; save(); },
        toggle(){ state.running ? api.pause() : api.start(); },
    };
    window.MojobPomodoro = api;

    function complete(){
        const finished = state.mode;
        const min = state.focusMin;
        const label = state.label || '';
        state.running = false;
        try { new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQ==').play(); } catch(e){}
        if (finished === 'focus') {
            fetch(STORE_URL, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body: JSON.stringify({ focus_minutes: min, label: label || null }) }).catch(()=>{});
            state.mode = 'break'; state.remaining = state.breakMin*60; state.label = '';
            if (window.showMojobToast) window.showMojobToast('Sesi fokus selesai! 🎉 Saatnya istirahat.');
            document.dispatchEvent(new CustomEvent('pomo:saved', { detail: { minutes: min, label: label } }));
        } else {
            state.mode = 'focus'; state.remaining = phaseSeconds('focus'); state.label = '';
            if (window.showMojobToast) window.showMojobToast('Istirahat selesai. Lanjut fokus!');
        }
        save(); emit();
    }

    function emit(){ document.dispatchEvent(new CustomEvent('pomo:change', { detail: api.get() })); updateIndicator(); }

    function fmt(s){ const m=Math.floor(s/60), x=s%60; return String(m).padStart(2,'0')+':'+String(x).padStart(2,'0'); }
    function updateIndicator(){
        const st = api.get();
        document.querySelectorAll('[data-feat="pomodoro"]').forEach(el => {
            let badge = el.querySelector('.pomo-badge');
            if (st.running) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'pomo-badge ml-auto inline-flex items-center gap-1 text-[10px] font-bold text-emerald-500 tabular-nums';
                    el.appendChild(badge);
                }
                badge.innerHTML = '<span style="width:6px;height:6px;border-radius:9999px;background:#10b981;display:inline-block;animation:pomoPulse 1.2s infinite"></span>' + fmt(st.remaining);
            } else if (badge) { badge.remove(); }
        });
    }

    setInterval(function () {
        if (state.running && remainingNow() <= 0) complete();
        document.dispatchEvent(new CustomEvent('pomo:tick', { detail: api.get() }));
        updateIndicator();
    }, 1000);

    updateIndicator();
})();
</script>
@endauth

@stack('scripts')
<script>
/* ── Collapsible Sidebar ── */
(function() {
    var ACTIVE   = '{{ $activeSection ?? "life" }}';
    var SECTIONS = ['life', 'karir', 'bisnis', 'finance', 'settings'];

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

    /* Live-update sidebar after feature save (no page refresh) */
    window.applySidebarFeatures = function(map) {
        // Toggle each feature nav item
        document.querySelectorAll('#sidebarNav [data-feat]').forEach(function(a) {
            var on = !!map[a.dataset.feat];
            a.classList.toggle('hidden', !on);
        });
        // Toggle section + divider visibility based on remaining visible items
        document.querySelectorAll('#sidebarNav .sidebar-section').forEach(function(sec) {
            var items   = sec.querySelectorAll('.nav-item');
            var visible = Array.prototype.some.call(items, function(it) { return !it.classList.contains('hidden'); });
            sec.classList.toggle('hidden', !visible);
            var divider = sec.nextElementSibling;
            if (divider && divider.classList.contains('sidebar-divider')) divider.classList.toggle('hidden', !visible);
            // Recompute open height
            var body = sec.querySelector('[id^="section-"]');
            if (body && body.style.maxHeight && body.style.maxHeight !== '0px') {
                body.style.maxHeight = 'none';
            }
        });
    };
})();

/* Lightweight global toast */
window.showMojobToast = function(msg) {
    var t = document.createElement('div');
    t.className = 'fixed top-4 right-4 z-[60] bg-gray-900 text-white rounded-2xl px-5 py-3 shadow-2xl text-sm font-bold animate-fade-in';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 3000);
};

/* ── Mobile menu drawer ── */
function openMobileMenu() {
    var m = document.getElementById('mobileMenu');
    if (!m) return;
    m.classList.remove('hidden');
    requestAnimationFrame(function(){ document.getElementById('mobileMenuPanel').classList.remove('-translate-x-full'); });
    document.body.style.overflow = 'hidden';
}
function closeMobileMenu() {
    var p = document.getElementById('mobileMenuPanel');
    if (!p) return;
    p.classList.add('-translate-x-full');
    document.body.style.overflow = '';
    setTimeout(function(){ document.getElementById('mobileMenu').classList.add('hidden'); }, 300);
}
document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeMobileMenu(); });

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
    panel.className = 'fp-month-panel';
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

    /* One global closer for all month panels (avoids listener piling up on re-init) */
    if (!window.__fpPanelCloser) {
        window.__fpPanelCloser = function () {
            document.querySelectorAll('.fp-month-panel').forEach(function (p) { p.style.display = 'none'; });
        };
        document.addEventListener('click', window.__fpPanelCloser);
    }

    /* Replace native select with button */
    select.parentNode.replaceChild(btn, select);

    /* Keep label in sync when navigating with prev/next arrows */
    fp.config.onMonthChange.push(setLabel);
}

/* ── Flatpickr Init (reusable — dipanggil ulang setelah konten di-swap AJAX) ── */
window.initDatePickers = function () {
    /* Bersihkan instance & panel bulan lama agar tidak menumpuk */
    (window.__fpInstances || []).forEach(function (fp) { try { fp.destroy(); } catch (e) {} });
    window.__fpInstances = [];
    document.querySelectorAll('.fp-month-panel').forEach(function (p) { p.remove(); });

    document.querySelectorAll('input[type="date"]').forEach(function (input) {
        if (input._flatpickr) { try { input._flatpickr.destroy(); } catch (e) {} }
        var originalOnchange = input.onchange;
        var maxDate    = input.getAttribute('max')  || null;
        var minDate    = input.getAttribute('min')  || null;
        var currentVal = input.value || null;

        flatpickr(input, {
            dateFormat   : 'Y-m-d',
            altInput     : true,          // show a clean text field, hide the native date input
            altFormat    : 'Y-m-d',       // keep the same readable format
            maxDate      : maxDate,
            minDate      : minDate,
            defaultDate  : currentVal,
            locale       : @json(app()->getLocale() === 'id' ? 'id' : 'default'),
            disableMobile: true,
            onReady: function(selectedDates, dateStr, instance) {
                fpBuildMonthPicker(instance);
                // Size the visible field to the date so the icon sits snug beside the text,
                // unless the field is meant to stretch full width.
                if (instance.altInput) {
                    if (!/\bw-full\b/.test(instance.altInput.className)) instance.altInput.size = 11;
                    // Consistent hover affordance (set in CSS via .mo-datefield).
                    instance.altInput.classList.add('mo-datefield');
                    // Make the sibling calendar icon react to hover too.
                    var icon = instance.altInput.parentNode && instance.altInput.parentNode.querySelector('svg');
                    if (icon) icon.classList.add('mo-datefield-icon');
                }
            },
            onChange: function(selectedDates, dateStr) {
                if (originalOnchange) originalOnchange.call(input);
            },
        });
        if (input._flatpickr) window.__fpInstances.push(input._flatpickr);
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initDatePickers);
} else {
    window.initDatePickers();
}

/* ── Instant forms (data-instant): submit tanpa reload, konten di-swap di tempat ──
   Dipakai untuk toggle cepat (mis. ceklis sholat) supaya tidak ada kedipan reload. */
document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-instant')) return;
    e.preventDefault();

    var fd = new FormData(form);
    form.querySelectorAll('button[type="submit"]').forEach(function (b) {
        b.classList.add('opacity-60', 'pointer-events-none');
    });

    fetch(form.action, {
        method: (form.method || 'POST').toUpperCase(),
        body: fd,
        credentials: 'same-origin',
        headers: { 'Accept': 'text/html' },
    }).then(function (res) { return res.text(); }).then(function (html) {
        var doc   = new DOMParser().parseFromString(html, 'text/html');
        var fresh = doc.getElementById('pageContent');
        var cur   = document.getElementById('pageContent');
        if (!fresh || !cur) { window.location.reload(); return; }

        var swap = function () {
            cur.innerHTML = fresh.innerHTML;
            if (window.initCustomSelects) try { window.initCustomSelects(); } catch (err) {}
            if (window.initDatePickers)  try { window.initDatePickers(); }  catch (err) {}
        };
        if (document.startViewTransition) document.startViewTransition(swap);
        else swap();
    }).catch(function () {
        // Fallback: submit biasa (full reload)
        form.removeAttribute('data-instant');
        form.requestSubmit ? form.requestSubmit() : form.submit();
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
