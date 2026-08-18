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
            <a href="{{ route('dashboard') }}" class="px-3 py-2.5 rounded-xl text-gray-500 hover:bg-gray-100 transition">← Ke aplikasi</a>
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

    <main class="flex-1 md:ml-60 min-w-0 p-4 md:p-8 pt-20 md:pt-8 w-full">
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

<script>
    // CSRF for POST forms already via @csrf; small helper for confirm-toggles.
    document.querySelectorAll('[data-confirm]').forEach(f => {
        f.addEventListener('submit', e => { if(!confirm(f.dataset.confirm)) e.preventDefault(); });
    });
</script>
@yield('scripts')
</body>
</html>
