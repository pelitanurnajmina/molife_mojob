@extends('layouts.app')
@section('title', __('Motivasi'))
@section('page-title', __('Motivasi'))
@section('breadcrumb', 'Life › Motivasi')

@section('content')
@php
    $initials = function ($src) {
        $words = preg_split('/\s+/', trim(preg_replace('/[^A-Za-z\s]/', '', $src)));
        $words = array_values(array_filter($words));
        $a = strtoupper(substr($words[0] ?? 'M', 0, 1));
        $b = strtoupper(substr($words[1] ?? '', 0, 1));
        return $a . $b ?: 'M';
    };
@endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Quote of the day ── --}}
    <div class="bg-gradient-to-br from-gray-900 via-gray-900 to-indigo-900 rounded-2xl md:rounded-3xl p-6 md:p-9 text-white relative overflow-hidden">
        <svg class="absolute -right-4 -top-6 w-40 h-40 text-white/5" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
        <div class="relative">
            {{-- Category chips --}}
            <div class="flex flex-wrap gap-1.5 mb-5">
                @foreach($categories as $key => $label)
                <button type="button" data-cat="{{ $key }}" onclick="setCat('{{ $key }}', this)"
                    class="cat-chip text-[11px] font-bold px-3 py-1.5 rounded-full transition-all {{ $key === 'all' ? 'bg-white text-gray-900' : 'bg-white/10 text-white/70 hover:bg-white/20' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            <p id="quoteText" class="text-xl md:text-2xl font-bold leading-relaxed">"{{ $quote['text'] }}"</p>

            <div class="flex items-center gap-2.5 mt-4">
                <div id="quoteAvatar" class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $initials($quote['src']) }}</div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-white/40 font-bold">{{ __('Kata') }}</p>
                    <p id="quoteSrc" class="text-sm font-bold text-white/80">{{ $quote['src'] }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 mt-6">
                <button type="button" id="favBtn" onclick="toggleFav()"
                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/10 hover:bg-white/20 px-3.5 py-2 rounded-xl transition-all">
                    <svg id="favIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span id="favLabel">{{ __('Simpan') }}</span>
                </button>
                <button type="button" onclick="shuffleQuote()"
                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/10 hover:bg-white/20 px-3.5 py-2 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Quote lain') }}
                </button>
                <button type="button" onclick="copyQuote()"
                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/10 hover:bg-white/20 px-3.5 py-2 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    {{ __('Salin') }}
                </button>
                <a id="quoteShare" href="#" target="_blank"
                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/10 hover:bg-white/20 px-3.5 py-2 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.263.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.886 9.884"/></svg>
                    {{ __('Bagikan') }}
                </a>
            </div>
        </div>
    </div>

    {{-- ── Alasan Besarku (Big Why) ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-5 md:p-7 border border-gray-50">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </span>
            <h3 class="font-bold">{{ __('Alasan Besarku') }}</h3>
        </div>
        <p class="text-xs text-gray-400 mb-3">{{ __('Kenapa kamu ingin berubah? Tulis alasan terdalammu. Baca ini saat semangatmu turun.') }}</p>
        <form method="POST" action="{{ route('motivasi.why') }}">
            @csrf
            <textarea name="big_why" rows="3" maxlength="1000"
                placeholder="{{ __('cth: Aku ingin jadi pribadi yang lebih sehat & dekat dengan Tuhan, supaya bisa membahagiakan keluargaku...') }}"
                class="w-full px-3.5 py-3 bg-rose-50/40 border border-rose-100 rounded-xl text-sm leading-relaxed text-gray-700 outline-none focus:border-rose-300 focus:bg-white resize-none transition-all">{{ $bigWhy }}</textarea>
            <div class="flex justify-end mt-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>

    {{-- ── Afirmasi + Tantangan ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl md:rounded-3xl p-5 md:p-7 text-white relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full"></div>
            <div class="relative">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </span>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/70">{{ __('Afirmasi Hari Ini') }}</p>
                </div>
                <p id="affirmText" class="text-lg md:text-xl font-bold leading-relaxed">"{{ $affirmation }}"</p>
                <button type="button" onclick="shuffleAffirm()"
                    class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/15 hover:bg-white/25 px-3.5 py-2 rounded-xl transition-all mt-5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Afirmasi lain') }}
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl md:rounded-3xl p-5 md:p-7 border border-gray-50 flex flex-col">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ __('Tantangan Kecil Hari Ini') }}</p>
            </div>
            <p class="text-lg md:text-xl font-bold text-gray-800 leading-relaxed flex-1">{{ $challenge }}</p>
            <button type="button" id="challengeBtn" onclick="toggleChallenge()"
                class="inline-flex items-center justify-center gap-2 text-sm font-bold border-2 border-gray-200 text-gray-600 px-4 py-3 rounded-xl transition-all mt-5 hover:border-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span id="challengeLabel">{{ __('Tandai selesai') }}</span>
            </button>
        </div>
    </div>

    {{-- ── Vision Board ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-5 md:p-7 border border-gray-50">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </span>
            <h3 class="font-bold">{{ __('Vision Board') }}</h3>
        </div>
        <p class="text-xs text-gray-400 mb-4">{{ __('Tempel impian & target hidupmu. Lihat tiap hari, tarik ke dalam hidupmu.') }}</p>

        @if(count($vision) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
            @foreach($vision as $v)
            <div class="group relative bg-gradient-to-br from-indigo-50 to-violet-50 border border-indigo-100/60 rounded-2xl p-4 text-center">
                <form method="POST" action="{{ route('motivasi.vision.delete', $v->id) }}" class="absolute top-1.5 right-1.5">
                    @csrf @method('DELETE')
                    <button type="button" onclick="askDelete(this, '{{ __('Hapus impian ini dari vision board?') }}')"
                        class="w-6 h-6 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-white/70 opacity-0 group-hover:opacity-100 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </form>
                <div class="text-3xl mb-1.5">{{ $v->emoji ?: '✨' }}</div>
                <p class="text-xs font-bold text-gray-700 leading-snug">{{ $v->text }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 mb-2 bg-gray-50 rounded-2xl">
            <p class="text-sm font-medium text-gray-500">{{ __('Belum ada impian') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Tambahkan impian pertamamu di bawah.') }}</p>
        </div>
        @endif

        <form method="POST" action="{{ route('motivasi.vision.add') }}">
            @csrf
            <p class="text-[11px] font-bold text-gray-400 mb-1.5">{{ __('Tambah impian baru') }}</p>
            <div class="flex items-center gap-2">
                {{-- Clickable emoji picker --}}
                <div class="relative flex-shrink-0">
                    <input type="hidden" name="emoji" id="emojiInput" value="✨">
                    <button type="button" id="emojiBtn" onclick="toggleEmojiPanel(event)"
                        class="w-11 h-11 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-xl hover:border-black transition-all">✨</button>
                    <div id="emojiPanel" class="hidden absolute z-30 left-0 mt-1.5 bg-white border border-gray-100 rounded-2xl shadow-xl p-2 grid grid-cols-6 gap-0.5 w-60">
                        @foreach(['✨','🎯','🏠','🎓','💪','💰','❤️','🧘','✈️','📚','🚗','💍','🌟','🔥','🙏','🏆','🌱','💼'] as $e)
                        <button type="button" onclick="pickEmoji('{{ $e }}')" class="w-9 h-9 rounded-lg hover:bg-gray-100 text-xl transition-all">{{ $e }}</button>
                        @endforeach
                    </div>
                </div>
                <input type="text" name="text" maxlength="80" required
                    placeholder="{{ __('cth: Punya rumah sendiri, Lulus kuliah, Berat ideal...') }}"
                    class="flex-1 min-w-0 h-11 px-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                <button type="submit" class="h-11 px-5 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all flex-shrink-0">
                    {{ __('Tambah') }}
                </button>
            </div>
        </form>
    </div>

    {{-- ── Quote Tersimpan ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-5 md:p-7 border border-gray-50">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </span>
            <h3 class="font-bold">{{ __('Quote Tersimpan') }}</h3>
        </div>
        <p class="text-xs text-gray-400 mb-4">{{ __('Koleksi quote favoritmu.') }}</p>

        <div id="favList" class="space-y-2">
            @forelse($favorites as $f)
            <div class="fav-row flex items-start gap-3 p-3 rounded-xl bg-gray-50" data-fav-text="{{ $f->text }}">
                <svg class="w-4 h-4 text-rose-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-700 leading-relaxed">"{{ $f->text }}"</p>
                    @if($f->src)<p class="text-xs text-gray-400 mt-0.5">— {{ $f->src }}</p>@endif
                </div>
                <button type="button" onclick="removeFav(this)" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-gray-200 transition-all flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @empty
            <p id="favEmpty" class="text-center text-gray-400 text-sm py-6">{{ __('Belum ada quote tersimpan. Tekan "Simpan" di quote yang kamu suka.') }}</p>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const QUOTES  = @json($quotes);
const AFFIRMS = @json($affirmations);
const FAV_URL = '{{ route('motivasi.favorite') }}';
const CSRF    = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
let   curCat  = 'all';
let   curQuote = @json($quote);
const favSet  = new Set(@json($favTexts));

function initials(src) {
    const w = (src || '').replace(/[^A-Za-z\s]/g, '').trim().split(/\s+/).filter(Boolean);
    return ((w[0]?.[0] || 'M') + (w[1]?.[0] || '')).toUpperCase();
}
function updateShare(text) { document.getElementById('quoteShare').href = 'https://wa.me/?text=' + encodeURIComponent(text); }
function updateHeart() {
    const on = favSet.has(curQuote.text);
    document.getElementById('favIcon').setAttribute('fill', on ? 'currentColor' : 'none');
    document.getElementById('favBtn').classList.toggle('text-rose-300', on);
    document.getElementById('favLabel').textContent = on ? '{{ __('Tersimpan') }}' : '{{ __('Simpan') }}';
}
function renderQuote(q) {
    curQuote = q;
    document.getElementById('quoteText').textContent = '"' + q.text + '"';
    document.getElementById('quoteSrc').textContent = q.src;
    document.getElementById('quoteAvatar').textContent = initials(q.src);
    updateShare(q.text + ' — ' + q.src);
    updateHeart();
}
function pool() { return curCat === 'all' ? QUOTES : QUOTES.filter(q => q.cat === curCat); }
function setCat(cat, btn) {
    curCat = cat;
    document.querySelectorAll('.cat-chip').forEach(c => {
        const active = c.dataset.cat === cat;
        c.className = 'cat-chip text-[11px] font-bold px-3 py-1.5 rounded-full transition-all ' + (active ? 'bg-white text-gray-900' : 'bg-white/10 text-white/70 hover:bg-white/20');
    });
    shuffleQuote();
}
function shuffleQuote() { const p = pool(); if (p.length) renderQuote(p[Math.floor(Math.random() * p.length)]); }
function copyQuote() {
    navigator.clipboard?.writeText('"' + curQuote.text + '" — ' + curQuote.src).then(() => {
        if (window.showMojobToast) window.showMojobToast('{{ __('Quote disalin!') }}');
    });
}
function shuffleAffirm() { document.getElementById('affirmText').textContent = '"' + AFFIRMS[Math.floor(Math.random() * AFFIRMS.length)] + '"'; }

async function postFav(text, src) {
    const res = await fetch(FAV_URL, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify({ text, src }) });
    return res.ok ? res.json() : null;
}
async function toggleFav() {
    const q = curQuote;
    const r = await postFav(q.text, q.src);
    if (!r) return;
    if (r.favorited) { favSet.add(q.text); addFavRow(q.text, q.src); }
    else { favSet.delete(q.text); removeFavRow(q.text); }
    updateHeart();
}
async function removeFav(btn) {
    const row = btn.closest('.fav-row');
    const text = row.dataset.favText;
    await postFav(text, '');         // toggle -> removes existing
    favSet.delete(text);
    row.remove();
    if (curQuote.text === text) updateHeart();
    checkFavEmpty();
}
function addFavRow(text, src) {
    document.getElementById('favEmpty')?.remove();
    const row = document.createElement('div');
    row.className = 'fav-row flex items-start gap-3 p-3 rounded-xl bg-gray-50';
    row.dataset.favText = text;
    row.innerHTML = `
        <svg class="w-4 h-4 text-rose-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-700 leading-relaxed">"${text}"</p>
            ${src ? '<p class="text-xs text-gray-400 mt-0.5">— ' + src + '</p>' : ''}
        </div>
        <button type="button" onclick="removeFav(this)" class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-gray-200 transition-all flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    document.getElementById('favList').prepend(row);
}
function removeFavRow(text) {
    document.querySelectorAll('.fav-row').forEach(r => { if (r.dataset.favText === text) r.remove(); });
    checkFavEmpty();
}
function checkFavEmpty() {
    const list = document.getElementById('favList');
    if (!list.querySelector('.fav-row') && !document.getElementById('favEmpty')) {
        const p = document.createElement('p');
        p.id = 'favEmpty';
        p.className = 'text-center text-gray-400 text-sm py-6';
        p.textContent = '{{ __('Belum ada quote tersimpan. Tekan "Simpan" di quote yang kamu suka.') }}';
        list.appendChild(p);
    }
}

/* Challenge done state (per day, localStorage) */
const CH_KEY = 'mojobChallenge_' + new Date().toISOString().slice(0, 10);
function renderChallenge() {
    const done = localStorage.getItem(CH_KEY) === '1';
    const btn = document.getElementById('challengeBtn');
    btn.className = 'inline-flex items-center justify-center gap-2 text-sm font-bold px-4 py-3 rounded-xl transition-all mt-5 ' +
        (done ? 'bg-emerald-500 text-white' : 'border-2 border-gray-200 text-gray-600 hover:border-gray-300');
    document.getElementById('challengeLabel').textContent = done ? '{{ __('Selesai! 🎉') }}' : '{{ __('Tandai selesai') }}';
}
function toggleChallenge() {
    const wasDone = localStorage.getItem(CH_KEY) === '1';
    localStorage.setItem(CH_KEY, wasDone ? '0' : '1');
    renderChallenge();
    if (!wasDone && window.showMojobToast) window.showMojobToast('{{ __('Keren! Satu langkah kecil tercapai.') }}');
}

updateShare(curQuote.text + ' — ' + curQuote.src);
updateHeart();
renderChallenge();

/* Vision board emoji picker */
function toggleEmojiPanel(e) {
    e.stopPropagation();
    document.getElementById('emojiPanel').classList.toggle('hidden');
}
function pickEmoji(emoji) {
    document.getElementById('emojiInput').value = emoji;
    document.getElementById('emojiBtn').textContent = emoji;
    document.getElementById('emojiPanel').classList.add('hidden');
}
document.addEventListener('click', (e) => {
    const panel = document.getElementById('emojiPanel');
    const btn = document.getElementById('emojiBtn');
    if (panel && !panel.classList.contains('hidden') && !panel.contains(e.target) && e.target !== btn) {
        panel.classList.add('hidden');
    }
});
</script>
@endpush
