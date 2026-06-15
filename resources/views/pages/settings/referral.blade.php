@extends('layouts.app')
@section('title', __('Referral'))
@section('page-title', __('Settings'))
@section('breadcrumb', 'Settings › Referral')

@section('content')
@php $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.'); @endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Hero ── --}}
    <div class="bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl md:rounded-3xl p-6 md:p-8 text-white relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -right-2 bottom-0 w-24 h-24 bg-white/10 rounded-full"></div>
        <div class="relative">
            <span class="inline-block text-[10px] font-bold bg-white/20 px-2.5 py-1 rounded-full mb-3">{{ __('Program Referral') }}</span>
            <h2 class="text-2xl md:text-3xl font-bold mb-1">{{ __('Ajak teman, dapat 20% komisi') }}</h2>
            <p class="text-sm text-white/80 max-w-lg">{{ __('Setiap teman yang kamu undang dan berlangganan, kamu dapat 20% dari pembayaran mereka — terus berulang selama mereka aktif.') }}</p>
        </div>
    </div>

    {{-- ── Stats ── --}}
    @php $canWithdraw = ($stats['earnings'] ?? 0) >= $payoutMin; @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-2xl font-bold">{{ $stats['invited'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Teman Diundang') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5">
            <p class="text-2xl font-bold text-violet-600">{{ $stats['converted'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Jadi Berlangganan') }}</p>
        </div>

        {{-- Saldo komisi + tombol cairkan (span 2) --}}
        <div class="col-span-2 bg-white rounded-2xl p-4 md:p-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-2xl font-bold text-green-600">{{ $rp($stats['earnings']) }}</p>
                <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Saldo Komisi') }}</p>
                @if(!$canWithdraw)
                <p class="text-[10px] text-gray-400 mt-0.5">{{ __('Min. :n untuk cairkan', ['n' => $rp($payoutMin)]) }}</p>
                @endif
            </div>
            @if($canWithdraw)
            <button type="button" onclick="openModal('modal-payout')"
                class="px-4 py-2.5 bg-green-500 text-white rounded-xl text-sm font-bold hover:bg-green-600 transition-all flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                {{ __('Cairkan') }}
            </button>
            @else
            <button type="button" disabled
                class="px-4 py-2.5 bg-gray-100 text-gray-400 rounded-xl text-sm font-bold cursor-not-allowed flex items-center gap-2 flex-shrink-0"
                title="{{ __('Saldo minimal :n', ['n' => $rp($payoutMin)]) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ __('Cairkan') }}
            </button>
            @endif
        </div>
    </div>

    {{-- ── Pending payout (if any) ── --}}
    @if($stats['pending'] > 0)
    <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 flex items-center gap-3">
        <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-orange-800">{{ $rp($stats['pending']) }} {{ __('sedang diproses') }}</p>
            <p class="text-xs text-orange-600">{{ __('Pencairan diproses dalam 3 hari kerja.') }}</p>
        </div>
    </div>
    @endif

    {{-- ── Share link ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-1">{{ __('Link Referral Kamu') }}</h3>
        <p class="text-xs text-gray-400 mb-5">{{ __('Bagikan link atau kode ini. Teman yang daftar lewat sini otomatis terhubung ke akunmu.') }}</p>

        {{-- Code --}}
        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Kode Referral') }}</label>
            <div class="flex items-center gap-2">
                <div class="flex-1 px-4 py-3 bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl font-bold text-lg tracking-widest text-center">{{ $code }}</div>
                <button type="button" onclick="copyRef('{{ $code }}', this)"
                    class="px-4 py-3 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all flex items-center gap-2 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    {{ __('Salin') }}
                </button>
            </div>
        </div>

        {{-- Link --}}
        <div class="mb-5">
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Link Undangan') }}</label>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ $link }}" id="refLink"
                    class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-600 outline-none">
                <button type="button" onclick="copyRef('{{ $link }}', this)"
                    class="px-4 py-3 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all flex items-center gap-2 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    {{ __('Salin') }}
                </button>
            </div>
        </div>

        {{-- Share buttons --}}
        <div class="flex flex-wrap gap-2">
            @php
                $shareText = rawurlencode(__('Yuk pakai Molife buat track hidup & karir kamu! Daftar lewat link aku:') . ' ' . $link);
            @endphp
            <a href="https://wa.me/?text={{ $shareText }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 bg-green-50 text-green-700 rounded-xl text-sm font-bold hover:bg-green-100 transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.263.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.886 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413"/></svg>
                WhatsApp
            </a>
            <a href="https://t.me/share/url?url={{ rawurlencode($link) }}&text={{ rawurlencode(__('Yuk pakai Molife!')) }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-700 rounded-xl text-sm font-bold hover:bg-blue-100 transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.139-5.061 3.345-.479.329-.913.489-1.302.481-.428-.009-1.252-.242-1.865-.44-.752-.244-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                Telegram
            </a>
            <button type="button" onclick="copyRef('{{ $link }}', this)"
                class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                {{ __('Bagikan') }}
            </button>
        </div>
    </div>

    {{-- ── How it works ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-5">{{ __('Cara Kerja') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
            $steps = [
                ['n'=>'1', 'title'=>__('Bagikan link'),     'desc'=>__('Kirim link atau kode referral ke teman, keluarga, atau followers kamu.'), 'icon'=>'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z'],
                ['n'=>'2', 'title'=>__('Teman berlangganan'),'desc'=>__('Mereka daftar lewat link kamu lalu upgrade ke Plus atau Pro.'),                'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['n'=>'3', 'title'=>__('Kamu dapat 20%'),    'desc'=>__('Komisi 20% masuk ke saldo kamu setiap mereka membayar — berulang.'),        'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            @endphp
            @foreach($steps as $s)
            <div class="p-4 bg-gray-50 rounded-2xl">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-orange-500 text-white rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $s['n'] }}</div>
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/></svg>
                    </div>
                </div>
                <p class="font-bold text-sm mb-1">{{ $s['title'] }}</p>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Payout note ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 flex items-start gap-3">
        <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-800">{{ __('Tentang Pencairan') }}</p>
            <p class="text-xs text-gray-500 leading-relaxed mt-0.5">{{ __('Komisi bisa dicairkan setelah mencapai minimal Rp 50.000 via transfer bank atau e-wallet. Pencairan diproses dalam 3 hari kerja.') }}</p>
        </div>
    </div>

    {{-- ── Riwayat pencairan ── --}}
    @if(count($payouts) > 0)
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4">{{ __('Riwayat Pencairan') }}</h3>
        <div class="space-y-2">
            @foreach($payouts as $p)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $p['status'] === 'done' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-500' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $p['status'] === 'done' ? 'M5 13l4 4L19 7' : 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">{{ $rp($p['amount']) }}</p>
                    <p class="text-[10px] text-gray-400">{{ strtoupper($p['method']) }} · {{ $p['account'] }} · {{ $p['date'] }}</p>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full flex-shrink-0 {{ $p['status'] === 'done' ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600' }}">
                    {{ $p['status'] === 'done' ? __('Selesai') : __('Diproses') }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ── Modal: Cairkan Komisi ── --}}
<div id="modal-payout" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-payout')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg">{{ __('Cairkan Komisi') }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Saldo tersedia:') }} <span class="font-bold text-green-600">{{ $rp($stats['earnings']) }}</span></p>
            </div>
            <button type="button" onclick="closeModal('modal-payout')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('settings.referral.payout') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-2">{{ __('Metode Pencairan') }}</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-3 rounded-xl border-2 border-gray-100 cursor-pointer has-[:checked]:border-green-500 has-[:checked]:bg-green-50/50 transition-all">
                        <input type="radio" name="method" value="bank" checked class="accent-green-500">
                        <span class="text-sm font-bold">{{ __('Transfer Bank') }}</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-xl border-2 border-gray-100 cursor-pointer has-[:checked]:border-green-500 has-[:checked]:bg-green-50/50 transition-all">
                        <input type="radio" name="method" value="ewallet" class="accent-green-500">
                        <span class="text-sm font-bold">E-Wallet</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Nomor Rekening / E-Wallet') }}</label>
                <input type="text" name="account" required placeholder="{{ __('cth: 1234567890 (BCA) atau 0812xxxx (GoPay)') }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Nama Pemilik') }}</label>
                <input type="text" name="name" required placeholder="{{ __('Nama sesuai rekening') }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-xs text-gray-500">
                {{ __('Jumlah dicairkan:') }} <span class="font-bold text-gray-800">{{ $rp($stats['earnings']) }}</span><br>
                {{ __('Diproses dalam 3 hari kerja.') }}
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('modal-payout')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">{{ __('Batal') }}</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-green-500 text-white text-sm font-bold hover:bg-green-600 transition-all">{{ __('Cairkan Sekarang') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function copyRef(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        var original = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> {{ __('Tersalin') }}';
        setTimeout(function(){ btn.innerHTML = original; }, 1500);
    });
}
</script>
@endpush
@endsection
