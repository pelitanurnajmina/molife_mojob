@extends('layouts.app')
@section('title', __('Settings'))
@section('page-title', __('Settings'))
@section('breadcrumb', 'Settings › Profil & Akun')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- ── Profile ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Profil') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Nama tampilan dan username akun') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.profile') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Nama Tampilan') }}</label>
                    <input type="text" name="display_name"
                        value="{{ old('display_name', $profile['display_name'] ?? '') }}"
                        placeholder="{{ __('Nama atau panggilan kamu') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-gray-400 focus:bg-white transition-all">
                    <p class="text-[10px] text-gray-400 mt-1">{{ __('Ditampilkan di sidebar dan greeting') }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Username') }}</label>
                    <input type="text" name="username"
                        value="{{ old('username', auth()->user()->username) }}" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-gray-400 focus:bg-white transition-all">
                    <p class="text-[10px] text-gray-400 mt-1">{{ __('Untuk login ke akun') }}</p>
                </div>
            </div>
            @if($errors->has('username'))
            <p class="text-xs text-red-500 mb-3">{{ $errors->first('username') }}</p>
            @endif
            <button type="submit" class="px-6 py-2.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Simpan Profil') }}
            </button>
        </form>
    </div>

    {{-- ── Preferensi (Onboarding) ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Preferensi') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Agama dan olahraga — mengatur fitur yang tampil') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.onboarding') }}">
            @csrf
            {{-- Religion --}}
            <div class="mb-5">
                <p class="text-xs font-bold text-gray-500 mb-3">{{ __('Agama / Kepercayaan') }}</p>
                @php
                $religions = [
                    ['value'=>'islam',   'label'=>'Islam',       'sub'=>'Sholat 5 waktu, Rawatib, Tepat Waktu'],
                    ['value'=>'kristen', 'label'=>'Kristen',     'sub'=>'Doa harian, Baca Alkitab, Ibadah Minggu'],
                    ['value'=>'hindu',   'label'=>'Hindu',       'sub'=>'Sembahyang pagi/sore, Meditasi'],
                    ['value'=>'buddha',  'label'=>'Buddha',      'sub'=>'Sembahyang pagi/sore, Meditasi'],
                    ['value'=>'lainnya', 'label'=>'Lainnya',     'sub'=>'Praktik spiritual & jurnal syukur'],
                    ['value'=>'none',    'label'=>'Tidak ada',   'sub'=>'Fitur spiritual disembunyikan'],
                ];
                $currentReligion = $profile['religion'] ?? '';
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($religions as $rel)
                    <label class="flex items-start gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all
                        {{ $currentReligion === $rel['value'] ? 'border-gray-900 bg-gray-50' : 'border-gray-100 hover:border-gray-200' }}">
                        <input type="radio" name="religion" value="{{ $rel['value'] }}"
                            {{ $currentReligion === $rel['value'] ? 'checked' : '' }}
                            class="mt-0.5 flex-shrink-0" onchange="this.closest('form').querySelectorAll('label').forEach(l=>l.classList.remove('border-gray-900','bg-gray-50')); this.closest('label').classList.add('border-gray-900','bg-gray-50')">
                        <div>
                            <p class="font-bold text-xs text-gray-800">{{ $rel['label'] }}</p>
                            <p class="text-[10px] text-gray-400 leading-relaxed">{{ $rel['sub'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Sports --}}
            <div class="mb-5">
                <p class="text-xs font-bold text-gray-500 mb-3">{{ __('Olahraga') }}</p>
                @php
                $sportOptions = [
                    ['value'=>'gym',         'label'=>'Gym / Fitness'],
                    ['value'=>'run',         'label'=>'Lari / Running'],
                    ['value'=>'cycling',     'label'=>'Bersepeda'],
                    ['value'=>'swimming',    'label'=>'Renang'],
                    ['value'=>'racket',      'label'=>'Tenis / Badminton'],
                    ['value'=>'custom_sport','label'=>'Olahraga Lainnya'],
                ];
                $currentSports = $profile['sports'] ?? [];
                @endphp
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($sportOptions as $sp)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 cursor-pointer transition-all
                        {{ in_array($sp['value'], $currentSports) ? 'border-gray-900 bg-gray-50' : 'border-gray-100 hover:border-gray-200' }}">
                        <input type="checkbox" name="sports[]" value="{{ $sp['value'] }}"
                            {{ in_array($sp['value'], $currentSports) ? 'checked' : '' }}
                            class="flex-shrink-0"
                            onchange="this.closest('label').classList.toggle('border-gray-900', this.checked); this.closest('label').classList.toggle('bg-gray-50', this.checked); this.closest('label').classList.toggle('border-gray-100', !this.checked)">
                        <span class="text-xs font-bold text-gray-700">{{ $sp['label'] }}</span>
                    </label>
                    @endforeach
                </div>
                @if(in_array('custom_sport', $currentSports))
                <input type="text" name="custom_sport_name"
                    value="{{ $profile['custom_sport_name'] ?? '' }}"
                    placeholder="{{ __('Nama olahraga (cth: Padel, Voli...)') }}"
                    class="w-full sm:w-64 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-black transition-all">
                @endif
            </div>

            <button type="submit" class="px-6 py-2.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Simpan Preferensi') }}
            </button>
        </form>
    </div>

    {{-- ── Feature Toggles ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Tampilkan Fitur') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Aktifkan atau sembunyikan dari navigasi. Data tidak terhapus.') }}</p>
            </div>
        </div>

        @php
        $featureGroups = [
            'Life' => [
                ['key'=>'sholat',      'label'=>'Sholat',           'desc'=>'Tracker sholat wajib, rawatib & sunnah'],
                ['key'=>'gym',         'label'=>'Gym',               'desc'=>'Log sesi gym dan kalori terbakar'],
                ['key'=>'run',         'label'=>'Lari',              'desc'=>'Tracker jarak, pace, dan sesi lari'],
                ['key'=>'cycling',     'label'=>'Bersepeda',         'desc'=>'Track km dan durasi bersepeda'],
                ['key'=>'swimming',    'label'=>'Renang',            'desc'=>'Track lap dan durasi renang'],
                ['key'=>'racket',      'label'=>'Tenis / Badminton', 'desc'=>'Track sesi racket sports'],
                ['key'=>'custom_sport','label'=>'Olahraga Lainnya',  'desc'=>'Olahraga custom sesuai pilihan'],
                ['key'=>'intimasi',    'label'=>'Intimasi',          'desc'=>'Tracker keintiman bersama pasangan'],
                ['key'=>'mental',      'label'=>'Mental',            'desc'=>'Mood tracker, energi, dan refleksi harian'],
                ['key'=>'tasks',       'label'=>'Tasks & Notes',     'desc'=>'Tugas harian, mingguan & catatan'],
                ['key'=>'statistik',   'label'=>'Statistik',         'desc'=>'Grafik dan ringkasan 30 hari terakhir'],
                ['key'=>'insights',    'label'=>'Insights',          'desc'=>'Life Score dan pola dari semua aktivitas'],
                ['key'=>'goals',       'label'=>'Goals & Reminder',  'desc'=>'Target pribadi dan pengingat harian'],
            ],
            'Karir' => [
                ['key'=>'lamaran',     'label'=>'Lamaran Kerja',     'desc'=>'Kelola lamaran dan status interview'],
                ['key'=>'persiapan',   'label'=>'Persiapan Melamar', 'desc'=>'Link, file, template CV & persiapan wawancara'],
            ],
            'Finance' => [
                ['key'=>'finance',     'label'=>'Finance',           'desc'=>'Transaksi, anggaran & tujuan tabungan'],
            ],
        ];
        @endphp

        <div class="space-y-6">
            @foreach($featureGroups as $groupName => $feats)
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3">{{ $groupName }}</p>
                <div class="divide-y divide-gray-100 rounded-2xl border border-gray-100 overflow-hidden">
                    @foreach($feats as $feat)
                    @php $enabled = $features[$feat['key']] ?? false; @endphp
                    <div class="feat-row flex items-center gap-4 px-4 py-3" data-key="{{ $feat['key'] }}" data-enabled="{{ $enabled ? 'true' : 'false' }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800">{{ $feat['label'] }}</p>
                            <p class="text-xs text-gray-400">{{ $feat['desc'] }}</p>
                        </div>
                        <button type="button" onclick="toggleFeature(this)" data-key="{{ $feat['key'] }}"
                            class="feat-toggle flex-shrink-0 relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none {{ $enabled ? 'bg-gray-900' : 'bg-gray-200' }}">
                            <span class="block w-4 h-4 bg-white rounded-full shadow-sm absolute top-1 transition-all duration-200 {{ $enabled ? 'left-6' : 'left-1' }}"></span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Security ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Keamanan') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Ganti password akun kamu') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.password') }}" class="max-w-md">
            @csrf
            @if($errors->has('current_password') || $errors->has('new_password'))
            <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-xl text-sm">
                @foreach($errors->only(['current_password','new_password']) as $err)<p>{{ $err }}</p>@endforeach
            </div>
            @endif
            <div class="space-y-3 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Password saat ini') }}</label>
                    <input type="password" name="current_password" required placeholder="••••••••"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Password baru') }}</label>
                    <input type="password" name="new_password" required placeholder="{{ __('Min. 6 karakter') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Konfirmasi password baru') }}</label>
                    <input type="password" name="new_password_confirmation" required placeholder="{{ __('Ulangi password baru') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-gray-400 focus:bg-white transition-all">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Ganti Password') }}
            </button>
        </form>
    </div>

    {{-- ── Danger Zone (Logout) ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 border border-red-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gray-900 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(mb_substr($profile['display_name'] ?? auth()->user()->username ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-bold">{{ $profile['display_name'] ?: auth()->user()->username }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()->username }} · {{ __('Akun aktif') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function toggleFeature(btn) {
    const key     = btn.dataset.key;
    const row     = btn.closest('.feat-row');
    const current = row.dataset.enabled === 'true';
    const newVal  = !current;
    applyToggle(btn, newVal);
    try {
        const res  = await fetch(TOGGLE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ feature: key }),
        });
        if (!res.ok) throw new Error();
        const json = await res.json();
        applyToggle(btn, json.enabled);
    } catch {
        applyToggle(btn, current);
    }
}

function applyToggle(btn, enabled) {
    const row   = btn.closest('.feat-row');
    const thumb = btn.querySelector('span');
    row.dataset.enabled = enabled ? 'true' : 'false';
    btn.classList.toggle('bg-gray-900', enabled);
    btn.classList.toggle('bg-gray-200', !enabled);
    thumb.classList.toggle('left-6', enabled);
    thumb.classList.toggle('left-1', !enabled);
}
</script>
@endpush
@endsection
