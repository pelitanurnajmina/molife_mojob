@extends('layouts.app')
@section('title', __('Motivasi'))
@section('page-title', __('Motivasi'))
@section('breadcrumb', 'Life › Motivasi')

@section('content')
<div class="space-y-4 md:space-y-6 max-w-3xl">

    {{-- ── Quote of the day ── --}}
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl md:rounded-3xl p-6 md:p-10 text-white relative overflow-hidden">
        <svg class="absolute -right-4 -top-4 w-32 h-32 text-white/5" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
        <div class="relative">
            <p class="text-[10px] font-bold uppercase tracking-widest text-white/50 mb-3">{{ __('Quote Hari Ini') }}</p>
            <p class="text-xl md:text-2xl font-bold leading-relaxed">"{{ $quote['text'] }}"</p>
            <p class="text-sm text-white/60 mt-4">— {{ $quote['src'] }}</p>
        </div>
    </div>

    {{-- ── Impact ── --}}
    <div>
        <div class="flex items-center gap-2 mb-3 px-1">
            <h3 class="font-bold">{{ __('Dampak Konsistensimu') }}</h3>
        </div>

        @if(count($impacts) === 0)
        <div class="bg-white rounded-2xl p-8 text-center border border-gray-50">
            <p class="text-sm text-gray-400">{{ __('Mulai catat aktivitasmu untuk melihat dampaknya di sini.') }}</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($impacts as $im)
            <div class="bg-white rounded-2xl p-5 border border-gray-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-{{ $im['color'] }}-50 text-{{ $im['color'] }}-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $im['icon'] }}"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold leading-none">
                        {{ $im['value'] }}<span class="text-sm text-gray-400 font-medium ml-0.5">{{ $im['unit'] }}</span>
                    </p>
                    <p class="text-xs font-bold text-gray-700 mt-1">{{ $im['label'] }}</p>
                    <p class="text-[11px] text-{{ $im['color'] }}-600 mt-0.5">{{ $im['msg'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
