@extends('layouts.app')
@section('title', 'Bisnis')
@section('page-title', __('Bisnis Overview'))
@section('breadcrumb', 'Bisnis › Overview')

@section('content')
@php
    $rp = fn($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $trendLabels = array_column($trend, 'week');
    $trendCounts = array_column($trend, 'count');
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── KPI row ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none">{{ $total }}</p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Total Proposal') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $thisMonthCount }} {{ __('bulan ini') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none">{{ $winRate }}<span class="text-base text-gray-400">%</span></p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Win Rate') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $counts['won'] }} {{ __('deal dari') }} {{ $closed }} {{ __('closing') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="text-base md:text-lg font-black text-gray-900 leading-tight truncate">{{ $rp($pipelineValue) }}</p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Nilai Pipeline') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $active }} {{ __('proposal aktif') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-base md:text-lg font-black text-gray-900 leading-tight truncate">{{ $rp($wonValue) }}</p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Nilai Deal') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ __('dari proposal menang') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        {{-- Pipeline --}}
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold">{{ __('Pipeline Bisnis') }}</h3>
                <a href="{{ route('bisnis.deals') }}" class="inline-flex items-center gap-1 text-xs font-bold text-gray-400 hover:text-black transition-all">
                    {{ __('Kelola') }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="space-y-3">
                @foreach($statuses as $key => $meta)
                @php $cnt = $counts[$key] ?? 0; $pct = $total > 0 ? round($cnt / $total * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-bold text-gray-700">{{ $meta['label'] }}</span>
                        <span class="text-xs font-bold text-gray-500">{{ $cnt }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%;background:{{ $meta['hex'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top industries --}}
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
            <h3 class="font-bold mb-4">{{ __('Bidang Klien Teratas') }}</h3>
            @forelse($industries as $ind)
            @php $maxC = $industries[0]['count'] ?? 1; $pct = $maxC > 0 ? round($ind['count'] / $maxC * 100) : 0; @endphp
            <div class="mb-3 last:mb-0">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-bold text-gray-700">{{ $ind['name'] }}</span>
                    <span class="text-xs font-bold text-gray-500">{{ $ind['count'] }}</span>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-indigo-500 h-full rounded-full" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-400 text-sm py-8">{{ __('Belum ada data bidang klien.') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Trend chart --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <h3 class="font-bold mb-4">{{ __('Proposal 12 Minggu Terakhir') }}</h3>
        <div style="height:200px"><canvas id="bizTrend"></canvas></div>
    </div>

    {{-- Recent --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold">{{ __('Proposal Terbaru') }}</h3>
            <a href="{{ route('bisnis.deals') }}" class="inline-flex items-center gap-1 text-xs font-bold text-gray-400 hover:text-black transition-all">
                {{ __('Lihat semua') }}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        @forelse($recent as $r)
        @php $m = $statuses[$r['status']] ?? ['label'=>$r['status'],'tw'=>'gray']; @endphp
        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-all">
            <div class="w-9 h-9 rounded-xl bg-{{ $m['tw'] }}-100 text-{{ $m['tw'] }}-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                {{ strtoupper(substr($r['client_name'], 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-800 truncate">{{ $r['client_name'] }}</p>
                <p class="text-xs text-gray-400 truncate">{{ $r['product'] ?: '—' }}{{ $r['date'] ? ' · ' . date('j M Y', strtotime($r['date'])) : '' }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-bold text-gray-700">{{ $r['value'] > 0 ? $rp($r['value']) : '—' }}</p>
                <span class="text-[10px] font-bold text-{{ $m['tw'] }}-600">{{ $m['label'] }}</span>
            </div>
        </div>
        @empty
        <div class="text-center py-10">
            <p class="text-sm text-gray-400 font-medium mb-3">{{ __('Belum ada proposal. Mulai catat proposal & klienmu.') }}</p>
            <a href="{{ route('bisnis.deals') }}" class="inline-block px-5 py-2.5 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Tambah Proposal') }}</a>
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('bizTrend').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($trendLabels),
        datasets: [{ data: @json($trendCounts), backgroundColor: '#6366f1', hoverBackgroundColor: '#4f46e5', borderRadius: 6, borderSkipped: false, maxBarThickness: 26 }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => c.raw + ' {{ __('proposal') }}' } } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 }, color: '#9ca3af' }, grid: { color: '#f9fafb', drawBorder: false } },
            x: { grid: { display: false }, ticks: { font: { size: 9 }, color: '#9ca3af' } },
        },
    },
});
</script>
@endpush
