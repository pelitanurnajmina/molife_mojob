@extends('layouts.app')
@section('title', __('Tampilan & Fitur'))
@section('page-title', __('Settings'))
@section('breadcrumb', 'Settings › Tampilan & Fitur')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- ── Preferensi (Religion + Sports) ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Preferensi') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Agama dan olahraga — menentukan fitur yang tampil di navigasi') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.onboarding') }}">
            @csrf
            {{-- Religion --}}
            <div class="mb-6">
                <p class="text-xs font-bold text-gray-500 mb-3">{{ __('Agama / Kepercayaan') }}</p>
                @php
                $religions = [
                    ['value'=>'islam',   'label'=>'Islam',       'sub'=>'Sholat 5 waktu, Rawatib, Takbir'],
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
                        {{ $currentReligion === $rel['value'] ? 'border-orange-100 bg-orange-50' : 'border-gray-100 hover:border-gray-200' }}"
                        id="relLabel_{{ $rel['value'] }}">
                        <input type="radio" name="religion" value="{{ $rel['value'] }}"
                            {{ $currentReligion === $rel['value'] ? 'checked' : '' }}
                            class="form-radio-orange mt-0.5"
                            onchange="document.querySelectorAll('[id^=relLabel_]').forEach(function(l){ l.classList.remove('border-orange-100','bg-orange-50'); l.classList.add('border-gray-100'); }); this.closest('label').classList.add('border-orange-100','bg-orange-50'); this.closest('label').classList.remove('border-gray-100');">
                        <div>
                            <p class="font-bold text-xs text-gray-800">{{ $rel['label'] }}</p>
                            <p class="text-[10px] text-gray-400 leading-relaxed mt-0.5">{{ $rel['sub'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Sports --}}
            <div class="mb-6">
                <p class="text-xs font-bold text-gray-500 mb-3">{{ __('Olahraga') }}</p>
                @php
                $sportOptions = [
                    ['value'=>'gym',         'label'=>'Gym / Fitness',      'icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
                    ['value'=>'run',         'label'=>'Lari / Running',     'icon'=>'M22 12h-4l-3 9L9 3l-3 9H2'],
                    ['value'=>'cycling',     'label'=>'Bersepeda',          'icon'=>'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
                    ['value'=>'swimming',    'label'=>'Renang',             'icon'=>'M7 16.5c2 1 4 1 6 0s4-1 6 0M7 11.5c2 1 4 1 6 0s4-1 6 0M3 7.5c2 1 4 1 6 0m-9 9V7.5'],
                    ['value'=>'racket',      'label'=>'Tenis / Badminton',  'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['value'=>'custom_sport','label'=>'Olahraga Lainnya',   'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                ];
                $currentSports = $profile['sports'] ?? [];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3">
                    @foreach($sportOptions as $sp)
                    @php $checked = in_array($sp['value'], $currentSports); @endphp
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all {{ $checked ? 'border-orange-100 bg-orange-50' : 'border-gray-100 hover:border-gray-200' }}"
                        id="spLabel_{{ $sp['value'] }}">
                        <input type="checkbox" name="sports[]" value="{{ $sp['value'] }}" {{ $checked ? 'checked' : '' }}
                            class="form-check-orange"
                            onchange="this.closest('label').classList.toggle('border-orange-100', this.checked); this.closest('label').classList.toggle('bg-orange-50', this.checked); this.closest('label').classList.toggle('border-gray-100', !this.checked)">
                        <svg class="sport-icon w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $sp['icon'] }}"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-700">{{ $sp['label'] }}</span>
                    </label>
                    @endforeach
                </div>
                <div id="customSportNameWrapper" class="{{ in_array('custom_sport', $currentSports) ? '' : 'hidden' }}">
                    <input type="text" name="custom_sport_name"
                        value="{{ $profile['custom_sport_name'] ?? '' }}"
                        placeholder="{{ __('Nama olahraga (cth: Padel, Voli, Basket...)') }}"
                        class="w-full sm:w-72 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium outline-none focus:border-black transition-all">
                </div>
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
                <h3 class="text-base font-bold">{{ __('Fitur Navigasi') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Aktifkan atau sembunyikan menu dari sidebar. Data tidak akan terhapus.') }}</p>
            </div>
        </div>

        @php
        $featureGroups = [
            ['label'=>'Life', 'items'=>[
                ['key'=>'sholat',      'label'=>'Sholat',            'desc'=>'Tracker sholat wajib, rawatib & sunnah'],
                ['key'=>'spiritual',   'label'=>'Spiritual',          'desc'=>'Ibadah / Sembahyang / Praktik spiritual'],
                ['key'=>'gym',         'label'=>'Gym',                'desc'=>'Log sesi gym dan kalori terbakar'],
                ['key'=>'run',         'label'=>'Lari',               'desc'=>'Tracker jarak, pace, dan sesi lari'],
                ['key'=>'cycling',     'label'=>'Bersepeda',          'desc'=>'Track km dan durasi bersepeda'],
                ['key'=>'swimming',    'label'=>'Renang',             'desc'=>'Track lap dan durasi renang'],
                ['key'=>'racket',      'label'=>'Tenis / Badminton',  'desc'=>'Track sesi racket sports'],
                ['key'=>'custom_sport','label'=>'Olahraga Custom',    'desc'=>'Olahraga sesuai pilihan kamu'],
                ['key'=>'intimasi',    'label'=>'Intimasi',           'desc'=>'Tracker keintiman bersama pasangan'],
                ['key'=>'porn',        'label'=>'Stop Porn',          'desc'=>'Streak bebas pornografi & kontrol diri'],
                ['key'=>'sosmed',      'label'=>'Kurangi Sosmed',     'desc'=>'Disiplin waktu media sosial'],
                ['key'=>'motivasi',    'label'=>'Motivasi',           'desc'=>'Quote harian & dampak konsistensimu'],
                ['key'=>'mental',      'label'=>'Mental',             'desc'=>'Mood tracker, energi, dan refleksi harian'],
                ['key'=>'tasks',       'label'=>'Tasks & Notes',      'desc'=>'Tugas harian, mingguan & catatan'],
                ['key'=>'statistik',   'label'=>'Statistik',          'desc'=>'Grafik dan ringkasan 30 hari terakhir'],
                ['key'=>'insights',    'label'=>'Insights',           'desc'=>'Life Score dan pola dari semua aktivitas'],
                ['key'=>'goals',       'label'=>'Goals & Reminder',   'desc'=>'Target pribadi dan pengingat harian'],
            ]],
            ['label'=>'Karir', 'items'=>[
                ['key'=>'lamaran',     'label'=>'Lamaran Kerja',      'desc'=>'Kelola lamaran dan status interview'],
                ['key'=>'persiapan',   'label'=>'Persiapan Melamar',  'desc'=>'Link, file, template CV & persiapan wawancara'],
            ]],
            ['label'=>'Finance', 'items'=>[
                ['key'=>'finance',     'label'=>'Finance',            'desc'=>'Transaksi, anggaran & tujuan tabungan'],
            ]],
        ];
        @endphp

        <div class="space-y-5">
            @foreach($featureGroups as $group)
            <div>
                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2">{{ $group['label'] }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($group['items'] as $feat)
                    @php $enabled = $features[$feat['key']] ?? false; @endphp
                    <div class="feat-row flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100" data-key="{{ $feat['key'] }}" data-enabled="{{ $enabled ? 'true' : 'false' }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">{{ $feat['label'] }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 leading-tight">{{ $feat['desc'] }}</p>
                        </div>
                        <button type="button" onclick="toggleFeatureLocal(this)" data-key="{{ $feat['key'] }}"
                            class="feat-toggle flex-shrink-0 relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none {{ $enabled ? 'bg-gray-900' : 'bg-gray-200' }}">
                            <span class="block w-4 h-4 bg-white rounded-full shadow absolute top-1 transition-all duration-200 {{ $enabled ? 'left-6' : 'left-1' }}"></span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Save bar --}}
        <div class="flex items-center justify-between gap-3 mt-6 pt-5 border-t border-gray-100">
            <p id="featHint" class="text-xs text-gray-400">{{ __('Ubah toggle lalu simpan — sidebar langsung diperbarui.') }}</p>
            <button type="button" id="saveFeaturesBtn" onclick="saveFeatures()"
                class="px-6 py-2.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all flex items-center gap-2">
                {{ __('Simpan Perubahan') }}
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Show/hide custom sport name field
document.querySelectorAll('input[value="custom_sport"]').forEach(cb => {
    cb.addEventListener('change', () => {
        document.getElementById('customSportNameWrapper').classList.toggle('hidden', !cb.checked);
    });
});

const SAVE_FEATURES_URL = '{{ route("settings.features.save") }}';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// Local toggle (no save until Simpan)
function toggleFeatureLocal(btn) {
    const row     = btn.closest('.feat-row');
    const enabled = !(row.dataset.enabled === 'true');
    const thumb   = btn.querySelector('span');
    row.dataset.enabled = enabled ? 'true' : 'false';
    btn.classList.toggle('bg-gray-900', enabled);
    btn.classList.toggle('bg-gray-200', !enabled);
    thumb.classList.toggle('left-6', enabled);
    thumb.classList.toggle('left-1', !enabled);
    document.getElementById('featHint').textContent = '{{ __("Ada perubahan belum disimpan…") }}';
}

async function saveFeatures() {
    const btn = document.getElementById('saveFeaturesBtn');
    const map = {};
    document.querySelectorAll('.feat-row').forEach(r => { map[r.dataset.key] = r.dataset.enabled === 'true'; });

    btn.disabled = true;
    const original = btn.textContent;
    btn.textContent = '{{ __("Menyimpan…") }}';
    try {
        const res = await fetch(SAVE_FEATURES_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ features: map }),
        });
        if (!res.ok) throw new Error();
        const json = await res.json();
        if (window.applySidebarFeatures) window.applySidebarFeatures(json.features);
        document.getElementById('featHint').textContent = '{{ __("Tersimpan & sidebar diperbarui.") }}';
        if (window.showMojobToast) window.showMojobToast('{{ __("Pengaturan fitur disimpan.") }}');
    } catch {
        document.getElementById('featHint').textContent = '{{ __("Gagal menyimpan, coba lagi.") }}';
    } finally {
        btn.disabled = false;
        btn.textContent = original;
    }
}
</script>
@endpush
