@extends('layouts.app')
@section('title', __('Keuangan'))
@section('page-title', __('Keuangan'))
@section('breadcrumb', __('Finance › Overview'))

@section('content')
@php
    $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $monthName = date('F Y', strtotime(date('Y-m-d')));
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Plan limit banner (Freemium only) ── --}}
    @if($isFreemium && $daysLimit)
    <x-upgrade-banner
        title="{{ __('Riwayat dibatasi :n hari terakhir', ['n' => $daysLimit]) }}"
        message="{{ __('Upgrade ke Plus untuk lihat semua riwayat keuangan tanpa batas + tren 6 bulan.') }}"
        cta="{{ __('Upgrade ke Plus') }}" />
    @endif

    @php $periodLabel = $isFreemium ? __(':n hari terakhir', ['n' => $daysLimit]) : $monthName; @endphp

    {{-- ── Balance Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl p-4 md:p-6">
            <p class="text-xs font-bold text-gray-400 mb-1">{{ __('Pemasukan') }} · {{ $periodLabel }}</p>
            <p class="text-2xl font-bold text-green-600">{{ $rp($summary['income']) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-6">
            <p class="text-xs font-bold text-gray-400 mb-1">{{ __('Pengeluaran') }} · {{ $periodLabel }}</p>
            <p class="text-2xl font-bold text-red-500">{{ $rp($summary['expense']) }}</p>
        </div>
        <div class="rounded-2xl p-4 md:p-6 {{ $summary['balance'] >= 0 ? 'bg-emerald-500' : 'bg-red-500' }} text-white">
            <p class="text-xs font-bold opacity-75 mb-1">{{ __('Saldo') }} · {{ $periodLabel }}</p>
            <p class="text-2xl font-bold">{{ $rp($summary['balance']) }}</p>
        </div>
    </div>

    {{-- ── 6-Month Trend (Plus & Pro only) ── --}}
    @if(!$isFreemium)
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-6">{{ __('Tren 6 Bulan Terakhir') }}</h3>
        <div style="height:200px"><canvas id="finTrendChart"></canvas></div>
    </div>
    @else
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 relative overflow-hidden">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold">{{ __('Tren 6 Bulan Terakhir') }}</h3>
            <span class="text-[10px] font-bold bg-orange-100 text-orange-600 px-2 py-1 rounded-full">PLUS</span>
        </div>
        <div class="relative" style="height:200px">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center bg-gradient-to-b from-white/70 to-white backdrop-blur-sm rounded-xl">
                <svg class="w-8 h-8 text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p class="text-sm font-bold text-gray-700">{{ __('Tersedia di paket Plus') }}</p>
                <a href="{{ route('settings.langganan') }}" class="mt-2 inline-block px-3 py-1.5 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 transition-all">
                    {{ __('Upgrade — Rp 17.000/bln') }}
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Budget Overview + Recent Transactions ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">

        {{-- Budget --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm">{{ __('Anggaran Bulan Ini') }}</h3>
                <a href="{{ route('finance.anggaran') }}" class="text-xs font-bold text-gray-400 hover:text-black transition-all">{{ __('Kelola') }} →</a>
            </div>
            @if(empty($budget))
            <div class="text-center py-6">
                <p class="text-sm text-gray-400">{{ __('Belum ada anggaran') }}</p>
                <a href="{{ route('finance.anggaran') }}" class="text-xs font-bold text-black hover:underline mt-1 inline-block">{{ __('+ Buat anggaran') }}</a>
            </div>
            @else
            <div class="space-y-3">
                @foreach($budget as $cat => $limit)
                @php $spent = $spentByCategory[$cat] ?? 0; $pct = $limit > 0 ? min(100, round(($spent/$limit)*100)) : 0; @endphp
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-gray-700">{{ $cat }}</span>
                        <span class="{{ $pct >= 90 ? 'text-red-500' : 'text-gray-400' }}">{{ $rp($spent) }} / {{ $rp($limit) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-400' : 'bg-green-500') }}" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm">{{ __('Transaksi Terakhir') }}</h3>
                <a href="{{ route('finance.transaksi') }}" class="text-xs font-bold text-gray-400 hover:text-black transition-all">{{ __('Lihat semua') }} →</a>
            </div>
            @if(empty($recentTxs))
            <div class="text-center py-6">
                <p class="text-sm text-gray-400">{{ __('Belum ada transaksi') }}</p>
                <a href="{{ route('finance.transaksi') }}" class="text-xs font-bold text-black hover:underline mt-1 inline-block">{{ __('+ Catat transaksi') }}</a>
            </div>
            @else
            <div class="space-y-2">
                @foreach($recentTxs as $tx)
                <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition-all">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 {{ $tx['type'] === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tx['type'] === 'income' ? 'M7 11l5-5m0 0l5 5m-5-5v12' : 'M17 13l-5 5m0 0l-5-5m5 5V6' }}"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">{{ $tx['category'] }}</p>
                        <p class="text-[10px] text-gray-400">{{ date('j M', strtotime($tx['date'])) }}{{ $tx['note'] ? ' · ' . $tx['note'] : '' }}</p>
                    </div>
                    <span class="text-xs font-bold flex-shrink-0 {{ $tx['type'] === 'income' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $tx['type'] === 'income' ? '+' : '-' }}{{ $rp($tx['amount']) }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── Savings Goals ── --}}
    @if(count($savingsGoals) > 0)
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold">{{ __('Tujuan Tabungan') }}</h3>
            <a href="{{ route('finance.tabungan') }}" class="text-xs font-bold text-gray-400 hover:text-black transition-all">{{ __('Kelola') }} →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($savingsGoals as $goal)
            @php
                $pct  = $goal['target'] > 0 ? min(100, round(($goal['current'] / $goal['target']) * 100)) : 0;
                $cols = ['emerald'=>'bg-emerald-500','blue'=>'bg-blue-500','violet'=>'bg-violet-500','orange'=>'bg-orange-400','pink'=>'bg-pink-500'];
                $col  = $cols[$goal['color'] ?? 'emerald'] ?? 'bg-emerald-500';
            @endphp
            <div class="p-4 bg-gray-50 rounded-2xl">
                <p class="font-bold text-sm mb-1 truncate">{{ $goal['name'] }}</p>
                <p class="text-xs text-gray-400 mb-3">{{ $rp($goal['current']) }} / {{ $rp($goal['target']) }}</p>
                <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden mb-1">
                    <div class="{{ $col }} h-full rounded-full" style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400 font-bold">{{ $pct }}%{{ $goal['deadline'] ? ' · Target: ' . date('M Y', strtotime($goal['deadline'])) : '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
@if(!$isFreemium)
<script>
new Chart(document.getElementById('finTrendChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json(array_column($trend, 'month')),
        datasets: [
            { label: '{{ __("Pemasukan") }}', data: @json(array_column($trend, 'income')),  backgroundColor: 'rgba(16,185,129,0.7)',  borderRadius: 6 },
            { label: '{{ __("Pengeluaran") }}',data: @json(array_column($trend, 'expense')), backgroundColor: 'rgba(239,68,68,0.65)',  borderRadius: 6 },
        ],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 11 } } },
            tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID') } }
        },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt', font: { size: 9 } }, grid: { color: '#f9fafb' } },
            x: { grid: { display: false } }
        },
    }
});
</script>
@endif
@endpush
