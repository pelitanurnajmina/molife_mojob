@extends('layouts.app')
@section('title', __('Langganan'))
@section('page-title', __('Settings'))
@section('breadcrumb', 'Settings › Langganan')

@section('content')
@php
    $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $today = \Carbon\Carbon::today();
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Current subscription ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Langganan Sekarang') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Status akses molife kamu') }}</p>
            </div>
        </div>

        @if($active)
        @php $left = max(0, $today->diffInDays($active->ends_at, false)); @endphp
        <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/10 rounded-full"></div>
            <div class="relative flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/60">{{ __('Paket Aktif') }}</span>
                    <p class="text-2xl font-black mt-1">molife · {{ $plans[$active->plan]['label'] ?? $active->plan }}</p>
                    <p class="text-sm text-white/80 mt-1">{{ __('Aktif sampai') }} {{ $active->ends_at->translatedFormat('j F Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-black">{{ $left }}</p>
                    <p class="text-xs text-white/70 font-bold">{{ __('hari tersisa') }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center gap-4 p-4 bg-gray-900 text-white rounded-2xl">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold">{{ __('Belum berlangganan') }}</p>
                <p class="text-xs text-gray-400">{{ __('Pilih paket di bawah untuk akses penuh semua modul') }}</p>
            </div>
            <span class="text-xs font-bold px-2.5 py-1 bg-white/10 rounded-full flex-shrink-0">{{ __('Free') }}</span>
        </div>
        @endif
    </div>

    {{-- ── Plans ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-1">{{ $active ? __('Perpanjang / Upgrade') : __('Bayar sekali. Pilih durasimu.') }}</h3>
        <p class="text-xs text-gray-400 mb-6">{{ __('Satu kali bayar untuk akses penuh semua modul. Makin panjang durasi, makin hemat.') }}</p>

        @php
            $perMonth = ['1'=>'± Rp 11.000/bln','3'=>'± Rp 9.700/bln','6'=>'± Rp 8.200/bln','12'=>'± Rp 7.400/bln'];
            $badge    = ['3'=>'Paling Populer','6'=>'Hemat 26%','12'=>'Hemat 33%'];
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($plans as $key => $p)
            @php $pop = $key === '3'; @endphp
            <div class="relative p-4 md:p-5 rounded-2xl border-2 flex flex-col {{ $pop ? 'border-gray-900 bg-gray-50/60' : 'border-gray-100 bg-white' }}">
                @if(!empty($badge[$key]))
                <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 whitespace-nowrap text-[9px] font-bold {{ $pop ? 'bg-gray-900' : 'bg-orange-500' }} text-white px-2.5 py-1 rounded-full">{{ $badge[$key] }}</span>
                @endif
                <p class="text-sm font-bold text-gray-700 mt-1">{{ $p['label'] }}</p>
                <p class="text-2xl md:text-[26px] font-black text-gray-900 leading-none mt-2">{{ $rp($p['price']) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">{{ $perMonth[$key] ?? '' }}</p>
                <button type="button"
                    onclick="openPay('{{ $key }}', '{{ $p['label'] }}', {{ $p['price'] }})"
                    class="mt-4 py-2.5 text-center text-xs font-bold rounded-xl transition-all {{ $pop ? 'bg-gray-900 text-white hover:bg-gray-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $active ? __('Pilih') : __('Pilih') }}
                </button>
            </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 md:p-5 rounded-2xl bg-gray-50">
            <p class="text-xs font-bold text-gray-500 mb-3">{{ __('Semua paket termasuk:') }}</p>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                @foreach(['Semua tracker: sholat, olahraga, pomodoro, mood, tugas','Career Hub & pelacak lamaran','Bisnis: proposal, klien, dokumen & analitik','Keuangan: pemasukan, pengeluaran, anggaran, tabungan','Statistik 30 hari, insight & Life Score','Export data ke CSV'] as $f)
                <li class="flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-gray-700">{{ $f }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- ── History ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4">{{ __('Riwayat Langganan') }}</h3>
        @if(count($history) === 0)
        <div class="text-center py-8">
            <p class="text-sm text-gray-400">{{ __('Belum ada riwayat langganan.') }}</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-3 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Paket') }}</th>
                        <th class="text-left px-3 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 whitespace-nowrap">{{ __('Periode') }}</th>
                        <th class="text-right px-3 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Harga') }}</th>
                        <th class="text-left px-3 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $s)
                    @php $expired = $s->ends_at->lt($today); @endphp
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="px-3 py-3.5 font-bold text-gray-800 whitespace-nowrap">molife · {{ $plans[$s->plan]['label'] ?? $s->plan }}</td>
                        <td class="px-3 py-3.5 text-gray-500 whitespace-nowrap">{{ $s->starts_at->translatedFormat('j M Y') }} – {{ $s->ends_at->translatedFormat('j M Y') }}</td>
                        <td class="px-3 py-3.5 text-right font-bold text-gray-700 whitespace-nowrap">{{ $rp($s->price) }}</td>
                        <td class="px-3 py-3.5">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $expired ? 'bg-gray-100 text-gray-500' : 'bg-green-100 text-green-700' }}">{{ $expired ? __('Berakhir') : __('Aktif') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

{{-- ── QRIS payment modal ── --}}
<div id="payModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" onclick="if(event.target===this)closePay()">
    <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl">
        {{-- Header --}}
        <div class="relative px-6 pt-6 pb-5 bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 text-white">
            <div class="absolute -right-10 -top-10 w-36 h-36 rounded-full" style="background:radial-gradient(circle,rgba(124,92,240,.35),transparent 70%)"></div>
            <button type="button" onclick="closePay()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="relative flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-white/50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM17 14h.01M14 17h.01M20 20h.01M20 14h.01M14 20h.01"/></svg>
                {{ __('Pembayaran QRIS') }}
            </div>
            <p class="relative text-sm text-white/70 mt-3">molife · <span id="payPlan" class="font-bold text-white"></span></p>
            <p id="payAmount" class="relative text-3xl font-black mt-0.5"></p>
        </div>

        {{-- Body --}}
        <div class="p-6">
            <div class="text-center">
                <div class="relative inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm">
                    <span class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-gray-900 rounded-tl"></span>
                    <span class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-gray-900 rounded-tr"></span>
                    <span class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-gray-900 rounded-bl"></span>
                    <span class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-gray-900 rounded-br"></span>
                    <img id="payQr" src="" alt="QRIS" width="200" height="200" class="block rounded-lg">
                </div>
                <p class="text-xs text-gray-400 mt-3">{{ __('Bayar dari aplikasi apa pun yang mendukung QRIS.') }}</p>
            </div>

            {{-- Waiting for payment --}}
            <div class="flex items-center justify-center gap-2 mt-5 text-sm font-bold text-gray-500">
                <svg class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                {{ __('Menunggu pembayaran...') }}
            </div>
            <div class="flex items-center justify-center gap-1.5 mt-3 text-[11px] text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ __('Pembayaran aman & terenkripsi · akses aktif otomatis setelah lunas') }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openPay(key, label, price) {
    document.getElementById('payPlan').textContent = label;
    document.getElementById('payAmount').textContent = 'Rp ' + price.toLocaleString('id-ID');
    document.getElementById('payPlanKey').value = key;
    const ref = 'MOLIFE-' + key + 'BLN-' + Date.now();
    document.getElementById('payQr').src = 'https://api.qrserver.com/v1/create-qr-code/?size=440x440&margin=0&data=' + encodeURIComponent(ref);
    document.getElementById('payModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closePay() {
    document.getElementById('payModal').classList.add('hidden');
    document.body.style.overflow = '';
}
</script>
@endpush
@endsection
