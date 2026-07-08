<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" style="background-color:#f4f1fb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Molife — {{ __('Aktifkan Langganan') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=6">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; } @view-transition { navigation: auto; } ::view-transition-old(root),::view-transition-new(root){animation-duration:.18s}</style>
</head>
@php
    $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $perMonth = ['1'=>'± Rp 11.000/bln','3'=>'± Rp 9.700/bln','6'=>'± Rp 8.200/bln','12'=>'± Rp 7.400/bln'];
    $badge    = ['3'=>'Paling Populer','6'=>'Hemat 26%','12'=>'Hemat 33%'];
@endphp
<body class="min-h-screen bg-gradient-to-br from-[#f4f1fb] via-[#fbf4f8] to-[#f0f5fc] flex items-center justify-center p-4">

<div class="w-full max-w-lg">
    {{-- Akses kolaborasi (tanpa langganan) untuk user yang diundang --}}
    @if(\App\Services\CollabService::hasAny(auth()->id()))
    <a href="{{ route('kolaborasi.index') }}"
       class="flex items-center gap-3 bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-4 hover:border-gray-400 transition-all">
        <div class="w-9 h-9 rounded-xl bg-gray-900 text-white flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900">{{ __('Kamu diundang berkolaborasi') }}</p>
            <p class="text-[11px] text-gray-400">{{ __('Buka workspace kolaborasi tanpa perlu langganan.') }}</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    </a>
    @endif

    {{-- top bar --}}
    <div class="flex justify-end mb-4 px-1">
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" aria-label="{{ __('Tutup') }}" title="{{ __('Kembali ke login') }}"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:border-gray-300 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </form>
    </div>

    {{-- ════ STEP 1: pilih paket ════ --}}
    <div id="stepPlans" class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-6 md:p-8">
        <span class="text-[10px] font-bold uppercase tracking-widest text-violet-500">{{ __('Aktifkan Molife') }}</span>
        <h1 class="text-2xl md:text-3xl font-black mt-2 tracking-tight">{{ __('Pilih paketmu') }}</h1>
        <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">{{ __('Bayar sekali, akses penuh semua modul. Makin panjang durasi, makin hemat.') }}</p>

        <div class="grid grid-cols-2 gap-3.5 mt-7">
            @foreach($plans as $key => $p)
            @php $pop = $key === '3'; @endphp
            <button type="button" id="card-{{ $key }}"
                onclick="choosePlan('{{ $key }}', {{ $p['price'] }}, '{{ $p['label'] }}')"
                class="plan-card relative text-left p-4 rounded-2xl border-2 transition-all {{ $pop ? 'border-gray-900' : 'border-gray-100 hover:border-gray-300' }}">
                @if(!empty($badge[$key]))
                <span class="absolute -top-2.5 left-4 whitespace-nowrap text-[8px] font-bold bg-gray-900 text-white px-2 py-0.5 rounded-full">{{ $badge[$key] }}</span>
                @endif
                <p class="text-[11px] font-bold text-gray-500">{{ $p['label'] }}</p>
                <p class="text-xl font-black text-gray-900 leading-tight mt-2">{{ $rp($p['price']) }}</p>
                <p class="text-[10px] text-gray-400 mt-1.5">{{ $perMonth[$key] ?? '' }}</p>
                @if(in_array($key, \App\Services\SubscriptionService::PREMIUM_PLANS, true))
                <p class="text-[9px] font-bold text-violet-600 mt-1">+ {{ __('Fitur AI') }}</p>
                @endif
            </button>
            @endforeach
        </div>

        <div class="mt-5 rounded-2xl bg-violet-50 border border-violet-100 p-3.5">
            <p class="text-[10px] font-bold text-violet-700 uppercase tracking-wide mb-1.5">{{ __('Eksklusif 6 Bulan & 1 Tahun') }}</p>
            <p class="text-[11px] text-violet-800 leading-relaxed">{{ __('Scan Struk AI (foto struk, transaksi terisi otomatis) dan rekomendasi Lowongan Kerja dari seluruh dunia.') }}</p>
        </div>

        <div class="mt-5 pt-5 border-t border-gray-100">
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

        <button type="button" id="btnConfirm" onclick="goToPayment()"
            class="w-full mt-6 py-3.5 rounded-xl bg-gray-900 text-white text-sm font-bold hover:bg-gray-800 transition-all">
            {{ __('Lanjut ke Pembayaran') }} · <span id="confirmLabel">3 Bulan</span>
        </button>
    </div>

    {{-- ════ STEP 2: pembayaran QRIS ════ --}}
    <div id="stepPay" class="hidden bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
        {{-- header --}}
        <div class="px-6 md:px-8 pt-6 pb-5 bg-gradient-to-br from-gray-900 to-gray-800 text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-36 h-36 rounded-full" style="background:radial-gradient(circle,rgba(124,92,240,.35),transparent 70%)"></div>
            <button type="button" onclick="backToPlans()"
                class="relative inline-flex items-center gap-1.5 text-[11px] font-bold text-white/70 hover:text-white transition-all mb-3">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                {{ __('Ganti paket') }}
            </button>
            <p class="relative text-[10px] font-bold uppercase tracking-widest text-white/50">{{ __('Pembayaran QRIS') }}</p>
            <p class="relative text-sm text-white/70 mt-2">Molife · <b id="payPlanLabel" class="text-white"></b></p>
            <p id="payAmount" class="relative text-3xl font-black mt-0.5"></p>
        </div>

        {{-- body --}}
        <div class="p-6 md:p-8 text-center">
            <div class="relative inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm">
                <span class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-gray-900 rounded-tl"></span>
                <span class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-gray-900 rounded-tr"></span>
                <span class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-gray-900 rounded-bl"></span>
                <span class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-gray-900 rounded-br"></span>
                <img id="qr" src="" alt="QRIS" width="210" height="210" class="block rounded-lg transition-opacity duration-300 opacity-0">
                <div id="qrLoading" class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-7 h-7 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-3 max-w-[250px] mx-auto">{{ __('Scan untuk membayar dari aplikasi apa pun yang mendukung QRIS.') }}</p>
            <p id="qrExpiry" class="hidden text-[11px] text-gray-400 mt-2"></p>
            <p id="payEnds" class="hidden text-[11px] font-bold text-emerald-600 mt-1"></p>

            <div id="qrErrorWrap" class="hidden mt-4">
                <p id="qrError" class="text-xs font-bold text-red-500"></p>
                <button type="button" onclick="createCharge()"
                    class="mt-3 px-5 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition-all">
                    {{ __('Coba Lagi') }}
                </button>
            </div>

            <div id="waitWrap" class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-gray-500">
                <svg class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                {{ __('Menunggu pembayaran...') }}
            </div>
            <div class="flex items-center justify-center gap-1.5 mt-3 text-[11px] text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ __('Pembayaran aman & terenkripsi · aktif otomatis setelah lunas') }}
            </div>
        </div>
    </div>
</div>

<script>
const STATUS_URL = '{{ route('subscription.status') }}';
const CHARGE_URL = '{{ route('subscription.charge') }}';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const PENDING    = @json($pendingCharge ?? null); // QR pending yang masih berlaku (dipakai ulang)

/* ── Step 1: pilih paket dulu ── */
let selectedPlan = { key: '3', price: {{ $plans['3']['price'] ?? 29000 }}, label: '3 Bulan' };

function choosePlan(key, price, label) {
    selectedPlan = { key, price, label };
    document.querySelectorAll('.plan-card').forEach(c => { c.classList.remove('border-gray-900'); c.classList.add('border-gray-100'); });
    const card = document.getElementById('card-' + key);
    card.classList.add('border-gray-900'); card.classList.remove('border-gray-100');
    document.getElementById('confirmLabel').textContent = label;
}

/* ── Pindah step (transisi halus bila didukung) ── */
function showStep(showId, hideId) {
    const doSwap = () => {
        document.getElementById(hideId).classList.add('hidden');
        document.getElementById(showId).classList.remove('hidden');
    };
    if (document.startViewTransition) document.startViewTransition(doSwap); else doSwap();
}

function goToPayment() {
    document.getElementById('payPlanLabel').textContent = selectedPlan.label;
    document.getElementById('payAmount').textContent = 'Rp ' + selectedPlan.price.toLocaleString('id-ID');
    showStep('stepPay', 'stepPlans');
    createCharge();
}

function backToPlans() {
    showStep('stepPlans', 'stepPay');
}

/* ── Render data pembayaran (dipakai untuk QR baru maupun QR pending yang di-reuse) ── */
function renderPayment(d) {
    document.getElementById('payPlanLabel').textContent = d.label;
    document.getElementById('payAmount').textContent = 'Rp ' + Number(d.amount).toLocaleString('id-ID');

    const qr = document.getElementById('qr');
    qr.onload = () => { qr.classList.remove('opacity-0'); document.getElementById('qrLoading').classList.add('hidden'); };
    qr.src = d.qr_url;

    if (d.ends_at) {
        const ends = document.getElementById('payEnds');
        ends.textContent = '{{ __('Langgananmu akan aktif sampai') }} ' + d.ends_at;
        ends.classList.remove('hidden');
    }
    if (d.expires_at) {
        const exp = document.getElementById('qrExpiry');
        exp.textContent = '{{ __('QR berlaku sampai') }} ' + d.expires_at;
        exp.classList.remove('hidden');
    }

    // Saat QR kedaluwarsa, tawarkan buat QR baru otomatis.
    if (window.__qrExpTimer) clearTimeout(window.__qrExpTimer);
    if (d.expires_in > 0) {
        window.__qrExpTimer = setTimeout(() => {
            showChargeError('{{ __('QR sudah kedaluwarsa. Klik untuk membuat QR baru.') }}');
        }, d.expires_in * 1000);
    }
}

/* ── Step 2: buat/ambil tagihan QRIS (server pakai ulang QR pending bila masih berlaku) ── */
let chargeSeq = 0;
async function createCharge() {
    const seq = ++chargeSeq;
    const qr = document.getElementById('qr');
    qr.classList.add('opacity-0');
    document.getElementById('qrLoading').classList.remove('hidden');
    document.getElementById('qrErrorWrap').classList.add('hidden');
    document.getElementById('payEnds').classList.add('hidden');
    document.getElementById('qrExpiry').classList.add('hidden');

    try {
        const r = await fetch(CHARGE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ plan: selectedPlan.key }),
        });
        const d = await r.json();
        if (seq !== chargeSeq) return; // user sudah pindah/mengulang
        if (!r.ok || !d.qr_url) {
            showChargeError(d.error || '{{ __('Gagal membuat pembayaran. Coba lagi sebentar.') }}');
            return;
        }
        renderPayment(d);
    } catch (e) {
        if (seq === chargeSeq) showChargeError('{{ __('Gagal terhubung. Periksa koneksi lalu coba lagi.') }}');
    }
}

function showChargeError(msg) {
    document.getElementById('qrLoading').classList.add('hidden');
    document.getElementById('qrError').textContent = msg;
    document.getElementById('qrErrorWrap').classList.remove('hidden');
}

/* ── Boot: kalau ada QR pending yang masih berlaku, langsung tampilkan Step 2 ── */
if (PENDING && PENDING.qr_url) {
    selectedPlan = { key: PENDING.plan, price: PENDING.amount, label: PENDING.label };
    const cardEl = document.getElementById('card-' + PENDING.plan);
    if (cardEl) choosePlan(PENDING.plan, PENDING.amount, PENDING.label);
    document.getElementById('stepPlans').classList.add('hidden');
    document.getElementById('stepPay').classList.remove('hidden');
    renderPayment(PENDING);
}

/* ── Deteksi otomatis pembayaran lunas (via webhook Midtrans) ── */
if (window.__subPoll) clearInterval(window.__subPoll);
window.__subPoll = setInterval(async () => {
    try {
        const r = await fetch(STATUS_URL, { headers: { 'Accept': 'application/json' } });
        const d = await r.json();
        if (d.active) window.location.href = '{{ route('dashboard') }}';
    } catch (e) {}
}, 5000);
</script>
</body>
</html>
