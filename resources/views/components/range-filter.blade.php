@props([
    'range' => 'month',   // current: month | 3m | 6m | 12m
    'route' => null,      // route name to link to
])

@php
    $options = [
        'month' => __('Bulan Ini'),
        '3m'    => __('3 Bulan'),
        '6m'    => __('6 Bulan'),
        '12m'   => __('1 Tahun'),
    ];
    $current = $options[$range] ?? $options['month'];
@endphp

<details class="relative group" {{ $attributes }}>
    <summary class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 cursor-pointer hover:border-gray-300 transition-all list-none select-none">
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ $current }}
        <svg class="w-3 h-3 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m6 9 6 6 6-6"/>
        </svg>
    </summary>

    <div class="absolute right-0 mt-2 w-40 bg-white rounded-2xl shadow-xl border border-gray-100 z-30 p-1.5">
        @foreach($options as $key => $label)
        <a href="{{ route($route, ['range' => $key]) }}"
           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all
                  {{ $range === $key ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            {{ $label }}
            @if($range === $key)
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            @endif
        </a>
        @endforeach
    </div>
</details>
