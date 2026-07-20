@extends('layouts.app')
@section('title', __('Career Hub'))
@section('page-title', __('Career Hub'))
@section('breadcrumb', __('Karir'))

@section('content')
@php
$statusMeta = [
    'wishlist'  => ['label' => 'Wishlist',      'pill' => 'pill-wishlist'],
    'applied'   => ['label' => __('Dikirim'),   'pill' => 'pill-applied'],
    'review'    => ['label' => 'Review',         'pill' => 'pill-review'],
    'interview' => ['label' => 'Interview',      'pill' => 'pill-interview'],
    'offer'     => ['label' => __('Tawaran'),   'pill' => 'pill-offer'],
    'hired'     => ['label' => __('Diterima'),  'pill' => 'pill-hired'],
    'rejected'  => ['label' => __('Ditolak'),   'pill' => 'pill-rejected'],
];
@endphp

<style>
.pill-wishlist  { background:#f3e8ff; color:#6d28d9; }
</style>

<div class="space-y-4 md:space-y-6">

    {{-- ── Career Goals ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-start justify-between mb-4 flex-wrap gap-2">
            <div>
                <h3 class="text-base font-bold">{{ __('Target Karir') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Tetapkan target jelas agar fokus dan termotivasi.') }}</p>
            </div>
            <button onclick="document.getElementById('goalsModal').classList.remove('hidden')"
                class="text-xs font-bold bg-black text-white px-4 py-2 rounded-xl hover:bg-gray-800 transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                {{ __('Edit Target') }}
            </button>
        </div>

        @if(empty($careerGoals['target_role']) && empty($careerGoals['target_company']) && empty($careerGoals['target_salary']))
        <div class="py-8 text-center border-2 border-dashed border-gray-100 rounded-2xl">
            <div class="w-14 h-14 bg-violet-50 text-violet-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-400">{{ __('Belum ada target karir') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('Klik "Edit Target" untuk mulai menetapkan target.') }}</p>
        </div>
        @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="p-4 bg-violet-50 rounded-2xl">
                <p class="text-[10px] font-bold text-violet-500 uppercase tracking-wide mb-1">{{ __('Target Posisi') }}</p>
                <p class="font-bold text-sm text-gray-800">{{ $careerGoals['target_role'] ?: '—' }}</p>
            </div>
            <div class="p-4 bg-blue-50 rounded-2xl">
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wide mb-1">{{ __('Target Perusahaan') }}</p>
                <p class="font-bold text-sm text-gray-800">{{ $careerGoals['target_company'] ?: '—' }}</p>
            </div>
            <div class="p-4 bg-green-50 rounded-2xl">
                <p class="text-[10px] font-bold text-green-500 uppercase tracking-wide mb-1">{{ __('Target Gaji') }}</p>
                <p class="font-bold text-sm text-gray-800">{{ $careerGoals['target_salary'] ?: '—' }}</p>
            </div>
            <div class="p-4 bg-orange-50 rounded-2xl">
                <p class="text-[10px] font-bold text-orange-500 uppercase tracking-wide mb-1">{{ __('Target Tanggal') }}</p>
                <p class="font-bold text-sm text-gray-800">{{ $careerGoals['target_date'] ? date('M Y', strtotime($careerGoals['target_date'])) : '—' }}</p>
            </div>
        </div>
        @if(!empty($careerGoals['notes']))
        <div class="mt-3 p-3 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-600 flex items-start gap-1.5">
                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ $careerGoals['notes'] }}
            </p>
        </div>
        @endif
        @endif
    </div>

    @if($jobTotal === 0)
    {{-- Empty state --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-10 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="font-bold text-lg mb-2">{{ __('Belum Ada Data Lamaran') }}</h3>
        <p class="text-sm text-gray-400 mb-6">{{ __('Mulai catat lamaran kerja kamu untuk melihat statistik di sini.') }}</p>
        <a href="{{ route('lamaran.index') }}"
            class="inline-block bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
            {{ __('Catat Lamaran Pertama') }}
        </a>
    </div>
    @else

    {{-- ── KPI Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-[10px] font-bold text-gray-400 mb-1 uppercase tracking-wide">{{ __('Total Lamaran') }}</p>
            <p class="text-3xl font-bold">{{ $jobTotal }}</p>
            <p class="text-[10px] text-gray-400 mt-1">
                {{ $thisMonthCount }} {{ __('bulan ini') }}
                @if($lastMonthCount > 0)<span class="text-gray-300 mx-0.5">·</span>{{ $lastMonthCount }} {{ __('bulan lalu') }}@endif
            </p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-[10px] font-bold text-gray-400 mb-1 uppercase tracking-wide">{{ __('Aktif') }}</p>
            <p class="text-3xl font-bold text-blue-600">{{ $active }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ __('Menunggu respon') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-[10px] font-bold text-gray-400 mb-1 uppercase tracking-wide">Response Rate</p>
            <p class="text-3xl font-bold {{ $responseRate >= 50 ? 'text-green-600' : '' }}">{{ $responseRate }}<span class="text-lg font-medium text-gray-400">%</span></p>
            <p class="text-[10px] text-gray-400 mt-1">{{ __('Lamaran yang direspons') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-[10px] font-bold text-gray-400 mb-1 uppercase tracking-wide">Interview Rate</p>
            <p class="text-3xl font-bold {{ $interviewRate >= 20 ? 'text-green-600' : '' }}">{{ $interviewRate }}<span class="text-lg font-medium text-gray-400">%</span></p>
            <p class="text-[10px] text-gray-400 mt-1">{{ __('Sampai tahap interview') }}</p>
        </div>
    </div>

    {{-- ── Pipeline + Upcoming Interviews ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Pipeline Funnel --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold">{{ __('Pipeline Lamaran') }}</h3>
                <a href="{{ route('lamaran.index') }}" class="text-xs text-gray-400 hover:text-black font-bold">{{ __('Kelola') }}</a>
            </div>
            <div class="space-y-2.5">
                @foreach($pipeline as $stage)
                @php $pct = $jobTotal > 0 ? round(($stage['count'] / $jobTotal) * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-gray-600">{{ $stage['label'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-gray-400">{{ $pct }}%</span>
                            <span class="text-sm font-bold w-5 text-right">{{ $stage['count'] }}</span>
                        </div>
                    </div>
                    <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all"
                            style="width:{{ $pct }}%; background-color:{{ $stage['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-5 pt-4 border-t border-gray-50 grid grid-cols-3 gap-3 text-center">
                <div><p class="text-xl font-bold">{{ $jobTotal }}</p><p class="text-[10px] text-gray-400">Total</p></div>
                <div><p class="text-xl font-bold text-blue-600">{{ ($jobCounts['interview'] ?? 0) }}</p><p class="text-[10px] text-gray-400">Interview</p></div>
                <div><p class="text-xl font-bold text-green-600">{{ ($jobCounts['offer'] ?? 0) + ($jobCounts['hired'] ?? 0) }}</p><p class="text-[10px] text-gray-400">Offer</p></div>
            </div>
        </div>

        {{-- Upcoming Interviews --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="font-bold mb-4">{{ __('Interview Mendatang') }}</h3>
            @if(count($upcomingInterviews) === 0)
            <div class="py-8 text-center">
                <div class="w-14 h-14 bg-blue-50 text-blue-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-400">{{ __('Tidak ada interview terjadwal') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('Interview akan muncul di sini setelah status lamaran berubah ke Interview.') }}</p>
            </div>
            @else
            <div class="space-y-2">
                @foreach(array_slice($upcomingInterviews, 0, 5) as $iv)
                @php
                    $daysUntil = (int) ceil((strtotime($iv['date']) - time()) / 86400);
                    $urgency   = $daysUntil <= 1 ? 'bg-red-50 border-red-200' : ($daysUntil <= 3 ? 'bg-orange-50 border-orange-200' : 'bg-gray-50 border-gray-100');
                @endphp
                <div class="p-3 rounded-xl border {{ $urgency }} flex items-center gap-3">
                    <div class="flex-shrink-0 text-center w-10">
                        <p class="text-lg font-bold leading-none">{{ date('d', strtotime($iv['date'])) }}</p>
                        <p class="text-[9px] text-gray-400 uppercase">{{ date('M', strtotime($iv['date'])) }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold truncate">{{ $iv['company'] }}</p>
                        <p class="text-[10px] text-gray-400 truncate">{{ $iv['position'] }} · {{ $iv['time'] }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        @if($daysUntil <= 1)
                        <span class="text-[9px] font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">{{ __('Besok!') }}</span>
                        @elseif($daysUntil <= 3)
                        <span class="text-[9px] font-bold text-orange-600 bg-orange-100 px-2 py-0.5 rounded-full">{{ $daysUntil }}d</span>
                        @else
                        <span class="text-[9px] font-bold text-gray-400">{{ $daysUntil }}d</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── Weekly Trend ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-1">{{ __('Tren Lamaran') }}</h3>
        <p class="text-xs text-gray-400 mb-6">{{ __('Jumlah lamaran per minggu (12 minggu terakhir)') }}</p>
        <div class="h-52">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- ── 60-day Heatmap ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-1">{{ __('Aktivitas Melamar') }}</h3>
        <p class="text-xs text-gray-400 mb-6">{{ __('60 hari terakhir — semakin gelap semakin banyak lamaran') }}</p>
        <div class="grid gap-1" style="grid-template-columns: repeat(60, minmax(0, 1fr));">
            @foreach($heatmap as $day)
            @php
                $cnt = $day['count'];
                $cls = $cnt === 0 ? 'bg-gray-100' : ($cnt === 1 ? 'bg-indigo-300' : ($cnt <= 3 ? 'bg-indigo-500' : 'bg-indigo-700'));
            @endphp
            <div class="rounded-sm h-5 {{ $cls }}" title="{{ $day['date'] }}: {{ $cnt }} lamaran"></div>
            @endforeach
        </div>
        <div class="flex items-center gap-2 mt-3 justify-end">
            <span class="text-[10px] text-gray-400">{{ __('Sedikit') }}</span>
            <div class="w-4 h-3 rounded-sm bg-gray-100"></div>
            <div class="w-4 h-3 rounded-sm bg-indigo-300"></div>
            <div class="w-4 h-3 rounded-sm bg-indigo-500"></div>
            <div class="w-4 h-3 rounded-sm bg-indigo-700"></div>
            <span class="text-[10px] text-gray-400">{{ __('Banyak') }}</span>
        </div>
    </div>

    {{-- ── Networking Contacts ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold">{{ __('Networking') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Kontak dan relasi profesional kamu.') }}</p>
            </div>
            <button onclick="document.getElementById('contactModal').classList.remove('hidden')"
                class="text-xs font-bold bg-black text-white px-3 py-2 rounded-xl hover:bg-gray-800 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('Tambah Kontak') }}
            </button>
        </div>

        @if(count($contacts) === 0)
        <div class="py-6 text-center border-2 border-dashed border-gray-100 rounded-xl">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-sm text-gray-400">{{ __('Belum ada kontak tersimpan.') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('Catat kontak dari recruiter, koneksi LinkedIn, event, dll.') }}</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($contacts as $contact)
            @php
                $chMeta = [
                    'linkedin' => ['bg' => 'bg-blue-600', 'label' => 'LinkedIn'],
                    'email'    => ['bg' => 'bg-gray-600',  'label' => 'Email'],
                    'referral' => ['bg' => 'bg-green-600', 'label' => 'Referral'],
                    'event'    => ['bg' => 'bg-orange-500','label' => 'Event'],
                    'other'    => ['bg' => 'bg-gray-400',  'label' => 'Other'],
                ];
                $ch = $chMeta[$contact['channel'] ?? 'other'] ?? $chMeta['other'];
            @endphp
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl group">
                <div class="w-10 h-10 bg-white rounded-xl border border-gray-100 flex items-center justify-center flex-shrink-0 font-bold text-gray-700">
                    {{ strtoupper(substr($contact['name'], 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm truncate">{{ $contact['name'] }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ $contact['role'] }} @ {{ $contact['company'] }}</p>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <span class="text-[9px] font-bold text-white {{ $ch['bg'] }} px-2 py-0.5 rounded-full">{{ $ch['label'] }}</span>
                    <form method="POST" action="{{ route('karir.contact.destroy', $contact['id']) }}" onsubmit="return confirm('{{ __('Hapus kontak ini?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="opacity-0 group-hover:opacity-100 transition-all w-6 h-6 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── Recent Applications ── --}}
    @if(count($recentApps) > 0)
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold">{{ __('Lamaran Terbaru') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('5 lamaran terakhir yang dicatat') }}</p>
            </div>
            <a href="{{ route('lamaran.index') }}" class="text-xs font-bold text-gray-500 hover:text-black px-3 py-1.5 bg-gray-50 rounded-lg">
                {{ __('Lihat semua') }}
            </a>
        </div>
        <div class="space-y-2">
            @foreach($recentApps as $app)
            @php $meta = $statusMeta[$app['status'] ?? 'applied'] ?? $statusMeta['applied']; @endphp
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 bg-white rounded-xl border border-gray-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-gray-600">{{ strtoupper(substr($app['company'] ?? '?', 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold truncate">{{ $app['company'] ?? '-' }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ $app['position'] ?? '-' }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $meta['pill'] }}">{{ $meta['label'] }}</span>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ date('d M', strtotime($app['applied_date'] ?? date('Y-m-d'))) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif {{-- end if jobTotal --}}
</div>

{{-- ── Career Goals Modal ── --}}
@push('modals')
<div id="goalsModal" class="hidden fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('goalsModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-3xl p-6 w-full max-w-lg animate-fade-in">
        <h3 class="font-bold text-base mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
            {{ __('Edit Target Karir') }}
        </h3>
        <form method="POST" action="{{ route('karir.goals') }}" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Target Posisi') }}</label>
                <input type="text" name="target_role" value="{{ $careerGoals['target_role'] }}"
                    placeholder="Senior Frontend Developer, Product Manager..."
                    class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Target Perusahaan') }}</label>
                <input type="text" name="target_company" value="{{ $careerGoals['target_company'] }}"
                    placeholder="Tokopedia, Gojek, Google..."
                    class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Target Gaji') }}</label>
                    <input type="text" name="target_salary" value="{{ $careerGoals['target_salary'] }}"
                        placeholder="15-20 jt/bulan"
                        class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Target Tanggal') }}</label>
                    <input type="date" name="target_date" value="{{ $careerGoals['target_date'] }}"
                        class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Catatan / Strategi') }}</label>
                <textarea name="notes" rows="2"
                    placeholder="Fokus pada skill React, persiapkan portofolio..."
                    class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300 resize-none">{{ $careerGoals['notes'] }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('goalsModal').classList.add('hidden')"
                    class="flex-1 py-3 bg-gray-100 rounded-2xl font-bold text-sm hover:bg-gray-200">{{ __('Batalkan') }}</button>
                <button type="submit" class="flex-1 py-3 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Contact Modal ── --}}
<div id="contactModal" class="hidden fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('contactModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-3xl p-6 w-full max-w-lg animate-fade-in">
        <h3 class="font-bold text-base mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            {{ __('Tambah Kontak') }}
        </h3>
        <form method="POST" action="{{ route('karir.contact.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Nama') }} *</label>
                    <input type="text" name="name" required placeholder="Budi Santoso"
                        class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Perusahaan') }}</label>
                    <input type="text" name="company" placeholder="Tokopedia"
                        class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Posisi / Role') }}</label>
                    <input type="text" name="role" placeholder="Engineering Manager"
                        class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Kenal Lewat') }}</label>
                    <select name="channel" class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
                        <option value="linkedin">LinkedIn</option>
                        <option value="email">Email</option>
                        <option value="referral">Referral</option>
                        <option value="event">Event / Meetup</option>
                        <option value="other">{{ __('Lainnya') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 block mb-1">{{ __('Catatan') }}</label>
                <input type="text" name="notes" placeholder="Ketemuan di meetup React ID, Rekruter aktif..."
                    class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm outline-none focus:border-gray-300">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('contactModal').classList.add('hidden')"
                    class="flex-1 py-3 bg-gray-100 rounded-2xl font-bold text-sm hover:bg-gray-200">{{ __('Batalkan') }}</button>
                <button type="submit" class="flex-1 py-3 bg-black text-white rounded-2xl font-bold text-sm hover:bg-gray-800">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
@if($jobTotal > 0)
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'bar',
    data: {
        labels: @json($trendLabels),
        datasets: [{
            label: 'Lamaran',
            data: @json($trendCounts),
            backgroundColor: 'rgba(99,102,241,0.15)',
            borderColor: '#6366f1', borderWidth: 2, borderRadius: 6,
        }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f3f4f6' } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        },
    },
});
@endif
</script>
@endpush
@endsection
