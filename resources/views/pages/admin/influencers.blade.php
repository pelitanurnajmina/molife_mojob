@extends('layouts.admin')
@section('title', 'Influencer & Promo')

@section('content')
@php $rp = fn($n) => 'Rp ' . number_format((int)$n, 0, ',', '.'); @endphp

<div class="w-full">
    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mb-1">Influencer & Kode Promo</h1>
    <p class="text-gray-500 text-sm mb-6">Daftarkan influencer, beri akses gratis, dan buat kode promo dengan diskon audiens + komisi mereka.</p>

    {{-- New account credentials (shown once) --}}
    @if(session('newAccount'))
    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-4 mb-5 text-sm">
        <div class="font-bold text-orange-700 mb-1">Akun influencer dibuat 🎉</div>
        <p class="text-orange-800/80 mb-2">Kirim kredensial ini ke influencer (tampil sekali saja). Dia bisa ganti password lewat "Lupa password".</p>
        <div class="font-mono bg-white border border-orange-200 rounded-lg px-3 py-2 inline-block">
            Email: <b>{{ session('newAccount')['email'] }}</b> &nbsp;·&nbsp; Password: <b>{{ session('newAccount')['password'] }}</b>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 mb-4 text-sm">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="grid lg:grid-cols-5 gap-5">
        {{-- Register form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h2 class="font-bold mb-4">Daftarkan Influencer</h2>
                <form method="POST" action="{{ route('admin.influencers.store') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Email influencer *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-black">
                        <p class="text-[11px] text-gray-400 mt-1">Kalau email belum punya akun, akun baru otomatis dibuat.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Nama / catatan</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="mis. Budi Setiawan"
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-black">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Akses gratis untuk influencer</label>
                        <select name="free_months" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
                            <option value="0" @selected(old('free_months')==='0')>Tanpa akses gratis</option>
                            <option value="1" @selected(old('free_months')==='1')>1 Bulan</option>
                            <option value="3" @selected(old('free_months','3')==='3')>3 Bulan</option>
                            <option value="6" @selected(old('free_months')==='6')>6 Bulan</option>
                            <option value="12" @selected(old('free_months')==='12')>1 Tahun</option>
                        </select>
                    </div>

                    <hr class="border-gray-100">

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Kode promo *</label>
                        <input type="text" name="code" value="{{ old('code') }}" required placeholder="BUDI40"
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm font-mono uppercase focus:outline-none focus:border-black"
                               oninput="this.value=this.value.toUpperCase()">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Diskon audiens (%)</label>
                            <input type="number" name="discount_percent" value="{{ old('discount_percent', 40) }}" min="0" max="100" required
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-black">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Komisi influencer (%)</label>
                            <input type="number" name="commission_percent" value="{{ old('commission_percent', 50) }}" min="0" max="100" required
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-black">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Batas pemakaian</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="kosong = tak terbatas"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-black">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Kedaluwarsa</label>
                            <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-black">
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-400 leading-relaxed">
                        Diskon & komisi berlaku untuk <b>pembayaran pertama</b> audiens. Komisi masuk ke saldo referral influencer dan dicairkan lewat menu Referral.
                    </p>
                    <button class="w-full bg-black text-white text-sm font-bold py-3 rounded-xl hover:bg-gray-800 transition">Daftarkan & Buat Kode</button>
                </form>
            </div>
        </div>

        {{-- Codes list --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold">Kode Aktif</h2>
                    <span class="text-xs text-gray-400">{{ $codes->count() }} kode</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($codes as $c)
                    <div class="p-4 md:p-5 {{ $c['active'] ? '' : 'opacity-50' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-base">{{ $c['code'] }}</span>
                                    @if(!$c['active'])<span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">NONAKTIF</span>@endif
                                </div>
                                <div class="text-sm text-gray-500 truncate">{{ $c['owner_name'] }} · {{ $c['owner_email'] }}</div>
                                @if($c['label'])<div class="text-xs text-gray-400 mt-0.5">{{ $c['label'] }}</div>@endif
                            </div>
                            <form method="POST" action="{{ route('admin.influencers.toggle', $c['id']) }}"
                                  data-confirm="{{ $c['active'] ? 'Nonaktifkan kode ini?' : 'Aktifkan lagi kode ini?' }}">
                                @csrf
                                <button class="text-xs font-semibold px-3 py-1.5 rounded-lg {{ $c['active'] ? 'bg-red-50 text-red-500 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                    {{ $c['active'] ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3 text-center">
                            <div class="bg-gray-50 rounded-xl py-2">
                                <div class="text-lg font-extrabold">{{ $c['discount'] }}%</div>
                                <div class="text-[10px] text-gray-400 font-semibold uppercase">Diskon</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl py-2">
                                <div class="text-lg font-extrabold">{{ $c['commission'] }}%</div>
                                <div class="text-[10px] text-gray-400 font-semibold uppercase">Komisi</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl py-2">
                                <div class="text-lg font-extrabold">{{ $c['used'] }}<span class="text-gray-300 text-sm">/{{ $c['paid'] }}</span></div>
                                <div class="text-[10px] text-gray-400 font-semibold uppercase">Daftar/Bayar</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl py-2">
                                <div class="text-sm font-extrabold">{{ $rp($c['earnings']) }}</div>
                                <div class="text-[10px] text-gray-400 font-semibold uppercase">Saldo</div>
                            </div>
                        </div>
                        <div class="text-[11px] text-gray-400 mt-2">
                            Link: <span class="font-mono">{{ url('/register?ref='.$c['code']) }}</span>
                            @if($c['max_uses']) · maks {{ $c['max_uses'] }}x @endif
                            @if($c['expires']) · s/d {{ $c['expires'] }} @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-10 text-center text-gray-400 text-sm">Belum ada kode promo. Daftarkan influencer pertamamu.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
