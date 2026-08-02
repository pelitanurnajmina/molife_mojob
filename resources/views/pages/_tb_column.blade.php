{{-- Satu kolom hari untuk grid time blocking. Param: $date, $data (timed/prayers/allday), $today --}}
@php
    $colorMap = [
        'blue'   => 'bg-blue-50 border-blue-200 text-blue-800 hover:border-blue-400',
        'green'  => 'bg-green-50 border-green-200 text-green-800 hover:border-green-400',
        'violet' => 'bg-violet-50 border-violet-200 text-violet-800 hover:border-violet-400',
        'amber'  => 'bg-amber-50 border-amber-200 text-amber-800 hover:border-amber-400',
        'rose'   => 'bg-rose-50 border-rose-200 text-rose-800 hover:border-rose-400',
        'gray'   => 'bg-gray-100 border-gray-300 text-gray-700 hover:border-gray-400',
    ];
    $fmt = fn($m) => sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
@endphp
<div class="tb-col relative border-l border-gray-100 {{ $date === $today ? 'bg-amber-50/20' : '' }}"
     data-date="{{ $date }}" style="height:1152px" onclick="tbSlotClick(event, this)">

    {{-- Garis jam --}}
    @for($h = 1; $h < 24; $h++)
    <div class="absolute left-0 right-0 border-t border-gray-50 pointer-events-none" style="top:{{ $h * 48 }}px"></div>
    @endfor

    {{-- Jadwal sholat (read-only, di belakang) --}}
    @foreach($data['prayers'] as $p)
    <div class="absolute left-0 right-0 px-1.5 flex items-center pointer-events-none z-0"
         style="top:{{ $p['startMin'] / 1440 * 100 }}%; height:{{ ($p['endMin'] - $p['startMin']) / 1440 * 100 }}%">
        <div class="w-full h-full rounded-md bg-emerald-50/70 border border-dashed border-emerald-200 flex items-center gap-1 px-1.5 overflow-hidden">
            <svg class="w-2.5 h-2.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m2.828 9.9a5 5 0 117.072 0"/></svg>
            <span class="text-[9px] font-bold text-emerald-700 truncate">{{ $p['title'] }} · {{ $p['timeLabel'] }}</span>
        </div>
    </div>
    @endforeach

    {{-- Blok manual (bisa diedit) --}}
    @foreach($data['timed'] as $e)
    @php
        // Tumpuk tipis: blok tetap berdampingan (kebaca) tapi saling menimpa sedikit,
        // yang lebih baru di atas + bayangan. Lebar dihitung agar total pas 100% dengan
        // overlap kecil, jadi teks tidak bertabrakan seperti cascade penuh.
        $lane  = $e['lane'];
        $count = $e['laneCount'];
        if ($count <= 1) {
            $left = 0; $width = 100;
        } else {
            $overlap = $count <= 2 ? 12 : ($count === 3 ? 10 : 8); // % saling timpa
            $width   = (100 + ($count - 1) * $overlap) / $count;
            $left    = $lane * ($width - $overlap);
        }
        $cls   = $colorMap[$e['color']] ?? $colorMap['blue'];
        $short = ($e['endMin'] - $e['startMin']) < 45;
        $zi    = 10 + $lane;
        $shadow = $lane > 0 ? 'shadow-md' : '';
    @endphp
    <div class="tb-block absolute rounded-lg border px-1.5 py-1 cursor-pointer overflow-hidden transition-all {{ $cls }} {{ $shadow }}"
         style="top:{{ $e['startMin'] / 1440 * 100 }}%; height:{{ ($e['endMin'] - $e['startMin']) / 1440 * 100 }}%; left:calc({{ $left }}% + 2px); width:calc({{ $width }}% - 4px); z-index:{{ $zi }}"
         data-tip-title="{{ $e['title'] }}"
         data-tip-time="{{ $fmt($e['startMin']) }}–{{ $fmt($e['endMin']) }}"
         data-tip-note="{{ $e['note'] }}"
         onmouseenter="tbShowTip(this)" onmouseleave="tbHideTip()"
         onclick="event.stopPropagation(); tbEditBlock({{ Illuminate\Support\Js::from([
            'id' => $e['id'], 'title' => $e['title'], 'note' => $e['note'], 'color' => $e['color'],
            'date' => $date, 'start_min' => $e['startMin'], 'end_min' => $e['endMin'],
         ]) }})">
        @if($short)
        {{-- Blok pendek: judul + jam mulai satu baris; judul truncate, jam tetap tampil --}}
        <div class="flex items-baseline gap-1">
            <span class="text-[10px] font-bold leading-tight truncate flex-1 min-w-0">{{ $e['title'] }}</span>
            <span class="text-[9px] opacity-70 leading-tight flex-shrink-0">{{ $fmt($e['startMin']) }}</span>
        </div>
        @else
        <p class="text-[10px] font-bold leading-tight truncate">{{ $e['title'] }}</p>
        <p class="text-[9px] opacity-70 leading-tight">{{ $fmt($e['startMin']) }}–{{ $fmt($e['endMin']) }}</p>
        @endif
    </div>
    @endforeach

    {{-- Garis "sekarang" (hanya hari ini, digambar via JS) --}}
    @if($date === $today)
    <div class="tb-now absolute left-0 right-0 z-20 pointer-events-none" style="top:0">
        <div class="relative">
            <div class="absolute -left-1 -top-1 w-2 h-2 rounded-full bg-red-500"></div>
            <div class="border-t border-red-500"></div>
        </div>
    </div>
    @endif
</div>
