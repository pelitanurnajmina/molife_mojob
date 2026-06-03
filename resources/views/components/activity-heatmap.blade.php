@props([
    'months' => 3,          // 3, 6, or 12
    'levels' => [],         // ['Y-m-d' => 0..4]
    'labels' => [],         // ['Y-m-d' => 'tooltip text'] (optional)
    'color'  => 'green',    // tailwind base color
    'today'  => null,
])

@php
    $today = $today ?? date('Y-m-d');

    // Build the day grid, aligned so each column = one week (Mon..Sun)
    $end    = new DateTime('today');
    $start  = (new DateTime('today'))->modify("-{$months} months")->modify('+1 day');
    $trueStart = $start->format('Y-m-d');
    $startDow  = (int) $start->format('N'); // 1=Mon .. 7=Sun
    if ($startDow > 1) $start->modify('-' . ($startDow - 1) . ' days');

    $weeks  = [];
    $week   = [];
    $cursor = clone $start;
    while ($cursor <= $end) {
        $week[] = $cursor->format('Y-m-d');
        if ((int) $cursor->format('N') === 7) { $weeks[] = $week; $week = []; }
        $cursor->modify('+1 day');
    }
    if (!empty($week)) $weeks[] = $week;
    $weekCount = count($weeks);

    // Month label per week column (when month of the week's first day changes)
    $monthNames = ['', __('Jan'),__('Feb'),__('Mar'),__('Apr'),__('Mei'),__('Jun'),
                       __('Jul'),__('Agu'),__('Sep'),__('Okt'),__('Nov'),__('Des')];
    $weekMonthLabel = [];
    $prevMonth = null;
    foreach ($weeks as $wi => $w) {
        $m = (int) substr($w[0], 5, 2);
        $weekMonthLabel[$wi] = ($m !== $prevMonth) ? $monthNames[$m] : '';
        $prevMonth = $m;
    }

    $shades = [
        0 => 'bg-gray-100',
        1 => "bg-{$color}-200",
        2 => "bg-{$color}-300",
        3 => "bg-{$color}-400",
        4 => "bg-{$color}-500",
    ];
    $dayLabels = ['', __('Sen'), '', __('Rab'), '', __('Jum'), '']; // alt rows only
@endphp

<div class="w-full">
    {{-- Responsive grid: fills full container width, square cells scale with width --}}
    <div style="display:grid; grid-template-columns: 1.5rem repeat({{ $weekCount }}, minmax(0,1fr)); gap:4px; width:100%;">

        {{-- Month labels row --}}
        <div></div>
        @foreach($weeks as $wi => $w)
        <div class="text-[9px] text-gray-400 font-medium leading-none mb-0.5 whitespace-nowrap overflow-visible">{{ $weekMonthLabel[$wi] }}</div>
        @endforeach

        {{-- 7 day rows (Mon..Sun) --}}
        @for($d = 0; $d < 7; $d++)
            <div class="text-[9px] text-gray-400 leading-none flex items-center">{{ $dayLabels[$d] }}</div>
            @foreach($weeks as $w)
                @php $ds = $w[$d] ?? null; @endphp
                @if($ds && $ds >= $trueStart && $ds <= $today)
                    @php
                        $lvl     = $levels[$ds] ?? 0;
                        $shade   = $shades[$lvl] ?? 'bg-gray-100';
                        $isToday = $ds === $today;
                        $tip     = $labels[$ds] ?? $ds;
                    @endphp
                    <div class="aspect-square rounded-sm {{ $shade }} {{ $isToday ? 'ring-1 ring-gray-900 ring-offset-1' : '' }}"
                         title="{{ $tip }}"></div>
                @else
                    <div class="aspect-square rounded-sm bg-transparent"></div>
                @endif
            @endforeach
        @endfor
    </div>

    {{-- Legend --}}
    <div class="flex items-center justify-end gap-1.5 mt-3">
        <span class="text-[9px] text-gray-400">{{ __('Sedikit') }}</span>
        <div class="w-3 h-3 rounded-sm bg-gray-100"></div>
        <div class="w-3 h-3 rounded-sm bg-{{ $color }}-200"></div>
        <div class="w-3 h-3 rounded-sm bg-{{ $color }}-300"></div>
        <div class="w-3 h-3 rounded-sm bg-{{ $color }}-400"></div>
        <div class="w-3 h-3 rounded-sm bg-{{ $color }}-500"></div>
        <span class="text-[9px] text-gray-400">{{ __('Banyak') }}</span>
    </div>
</div>
