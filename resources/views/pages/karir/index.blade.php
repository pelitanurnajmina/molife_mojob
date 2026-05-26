@extends('layouts.app')
@section('title', 'Statistik Karir')
@section('page-title', 'Statistik Karir')
@section('breadcrumb', 'Karir')

@section('content')
@php
$statusMeta = [
    'applied'   => ['label'=>'Dikirim',   'pill'=>'pill-applied'],
    'review'    => ['label'=>'Review',    'pill'=>'pill-review'],
    'interview' => ['label'=>'Interview', 'pill'=>'pill-interview'],
    'offer'     => ['label'=>'Tawaran',   'pill'=>'pill-offer'],
    'hired'     => ['label'=>'Diterima',  'pill'=>'pill-hired'],
    'rejected'  => ['label'=>'Ditolak',   'pill'=>'pill-rejected'],
];
@endphp
<div class="space-y-4 md:space-y-6">

    @if($jobTotal === 0)
    {{-- Empty state --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-10 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="font-bold text-lg mb-2">Belum Ada Data Lamaran</h3>
        <p class="text-sm text-gray-400 mb-6">Mulai catat lamaran kerja kamu untuk melihat statistik di sini.</p>
        <a href="{{ route('lamaran.index') }}"
            class="inline-block bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
            Catat Lamaran Pertama
        </a>
    </div>
    @else

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-xs font-bold text-gray-400 mb-1">Total Lamaran</p>
            <p class="text-3xl font-bold">{{ $jobTotal }}</p>
            <p class="text-[10px] text-gray-400 mt-1">
                {{ $thisMonthCount }} bulan ini
                @if($lastMonthCount > 0)
                · {{ $lastMonthCount }} bulan lalu
                @endif
            </p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-xs font-bold text-gray-400 mb-1">Response Rate</p>
            <p class="text-3xl font-bold">{{ $responseRate }}<span class="text-lg font-medium text-gray-400">%</span></p>
            <p class="text-[10px] text-gray-400 mt-1">Lamaran yang direspons</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-xs font-bold text-gray-400 mb-1">Interview Rate</p>
            <p class="text-3xl font-bold">{{ $interviewRate }}<span class="text-lg font-medium text-gray-400">%</span></p>
            <p class="text-[10px] text-gray-400 mt-1">Sampai tahap interview</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-xs font-bold text-gray-400 mb-1">Success Rate</p>
            <p class="text-3xl font-bold {{ $successRate > 0 ? 'text-green-600' : '' }}">
                {{ $successRate }}<span class="text-lg font-medium text-gray-400">%</span>
            </p>
            <p class="text-[10px] text-gray-400 mt-1">Offer & diterima</p>
        </div>
    </div>

    {{-- Trend + Pipeline --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">

        {{-- Weekly Trend Chart --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="font-bold mb-1">Tren Lamaran</h3>
            <p class="text-xs text-gray-400 mb-6">Jumlah lamaran per minggu (12 minggu terakhir)</p>
            <div class="h-52">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Pipeline Funnel --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="font-bold mb-1">Pipeline Lamaran</h3>
            <p class="text-xs text-gray-400 mb-6">Distribusi status dari {{ $jobTotal }} lamaran</p>
            <div class="space-y-3">
                @foreach($pipeline as $stage)
                @php $pct = $jobTotal > 0 ? round(($stage['count'] / $jobTotal) * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-gray-600">{{ $stage['label'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">{{ $pct }}%</span>
                            <span class="text-sm font-bold w-6 text-right">{{ $stage['count'] }}</span>
                        </div>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all"
                            style="width:{{ $pct }}%; background-color:{{ $stage['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-gray-50 grid grid-cols-3 gap-3 text-center">
                <div>
                    <p class="text-xl font-bold">{{ $jobTotal }}</p>
                    <p class="text-[10px] text-gray-400">Total</p>
                </div>
                <div>
                    <p class="text-xl font-bold text-blue-600">{{ ($jobCounts['interview'] ?? 0) + ($jobCounts['offer'] ?? 0) + ($jobCounts['hired'] ?? 0) }}</p>
                    <p class="text-[10px] text-gray-400">Interview</p>
                </div>
                <div>
                    <p class="text-xl font-bold text-green-600">{{ ($jobCounts['offer'] ?? 0) + ($jobCounts['hired'] ?? 0) }}</p>
                    <p class="text-[10px] text-gray-400">Offer</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 60-day Application Heatmap --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-1">Aktivitas Melamar</h3>
        <p class="text-xs text-gray-400 mb-6">60 hari terakhir — semakin gelap semakin banyak lamaran</p>
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
            <span class="text-[10px] text-gray-400">Sedikit</span>
            <div class="w-4 h-3 rounded-sm bg-gray-100"></div>
            <div class="w-4 h-3 rounded-sm bg-indigo-300"></div>
            <div class="w-4 h-3 rounded-sm bg-indigo-500"></div>
            <div class="w-4 h-3 rounded-sm bg-indigo-700"></div>
            <span class="text-[10px] text-gray-400">Banyak</span>
        </div>
    </div>

    {{-- Recent Applications --}}
    @if(count($recentApps) > 0)
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold">Lamaran Terbaru</h3>
                <p class="text-xs text-gray-400 mt-0.5">5 lamaran terakhir yang dicatat</p>
            </div>
            <a href="{{ route('lamaran.index') }}"
                class="text-xs font-bold text-gray-500 hover:text-black transition-all px-3 py-1.5 bg-gray-50 rounded-lg">
                Lihat semua
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
            borderColor: '#6366f1',
            borderWidth: 2,
            borderRadius: 6,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => `${ctx.parsed.y} lamaran` } },
        },
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
