@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', __('Life Overview'))
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
        {{-- Sholat --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gray-50">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">{{ __('Daily Goal') }}</span>
            </div>
            <h3 class="text-gray-400 text-sm font-medium">{{ __('Sholat Hari Ini') }}</h3>
            <div class="flex items-end gap-2 mt-1">
                <span class="text-3xl font-bold">{{ $todayStats['wajib'] }}/5</span>
                <span class="text-xs text-gray-400 mb-1">{{ __('wajib') }}</span>
            </div>
            <div class="w-full bg-gray-100 h-2 rounded-full mt-4">
                <div class="bg-green-500 h-2 rounded-full transition-all" style="width:{{ ($todayStats['wajib']/5)*100 }}%"></div>
            </div>
            <p class="text-[10px] text-gray-500 mt-2">+ {{ $todayStats['takbir'] }} takbir, {{ $todayStats['rawatib'] }} rawatib, {{ $todayStats['sunnah'] }} sunnah</p>
        </div>

        {{-- Gym --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gray-50">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xs font-bold px-2 py-1 rounded {{ $isGymToday ? 'text-green-600 bg-green-50' : 'text-blue-600 bg-blue-50' }}">
                    {{ $isGymToday ? __('Workout Done') : __('Rest Day') }}
                </span>
            </div>
            <h3 class="text-gray-400 text-sm font-medium">{{ __('Gym Minggu Ini') }}</h3>
            <div class="flex items-end gap-2 mt-1">
                <span class="text-3xl font-bold">{{ $gymWeekly }}/4</span>
                <span class="text-xs text-gray-400 mb-1">sessions</span>
            </div>
            <div class="flex gap-1 mt-4">
                @for($i = 0; $i < 4; $i++)
                <div class="flex-1 h-2 rounded-full {{ $i < $gymWeekly ? 'bg-blue-500' : 'bg-gray-100' }}"></div>
                @endfor
            </div>
        </div>

        {{-- Streak --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gray-50">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm font-medium">{{ __('Sholat Streak') }}</h3>
            <div class="flex items-end gap-2 mt-1">
                <span class="text-3xl font-bold">{{ $streak }}</span>
                <span class="text-xs text-gray-400 mb-1">{{ __('hari berturut-turut') }}</span>
            </div>
            <p class="text-[10px] text-gray-400 mt-4 italic">"{{ __('Konsistensi adalah kunci ketenangan.') }}"</p>
        </div>
    </div>

    {{-- Chart + Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl md:rounded-3xl p-4 md:p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold">Weekly Overview</h3>
                <span class="text-xs border-none bg-gray-50 rounded-lg px-2 py-1">This Week</span>
            </div>
            <div class="h-64">
                <canvas id="weekChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6">
            <h3 class="font-bold mb-4 md:mb-6">{{ __('Summary Bulan Ini') }}</h3>
            <div class="space-y-4">
                <div class="p-4 bg-green-50 rounded-2xl">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-green-700">{{ __('Sholat') }}</span>
                        <span class="text-xs text-green-600">{{ $sholatDaysThisMonth }} {{ __('hari') }}</span>
                    </div>
                    <p class="text-[10px] text-green-600">{{ $todayStats['takbir'] }}/5 takbir {{ __('harian') }}</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-2xl">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-blue-700">Gym</span>
                        <span class="text-xs text-blue-600">{{ $gymMonthly }}× {{ __('bulan ini') }}</span>
                    </div>
                    <p class="text-[10px] text-blue-600">{{ $caloriesWeek }} cal {{ __('minggu ini') }}</p>
                </div>
                <div class="p-4 bg-emerald-50 rounded-2xl">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-emerald-700">Run</span>
                        <span class="text-xs text-emerald-600">{{ $runWeeklyCount }}× {{ __('minggu ini') }}</span>
                    </div>
                    <p class="text-[10px] text-emerald-600">{{ number_format($runMonthlyDist, 1) }} km {{ __('bulan ini') }}</p>
                </div>
                <div class="p-4 bg-pink-50 rounded-2xl">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-pink-700">{{ __('Intimasi') }}</span>
                        <span class="text-xs text-pink-600">{{ $intimacyMonthly }}x {{ __('bulan ini') }}</span>
                    </div>
                    <p class="text-[10px] text-pink-600">{{ $intimacyToday }}x {{ __('Hari ini') }}</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-2xl">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-orange-700">Tasks</span>
                        <span class="text-xs text-orange-600">{{ count(array_filter($dailyTodos, fn($t)=>$t['done'])) }}/{{ count($dailyTodos) }} {{ __('harian') }}</span>
                    </div>
                    <p class="text-[10px] text-orange-600">{{ count(array_filter($weeklyTodos, fn($t)=>$t['done'])) }}/{{ count($weeklyTodos) }} {{ __('mingguan') }}</p>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@push('scripts')
<script>
const weekCtx = document.getElementById('weekChart').getContext('2d');
new Chart(weekCtx, {
    type: 'line',
    data: {
        labels: ['{{ __('Sen') }}','{{ __('Sel') }}','{{ __('Rab') }}','{{ __('Kam') }}','{{ __('Jum') }}','{{ __('Sab') }}','{{ __('Min') }}'],
        datasets: [
            {
                label: '{{ __('Sholat') }}',
                data: @json($weekSpiritualData),
                borderColor: '#10B981', tension: 0.4, borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#10B981',
            },
            {
                label: 'Fitness',
                data: @json($weekFitnessData),
                borderColor: '#3B82F6', tension: 0.4, borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#3B82F6',
            },
        ],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: true, position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', padding: 15, font: { size: 11, weight: '600' } } } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } },
    }
});
</script>
@endpush
