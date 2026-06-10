@extends('layouts.app')
@section('title', __('Anggaran'))
@section('page-title', __('Anggaran'))
@section('breadcrumb', __('Finance › Anggaran'))

@section('content')
@php $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.'); @endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Summary ── --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-xl font-bold text-gray-800">{{ $rp($totalBudget) }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Total Anggaran') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center">
            <p class="text-xl font-bold text-red-500">{{ $rp($totalSpent) }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Total Terpakai') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center">
            @php $sisa = $totalBudget - $totalSpent; @endphp
            <p class="text-xl font-bold {{ $sisa >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $rp($sisa) }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Sisa Anggaran') }}</p>
        </div>
    </div>

    {{-- ── Set Budget Form ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold">{{ __('Atur Anggaran') }}</h3>
            <form method="GET" action="{{ route('finance.anggaran') }}" style="display:inline-flex">
                <div class="relative">
                    <input type="month" name="month" value="{{ $monthKey }}" max="{{ date('Y-m') }}"
                        onchange="this.form.submit()"
                        class="px-3 py-2 pr-9 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-black transition-all">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </form>
        </div>

        @php
            // Fixed categories exclude the generic "Lainnya" — custom-named ones are handled below.
            $fixedCats  = array_values(array_filter($expenseCats, fn($c) => $c !== 'Lainnya'));
            $customCats = array_diff(array_keys($budget), $expenseCats); // saved custom names
        @endphp
        <form method="POST" action="{{ route('finance.anggaran.set') }}">
            @csrf
            <input type="hidden" name="month" value="{{ $monthKey }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                @foreach($fixedCats as $cat)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <span class="text-sm font-bold text-gray-700 flex-1 min-w-0">{{ $cat }}</span>
                    <input type="number" name="budgets[{{ $cat }}]" min="0" placeholder="0"
                        value="{{ $budget[$cat] ?? '' }}"
                        class="w-32 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold outline-none focus:border-black transition-all text-right">
                </div>
                @endforeach
            </div>

            {{-- Custom "Lainnya" categories (named manually) --}}
            <div class="border-t border-gray-100 pt-4 mb-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-bold text-gray-700">{{ __('Kategori Lainnya') }}</p>
                        <p class="text-[11px] text-gray-400">{{ __('Tulis sendiri nama kategorinya, mis. "Donasi", "Hobi", "Liburan".') }}</p>
                    </div>
                    <button type="button" onclick="addCustomCat()"
                        class="text-xs font-bold text-black bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg transition-all flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Tambah') }}
                    </button>
                </div>
                <div id="customCatList" class="space-y-2">
                    @foreach($customCats as $cat)
                    <div class="flex items-center gap-2 custom-cat-row">
                        <input type="text" name="custom_names[]" value="{{ $cat }}" maxlength="50"
                            placeholder="{{ __('Nama kategori...') }}"
                            class="flex-1 min-w-0 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold outline-none focus:border-black transition-all">
                        <input type="number" name="custom_amounts[]" min="0" placeholder="0" value="{{ $budget[$cat] }}"
                            class="w-28 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold outline-none focus:border-black transition-all text-right">
                        <button type="button" onclick="this.closest('.custom-cat-row').remove()"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Simpan Anggaran') }}
            </button>
        </form>
    </div>

    {{-- ── Progress per Category ── --}}
    @if(!empty($budget))
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-5">{{ __('Progres Pengeluaran') }} · {{ date('F Y', strtotime($monthKey . '-01')) }}</h3>
        <div class="space-y-4">
            @foreach($budget as $cat => $limit)
            @php $spent = $spentByCategory[$cat] ?? 0; $pct = $limit > 0 ? min(100, round(($spent/$limit)*100)) : 0; @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-bold text-gray-800">{{ $cat }}</span>
                    <div class="text-right">
                        <span class="text-xs font-bold {{ $pct >= 90 ? 'text-red-500' : 'text-gray-600' }}">{{ $rp($spent) }}</span>
                        <span class="text-xs text-gray-400"> / {{ $rp($limit) }}</span>
                    </div>
                </div>
                <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-400' : 'bg-green-500') }}" style="width:{{ $pct }}%"></div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-[10px] text-gray-400">{{ $pct }}% {{ __('terpakai') }}</span>
                    @if($pct >= 90)<span class="text-[10px] text-red-500 font-bold">{{ __('Hampir habis!') }}</span>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
function addCustomCat() {
    const list = document.getElementById('customCatList');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 custom-cat-row';
    row.innerHTML = `
        <input type="text" name="custom_names[]" maxlength="50" placeholder="{{ __('Nama kategori...') }}"
            class="flex-1 min-w-0 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold outline-none focus:border-black transition-all">
        <input type="number" name="custom_amounts[]" min="0" placeholder="0"
            class="w-28 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold outline-none focus:border-black transition-all text-right">
        <button type="button" onclick="this.closest('.custom-cat-row').remove()"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    list.appendChild(row);
    row.querySelector('input').focus();
}
</script>
@endpush
@endsection
