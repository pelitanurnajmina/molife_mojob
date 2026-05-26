@extends('layouts.app')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('breadcrumb', 'Settings')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- Profile & Security --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">

        {{-- Username --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold">Profil</h3>
                    <p class="text-xs text-gray-400">Ubah username akun kamu</p>
                </div>
            </div>

            @if(session('toast'))
            <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('toast') }}
            </div>
            @endif

            @if($errors->has('username'))
            <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-xl text-sm font-medium">
                {{ $errors->first('username') }}
            </div>
            @endif

            <form method="POST" action="{{ route('settings.profile') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Username saat ini</label>
                    <p class="text-sm font-bold text-gray-800 bg-gray-50 px-4 py-3 rounded-xl">{{ auth()->user()->username }}</p>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Username baru</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        placeholder="Masukkan username baru"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-black text-white rounded-xl font-bold hover:bg-gray-800 transition-all text-sm">
                    Simpan Username
                </button>
            </form>
        </div>

        {{-- Password --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold">Keamanan</h3>
                    <p class="text-xs text-gray-400">Ganti password kamu</p>
                </div>
            </div>

            @if(session('pass_toast'))
            <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('pass_toast') }}
            </div>
            @endif

            @if($errors->has('current_password') || $errors->has('new_password'))
            <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-xl text-sm font-medium space-y-1">
                @foreach($errors->only(['current_password','new_password']) as $err)
                <p>{{ $err }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('settings.password') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Password saat ini</label>
                    <input type="password" name="current_password" required placeholder="••••••••"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Password baru</label>
                    <input type="password" name="new_password" required placeholder="Min. 6 karakter"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Konfirmasi password baru</label>
                    <input type="password" name="new_password_confirmation" required placeholder="Ulangi password baru"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-black text-white rounded-xl font-bold hover:bg-gray-800 transition-all text-sm">
                    Ganti Password
                </button>
            </form>
        </div>
    </div>

    {{-- Feature Toggles --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="mb-6">
            <h3 class="text-base font-bold">Tampilkan Fitur</h3>
            <p class="text-xs text-gray-400 mt-0.5">Aktifkan atau sembunyikan fitur dari navigasi. Data tidak akan terhapus.</p>
        </div>

        @php
        $featureList = [
            ['key' => 'sholat',    'label' => 'Sholat',            'desc' => 'Tracker sholat wajib, rawatib & sunnah',     'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
            ['key' => 'gym',       'label' => 'Gym',               'desc' => 'Log sesi gym dan kalori terbakar',            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['key' => 'run',       'label' => 'Lari / Run',        'desc' => 'Tracker jarak, pace, dan sesi lari',          'icon' => 'M22 12h-4l-3 9L9 3l-3 9H2'],
            ['key' => 'intimasi',  'label' => 'Intimasi',          'desc' => 'Tracker keintiman bersama pasangan',          'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ['key' => 'tasks',     'label' => 'Tasks',             'desc' => 'Tugas harian, mingguan & refleksi',           'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['key' => 'statistik', 'label' => 'Statistik',         'desc' => 'Grafik dan ringkasan 30 hari terakhir',       'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['key' => 'goals',     'label' => 'Goals & Reminder',  'desc' => 'Target pribadi dan pengingat harian',         'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
            ['key' => 'lamaran',   'label' => 'Lamaran Kerja',     'desc' => 'Kelola lamaran dan status interview',         'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ['key' => 'persiapan', 'label' => 'Persiapan Melamar', 'desc' => 'Link, file, dan template CV / cover letter',  'icon' => 'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z'],
        ];
        @endphp

        <div class="divide-y divide-gray-100">
            @foreach($featureList as $feat)
            @php $enabled = $features[$feat['key']] ?? true; @endphp
            <div class="feat-row flex items-center gap-4 py-4 first:pt-0 last:pb-0"
                 data-key="{{ $feat['key'] }}"
                 data-enabled="{{ $enabled ? 'true' : 'false' }}">

                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}"/>
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">{{ $feat['label'] }}</p>
                    <p class="text-xs text-gray-400">{{ $feat['desc'] }}</p>
                </div>

                <button type="button"
                    onclick="toggleFeature(this)"
                    data-key="{{ $feat['key'] }}"
                    aria-label="Toggle {{ $feat['label'] }}"
                    class="feat-toggle flex-shrink-0 relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 {{ $enabled ? 'bg-green-500' : 'bg-gray-200' }}">
                    <span class="block w-4 h-4 bg-white rounded-full shadow-sm absolute top-1 transition-all duration-200 {{ $enabled ? 'left-6' : 'left-1' }}"></span>
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Logout --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-black flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->username ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-bold">{{ auth()->user()->username }}</p>
                    <p class="text-xs text-gray-400">Akun aktif</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
const TOGGLE_URL = '{{ route("settings.toggle-feature") }}';
const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function toggleFeature(btn) {
    const key     = btn.dataset.key;
    const current = btn.closest('.feat-row').dataset.enabled === 'true';
    const newVal  = !current;

    applyToggle(btn, newVal);

    try {
        const res  = await fetch(TOGGLE_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({ feature: key }),
        });
        if (!res.ok) throw new Error();
        const json = await res.json();
        applyToggle(btn, json.enabled);
        showToast(json.enabled ? 'Fitur diaktifkan' : 'Fitur disembunyikan');
    } catch {
        applyToggle(btn, current);
        showToast('Gagal menyimpan, coba lagi.', true);
    }
}

function applyToggle(btn, enabled) {
    const row   = btn.closest('.feat-row');
    const thumb = btn.querySelector('span');

    row.dataset.enabled = enabled ? 'true' : 'false';
    btn.classList.toggle('bg-green-500', enabled);
    btn.classList.toggle('bg-gray-200',  !enabled);
    thumb.classList.toggle('left-6', enabled);
    thumb.classList.toggle('left-1',  !enabled);
}

let toastTimer;
function showToast(msg, isError = false) {
    document.getElementById('feat-toast')?.remove();
    clearTimeout(toastTimer);
    const el = document.createElement('div');
    el.id = 'feat-toast';
    el.className = `fixed bottom-24 md:bottom-6 right-4 md:right-6 z-50 px-4 py-3 ${isError ? 'bg-red-600' : 'bg-gray-900'} text-white text-sm font-medium rounded-2xl shadow-xl flex items-center gap-2`;
    el.style.animation = 'fadeIn .2s ease both';
    el.innerHTML = `<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="${isError
            ? 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'
            : 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z'
        }" clip-rule="evenodd"/></svg>${msg}`;
    document.body.appendChild(el);
    toastTimer = setTimeout(() => el.remove(), 2800);
}
</script>
@endpush
@endsection
