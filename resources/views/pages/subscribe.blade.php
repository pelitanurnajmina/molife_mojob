<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Molife — {{ __('Aktifkan Langganan') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
@php
    $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $perMonth = ['1'=>'± Rp 11.000/bln','3'=>'± Rp 9.700/bln','6'=>'± Rp 8.200/bln','12'=>'± Rp 7.400/bln'];
    $badge    = ['3'=>'Paling Populer','6'=>'Hemat 26%','12'=>'Hemat 33%'];
@endphp
<body class="min-h-screen bg-gradient-to-br from-[#f4f1fb] via-[#fbf4f8] to-[#f0f5fc] flex items-center justify-center p-4">

<div class="w-full max-w-4xl">
    {{-- top bar --}}
    <div class="flex justify-end mb-4 px-1">
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" aria-label="{{ __('Tutup') }}" title="{{ __('Kembali ke login') }}"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:border-gray-300 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 grid md:grid-cols-2">

        {{-- ── Left: plans + explanation ── --}}
        <div class="p-6 md:p-8">
            <span class="text-[10px] font-bold uppercase tracking-widest text-violet-500">{{ __('Aktifkan molife') }}</span>
            <h1 class="text-2xl md:text-3xl font-black mt-2 tracking-tight">{{ __('Pilih paketmu') }}</h1>
            <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">{{ __('Bayar sekali, akses penuh semua modul. Makin panjang durasi, makin hemat.') }}</p>

            <div class="grid grid-cols-2 gap-3.5 mt-7">
                @foreach($plans as $key => $p)
                @php $pop = $key === '3'; @endphp
                <button type="button" id="card-{{ $key }}"
                    onclick="selectPlan('{{ $key }}',{{ $p['price'] }})"
                    class="plan-card relative text-left p-4 rounded-2xl border-2 transition-all {{ $pop ? 'border-gray-900' : 'border-gray-100 hover:border-gray-300' }}">
                    @if(!empty($badge[$key]))
                    <span class="absolute -top-2.5 left-4 whitespace-nowrap text-[8px] font-bold bg-gray-900 text-white px-2 py-0.5 rounded-full">{{ $badge[$key] }}</span>
                    @endif
                    <p class="text-[11px] font-bold text-gray-500">{{ $p['label'] }}</p>
                    <p class="text-xl font-black text-gray-900 leading-tight mt-2">{{ $rp($p['price']) }}</p>
                    <p class="text-[10px] text-gray-400 mt-1.5">{{ $perMonth[$key] ?? '' }}</p>
                </button>
                @endforeach
            </div>

            <div class="mt-6 pt-5 border-t border-gray-100">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-3">{{ __('Semua paket termasuk') }}</p>
                <ul class="space-y-2 text-xs">
                    @foreach(['Semua tracker Life: sholat, olahraga, pomodoro, mood, tugas','Career Hub, Bisnis & Keuangan lengkap','Statistik, insight & Life Score harian','Export data ke CSV'] as $f)
                    <li class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-gray-600">{{ $f }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- ── Right: QR + waiting ── --}}
        <div class="p-6 md:p-8 bg-gray-50 border-t md:border-t-0 md:border-l border-gray-100 flex flex-col items-center justify-center text-center">
            <p class="text-xs text-gray-400">{{ __('Total pembayaran') }}</p>
            <p id="amount" class="text-3xl font-black text-gray-900"></p>

            <div class="relative inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm mt-4">
                <span class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-gray-900 rounded-tl"></span>
                <span class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-gray-900 rounded-tr"></span>
                <span class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-gray-900 rounded-bl"></span>
                <span class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-gray-900 rounded-br"></span>
                <img id="qr" src="" alt="QRIS" width="200" height="200" class="block rounded-lg transition-opacity duration-300 opacity-0">
                <div id="qrLoading" class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-7 h-7 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3 max-w-[230px]">{{ __('Scan untuk membayar dari aplikasi apa pun yang mendukung QRIS.') }}</p>
            <p id="qrError" class="hidden text-xs font-bold text-red-500 mt-3 max-w-[230px]"></p>

            <div class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-gray-500">
                <svg class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                {{ __('Menunggu pembayaran...') }}
            </div>
            <div class="flex items-center justify-center gap-1.5 mt-3 text-[11px] text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ __('Pembayaran aman & terenkripsi') }}
            </div>
        </div>
    </div>
</div>

<script>
const STATUS_URL = '{{ route('subscription.status') }}';
const CHARGE_URL = '{{ route('subscription.charge') }}';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let _reqSeq = 0;

async function selectPlan(key, price) {
    document.getElementById('amount').textContent = 'Rp ' + price.toLocaleString('id-ID');
    document.querySelectorAll('.plan-card').forEach(c => { c.classList.remove('border-gray-900'); c.classList.add('border-gray-100'); });
    const card = document.getElementById('card-' + key);
    if (card) { card.classList.add('border-gray-900'); card.classList.remove('border-gray-100'); }

    const qr = document.getElementById('qr');
    qr.classList.add('opacity-0');
    document.getElementById('qrLoading').classList.remove('hidden');
    document.getElementById('qrError').classList.add('hidden');

    const seq = ++_reqSeq;
    try {
        const r = await fetch(CHARGE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ plan: key }),
        });
        const d = await r.json();
        if (seq !== _reqSeq) return; // a newer plan was picked
        if (!r.ok || !d.qr_url) { showError(d.error || '{{ __('Gagal membuat pembayaran. Coba lagi.') }}'); return; }
        qr.onload = () => { qr.classList.remove('opacity-0'); document.getElementById('qrLoading').classList.add('hidden'); };
        qr.src = d.qr_url;
    } catch (e) {
        if (seq === _reqSeq) showError('{{ __('Gagal terhubung. Coba lagi.') }}');
    }
}
function showError(msg) {
    document.getElementById('qrLoading').classList.add('hidden');
    const el = document.getElementById('qrError');
    el.textContent = msg; el.classList.remove('hidden');
}
selectPlan('3', {{ $plans['3']['price'] ?? 29000 }});

/* Auto-detect activation (Midtrans webhook flips status) and continue to dashboard */
setInterval(async () => {
    try {
        const r = await fetch(STATUS_URL, { headers: { 'Accept': 'application/json' } });
        const d = await r.json();
        if (d.active) window.location.href = '{{ route('dashboard') }}';
    } catch (e) {}
}, 5000);
</script>
</body>
</html>
