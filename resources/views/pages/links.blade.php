@extends('layouts.app')
@section('title', 'Link Penting')
@section('page-title', __('Link Penting'))
@section('breadcrumb', 'Life › Link Penting')

@section('content')
<div class="space-y-4 md:space-y-6">

    <div class="bg-white rounded-2xl md:rounded-3xl border border-gray-50 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-sm">{{ __('Link Penting') }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Simpan link yang sering kamu butuhkan beserta fungsinya.') }}</p>
            </div>
            <button type="button" onclick="openModal('modal-add')"
                class="flex items-center gap-1.5 bg-black text-white px-3 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('Tambah Link') }}
            </button>
        </div>

        @if(count($links) > 0)
        <div class="divide-y divide-gray-50">
            @foreach($links as $link)
            <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50/50 transition-all">
                <div class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ $link['title'] }}</p>
                    <a href="{{ $link['url'] }}" target="_blank" class="text-xs text-gray-400 hover:text-black transition-all truncate block">{{ $link['url'] }}</a>
                    @if($link['notes'])<p class="text-xs text-gray-400 mt-0.5 italic">{{ $link['notes'] }}</p>@endif
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <a href="{{ $link['url'] }}" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Buka') }}">
                        <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <button type="button" onclick="copyLink('{{ $link['url'] }}', this)" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Salin') }}">
                        <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                    <button type="button" onclick='openEdit(@json($link))' class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Edit') }}">
                        <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form action="{{ route('links.destroy', $link['id']) }}" method="POST" class="contents">
                        @csrf @method('DELETE')
                        <button type="button" onclick="askDelete(this, '{{ __('Hapus link ini?') }}')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all" title="{{ __('Hapus') }}">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-14 text-center text-gray-400">
            <svg class="w-9 h-9 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <p class="text-sm font-medium text-gray-500">{{ __('Belum ada link tersimpan') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Klik "Tambah Link" untuk menyimpan link pertamamu.') }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Add modal --}}
<div id="modal-add" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-add')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Tambah Link') }}</h2>
            <button type="button" onclick="closeModal('modal-add')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('links.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            @include('pages._link_fields')
            <button type="submit" class="w-full py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Simpan') }}</button>
        </form>
    </div>
</div>

{{-- Edit modal --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-edit')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Edit Link') }}</h2>
            <button type="button" onclick="closeModal('modal-edit')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="" method="POST" id="editForm" class="p-6 space-y-4">
            @csrf
            @include('pages._link_fields')
            <button type="submit" class="w-full py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Perbarui') }}</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(id){ document.getElementById(id).classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeModal(id){ document.getElementById(id).classList.add('hidden'); document.body.style.overflow=''; }
function copyLink(url, btn){
    navigator.clipboard?.writeText(url).then(() => {
        const o = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => btn.innerHTML = o, 1500);
    });
}
function openEdit(l){
    const f = document.getElementById('editForm');
    f.action = '{{ url('links') }}/' + l.id;
    f.querySelector('[name="title"]').value = l.title ?? '';
    f.querySelector('[name="url"]').value = l.url ?? '';
    f.querySelector('[name="notes"]').value = l.notes ?? '';
    openModal('modal-edit');
}
</script>
@endpush
@endsection
