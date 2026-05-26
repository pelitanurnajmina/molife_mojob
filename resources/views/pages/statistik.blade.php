@extends('layouts.app')
@section('title', 'Statistik')
@section('page-title', 'Data Analysis')
@section('breadcrumb', 'Statistik')

@section('content')
<div class="space-y-4 md:space-y-6">
    {{-- 30 day heatmap --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base md:text-lg font-bold mb-6 md:mb-8 text-center">Konsistensi 30 Hari Terakhir</h3>
        <div class="space-y-6 md:space-y-10">
            {{-- Sholat --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-bold text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        SHOLAT COMPLETION
                    </p>
                    <div class="flex items-center gap-3 text-[10px]">
                        <div class="flex items-center gap-1"><div class="w-3 h-3 bg-green-500 rounded"></div><span class="text-gray-500">5 Wajib</span></div>
                        <div class="flex items-center gap-1"><div class="w-3 h-3 bg-yellow-400 rounded"></div><span class="text-gray-500">5 Takbir</span></div>
                    </div>
                </div>
                <div class="grid grid-cols-10 sm:grid-cols-[repeat(15,1fr)] md:grid-cols-[repeat(30,1fr)] gap-0.5 md:gap-1 h-6 md:h-8">
                    @foreach($stats30 as $day)
                    @php
                        $cls = $day['sholat']['takbir'] >= 5 ? 'bg-yellow-400' : ($day['sholat']['wajib'] >= 5 ? 'bg-green-500' : 'bg-gray-100');
                    @endphp
                    <div class="rounded {{ $cls }}" title="{{ $day['date'] }}: {{ $day['sholat']['wajib'] }}/5 wajib"></div>
                    @endforeach
                </div>
            </div>

            {{-- Gym --}}
            <div>
                <p class="text-xs font-bold text-gray-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    GYM SESSIONS
                </p>
                <div class="grid grid-cols-10 sm:grid-cols-[repeat(15,1fr)] md:grid-cols-[repeat(30,1fr)] gap-0.5 md:gap-1 h-6 md:h-8">
                    @foreach($stats30 as $day)
                    <div class="rounded {{ $day['gym']['done'] ? 'bg-blue-500' : 'bg-gray-100' }}" title="{{ $day['date'] }}: {{ $day['gym']['done'] ? 'Gym' : 'Rest' }}"></div>
                    @endforeach
                </div>
            </div>

            {{-- Intimasi --}}
            <div>
                <p class="text-xs font-bold text-gray-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    INTIMASI
                </p>
                <div class="grid grid-cols-10 sm:grid-cols-[repeat(15,1fr)] md:grid-cols-[repeat(30,1fr)] gap-0.5 md:gap-1 h-6 md:h-8">
                    @foreach($stats30 as $day)
                    <div class="rounded {{ $day['intimacy'] > 0 ? 'bg-pink-500' : 'bg-gray-100' }}" title="{{ $day['date'] }}: {{ $day['intimacy'] }}x"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Doughnut + Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="font-bold mb-6 text-center">Completion Rate (30 Hari)</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 flex flex-col justify-center items-center text-center">
            <div class="w-20 h-20 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
            <h3 class="text-xl font-bold">Keep Going!</h3>
            <p class="text-sm text-gray-500 mt-2 max-w-xs">Konsistensi adalah kunci. Terus jaga tracking harian Anda untuk hasil yang optimal.</p>
            <div class="grid grid-cols-3 gap-4 mt-6 w-full">
                <div class="p-3 bg-green-50 rounded-xl">
                    <div class="text-xl font-bold text-green-700">{{ $streak }}</div>
                    <div class="text-[10px] text-green-600">Day Streak</div>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl">
                    <div class="text-xl font-bold text-blue-700">{{ $gymMonthly }}</div>
                    <div class="text-[10px] text-blue-600">Gym/Bulan</div>
                </div>
                <div class="p-3 bg-pink-50 rounded-xl">
                    <div class="text-xl font-bold text-pink-700">{{ $intimacyMonthly }}</div>
                    <div class="text-[10px] text-pink-600">Intimasi/Bulan</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('doughnutChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Sholat', 'Gym', 'Intimacy'],
        datasets: [{ data: [{{ $sholatDays }}, {{ $gymDays }}, {{ $intimacyDays }}], backgroundColor: ['#10B981', '#3B82F6', '#F472B6'] }],
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
            tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.parsed} hari` } }
        },
    }
});
</script>
@endpush
