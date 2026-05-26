<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Molife — @yield('title', 'Dashboard')</title>
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
        .nav-item { transition: all .15s; }
        input[type=time]::-webkit-calendar-picker-indicator { opacity: .5; cursor: pointer; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade-in { animation: fadeIn .3s ease; }
        /* Status pills */
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
<div id="toast" class="fixed top-4 right-4 z-50 bg-black text-white rounded-2xl px-5 py-4 shadow-2xl flex items-center gap-3 max-w-sm animate-fade-in">
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
    $_feats = auth()->check() ? \App\Models\UserStorage::fromSession()->getFeatures() : [];
    $_f = fn($k) => $_feats[$k] ?? true;
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="hidden md:flex w-64 bg-white border-r border-gray-100 flex-col p-6 fixed h-full z-10 overflow-y-auto">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center text-white">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
            </div>
            <span class="font-bold text-xl tracking-tight">Molife</span>
        </div>

        <nav class="flex-1 space-y-1">
            {{-- Life Section --}}
            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 px-3">Life</p>
            @php
                $lifeNav = [
                    ['route'=>'dashboard', 'label'=>'Dashboard',                 'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'feature'=>null],
                    ['route'=>'sholat',   'label'=>__('Sholat'),                 'icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'feature'=>'sholat'],
                    ['route'=>'gym',      'label'=>'Gym',                        'icon'=>'M13 10V3L4 14h7v7l9-11h-7z', 'feature'=>'gym'],
                    ['route'=>'run',      'label'=>__('Run / Lari'),             'icon'=>'M22 12h-4l-3 9L9 3l-3 9H2', 'feature'=>'run'],
                    ['route'=>'intimasi', 'label'=>__('Intimasi'),               'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'feature'=>'intimasi'],
                    ['route'=>'tasks',    'label'=>'Tasks & Notes',              'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'feature'=>'tasks'],
                    ['route'=>'statistik','label'=>__('Statistik'),              'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'feature'=>'statistik'],
                    ['route'=>'goals',    'label'=>__('Goals & Reminder'),       'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'feature'=>'goals'],
                ];
                $lifeNav = array_values(array_filter($lifeNav, fn($i) => $i['feature'] === null || $_f($i['feature'])));
            @endphp
            @foreach($lifeNav as $item)
            <a href="{{ route($item['route']) }}" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-sm font-medium {{ request()->routeIs($item['route']) ? 'nav-active' : 'text-gray-500 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                {{ $item['label'] }}
            </a>
            @endforeach

            {{-- Career Section --}}
            @php
                $careerNav = array_values(array_filter([
                    ['route'=>'karir',           'match'=>'karir',       'label'=>__('Statistik Karir'),   'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'feature'=>'lamaran'],
                    ['route'=>'lamaran.index',   'match'=>'lamaran.*',   'label'=>__('Lamaran Kerja'),     'icon'=>'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'feature'=>'lamaran'],
                    ['route'=>'persiapan.index', 'match'=>'persiapan.*', 'label'=>__('Persiapan Melamar'), 'icon'=>'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z', 'feature'=>'persiapan'],
                ], fn($i) => $_f($i['feature'])));
            @endphp
            @if(count($careerNav) > 0)
            <div class="pt-4">
                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 px-3">Karir</p>
                @foreach($careerNav as $item)
                <a href="{{ route($item['route']) }}" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-sm font-medium {{ request()->routeIs($item['match']) ? 'nav-active' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                    {{ $item['label'] }}
                </a>
                @endforeach
            </div>
            @endif
        </nav>

        <div class="mt-6 pt-6 border-t border-gray-100">
            <a href="{{ route('settings') }}" class="flex items-center gap-3 p-3 rounded-2xl hover:bg-gray-50 transition-all {{ request()->routeIs('settings') ? 'bg-gray-100' : '' }}">
                <div class="w-9 h-9 rounded-full bg-black flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->username ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold truncate">{{ auth()->user()->username ?? 'User' }}</p>
                    <p class="text-[10px] text-gray-400">{{ __('Profil & Pengaturan') }}</p>
                </div>
            </a>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="md:ml-64 flex-1 p-4 md:p-8 w-full">
        <header class="flex justify-between items-center mb-6 md:mb-8">
            <div>
                <h1 class="text-xl md:text-2xl font-bold">@yield('page-title')</h1>
                <p class="text-gray-400 text-xs md:text-sm hidden md:block">Molife › @yield('breadcrumb')</p>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                {{-- Language switcher --}}
                <div class="flex items-center bg-white rounded-full border border-gray-100 p-0.5">
                    <a href="{{ route('lang.switch', 'id') }}"
                       class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'id' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}"
                       class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ app()->getLocale() === 'en' ? 'bg-black text-white' : 'text-gray-400 hover:text-gray-700' }}">EN</a>
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

        @yield('content')
    </main>
</div>

{{-- Mobile Bottom Nav --}}
@php
    $mobileNavAll = [
        ['route'=>'dashboard',       'match'=>'dashboard',   'label'=>'Home',      'feature'=>null,       'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route'=>'sholat',          'match'=>'sholat',      'label'=>'Sholat',    'feature'=>'sholat',   'icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
        ['route'=>'gym',             'match'=>'gym',         'label'=>'Gym',       'feature'=>'gym',      'icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
        ['route'=>'run',             'match'=>'run',         'label'=>'Run',       'feature'=>'run',      'icon'=>'M22 12h-4l-3 9L9 3l-3 9H2'],
        ['route'=>'intimasi',        'match'=>'intimasi',    'label'=>'Intim',     'feature'=>'intimasi', 'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
        ['route'=>'tasks',           'match'=>'tasks',       'label'=>'Tasks',     'feature'=>'tasks',    'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ['route'=>'statistik',       'match'=>'statistik',   'label'=>'Stats',     'feature'=>'statistik','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['route'=>'goals',           'match'=>'goals',       'label'=>'Goals',     'feature'=>'goals',    'icon'=>'M13 3l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
        ['route'=>'karir',           'match'=>'karir',       'label'=>'Statistik', 'feature'=>'lamaran',  'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ['route'=>'lamaran.index',   'match'=>'lamaran.*',   'label'=>'Lamaran',   'feature'=>'lamaran',  'icon'=>'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['route'=>'persiapan.index', 'match'=>'persiapan.*', 'label'=>'Persiapan', 'feature'=>'persiapan','icon'=>'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z'],
        ['route'=>'settings',        'match'=>'settings',    'label'=>'Setting',   'feature'=>null,       'icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
    ];
    $mobileNav = array_values(array_filter($mobileNavAll, fn($i) => $i['feature'] === null || $_f($i['feature'])));
    $mobileCount = count($mobileNav);
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
/* ── Flatpickr Init ── */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="date"]').forEach(function (input) {
        var originalOnchange = input.onchange;         // capture HTML onchange attr
        var maxDate  = input.getAttribute('max')  || null;
        var minDate  = input.getAttribute('min')  || null;
        var currentVal = input.value || null;

        flatpickr(input, {
            dateFormat   : 'Y-m-d',
            maxDate      : maxDate,
            minDate      : minDate,
            defaultDate  : currentVal,
            locale       : 'id',
            disableMobile: true,
            onChange: function (selectedDates, dateStr) {
                if (originalOnchange) {
                    originalOnchange.call(input);
                }
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
