@props([
    'rows'      => [],
    'color'     => 'pink',          // tailwind base color for active cells
    'legendOff' => 'Tidak ada',
    'legendOn'  => 'Ada aktivitas',
])

<div class="space-y-4">
    @foreach($rows as $row)
    <div>
        <p class="text-xs font-bold text-gray-500 mb-1.5">{{ $row['label'] }}</p>
        <div class="grid gap-[3px]" style="grid-template-columns: repeat({{ count($row['cells']) }}, minmax(0,1fr));">
            @foreach($row['cells'] as $cell)
            <div class="rounded-sm {{ $cell['active'] ? 'bg-'.$color.'-500' : 'bg-gray-100' }}"
                 style="height:28px" title="{{ $cell['title'] }}"></div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<div class="flex items-center gap-4 text-xs text-gray-500 mt-5">
    <div class="flex items-center gap-2"><div class="w-4 h-4 bg-gray-100 rounded"></div><span>{{ $legendOff }}</span></div>
    <div class="flex items-center gap-2"><div class="w-4 h-4 bg-{{ $color }}-500 rounded"></div><span>{{ $legendOn }}</span></div>
</div>
