<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" style="background-color:#F8F9FA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Molife Admin — @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=26">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .adm-link.active { background:#000; color:#fff; }

        /* Dropdown on-brand (ganti tampilan bawaan browser) */
        .adm select {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.7rem center;
            background-size: 1rem;
            padding-right: 2.25rem;
            cursor: pointer;
        }
        .adm select:focus { outline: none; border-color: #111; }
    </style>
</head>
<body class="bg-[#F8F9FA] text-gray-900">
<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="hidden md:flex md:flex-col md:w-60 md:fixed md:inset-y-0 bg-white border-r border-gray-200 p-4">
        <div class="px-2 py-3 mb-2">
            <div class="text-lg font-extrabold tracking-tight">Molife <span class="text-orange-500">Admin</span></div>
            <div class="text-xs text-gray-400 mt-0.5">{{ auth()->user()->email }}</div>
        </div>
        @php $r = request()->route()->getName(); @endphp
        <nav class="flex flex-col gap-1 text-sm font-semibold">
            <a href="{{ route('admin.dashboard') }}" class="adm-link {{ $r==='admin.dashboard'?'active':'' }} px-3 py-2.5 rounded-xl hover:bg-gray-100 transition">Ringkasan</a>
            <a href="{{ route('admin.users') }}" class="adm-link {{ $r==='admin.users'?'active':'' }} px-3 py-2.5 rounded-xl hover:bg-gray-100 transition">Pengguna</a>
            <a href="{{ route('admin.influencers') }}" class="adm-link {{ $r==='admin.influencers'?'active':'' }} px-3 py-2.5 rounded-xl hover:bg-gray-100 transition">Influencer & Promo</a>
        </nav>
        <div class="mt-auto flex flex-col gap-1 text-sm font-semibold pt-4 border-t border-gray-100">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-100 transition">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Ke aplikasi
            </a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="w-full text-left px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 transition">Keluar</button>
            </form>
        </div>
    </aside>

    {{-- Mobile top bar --}}
    <div class="md:hidden fixed top-0 inset-x-0 z-20 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
        <div class="font-extrabold">Molife <span class="text-orange-500">Admin</span></div>
        <div class="flex gap-3 text-sm font-semibold">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard')?'text-black':'text-gray-400' }}">Ringkasan</a>
            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users')?'text-black':'text-gray-400' }}">User</a>
            <a href="{{ route('admin.influencers') }}" class="{{ request()->routeIs('admin.influencers')?'text-black':'text-gray-400' }}">Promo</a>
        </div>
    </div>

    <main class="adm flex-1 md:ml-60 min-w-0 p-4 md:p-8 pt-20 md:pt-8 w-full">
        @yield('content')
    </main>
</div>

{{-- Toast --}}
@if(session('toast'))
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-black text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg">
        {{ session('toast') }}
    </div>
    <script>setTimeout(()=>document.getElementById('toast')?.remove(), 4000);</script>
@endif

{{-- Modal konfirmasi bergaya Molife (menggantikan confirm() bawaan browser) --}}
<div id="admConfirm" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index:100">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="admCloseConfirm()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <div class="w-11 h-11 mx-auto mb-3 rounded-full bg-orange-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <p id="admConfirmMsg" class="text-center text-sm text-gray-600 mb-6 leading-relaxed font-semibold"></p>
        <div class="flex gap-3">
            <button type="button" onclick="admCloseConfirm()" class="flex-1 py-3 bg-gray-100 rounded-2xl font-bold text-sm hover:bg-gray-200 transition">Batal</button>
            <button type="button" id="admConfirmOk" class="flex-1 py-3 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800 transition">Ya, lanjut</button>
        </div>
    </div>
</div>

<script>
    // Konfirmasi bergaya kita untuk form yang punya data-confirm.
    (function () {
        var pending = null;
        var modal = document.getElementById('admConfirm');
        var msgEl = document.getElementById('admConfirmMsg');

        window.admCloseConfirm = function () {
            modal.classList.add('hidden'); modal.classList.remove('flex'); pending = null;
        };

        document.querySelectorAll('form[data-confirm]').forEach(function (f) {
            f.addEventListener('submit', function (e) {
                e.preventDefault();
                pending = f;
                msgEl.textContent = f.dataset.confirm;
                modal.classList.remove('hidden'); modal.classList.add('flex');
            });
        });

        document.getElementById('admConfirmOk').addEventListener('click', function () {
            if (pending) { var f = pending; pending = null; f.submit(); } // .submit() lewati listener → langsung kirim
        });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') window.admCloseConfirm(); });
    })();
</script>
@yield('scripts')
@include('partials.custom-select')
</body>
</html>
