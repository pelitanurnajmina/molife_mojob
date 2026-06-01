@extends('layouts.app')
@section('title', __('Tabungan'))
@section('page-title', __('Tabungan'))
@section('breadcrumb', __('Finance › Tabungan'))

@section('content')
@php
    $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $colorOptions = ['emerald','blue','violet','orange','pink','indigo'];
    $colorMap = [
        'emerald' => ['bar'=>'bg-emerald-500','badge'=>'bg-emerald-50 text-emerald-700','ring'=>'ring-emerald-400'],
        'blue'    => ['bar'=>'bg-blue-500',   'badge'=>'bg-blue-50 text-blue-700',      'ring'=>'ring-blue-400'],
        'violet'  => ['bar'=>'bg-violet-500', 'badge'=>'bg-violet-50 text-violet-700',  'ring'=>'ring-violet-400'],
        'orange'  => ['bar'=>'bg-orange-400', 'badge'=>'bg-orange-50 text-orange-700',  'ring'=>'ring-orange-400'],
        'pink'    => ['bar'=>'bg-pink-500',   'badge'=>'bg-pink-50 text-pink-700',      'ring'=>'ring-pink-400'],
        'indigo'  => ['bar'=>'bg-indigo-500', 'badge'=>'bg-indigo-50 text-indigo-700',  'ring'=>'ring-indigo-400'],
    ];
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Goals list ── --}}
    @if(empty($goals))
    <div class="bg-white rounded-2xl md:rounded-3xl p-8 text-center">
        <div class="w-16 h-16 bg-emerald-50 text-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <p class="font-bold text-gray-700 mb-1">{{ __('Belum ada tujuan tabungan') }}</p>
        <p class="text-sm text-gray-400">{{ __('Buat tujuan pertamamu di bawah!') }}</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($goals as $goal)
        @php
            $pct  = $goal['target'] > 0 ? min(100, round(($goal['current'] / $goal['target']) * 100)) : 0;
            $cm   = $colorMap[$goal['color'] ?? 'emerald'] ?? $colorMap['emerald'];
            $sisa = $goal['target'] - $goal['current'];
        @endphp
        <div class="bg-white rounded-2xl p-5 flex flex-col gap-3">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-bold text-gray-900">{{ $goal['name'] }}</p>
                    @if($goal['deadline'])
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ __('Target') }}: {{ date('j F Y', strtotime($goal['deadline'])) }}</p>
                    @endif
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $cm['badge'] }}">{{ $pct }}%</span>
            </div>

            <div>
                <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden mb-2">
                    <div class="{{ $cm['bar'] }} h-full rounded-full transition-all" style="width:{{ $pct }}%"></div>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="font-bold text-gray-700">{{ $rp($goal['current']) }}</span>
                    <span class="text-gray-400">{{ $rp($goal['target']) }}</span>
                </div>
            </div>

            @if($pct < 100)
            <p class="text-xs text-gray-400">{{ __('Kurang') }} <span class="font-bold text-gray-700">{{ $rp($sisa) }}</span> {{ __('lagi') }}</p>
            @else
            <p class="text-xs font-bold text-emerald-600">{{ __('Tujuan tercapai!') }}</p>
            @endif

            <div class="flex gap-2 pt-1">
                <button type="button" onclick="openEditGoal({{ json_encode($goal) }})"
                    class="flex-1 py-2 rounded-xl bg-gray-50 text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all">{{ __('Edit') }}</button>
                <form method="POST" action="{{ route('finance.tabungan.delete', $goal['id']) }}" class="flex-shrink-0">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('{{ __('Hapus tujuan ini?') }}')"
                        class="px-3 py-2 rounded-xl text-xs font-bold text-red-400 hover:bg-red-50 transition-all">{{ __('Hapus') }}</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Add / Edit Goal Form ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8" id="goalFormCard">
        <h3 class="font-bold mb-5" id="goalFormTitle">{{ __('Tambah Tujuan Tabungan') }}</h3>
        <form method="POST" action="{{ route('finance.tabungan.save') }}" id="goalForm">
            @csrf
            <input type="hidden" name="id" id="goalId">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Nama Tujuan') }} *</label>
                    <input type="text" name="name" id="goalName" required placeholder="{{ __('cth: Beli Motor, Dana Darurat...') }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-black transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Target (Rp)') }} *</label>
                    <input type="number" name="target" id="goalTarget" required min="1" placeholder="10000000"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-black transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Sudah Terkumpul (Rp)') }}</label>
                    <input type="number" name="current" id="goalCurrent" min="0" placeholder="0"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-black transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Deadline') }}</label>
                    <div class="relative">
                        <input type="date" name="deadline" id="goalDeadline"
                            class="w-full px-4 py-3 pr-10 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-black transition-all">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-400 mb-2">{{ __('Warna') }}</label>
                <div class="flex gap-2">
                    @foreach($colorOptions as $c)
                    @php $b = ['emerald'=>'#10b981','blue'=>'#3b82f6','violet'=>'#8b5cf6','orange'=>'#fb923c','pink'=>'#ec4899','indigo'=>'#6366f1'][$c]; @endphp
                    <button type="button" onclick="selectColor('{{ $c }}')" id="colorBtn{{ $c }}"
                        class="w-8 h-8 rounded-full border-2 border-transparent transition-all"
                        style="background:{{ $b }}"></button>
                    @endforeach
                </div>
                <input type="hidden" name="color" id="goalColor" value="emerald">
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="resetGoalForm()" class="flex-1 py-3 bg-gray-100 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">{{ __('Batal') }}</button>
                <button type="submit" class="flex-[2] py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function selectColor(c) {
    document.getElementById('goalColor').value = c;
    document.querySelectorAll('[id^="colorBtn"]').forEach(b => {
        b.style.borderColor = 'transparent'; b.style.transform = '';
    });
    const btn = document.getElementById('colorBtn' + c);
    btn.style.borderColor = '#111'; btn.style.transform = 'scale(1.2)';
}
selectColor('emerald');

function openEditGoal(goal) {
    document.getElementById('goalId').value      = goal.id || '';
    document.getElementById('goalName').value    = goal.name || '';
    document.getElementById('goalTarget').value  = goal.target || '';
    document.getElementById('goalCurrent').value = goal.current || 0;
    document.getElementById('goalDeadline').value= goal.deadline || '';
    selectColor(goal.color || 'emerald');
    document.getElementById('goalFormTitle').textContent = '{{ __("Edit Tujuan") }}';
    document.getElementById('goalFormCard').scrollIntoView({ behavior: 'smooth' });
}

function resetGoalForm() {
    document.getElementById('goalId').value      = '';
    document.getElementById('goalName').value    = '';
    document.getElementById('goalTarget').value  = '';
    document.getElementById('goalCurrent').value = '';
    document.getElementById('goalDeadline').value= '';
    selectColor('emerald');
    document.getElementById('goalFormTitle').textContent = '{{ __("Tambah Tujuan Tabungan") }}';
}
</script>
@endpush
