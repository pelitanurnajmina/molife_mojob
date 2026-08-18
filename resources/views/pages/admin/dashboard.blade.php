@extends('layouts.admin')
@section('title', 'Ringkasan')

@section('content')
@php
    $rp = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');
@endphp

<div class="w-full">
    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mb-1">Ringkasan</h1>
    <p class="text-gray-500 text-sm mb-6">Sekilas pertumbuhan & pendapatan Molife.</p>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-8">
        @php
        $cards = [
            ['Total Pengguna', $stats['total_users'], 'Daftar hari ini: '.$stats['new_today']],
            ['Daftar 7 Hari', $stats['new_week'], 'Pengguna baru minggu ini'],
            ['Sudah Bayar', $stats['paying_users'], 'Belum bayar: '.$stats['free_users']],
            ['Langganan Aktif', $stats['active_subs'], 'Termasuk akses gratis'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $c[0] }}</div>
            <div class="text-2xl md:text-3xl font-extrabold mt-1">{{ number_format($c[1],0,',','.') }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $c[2] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Revenue --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 mb-8">
        <div class="bg-black text-white rounded-2xl p-5">
            <div class="text-xs font-semibold text-white/60 uppercase tracking-wide">Pendapatan Total</div>
            <div class="text-3xl font-extrabold mt-1">{{ $rp($stats['revenue']) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pendapatan Bulan Ini</div>
            <div class="text-3xl font-extrabold mt-1">{{ $rp($stats['revenue_month']) }}</div>
        </div>
    </div>

    {{-- Recent signups --}}
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-bold">Pendaftar Terbaru</h2>
        <a href="{{ route('admin.users') }}" class="text-sm font-semibold text-orange-500 hover:underline">Lihat semua</a>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-semibold px-4 py-3">Nama</th>
                        <th class="text-left font-semibold px-4 py-3">Email</th>
                        <th class="text-left font-semibold px-4 py-3">Daftar</th>
                        <th class="text-left font-semibold px-4 py-3">Onboarding</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recent as $u)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3 font-semibold whitespace-nowrap">{{ $u['name'] }}
                            @if($u['is_admin'])<span class="ml-1 text-[10px] font-bold text-orange-500 align-middle">ADMIN</span>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $u['email'] }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $u['joined'] }}</td>
                        <td class="px-4 py-3">
                            @if($u['setup_done'])
                                <span class="text-green-600 text-xs font-semibold">Selesai</span>
                            @else
                                <span class="text-gray-400 text-xs font-semibold">Belum</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
