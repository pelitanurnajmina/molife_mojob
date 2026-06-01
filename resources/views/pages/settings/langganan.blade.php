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
                <h3 class="text-base font-bold">{{ __('Plan Kamu') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Status langganan saat ini') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 p-4 bg-gray-900 text-white rounded-2xl">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold">Freemium</p>
                <p class="text-xs text-gray-400">{{ __('Akses ke semua fitur dasar') }}</p>
            </div>
            <span class="text-xs font-bold px-2.5 py-1 bg-white/10 rounded-full flex-shrink-0">{{ __('Aktif') }}</span>
        </div>
    </div>

    {{-- ── Plan Comparison ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-2">{{ __('Pilih Plan Kamu') }}</h3>
        <p class="text-xs text-gray-400 mb-6">{{ __('Dapatkan fitur tambahan untuk pengalaman yang lebih lengkap.') }}</p>

        @php
        $plans = [
            [
                'key'     => 'freemium',
                'name'    => 'Freemium',
                'tagline' => __('Coba semua fitur dasar'),
                'price'   => 0,
                'badge'   => null,
                'current' => true,
                'available' => true,
                'features' => [
                    'Tracking Life (Sholat, Olahraga, Mood, dll)',
                    'Maksimal 10 lamaran kerja',
                    'Riwayat keuangan 7 hari terakhir',
                    'Statistik 30 hari terakhir',
                ],
                'border'  => 'border-gray-200',
                'btn'     => 'gray',
            ],
            [
                'key'     => 'plus',
                'name'    => 'Plus',
                'tagline' => __('Lepas semua batasan'),
                'price'   => 17000,
                'badge'   => __('Populer'),
                'current' => false,
                'available' => false,
                'features' => [
                    'Semua fitur Freemium',
                    'Lamaran kerja tanpa batas',
                    'Riwayat keuangan tanpa batas',
                    'Statistik 1 tahun penuh',
                ],
                'border'  => 'border-orange-400',
                'btn'     => 'orange',
            ],
            [
                'key'     => 'pro',
                'name'    => 'Pro',
                'tagline' => __('Untuk pengguna profesional'),
                'price'   => 49000,
                'badge'   => null,
                'current' => false,
                'available' => false,
                'features' => [
                    'Semua fitur Plus',
                    'Export laporan ke PDF',
                    'Priority support 24/7',
                ],
                'border'  => 'border-gray-900',
                'btn'     => 'black',
            ],
        ];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach($plans as $plan)
            <div class="relative p-5 rounded-2xl border-2 {{ $plan['border'] }} {{ $plan['current'] ? 'bg-gray-50' : 'bg-white' }} flex flex-col">

                @if($plan['badge'])
                <span class="absolute -top-2.5 left-5 text-[10px] font-bold bg-orange-500 text-white px-2.5 py-1 rounded-full">{{ $plan['badge'] }}</span>
                @endif

                {{-- Header --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1">
                        <p class="font-bold text-base">{{ $plan['name'] }}</p>
                        @if($plan['current'])
                        <span class="text-[10px] font-bold bg-gray-900 text-white px-2 py-0.5 rounded-full">{{ __('Aktif') }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400">{{ $plan['tagline'] }}</p>
                </div>

                {{-- Price --}}
                <div class="mb-5">
                    @if($plan['price'] === 0)
                    <p class="text-3xl font-bold">{{ __('Gratis') }}</p>
                    <p class="text-[10px] text-gray-400">{{ __('Selamanya') }}</p>
                    @else
                    <p class="text-3xl font-bold">{{ $rp($plan['price']) }}<span class="text-sm font-medium text-gray-400"> /bulan</span></p>
                    <p class="text-[10px] text-gray-400">{{ __('Atau') }} {{ $rp($plan['price'] * 10) }}/{{ __('tahun') }} <span class="text-orange-500 font-bold">({{ __('hemat 17%') }})</span></p>
                    @endif
                </div>

                {{-- Features --}}
                <ul class="space-y-2.5 text-xs mb-5 flex-1">
                    @foreach($plan['features'] as $f)
                    <li class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5
                            {{ $plan['key'] === 'freemium' ? 'text-green-500' : ($plan['key'] === 'plus' ? 'text-orange-500' : 'text-gray-900') }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-gray-700">{{ $f }}</span>
                    </li>
                    @endforeach
                </ul>

                {{-- CTA --}}
                @if($plan['current'])
                <div class="py-2.5 text-center text-xs font-bold text-gray-400 bg-gray-100 rounded-xl">{{ __('Plan saat ini') }}</div>
                @elseif(!$plan['available'])
                <button type="button" disabled
                    class="py-2.5 text-center text-xs font-bold rounded-xl cursor-not-allowed
                    {{ $plan['btn'] === 'orange' ? 'bg-orange-50 text-orange-400' : 'bg-gray-50 text-gray-400' }}">
                    {{ __('Segera Hadir') }}
                </button>
                @else
                <button type="button"
                    class="py-2.5 text-center text-xs font-bold rounded-xl transition-all
                    {{ $plan['btn'] === 'orange'
                        ? 'bg-orange-500 text-white hover:bg-orange-600'
                        : 'bg-gray-900 text-white hover:bg-gray-800' }}">
                    {{ __('Upgrade ke') }} {{ $plan['name'] }}
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── FAQ ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-5">{{ __('Pertanyaan Umum') }}</h3>
        @php
        $faqs = [
            ['q' => __('Apakah saya bisa membatalkan kapan saja?'),         'a' => __('Tentu. Kamu bisa downgrade ke Freemium kapan saja tanpa biaya tambahan.')],
            ['q' => __('Apakah data saya hilang kalau saya downgrade?'),    'a' => __('Tidak. Semua data tetap aman. Hanya akses ke fitur premium yang akan terbatas sesuai plan kamu.')],
            ['q' => __('Metode pembayaran apa saja yang didukung?'),        'a' => __('Transfer bank, e-wallet (GoPay, OVO, DANA, ShopeePay), kartu kredit, dan QRIS.')],
            ['q' => __('Apakah ada garansi uang kembali?'),                  'a' => __('Ya, 7 hari sejak pembayaran. Hubungi support jika tidak puas dan kami akan refund penuh.')],
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
