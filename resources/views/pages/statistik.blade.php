@extends('layouts.app')
@section('title', __('Statistik'))
@section('page-title', __('Statistik'))
@section('breadcrumb', __('Statistik'))

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- ── 30-day heatmap (adaptive) ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base md:text-lg font-bold mb-6 md:mb-8 text-center">{{ __('Konsistensi 30 Hari Terakhir') }}</h3>

        @if(count($heatmapRows) === 0)
        <div class="text-center py-10">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="text-sm text-gray-400 font-medium">{{ __('Belum ada fitur aktif untuk ditampilkan') }}</p>
        </div>
        @else
        <div class="space-y-6 md:space-y-8">
            @foreach($heatmapRows as $row)
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $row['icon'] }}"/></svg>
                        {{ $row['label'] }}
                    </p>
                    @if($row['key'] === 'sholat')
                    <div class="flex items-center gap-3 text-[10px]">
                        <div class="flex items-center gap-1"><div class="w-3 h-3 bg-green-500 rounded"></div><span class="text-gray-500">5 {{ __('Wajib') }}</span></div>
                        <div class="flex items-center gap-1"><div class="w-3 h-3 bg-yellow-400 rounded"></div><span class="text-gray-500">5 {{ \App\Support\Profile::prayerQuality()['label'] }}</span></div>
                    </div>
                    @endif
                </div>
                <div class="grid grid-cols-10 sm:grid-cols-[repeat(15,1fr)] md:grid-cols-[repeat(30,1fr)] gap-0.5 md:gap-1 h-6 md:h-8">
                    @foreach($row['days'] as $idx => $state)
                    @php
                        $cls = match($state) {
                            'done'    => $row['color'],
                            'alt'     => $row['alt'] ?? $row['color'],
                            'partial' => $row['alt'] ?? $row['color'],
                            default   => 'bg-gray-100',
                        };
                    @endphp
                    <div class="rounded {{ $cls }}" title="{{ $row['titles'][$idx] ?? '' }}"></div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── Doughnut + Summary ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        {{-- Doughnut --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="font-bold mb-6 text-center">{{ __('Hari Aktif (30 Hari)') }}</h3>
            @if(count($doughnutData) > 0)
            <div class="h-64 flex items-center justify-center">
                <canvas id="doughnutChart"></canvas>
            </div>
            @else
            <div class="h-64 flex flex-col items-center justify-center text-center">
                <p class="text-sm text-gray-400 font-medium">{{ __('Belum ada aktivitas tercatat') }}</p>
                <p class="text-xs text-gray-300 mt-1">{{ __('Mulai catat aktivitasmu hari ini!') }}</p>
            </div>
            @endif
        </div>

        {{-- Summary --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 flex flex-col justify-center items-center text-center">
            <div class="w-20 h-20 bg-yellow-50 text-yellow-500 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold">Keep Going!</h3>
            <p class="text-sm text-gray-500 mt-2 max-w-xs">{{ __('Konsistensi adalah kunci. Terus jaga tracking harian Anda untuk hasil yang optimal.') }}</p>
            <div class="grid grid-cols-2 gap-3 mt-6 w-full">
                @if($features['sholat'] ?? false)
                <div class="p-3 bg-green-50 rounded-xl col-span-2">
                    <div class="text-xl font-bold text-green-700">{{ $streak }}</div>
                    <div class="flex items-center justify-center gap-1 text-[10px] text-green-600">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                        {{ __('Hari Streak Sholat') }}
                    </div>
                </div>
                @endif
                @if($features['gym'] ?? false)
                <div class="p-3 bg-blue-50 rounded-xl">
                    <div class="text-xl font-bold text-blue-700">{{ $gymMonthly }}</div>
                    <div class="text-[10px] text-blue-600">{{ __('Gym/Bulan') }}</div>
                </div>
                @endif
                @if($features['run'] ?? false)
                <div class="p-3 bg-emerald-50 rounded-xl">
                    <div class="text-xl font-bold text-emerald-700">{{ $runMonthly }}</div>
                    <div class="text-[10px] text-emerald-600">{{ __('Lari/Bulan') }}</div>
                </div>
                @endif
                @if($features['mental'] ?? false)
                <div class="p-3 bg-violet-50 rounded-xl {{ ($features['intimasi'] ?? false) ? '' : 'col-span-2' }}">
                    <div class="text-xl font-bold text-violet-700">{{ $moodAvg30 > 0 ? $moodAvg30 : '—' }}</div>
                    <div class="text-[10px] text-violet-600">{{ __('Avg Mood') }}</div>
                </div>
                @endif
                @if($features['intimasi'] ?? false)
                <div class="p-3 bg-pink-50 rounded-xl {{ ($features['mental'] ?? false) ? '' : 'col-span-2' }}">
                    <div class="text-xl font-bold text-pink-700">{{ $intimacyMonthly }}</div>
                    <div class="text-[10px] text-pink-600">{{ __('Intimasi/Bulan') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(count($doughnutData) > 0)
<script>
const ctx = document.getElementById('doughnutChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: @json($doughnutLabels),
        datasets: [{ data: @json($doughnutData), backgroundColor: @json($doughnutColors), borderWidth: 0 }],
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 }, padding: 12 } },
            tooltip: { callbacks: { label: (c) => `${c.label}: ${c.parsed} {{ __('hari') }}` } }
        },
    }
});
</script>
@endif
@endpush
