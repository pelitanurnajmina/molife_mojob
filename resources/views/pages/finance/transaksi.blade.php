@extends('layouts.app')
@section('title', __('Transaksi'))
@section('page-title', __('Transaksi'))
@section('breadcrumb', __('Finance › Transaksi'))

@section('content')
@php $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.'); @endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Plan limit banner (Freemium) ── --}}
    @if($isFreemium && $daysLimit)
    <x-upgrade-banner
        title="{{ $hiddenCount > 0
            ? __(':n transaksi disembunyikan', ['n' => $hiddenCount])
            : __('Riwayat dibatasi :n hari terakhir', ['n' => $daysLimit]) }}"
        message="{{ __('Tanpa langganan aktif, riwayat hanya :n hari terakhir. Aktifkan langganan untuk tanpa batas.', ['n' => $daysLimit]) }}"
        cta="{{ __('Aktifkan Langganan') }}"
        variant="{{ $hiddenCount > 0 ? 'warning' : 'soft' }}" />
    @endif

    {{-- ── Month picker + Balance ── --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="bg-white rounded-2xl p-4 flex-1 flex items-center gap-4">
            <form method="GET" action="{{ route('finance.transaksi') }}" style="display:inline-flex">
                <div class="relative">
                    <input type="month" name="month" value="{{ $monthKey }}" max="{{ date('Y-m') }}"
                        onchange="this.form.submit()"
                        class="px-3 py-2 pr-9 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-green-400 transition-all">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-2xl p-4 flex items-center justify-around gap-4 flex-1 sm:flex-none sm:w-auto">
            <div class="text-center">
                <p class="text-xs text-gray-400 font-bold">{{ __('Pemasukan') }}</p>
                <p class="text-sm font-bold text-green-600">+{{ $rp($summary['income']) }}</p>
            </div>
            <div class="w-px h-8 bg-gray-100"></div>
            <div class="text-center">
                <p class="text-xs text-gray-400 font-bold">{{ __('Pengeluaran') }}</p>
                <p class="text-sm font-bold text-red-500">-{{ $rp($summary['expense']) }}</p>
            </div>
            <div class="w-px h-8 bg-gray-100"></div>
            <div class="text-center">
                <p class="text-xs text-gray-400 font-bold">{{ __('Saldo') }}</p>
                <p class="text-sm font-bold {{ $summary['balance'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $rp($summary['balance']) }}</p>
            </div>
        </div>
    </div>

    {{-- ── Add Transaction ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-5">{{ __('Tambah Transaksi') }}</h3>
        <form method="POST" action="{{ route('finance.transaksi.add') }}" id="addTxForm">
            @csrf
            {{-- Type toggle --}}
            <div class="flex gap-2 mb-4">
                <button type="button" onclick="setTxType('income')" id="btnIncome"
                    class="flex-1 py-2.5 rounded-xl border-2 font-bold text-sm transition-all border-green-500 bg-green-500 text-white">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    {{ __('Pemasukan') }}
                </button>
                <button type="button" onclick="setTxType('expense')" id="btnExpense"
                    class="flex-1 py-2.5 rounded-xl border-2 font-bold text-sm transition-all border-gray-200 bg-gray-50 text-gray-500">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    {{ __('Pengeluaran') }}
                </button>
            </div>
            <input type="hidden" name="type" id="txType" value="income">

            <div class="grid grid-cols-2 gap-3 mb-3">
                {{-- Category --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Kategori') }}</label>
                    <select name="category" id="txCategory"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-black transition-all">
                        <optgroup label="{{ __('Pemasukan') }}" id="incomeOpts">
                            @foreach($incomeCats as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                        </optgroup>
                        <optgroup label="{{ __('Pengeluaran') }}" id="expenseOpts" style="display:none">
                            @foreach($expenseCats as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                        </optgroup>
                    </select>
                </div>
                {{-- Date --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Tanggal') }}</label>
                    <div class="relative">
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2.5 pr-9 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-black transition-all">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                {{-- Amount --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Jumlah (Rp)') }}</label>
                    <input type="number" name="amount" min="1" placeholder="0"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold outline-none focus:border-black transition-all" required>
                </div>
                {{-- Note --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">{{ __('Catatan') }}</label>
                    <input type="text" name="note" placeholder="{{ __('Opsional...') }}" maxlength="200"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all">
                {{ __('Tambah Transaksi') }}
            </button>
        </form>
    </div>

    {{-- ── Transaction List ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4">{{ __('Riwayat') }} · {{ date('F Y', strtotime($monthKey . '-01')) }}</h3>

        @if(empty($transactions))
        <div class="text-center py-10">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-sm text-gray-400 font-medium">{{ __('Belum ada transaksi bulan ini') }}</p>
        </div>
        @else
        @php $lastDate = ''; @endphp
        <div class="space-y-1">
            @foreach($transactions as $tx)
            @if($tx['date'] !== $lastDate)
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pt-3 pb-1 first:pt-0">
                {{ date('l, j F', strtotime($tx['date'])) }}
            </p>
            @php $lastDate = $tx['date']; @endphp
            @endif
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-all group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $tx['type'] === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tx['type'] === 'income' ? 'M7 11l5-5m0 0l5 5m-5-5v12' : 'M17 13l-5 5m0 0l-5-5m5 5V6' }}"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">{{ $tx['category'] }}</p>
                    @if($tx['note'])<p class="text-xs text-gray-400 truncate">{{ $tx['note'] }}</p>@endif
                </div>
                <span class="text-sm font-bold flex-shrink-0 {{ $tx['type'] === 'income' ? 'text-green-600' : 'text-red-500' }}">
                    {{ $tx['type'] === 'income' ? '+' : '-' }}{{ $rp($tx['amount']) }}
                </span>
                <form method="POST" action="{{ route('finance.transaksi.delete', $tx['id']) }}" class="flex items-center flex-shrink-0 m-0 leading-none sm:opacity-0 sm:group-hover:opacity-100 transition-all">
                    @csrf @method('DELETE')
                    <button type="button" onclick="askDelete(this, '{{ __('Hapus transaksi ini?') }}')"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all">
                        <svg class="w-3.5 h-3.5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function setTxType(type) {
    document.getElementById('txType').value = type;
    const isIncome = type === 'income';
    document.getElementById('btnIncome').className  = 'flex-1 py-2.5 rounded-xl border-2 font-bold text-sm transition-all ' + (isIncome  ? 'border-green-500 bg-green-500 text-white' : 'border-gray-200 bg-gray-50 text-gray-500');
    document.getElementById('btnExpense').className = 'flex-1 py-2.5 rounded-xl border-2 font-bold text-sm transition-all ' + (!isIncome ? 'border-red-500 bg-red-500 text-white' : 'border-gray-200 bg-gray-50 text-gray-500');
    document.getElementById('incomeOpts').style.display  = isIncome  ? '' : 'none';
    document.getElementById('expenseOpts').style.display = !isIncome ? '' : 'none';
    const sel = document.getElementById('txCategory');
    sel.querySelectorAll('option').forEach(o => o.selected = false);
    const opts = (isIncome ? document.getElementById('incomeOpts') : document.getElementById('expenseOpts')).querySelectorAll('option');
    if (opts[0]) opts[0].selected = true;
}
</script>
@endpush
