@extends('layouts.admin')
@section('title', 'Detail Influencer')

@section('content')
@php
    $rp = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.');
    $conv = $stats['signups'] > 0 ? round($stats['payers'] / $stats['signups'] * 100) : 0;
@endphp

<div class="w-full">
    <a href="{{ route('admin.influencers') }}" class="text-sm font-semibold text-gray-400 hover:text-black">← Kembali ke daftar</a>

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 md:p-6 mt-3 mb-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2.5">
                    <span class="font-mono font-extrabold text-2xl">{{ $code->code }}</span>
                    @if($code->active)
                        <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-1 rounded">AKTIF</span>
                    @else
                        <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded">NONAKTIF</span>
                    @endif
                </div>
                <div class="text-gray-600 font-semibold mt-1">{{ $owner->profile->display_name ?? $owner->username ?? '—' }}</div>
                <div class="text-sm text-gray-400">{{ $owner->email }}</div>
                @if($code->label)<div class="text-xs text-gray-400 mt-1">{{ $code->label }}</div>@endif
                <div class="text-[11px] text-gray-400 mt-2">
                    Link bagikan: <span class="font-mono">{{ url('/register?ref='.$code->code) }}</span>
                </div>
            </div>
            <div class="flex flex-col items-end gap-2">
                <div class="flex gap-2">
                    <div class="text-center bg-gray-50 rounded-xl px-4 py-2">
                        <div class="text-xl font-extrabold">{{ $code->discount_percent }}%</div>
                        <div class="text-[10px] text-gray-400 font-semibold uppercase">Diskon</div>
                    </div>
                    <div class="text-center bg-gray-50 rounded-xl px-4 py-2">
                        <div class="text-xl font-extrabold">{{ $code->commission_percent }}%</div>
                        <div class="text-[10px] text-gray-400 font-semibold uppercase">Komisi</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.influencers.toggle', $code->id) }}"
                      data-confirm="{{ $code->active ? 'Nonaktifkan kode ini?' : 'Aktifkan lagi kode ini?' }}">
                    @csrf
                    <button class="text-xs font-semibold px-3 py-1.5 rounded-lg {{ $code->active ? 'bg-red-50 text-red-500 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                        {{ $code->active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Penghasilan --}}
    <h2 class="text-lg font-bold mb-3">Penghasilan</h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-black text-white rounded-2xl p-4 md:p-5">
            <div class="text-xs font-semibold text-white/60 uppercase tracking-wide">Saldo Sekarang</div>
            <div class="text-2xl font-extrabold mt-1">{{ $rp($stats['balance']) }}</div>
            <div class="text-[11px] text-white/50 mt-1">Siap dicairkan</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Sedang Diproses</div>
            <div class="text-2xl font-extrabold mt-1">{{ $rp($stats['pending']) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Pencairan pending</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Sudah Dicairkan</div>
            <div class="text-2xl font-extrabold mt-1">{{ $rp($stats['paid_out']) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Total transfer</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Komisi Kode</div>
            <div class="text-2xl font-extrabold mt-1">{{ $rp($stats['total_comm']) }}</div>
            <div class="text-[11px] text-gray-400 mt-1">Sepanjang waktu</div>
        </div>
    </div>

    {{-- Statistik kode --}}
    <h2 class="text-lg font-bold mb-3">Statistik Kode</h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Daftar Pakai Kode</div>
            <div class="text-2xl font-extrabold mt-1">{{ number_format($stats['signups'],0,',','.') }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Sudah Bayar</div>
            <div class="text-2xl font-extrabold mt-1">{{ number_format($stats['payers'],0,',','.') }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Konversi</div>
            <div class="text-2xl font-extrabold mt-1">{{ $conv }}%</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pendapatan Dihasilkan</div>
            <div class="text-2xl font-extrabold mt-1">{{ $rp($stats['revenue']) }}</div>
        </div>
    </div>

    {{-- Daftar audiens --}}
    <h2 class="text-lg font-bold mb-3">Audiens yang Pakai Kode Ini <span class="text-gray-400 font-normal text-sm">({{ $stats['signups'] }})</span></h2>
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-semibold px-4 py-3">Nama</th>
                        <th class="text-left font-semibold px-4 py-3">Email</th>
                        <th class="text-left font-semibold px-4 py-3">Daftar</th>
                        <th class="text-left font-semibold px-4 py-3">Status</th>
                        <th class="text-right font-semibold px-4 py-3">Dibayar</th>
                        <th class="text-right font-semibold px-4 py-3">Komisi kamu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rows as $r)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3 font-semibold whitespace-nowrap">{{ $r['name'] }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $r['email'] }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $r['joined'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($r['paid'])
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    <span class="font-semibold text-green-600 text-xs">Bayar</span>
                                </span>
                                <div class="text-[11px] text-gray-400">{{ $r['paid_at'] }}</div>
                            @else
                                <span class="text-gray-400 text-xs font-semibold">Belum bayar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            {{ $r['total_paid'] > 0 ? $rp($r['total_paid']) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap font-semibold {{ $r['commission'] > 0 ? 'text-black' : 'text-gray-300' }}">
                            {{ $r['commission'] > 0 ? $rp($r['commission']) : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada yang daftar pakai kode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
