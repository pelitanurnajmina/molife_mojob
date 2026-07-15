@extends('layouts.app')
@section('title', __('Langganan'))
@section('page-title', __('Settings'))
@section('breadcrumb', 'Settings › Langganan')

@section('content')
@php
    $rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $today = \Carbon\Carbon::today();
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Menunggu pembayaran: QR pending yang belum dibayar (mis. modal tak sengaja ditutup) ── --}}
    @if(!empty($pendingCharge))
    <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 md:p-5 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-amber-900">{{ __('Menunggu pembayaran') }} · {{ $pendingCharge['label'] }} · Rp {{ number_format($pendingCharge['amount'], 0, ',', '.') }}</p>
            <p class="text-xs text-amber-700/80 mt-0.5">{{ __('Kamu punya QR yang belum dibayar. QR berlaku sampai') }} {{ $pendingCharge['expires_at'] }}.</p>
        </div>
        <button type="button" onclick="openPay('{{ $pendingCharge['plan'] }}')"
            class="flex-shrink-0 px-4 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition-all">
            {{ __('Lanjutkan Pembayaran') }}
        </button>
    </div>
    @endif

    {{-- ── Current subscription ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ __('Langganan Sekarang') }}</h3>
                <p class="text-xs text-gray-400">{{ __('Status akses molife kamu') }}</p>
            </div>
        </div>

        @if($active)
        @php $left = max(0, $today->diffInDays($active->ends_at, false)); @endphp
        <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/10 rounded-full"></div>
            <div class="relative flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/60">{{ __('Paket Aktif') }}</span>
                    <p class="text-2xl font-black mt-1">molife · {{ $plans[$active->plan]['label'] ?? $active->plan }}</p>
                    <p class="text-sm text-white/80 mt-1">{{ __('Aktif sampai') }} {{ $active->ends_at->translatedFormat('j F Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-black">{{ $left }}</p>
                    <p class="text-xs text-white/70 font-bold">{{ __('hari tersisa') }}</p>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center gap-4 p-4 bg-gray-900 text-white rounded-2xl">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold">{{ __('Belum berlangganan') }}</p>
                <p class="text-xs text-gray-400">{{ __('Pilih paket di bawah untuk akses penuh semua modul') }}</p>
            </div>
            <span class="text-xs font-bold px-2.5 py-1 bg-white/10 rounded-full flex-shrink-0">{{ __('Free') }}</span>
        </div>
        @endif
    </div>

    {{-- ── Plans ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-1">{{ $active ? __('Perpanjang / Upgrade') : __('Bayar sekali. Pilih durasimu.') }}</h3>
        <p class="text-xs text-gray-400 mb-6">{{ __('Satu kali bayar untuk akses penuh semua modul. Makin panjang durasi, makin hemat.') }}</p>

        @php
            $perMonth = ['1'=>'± Rp 11.000/bln','3'=>'± Rp 9.700/bln','6'=>'± Rp 8.200/bln','12'=>'± Rp 7.400/bln'];
            $badge    = ['3'=>'Paling Populer','6'=>'Hemat 26%','12'=>'Hemat 33%'];
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($plans as $key => $p)
            @php
                // Kunci array numerik di-cast PHP jadi int, samakan tipe dulu.
                $pop       = (string) $key === '3';
                $isCurrent = $active && (string) $active->plan === (string) $key;
            @endphp
            <div class="relative p-4 md:p-5 rounded-2xl border-2 flex flex-col {{ $isCurrent ? 'border-gray-900 bg-gray-50/60' : ($pop ? 'border-gray-900 bg-gray-50/60' : 'border-gray-100 bg-white') }}">
                @if($isCurrent)
                <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 whitespace-nowrap text-[9px] font-bold bg-gray-900 text-white px-2.5 py-1 rounded-full">{{ __('Paket Kamu') }}</span>
                @elseif(!empty($badge[$key]))
                <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 whitespace-nowrap text-[9px] font-bold {{ $pop ? 'bg-gray-900' : 'bg-orange-500' }} text-white px-2.5 py-1 rounded-full">{{ $badge[$key] }}</span>
                @endif
                <p class="text-sm font-bold text-gray-700 mt-1">{{ $p['label'] }}</p>
                <p class="text-2xl md:text-[26px] font-black text-gray-900 leading-none mt-2">{{ $rp($p['price']) }}</p>
                <p class="text-[10px] text-gray-400 mt-1">{{ $perMonth[$key] ?? '' }}</p>
                @if(in_array((string) $key, \App\Services\SubscriptionService::PREMIUM_PLANS, true))
                <p class="text-[10px] font-bold text-violet-600 mt-1.5 inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    {{ __('Termasuk Fitur AI') }}
                </p>
                @endif
                @if($isCurrent)
                <div class="mt-4 py-2.5 text-center text-xs font-bold rounded-xl bg-gray-100 text-gray-500 inline-flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Sedang Aktif') }}
                </div>
                @else
                <button type="button"
                    onclick="openPay('{{ $key }}')"
                    class="mt-4 py-2.5 text-center text-xs font-bold rounded-xl transition-all {{ $pop ? 'bg-gray-900 text-white hover:bg-gray-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $active ? __('Perpanjang') : __('Pilih') }}
                </button>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 md:p-5 rounded-2xl bg-violet-50 border border-violet-100">
            <p class="text-xs font-bold text-violet-700 mb-2 inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                {{ __('Eksklusif paket 6 Bulan & 1 Tahun') }}
            </p>
            <ul class="space-y-1.5 text-xs text-violet-800">
                <li>{{ __('Scan Struk AI: foto struk belanja, transaksi terisi otomatis') }}</li>
                <li>{{ __('Lowongan Kerja: rekomendasi lowongan dari seluruh dunia sesuai target kariermu') }}</li>
            </ul>
        </div>

        <div class="mt-4 p-4 md:p-5 rounded-2xl bg-gray-50">
            <p class="text-xs font-bold text-gray-500 mb-3">{{ __('Semua paket termasuk:') }}</p>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                @foreach(['Semua tracker: sholat, olahraga, pomodoro, mood, tugas','Career Hub & pelacak lamaran','Bisnis: proposal, klien, dokumen & analitik','Keuangan: pemasukan, pengeluaran, anggaran, tabungan','Statistik 30 hari, insight & Life Score','Export data ke CSV'] as $f)
                <li class="flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-gray-700">{{ $f }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- ── History ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="font-bold mb-4">{{ __('Riwayat Langganan') }}</h3>
        @if(count($history) === 0)
        <div class="text-center py-8">
            <p class="text-sm text-gray-400">{{ __('Belum ada riwayat langganan.') }}</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-3 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Paket') }}</th>
                        <th class="text-left px-3 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 whitespace-nowrap">{{ __('Periode') }}</th>
                        <th class="text-right px-3 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Harga') }}</th>
                        <th class="text-left px-3 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $s)
                    @php
                        // Label mengikuti status pembayaran, bukan sekadar tanggal.
                        [$badgeClass, $badgeLabel] = match (true) {
                            $s->status === 'pending'                           => ['bg-amber-100 text-amber-700', __('Menunggu Pembayaran')],
                            $s->status === 'cancelled'                         => ['bg-gray-100 text-gray-500', __('Dibatalkan')],
                            $s->status === 'failed'                            => ['bg-red-100 text-red-600', __('Gagal')],
                            $s->status === 'active' && $s->ends_at->lt($today) => ['bg-gray-100 text-gray-500', __('Berakhir')],
                            $s->status === 'active'                            => ['bg-green-100 text-green-700', __('Aktif')],
                            default                                            => ['bg-gray-100 text-gray-500', ucfirst($s->status)],
                        };
                    @endphp
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="px-3 py-3.5 font-bold text-gray-800 whitespace-nowrap">molife · {{ $plans[$s->plan]['label'] ?? $s->plan }}</td>
                        <td class="px-3 py-3.5 text-gray-500 whitespace-nowrap">{{ $s->starts_at->translatedFormat('j M Y') }} – {{ $s->ends_at->translatedFormat('j M Y') }}</td>
                        <td class="px-3 py-3.5 text-right font-bold text-gray-700 whitespace-nowrap">{{ $rp($s->price) }}</td>
                        <td class="px-3 py-3.5">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

{{-- ── QRIS payment modal ── --}}
<div id="payModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" onclick="if(event.target===this)closePay()">
    <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl">
        {{-- Header --}}
        <div class="relative px-6 pt-6 pb-5 bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 text-white">
            <div class="absolute -right-10 -top-10 w-36 h-36 rounded-full" style="background:radial-gradient(circle,rgba(124,92,240,.35),transparent 70%)"></div>
            <button type="button" onclick="closePay()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="relative flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-white/50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM17 14h.01M14 17h.01M20 20h.01M20 14h.01M14 20h.01"/></svg>
                <span id="payKicker">{{ __('Pembayaran QRIS') }}</span>
            </div>
            <p class="relative text-sm text-white/70 mt-3">molife · <span id="payPlan" class="font-bold text-white"></span></p>
            <p id="payAmount" class="relative text-3xl font-black mt-0.5"></p>
        </div>

        {{-- Body --}}
        <div class="p-6">
            {{-- What you get: clear extend/new explanation --}}
            <div id="payInfo" class="mb-5 rounded-2xl bg-gray-50 border border-gray-100 p-4 text-xs leading-relaxed"></div>

            <div class="text-center">
                <div class="relative inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm">
                    <span class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-gray-900 rounded-tl"></span>
                    <span class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-gray-900 rounded-tr"></span>
                    <span class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-gray-900 rounded-bl"></span>
                    <span class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-gray-900 rounded-br"></span>
                    <img id="payQr" src="" alt="QRIS" width="200" height="200" class="block rounded-lg transition-opacity duration-300 opacity-0">
                    <div id="payQrLoading" class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-7 h-7 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3">{{ __('Bayar dari aplikasi apa pun yang mendukung QRIS.') }}</p>
                <p id="payExpiry" class="hidden text-[11px] text-gray-400 mt-1"></p>
                <p id="payError" class="hidden text-xs font-bold text-red-500 mt-3 max-w-[230px] mx-auto"></p>
                <button id="payRetry" type="button" onclick="openPay(window.__lastPayKey)"
                    class="hidden mt-3 px-5 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition-all">
                    {{ __('Coba Lagi') }}
                </button>
            </div>

            {{-- Waiting for payment --}}
            <div class="flex items-center justify-center gap-2 mt-5 text-sm font-bold text-gray-500">
                <svg class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/></svg>
                {{ __('Menunggu pembayaran...') }}
            </div>
            <div class="flex items-center justify-center gap-1.5 mt-3 text-[11px] text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ __('Pembayaran aman & terenkripsi · akses aktif otomatis setelah lunas') }}
            </div>
        </div>
    </div>
</div>

@php
    // Pre-compute each plan's resulting end date, mirroring SubscriptionController@confirm:
    // extend from current end (+1 day) if still active, else start today.
    $base = $active ? $active->ends_at->copy()->addDay() : \Carbon\Carbon::today();
    $planMeta = [];
    foreach ($plans as $key => $p) {
        $planMeta[$key] = [
            'label'  => $p['label'],
            'price'  => $p['price'],
            'months' => $p['months'],
            'newEnd' => $base->copy()->addMonths($p['months'])->translatedFormat('j F Y'),
        ];
    }
@endphp
@push('scripts')
<script>
const PLAN_META    = @json($planMeta);
const IS_RENEWAL   = {{ $active ? 'true' : 'false' }};
const CURRENT_END  = @json($active ? $active->ends_at->translatedFormat('j F Y') : null);
const CURRENT_ENDS = @json($active ? $active->ends_at->toDateString() : null);
const STATUS_URL   = '{{ route('subscription.status') }}';
const CHARGE_URL   = '{{ route('subscription.charge') }}';
const CSRF_TOKEN   = '{{ csrf_token() }}';

async function openPay(key) {
    const m = PLAN_META[key];
    if (!m) return;
    window.__lastPayKey = key;

    document.getElementById('payKicker').textContent = IS_RENEWAL ? '{{ __('Perpanjang Langganan') }}' : '{{ __('Aktifkan Langganan') }}';
    document.getElementById('payPlan').textContent   = m.label;
    document.getElementById('payAmount').textContent = 'Rp ' + m.price.toLocaleString('id-ID');

    // Clear, non-confusing explanation of exactly what this payment does.
    const info = document.getElementById('payInfo');
    if (IS_RENEWAL) {
        info.innerHTML =
            '<div class="flex items-start gap-2.5">' +
            '<svg class="w-4 h-4 text-gray-900 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
            '<div class="text-gray-600">' +
            '<p class="font-bold text-gray-900 mb-1">{{ __('Durasi ditambahkan, bukan diganti') }}</p>' +
            '<p>{{ __('Paketmu sekarang aktif sampai') }} <b class="text-gray-900">' + CURRENT_END + '</b>. {{ __('Tambahan') }} <b class="text-gray-900">' + m.label + '</b> {{ __('akan menyambung dari tanggal itu.') }}</p>' +
            '<div class="mt-2.5 flex items-center justify-between rounded-xl bg-white border border-gray-100 px-3 py-2">' +
            '<span class="text-[11px] text-gray-400 font-bold uppercase tracking-wide">{{ __('Aktif sampai jadi') }}</span>' +
            '<span class="text-sm font-black text-gray-900">' + m.newEnd + '</span></div>' +
            '</div></div>';
    } else {
        info.innerHTML =
            '<div class="flex items-start gap-2.5">' +
            '<svg class="w-4 h-4 text-gray-900 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
            '<div class="text-gray-600">' +
            '<p class="font-bold text-gray-900 mb-1">{{ __('Akses penuh semua modul') }}</p>' +
            '<p>{{ __('Sekali bayar untuk') }} <b class="text-gray-900">' + m.label + '</b>, {{ __('tanpa perpanjangan otomatis.') }}</p>' +
            '<div class="mt-2.5 flex items-center justify-between rounded-xl bg-white border border-gray-100 px-3 py-2">' +
            '<span class="text-[11px] text-gray-400 font-bold uppercase tracking-wide">{{ __('Aktif sampai') }}</span>' +
            '<span class="text-sm font-black text-gray-900">' + m.newEnd + '</span></div>' +
            '</div></div>';
    }

    // Show modal with a loading QR, then fetch the real Midtrans QRIS code.
    const qr = document.getElementById('payQr');
    qr.classList.add('opacity-0');
    document.getElementById('payQrLoading').classList.remove('hidden');
    document.getElementById('payError').classList.add('hidden');
    document.getElementById('payRetry').classList.add('hidden');
    document.getElementById('payExpiry').classList.add('hidden');
    document.getElementById('payModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    try {
        const r = await fetch(CHARGE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ plan: key }),
        });
        const d = await r.json();
        if (!r.ok || !d.qr_url) {
            showPayError(d.error || '{{ __('Gagal membuat pembayaran. Coba lagi.') }}', true);
            return;
        }
        qr.onload = () => { qr.classList.remove('opacity-0'); document.getElementById('payQrLoading').classList.add('hidden'); };
        qr.src = d.qr_url;

        // Masa berlaku QR (server pakai ulang QR pending yang sama selama belum kedaluwarsa).
        if (d.expires_at) {
            const exp = document.getElementById('payExpiry');
            exp.textContent = '{{ __('QR berlaku sampai') }} ' + d.expires_at;
            exp.classList.remove('hidden');
        }
        if (window.__payExpTimer) clearTimeout(window.__payExpTimer);
        if (d.expires_in > 0) {
            window.__payExpTimer = setTimeout(() => {
                showPayError('{{ __('QR sudah kedaluwarsa. Klik Coba Lagi untuk membuat QR baru.') }}', true);
            }, d.expires_in * 1000);
        }

        startPolling();
    } catch (e) {
        showPayError('{{ __('Gagal terhubung. Periksa koneksi lalu coba lagi.') }}', true);
    }
}

function showPayError(msg, withRetry) {
    document.getElementById('payQrLoading').classList.add('hidden');
    const el = document.getElementById('payError');
    el.textContent = msg;
    el.classList.remove('hidden');
    if (withRetry) document.getElementById('payRetry').classList.remove('hidden');
}

function closePay() {
    document.getElementById('payModal').classList.add('hidden');
    document.body.style.overflow = '';
    stopPolling();
}

// Auto-activate when the payment is confirmed (gateway webhook flips the subscription),
// then refresh so the active card, plan badges, and history all update.
let _poll = null;
function startPolling() {
    stopPolling();
    _poll = setInterval(async () => {
        try {
            const r = await fetch(STATUS_URL, { headers: { 'Accept': 'application/json' } });
            const d = await r.json();
            const newActivation = d.active && !CURRENT_ENDS;          // was free, now subscribed
            const extended      = d.active && CURRENT_ENDS && d.ends_at && d.ends_at !== CURRENT_ENDS; // end date moved
            if (newActivation || extended) { window.location.reload(); }
        } catch (e) {}
    }, 5000);
}
function stopPolling() { if (_poll) { clearInterval(_poll); _poll = null; } }
</script>
@endpush
@endsection
