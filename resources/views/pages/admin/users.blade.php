@extends('layouts.admin')
@section('title', 'Pengguna')

@section('content')
@php $rp = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.'); @endphp

<div class="max-w-6xl">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-1">
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Pengguna</h1>
        <span class="text-sm text-gray-400">{{ number_format($users->total(),0,',','.') }} total</span>
    </div>
    <p class="text-gray-500 text-sm mb-5">Semua yang mendaftar, status bayar, dan dari mana mereka datang.</p>

    {{-- Search --}}
    <form method="GET" class="mb-4 flex gap-2 max-w-md">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, email, atau username…"
               class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-black">
        <button class="bg-black text-white text-sm font-semibold px-4 py-2.5 rounded-xl">Cari</button>
        @if($q)<a href="{{ route('admin.users') }}" class="text-sm font-semibold text-gray-400 px-3 py-2.5">Reset</a>@endif
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-semibold px-4 py-3">Nama</th>
                        <th class="text-left font-semibold px-4 py-3">Email</th>
                        <th class="text-left font-semibold px-4 py-3">Daftar</th>
                        <th class="text-left font-semibold px-4 py-3">Status</th>
                        <th class="text-left font-semibold px-4 py-3">Total Bayar</th>
                        <th class="text-left font-semibold px-4 py-3">Via</th>
                        <th class="text-right font-semibold px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rows as $u)
                    <tr class="hover:bg-gray-50/60 align-middle">
                        <td class="px-4 py-3 font-semibold whitespace-nowrap">{{ $u['name'] }}
                            @if($u['is_admin'])<span class="ml-1 text-[10px] font-bold text-orange-500">ADMIN</span>@endif
                            <div class="text-xs text-gray-400 font-normal">@ {{ $u['username'] }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $u['email'] }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $u['joined'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($u['active_plan'])
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    <span class="font-semibold text-gray-700">{{ $u['active_plan'] }}</span>
                                </span>
                                <div class="text-xs {{ $u['is_comp'] ? 'text-orange-500' : 'text-gray-400' }}">
                                    {{ $u['is_comp'] ? 'Gratis (comp)' : 'Berbayar' }} · s/d {{ $u['active_until'] }}
                                </div>
                            @else
                                <span class="text-gray-400 text-xs font-semibold">Free</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($u['paid_total'] > 0)
                                <span class="font-semibold">{{ $rp($u['paid_total']) }}</span>
                                <div class="text-xs text-gray-400">{{ $u['paid_count'] }}x bayar</div>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($u['via'])
                                <span class="text-xs font-semibold bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">{{ $u['via'] }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <details class="inline-block text-left relative">
                                <summary class="list-none cursor-pointer text-xs font-semibold text-gray-500 hover:text-black bg-gray-100 hover:bg-gray-200 px-2.5 py-1.5 rounded-lg">Beri akses</summary>
                                <div class="absolute right-0 mt-1 z-10 bg-white border border-gray-200 rounded-xl shadow-lg p-3 w-44">
                                    <form method="POST" action="{{ route('admin.grant') }}" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $u['id'] }}">
                                        <div class="text-xs text-gray-400 font-semibold">Durasi gratis</div>
                                        <select name="months" class="w-full text-sm border border-gray-200 rounded-lg px-2 py-1.5">
                                            <option value="1">1 Bulan</option>
                                            <option value="3">3 Bulan</option>
                                            <option value="6">6 Bulan</option>
                                            <option value="12">1 Tahun</option>
                                        </select>
                                        <button class="w-full bg-black text-white text-xs font-semibold py-1.5 rounded-lg">Berikan</button>
                                    </form>
                                </div>
                            </details>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">Tidak ada pengguna cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="mt-4 flex items-center justify-between text-sm">
        <div class="text-gray-400">Hal. {{ $users->currentPage() }} / {{ $users->lastPage() }}</div>
        <div class="flex gap-2">
            @if($users->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-300 font-semibold">← Sebelumnya</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 font-semibold hover:bg-gray-50">← Sebelumnya</a>
            @endif
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 font-semibold hover:bg-gray-50">Berikutnya →</a>
            @else
                <span class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-300 font-semibold">Berikutnya →</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
