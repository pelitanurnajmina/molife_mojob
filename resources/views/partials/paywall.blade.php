{{-- Subscription paywall shown right after signup / after login when not subscribed --}}
@php
    $rpw = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $perMonth = ['1'=>'± 11rb/bln','3'=>'± 9,7rb/bln','6'=>'± 8,2rb/bln','12'=>'± 7,4rb/bln'];
    $badge    = ['3'=>'Populer','6'=>'Hemat 26%','12'=>'Hemat 33%'];
@endphp
<div id="paywall" class="fixed inset-0 z-[300] bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl overflow-hidden max-h-[94vh] flex flex-col">
        {{-- Header --}}
        <div class="relative px-6 pt-6 pb-5 bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 text-white text-center flex-shrink-0">
            <div class="absolute -right-10 -top-10 w-36 h-36 rounded-full" style="background:radial-gradient(circle,rgba(124,92,240,.4),transparent 70%)"></div>
            <div class="relative">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white/50">{{ __('Aktifkan molife') }}</span>
                <h2 class="text-2xl font-black mt-2">{{ __('Pilih paketmu') }}</h2>
                <p class="text-sm text-white/70 mt-1">{{ __('Satu kali bayar untuk akses penuh semua modul.') }}</p>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 overflow-y-auto">
            {{-- Plan options --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
                @foreach($plans as $key => $p)
                @php $pop = $key === '3'; @endphp
                <button type="button" id="pwCard-{{ $key }}"
                    onclick="selectPlan('{{ $key }}','{{ $p['label'] }}',{{ $p['price'] }})"
                    class="pw-card relative text-left p-3.5 rounded-2xl border-2 transition-all {{ $pop ? 'border-gray-900' : 'border-gray-100 hover:border-gray-300' }}">
                    @if(!empty($badge[$key]))
                    <span class="absolute -top-2 left-1/2 -translate-x-1/2 whitespace-nowrap text-[8px] font-bold bg-gray-900 text-white px-2 py-0.5 rounded-full">{{ $badge[$key] }}</span>
                    @endif
                    <p class="text-[11px] font-bold text-gray-500">{{ $p['label'] }}</p>
                    <p class="text-base font-black text-gray-900 leading-tight mt-1">{{ $rpw($p['price']) }}</p>
                    <p class="text-[9px] text-gray-400 mt-0.5">{{ $perMonth[$key] ?? '' }}</p>
                </button>
                @endforeach
            </div>

            {{-- QR --}}
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">{{ __('Total pembayaran') }}</p>
                <p id="pwAmount" class="text-2xl font-black text-gray-900"></p>
                <div class="relative inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm mt-3">
                    <span class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-gray-900 rounded-tl"></span>
                    <span class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-gray-900 rounded-tr"></span>
                    <span class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-gray-900 rounded-bl"></span>
                    <span class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-gray-900 rounded-br"></span>
                    <img id="pwQr" src="" alt="QRIS" width="190" height="190" class="block rounded-lg">
                </div>
                <p class="text-xs text-gray-400 mt-3">{{ __('Scan untuk membayar dari aplikasi apa pun yang mendukung QRIS.') }}</p>
            </div>

            <form method="POST" action="{{ route('subscription.confirm') }}" class="mt-5">
                @csrf
                <input type="hidden" name="plan" id="pwPlanKey">
                <button type="submit" class="w-full py-3.5 rounded-2xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Saya sudah bayar') }}
                </button>
            </form>
            <div class="flex items-center justify-center gap-1.5 mt-3 text-[11px] text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ __('Pembayaran aman & terenkripsi') }}
            </div>
            <button type="button" onclick="document.getElementById('paywall').remove(); document.body.style.overflow='';"
                class="block mx-auto mt-3 text-xs font-bold text-gray-400 hover:text-gray-700 transition-all">{{ __('Lewati untuk sekarang') }}</button>
        </div>
    </div>
</div>
<script>
(function () {
    document.body.style.overflow = 'hidden';
    window.selectPlan = function (key, label, price) {
        document.getElementById('pwPlanKey').value = key;
        document.getElementById('pwAmount').textContent = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('pwQr').src = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&margin=0&data=' + encodeURIComponent('MOLIFE-' + key + 'BLN-' + Date.now());
        document.querySelectorAll('.pw-card').forEach(c => {
            c.classList.remove('border-gray-900'); c.classList.add('border-gray-100');
        });
        const card = document.getElementById('pwCard-' + key);
        card.classList.add('border-gray-900'); card.classList.remove('border-gray-100');
    };
    selectPlan('3', '3 Bulan', {{ $plans['3']['price'] ?? 29000 }});
})();
</script>
