@extends('layouts.app')
@section('title', __('Langganan'))
@section('page-title', __('Settings'))
@section('breadcrumb', 'Settings › Langganan')

@section('content')
@php $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.'); @endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Current Plan ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Status Akses') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Akses molife kamu saat ini') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 p-4 bg-gray-900 text-white rounded-2xl">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold">{{ __('Masa Uji Coba') }}</p>
                <p class="text-xs text-gray-400">{{ __('Pilih durasi di bawah untuk akses penuh semua modul') }}</p>
            </div>
            <span class="text-xs font-bold px-2.5 py-1 bg-white/10 rounded-full flex-shrink-0">{{ __('Aktif') }}</span>
        </div>
    </div>

    {{-- ── Pricing (bayar sekali, pilih durasi) ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-1">{{ __('Bayar sekali. Pilih durasimu.') }}</h3>
        <p class="text-xs text-gray-400 mb-6">{{ __('Satu kali bayar untuk akses penuh semua modul. Makin panjang durasi, makin hemat. Tanpa perpanjangan otomatis.') }}</p>

        @php
        $plans = [
            ['key'=>'1',  'label'=>'1 Bulan', 'price'=>11000, 'per'=>'± Rp 11.000/bln', 'badge'=>null,           'popular'=>false],
            ['key'=>'3',  'label'=>'3 Bulan', 'price'=>29000, 'per'=>'± Rp 9.700/bln',  'badge'=>'Paling Populer','popular'=>true],
            ['key'=>'6',  'label'=>'6 Bulan', 'price'=>49000, 'per'=>'± Rp 8.200/bln',  'badge'=>'Hemat 26%',     'popular'=>false],
            ['key'=>'12', 'label'=>'1 Tahun', 'price'=>89000, 'per'=>'± Rp 7.400/bln',  'badge'=>'Hemat 33%',     'popular'=>false],
        ];
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($plans as $plan)
            <div class="relative p-4 md:p-5 rounded-2xl border-2 flex flex-col {{ $plan['popular'] ? 'border-gray-900 bg-gray-50/60' : 'border-gray-100 bg-white' }}">
                @if($plan['badge'])
                <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 whitespace-nowrap text-[9px] font-bold {{ $plan['popular'] ? 'bg-gray-900' : 'bg-orange-500' }} text-white px-2.5 py-1 rounded-full">{{ $plan['badge'] }}</span>
                @endif
                <p class="text-sm font-bold text-gray-700 mt-1">{{ $plan['label'] }}</p>
                <p class="text-2xl md:text-[26px] font-black text-gray-900 leading-none mt-2">{{ $rp($plan['price']) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">{{ $plan['per'] }}</p>
                <button type="button"
                    class="mt-4 py-2.5 text-center text-xs font-bold rounded-xl transition-all {{ $plan['popular'] ? 'bg-gray-900 text-white hover:bg-gray-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('Pilih') }}
                </button>
            </div>
            @endforeach
        </div>

        {{-- Shared features --}}
        <div class="mt-6 p-4 md:p-5 rounded-2xl bg-gray-50">
            <p class="text-xs font-bold text-gray-500 mb-3">{{ __('Semua paket termasuk:') }}</p>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                @foreach([
                    'Semua tracker: sholat, olahraga, pomodoro, mood, tugas',
                    'Career Hub & pelacak lamaran lengkap',
                    'Bisnis: proposal, klien, dokumen & analitik',
                    'Keuangan: pemasukan, pengeluaran, anggaran, tabungan',
                    'Statistik 30 hari, insight & Life Score harian',
                    'Export data ke CSV',
                ] as $f)
                <li class="flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-gray-700">{{ $f }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        <p class="text-center text-[11px] text-gray-400 mt-4">{{ __('Akses penuh ke semua modul · Tanpa perpanjangan otomatis · Pembayaran bersifat final') }}</p>
    </div>

    {{-- ── FAQ ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-5">{{ __('Pertanyaan Umum') }}</h3>
        @php
        $faqs = [
            ['q' => __('Apakah ini langganan otomatis?'),               'a' => __('Tidak. Kamu bayar sekali untuk durasi yang dipilih. Tidak ada perpanjangan otomatis. Setelah masa aktif habis, kamu sendiri yang memutuskan untuk memperpanjang.')],
            ['q' => __('Apa bedanya tiap durasi?'),                     'a' => __('Isinya sama persis, akses penuh ke semua modul. Bedanya hanya lama akses dan harga per bulan, makin panjang durasi makin hemat.')],
            ['q' => __('Apakah data saya hilang kalau masa aktif habis?'), 'a' => __('Tidak. Semua datamu tetap aman. Kamu tinggal memperpanjang untuk membuka kembali akses penuh.')],
            ['q' => __('Metode pembayaran apa saja yang didukung?'),    'a' => __('Transfer bank, e-wallet (GoPay, OVO, DANA, ShopeePay), kartu kredit, dan QRIS.')],
        ];
        @endphp
        <div class="space-y-2">
            @foreach($faqs as $i => $faq)
            <details class="group rounded-xl border border-gray-100 overflow-hidden">
                <summary class="flex items-center justify-between gap-3 p-4 cursor-pointer hover:bg-gray-50 transition-all list-none">
                    <span class="text-sm font-bold text-gray-800">{{ $faq['q'] }}</span>
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m6 9 6 6 6-6"/>
                    </svg>
                </summary>
                <div class="px-4 pb-4 text-xs text-gray-500 leading-relaxed">{{ $faq['a'] }}</div>
            </details>
            @endforeach
        </div>
    </div>

</div>
@endsection
