{{-- Chip prioritas tugas: ikon chevron berwarna ala papan kanban. Param: $p (low|normal|high|urgent) --}}
@php
    $prioMeta = [
        'urgent' => ['label' => __('Urgent'),  'cls' => 'bg-red-50 text-red-500',     'icon' => 'M5 11l7-7 7 7M5 19l7-7 7 7'],
        'high'   => ['label' => __('Tinggi'),  'cls' => 'bg-amber-50 text-amber-500', 'icon' => 'M5 15l7-7 7 7'],
        'normal' => ['label' => __('Normal'),  'cls' => 'bg-gray-50 text-gray-400',   'icon' => 'M5 9h14M5 15h14'],
        'low'    => ['label' => __('Rendah'),  'cls' => 'bg-blue-50 text-blue-500',   'icon' => 'M19 9l-7 7-7-7'],
    ];
    $pm = $prioMeta[$p ?? 'normal'] ?? $prioMeta['normal'];
@endphp
<span title="{{ __('Prioritas') }}: {{ $pm['label'] }}"
      class="inline-flex items-center justify-center w-[18px] h-[18px] rounded-md flex-shrink-0 {{ $pm['cls'] }}">
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $pm['icon'] }}"/></svg>
</span>
