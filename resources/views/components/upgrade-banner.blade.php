@props([
    'title'   => 'Upgrade ke Plus',
    'message' => 'Buka semua batasan dengan langganan Plus',
    'cta'     => 'Lihat Plan',
    'variant' => 'soft', // 'soft' | 'warning'
])

@php
$styles = match($variant) {
    'warning' => [
        'wrap'  => 'bg-orange-50 border-orange-200',
        'icon'  => 'bg-orange-500 text-white',
        'title' => 'text-orange-900',
        'msg'   => 'text-orange-700',
    ],
    default => [
        'wrap'  => 'bg-gradient-to-r from-orange-50 to-amber-50 border-orange-100',
        'icon'  => 'bg-white text-orange-500',
        'title' => 'text-gray-900',
        'msg'   => 'text-gray-600',
    ],
};
@endphp

<div class="flex items-center gap-3 md:gap-4 p-4 rounded-2xl border {{ $styles['wrap'] }}">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $styles['icon'] }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    </div>
    <div class="flex-1 min-w-0">
        <p class="font-bold text-sm {{ $styles['title'] }}">{{ $title }}</p>
        <p class="text-xs mt-0.5 {{ $styles['msg'] }}">{{ $message }}</p>
    </div>
    <a href="{{ route('settings.langganan') }}"
       class="flex-shrink-0 px-3 py-1.5 md:px-4 md:py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl transition-all whitespace-nowrap">
        {{ $cta }} →
    </a>
</div>
