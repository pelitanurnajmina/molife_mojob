@extends('layouts.app')
@section('title', __('Ide & Script'))
@section('page-title', __('Ide & Script'))
@section('breadcrumb', __('Life') . ' › ' . __('Ide & Script'))

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h3 class="font-bold">{{ __('Ide & Script') }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Simpan ide konten dan script, baca lagi kapan pun kamu butuh.') }}</p>
        </div>
        <button type="button" onclick="isAdd()"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-black text-white text-xs font-bold hover:bg-gray-800 transition-all flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span id="isAddLabel">{{ __('Tambah Ide') }}</span>
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-2xl w-fit">
        <button type="button" id="isTabBtnidea" onclick="isTab('idea')" class="is-tab px-5 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            {{ __('Ide') }} <span class="text-[10px] font-bold opacity-50">{{ count($ideas) }}</span>
        </button>
        <button type="button" id="isTabBtnscript" onclick="isTab('script')" class="is-tab px-5 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            {{ __('Script') }} <span class="text-[10px] font-bold opacity-50">{{ count($scripts) }}</span>
        </button>
    </div>

    @php
        $panels = ['idea' => $ideas, 'script' => $scripts];
        $emptyText = ['idea' => __('Belum ada ide. Klik "Tambah" untuk menyimpan ide pertamamu.'), 'script' => __('Belum ada script. Klik "Tambah" untuk menyimpan script pertamamu.')];
    @endphp
    @foreach($panels as $type => $list)
    <div id="isPanel{{ $type }}" class="is-panel hidden">
        @if(count($list))
        <div class="bg-white rounded-2xl md:rounded-3xl border border-gray-50 overflow-hidden">
            @foreach($list as $item)
            @php $j = Illuminate\Support\Js::from($item); @endphp
            <div class="flex items-center gap-3 px-4 md:px-5 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-all">
                <div class="flex-1 min-w-0 cursor-pointer" onclick="isRead({{ $j }})">
                    <p class="font-bold text-gray-800 truncate">{{ $item['title'] }}</p>
                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ $item['content'] ?: '—' }}</p>
                </div>
                <span class="hidden sm:block text-[10px] text-gray-300 font-bold whitespace-nowrap flex-shrink-0">{{ $item['updated'] }}</span>
                <div class="flex items-center gap-0.5 flex-shrink-0">
                    <button type="button" onclick="isRead({{ $j }})" title="{{ __('Lihat detail') }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <button type="button" onclick="isEdit({{ $j }})" title="{{ __('Edit') }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button type="button" onclick="isDelete({{ $j }})" title="{{ __('Hapus') }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-2xl md:rounded-3xl border border-gray-50 text-center py-14 px-4">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3a6 6 0 00-3.6 10.8c.44.33.6.87.6 1.42V16a1 1 0 001 1h4a1 1 0 001-1v-.78c0-.55.16-1.09.6-1.42A6 6 0 0012 3z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">{{ $emptyText[$type] }}</p>
        </div>
        @endif
    </div>
    @endforeach

</div>

{{-- ── Modal tambah/edit ── --}}
<div id="modal-is" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-is')">
    <div id="isModalCard" class="bg-white rounded-3xl w-full max-w-lg my-8 transition-[max-width] duration-200">
        <div class="flex items-start justify-between gap-3 px-6 pt-6 pb-4 border-b border-gray-50">
            <h2 class="font-bold text-lg" id="isModalTitle">{{ __('Ide / Script Baru') }}</h2>
            <button type="button" onclick="closeModal('modal-is')" class="w-8 h-8 -mr-1.5 -mt-1 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="" id="isForm" class="px-6 pt-5 pb-6">
            @csrf
            <input type="hidden" name="type" id="isType" value="idea">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Judul') }} <span class="text-red-400">*</span></label>
                    <input type="text" name="title" id="isTitle" maxlength="200" required placeholder="{{ __('cth: Reels tips hemat, Script opening video') }}"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Isi') }}</label>
                    <textarea name="content" id="isContent" rows="8" maxlength="20000" placeholder="{{ __('Tulis ide atau script lengkapmu di sini...') }}"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-black focus:bg-white resize-y transition-all"></textarea>
                </div>
            </div>
            <button type="submit" class="w-full mt-6 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all" id="isSubmit">{{ __('Simpan') }}</button>
        </form>
        <form method="POST" action="" id="isDeleteForm" class="hidden px-6 pb-5 -mt-2 text-center">
            @csrf @method('DELETE')
            <button type="button" onclick="askDelete(this, '{{ __('Hapus item ini?') }}')"
                class="text-[11px] font-bold text-gray-400 hover:text-red-500 transition-all">{{ __('Hapus item ini') }}</button>
        </form>
    </div>
</div>

{{-- ── Modal baca ── --}}
<div id="modal-is-read" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-is-read')">
    <div class="bg-white rounded-3xl w-full max-w-lg my-8">
        <div class="flex items-start justify-between gap-3 px-6 pt-6 pb-4 border-b border-gray-50">
            <div class="min-w-0">
                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full" id="isReadBadge">{{ __('Ide') }}</span>
                <h2 class="font-bold text-lg leading-snug mt-1.5 break-words" id="isReadTitle">—</h2>
            </div>
            <button type="button" onclick="closeModal('modal-is-read')" class="w-8 h-8 -mr-1.5 -mt-1 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="px-6 pt-5 pb-6">
            <div class="max-h-[55vh] overflow-y-auto">
                <p id="isReadContent" class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words">—</p>
            </div>
            <div class="flex gap-2 mt-6">
                <button type="button" onclick="isCopy()" id="isCopyBtn"
                    class="flex-1 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-bold text-gray-700 hover:border-gray-400 transition-all inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    {{ __('Salin') }}
                </button>
                <button type="button" onclick="isEditFromRead()"
                    class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Edit') }}</button>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="" id="isListDeleteForm" class="hidden">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script>
var IS_URL = '{{ url('ide-script') }}';
var isCurrent = null;

function openModal(id){ document.getElementById(id).classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeModal(id){ document.getElementById(id).classList.add('hidden'); document.body.style.overflow=''; }

/* Script butuh POV lebih luas → popup lebih lebar + area ketik lebih tinggi */
function isSetSize(type){
    const card = document.getElementById('isModalCard');
    const content = document.getElementById('isContent');
    const title = document.getElementById('isTitle');
    if (type === 'script') {
        card.classList.remove('max-w-lg'); card.classList.add('max-w-3xl');
        content.rows = 18;
        content.placeholder = '{{ __('Tulis script lengkapmu di sini...') }}';
        title.placeholder = '{{ __('cth: Script opening video promosi') }}';
    } else {
        card.classList.remove('max-w-3xl'); card.classList.add('max-w-lg');
        content.rows = 8;
        content.placeholder = '{{ __('Tulis idemu di sini...') }}';
        title.placeholder = '{{ __('cth: Reels tips hemat belanja') }}';
    }
}

/* Tab */
function isTab(t){
    document.querySelectorAll('.is-panel').forEach(p => p.classList.add('hidden'));
    document.getElementById('isPanel' + t).classList.remove('hidden');
    document.querySelectorAll('.is-tab').forEach(b => { b.classList.remove('bg-white','shadow-sm','text-black'); b.classList.add('text-gray-500'); });
    const btn = document.getElementById('isTabBtn' + t);
    btn.classList.add('bg-white','shadow-sm','text-black'); btn.classList.remove('text-gray-500');
    // Tombol tambah mengikuti tab aktif
    document.getElementById('isAddLabel').textContent = t === 'script' ? '{{ __('Tambah Script') }}' : '{{ __('Tambah Ide') }}';
    try { localStorage.setItem('isTab', t); } catch(e){}
}

/* Tambah (jenis mengikuti tab aktif) */
function isAdd(){
    const f = document.getElementById('isForm');
    f.action = IS_URL;
    const active = document.querySelector('.is-panel:not(.hidden)');
    const type = active && active.id === 'isPanelscript' ? 'script' : 'idea';
    document.getElementById('isModalTitle').textContent = type === 'script' ? '{{ __('Tambah Script') }}' : '{{ __('Tambah Ide') }}';
    document.getElementById('isSubmit').textContent = '{{ __('Simpan') }}';
    document.getElementById('isType').value = type;
    isSetSize(type);
    document.getElementById('isTitle').value = '';
    document.getElementById('isContent').value = '';
    document.getElementById('isDeleteForm').classList.add('hidden');
    openModal('modal-is');
}

/* Edit */
function isEdit(item){
    const f = document.getElementById('isForm');
    f.action = IS_URL + '/' + item.id;
    document.getElementById('isModalTitle').textContent = item.type === 'script' ? '{{ __('Ubah Script') }}' : '{{ __('Ubah Ide') }}';
    document.getElementById('isSubmit').textContent = '{{ __('Perbarui') }}';
    document.getElementById('isType').value = item.type;
    isSetSize(item.type);
    document.getElementById('isTitle').value = item.title || '';
    document.getElementById('isContent').value = item.content || '';
    const del = document.getElementById('isDeleteForm');
    del.action = IS_URL + '/' + item.id;
    del.classList.remove('hidden');
    openModal('modal-is');
}

/* Baca */
function isRead(item){
    isCurrent = item;
    const badge = document.getElementById('isReadBadge');
    badge.textContent = item.type === 'script' ? '{{ __('Script') }}' : '{{ __('Ide') }}';
    badge.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full ' + (item.type === 'script' ? 'bg-violet-50 text-violet-600' : 'bg-amber-50 text-amber-600');
    document.getElementById('isReadTitle').textContent = item.title || '';
    document.getElementById('isReadContent').textContent = item.content || '—';
    openModal('modal-is-read');
}

function isEditFromRead(){ closeModal('modal-is-read'); if (isCurrent) isEdit(isCurrent); }

/* Hapus dari list (dengan konfirmasi) */
var IS_DELETE_MSG = @json(__('Hapus ":title"?'));
function isDelete(item){
    const f = document.getElementById('isListDeleteForm');
    f.action = IS_URL + '/' + item.id;
    askDelete(f, IS_DELETE_MSG.replace(':title', item.title || ''));
}

function isCopy(){
    if (!isCurrent) return;
    const text = isCurrent.content || '';
    const done = () => { const b = document.getElementById('isCopyBtn'); const old = b.innerHTML; b.innerHTML = '{{ __('Tersalin') }}'; setTimeout(() => b.innerHTML = old, 1500); };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(() => fallbackCopy(text, done));
    } else { fallbackCopy(text, done); }
}
function fallbackCopy(text, done){
    const ta = document.createElement('textarea'); ta.value = text; ta.style.position='fixed'; ta.style.opacity='0';
    document.body.appendChild(ta); ta.select();
    try { document.execCommand('copy'); done(); } catch(e){}
    document.body.removeChild(ta);
}

/* Init tab (ingat pilihan terakhir) */
(function(){
    let t = 'idea';
    try { const s = localStorage.getItem('isTab'); if (s === 'script') t = 'script'; } catch(e){}
    isTab(t);
})();
</script>
@endpush
