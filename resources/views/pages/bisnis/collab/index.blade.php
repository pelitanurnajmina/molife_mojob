@extends('layouts.app')
@section('title', 'Kolaborasi')
@section('page-title', __('Kolaborasi'))
@section('breadcrumb', 'Bisnis › Kolaborasi')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- Undangan pending --}}
    @if(count($pending))
    <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 md:p-5">
        <p class="text-sm font-bold text-amber-900 mb-3">{{ __('Undangan menunggu konfirmasi') }}</p>
        <div class="space-y-2">
            @foreach($pending as $inv)
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 bg-white rounded-xl border border-amber-100 p-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $inv->product->name }}</p>
                    <p class="text-[11px] text-gray-400">{{ __('diundang oleh') }} {{ $inv->owner?->username ?: '-' }}</p>
                </div>
                <a href="{{ route('kolaborasi.terima', $inv->token) }}"
                   class="px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition-all text-center flex-shrink-0">
                    {{ __('Terima Undangan') }}
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Produk yang dibagikan ke saya --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <h3 class="font-bold mb-1">{{ __('Produk Kolaborasi') }}</h3>
        <p class="text-xs text-gray-400 mb-5">{{ __('Produk bisnis yang dibagikan ke kamu. Kamu bisa ikut mengelola proposal, template pesan, dan melihat statistiknya.') }}</p>

        @if(count($shared) === 0)
        <div class="text-center py-10">
            <div class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0 .656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-sm text-gray-400">{{ __('Belum ada produk yang dibagikan ke kamu.') }}</p>
            <p class="text-xs text-gray-300 mt-1">{{ __('Minta pemilik bisnis mengundang email akun kamu.') }}</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($shared as $c)
            <a href="{{ route('kolaborasi.workspace', $c->business_product_id) }}"
               class="group flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:border-gray-300 hover:shadow-sm transition-all">
                <div class="w-11 h-11 rounded-xl bg-gray-900 text-white flex items-center justify-center flex-shrink-0 text-sm font-black">
                    {{ strtoupper(substr($c->product->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate group-hover:text-black">{{ $c->product->name }}</p>
                    <p class="text-[11px] text-gray-400">{{ __('milik') }} {{ $c->owner?->username ?: '-' }} · {{ __('bergabung') }} {{ optional($c->accepted_at)->translatedFormat('j M Y') }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
