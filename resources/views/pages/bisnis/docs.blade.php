@extends('layouts.app')
@section('title', 'Dokumen Bisnis')
@section('page-title', __('Dokumen & File'))
@section('breadcrumb', 'Bisnis › Dokumen')

@section('content')
@php
    $fmtSize = function ($b) {
        if (!$b) return '';
        $u = ['B','KB','MB','GB']; $i = 0;
        while ($b >= 1024 && $i < 3) { $b /= 1024; $i++; }
        return round($b, 1) . ' ' . $u[$i];
    };
    $typeMeta = [
        'proposal' => ['bg'=>'bg-indigo-50','text'=>'text-indigo-700'],
        'kontrak'  => ['bg'=>'bg-blue-50','text'=>'text-blue-700'],
        'invoice'  => ['bg'=>'bg-amber-50','text'=>'text-amber-700'],
        'katalog'  => ['bg'=>'bg-purple-50','text'=>'text-purple-700'],
        'profil'   => ['bg'=>'bg-sky-50','text'=>'text-sky-700'],
        'lainnya'  => ['bg'=>'bg-gray-100','text'=>'text-gray-600'],
    ];
    $tm = fn($t) => $typeMeta[$t] ?? $typeMeta['lainnya'];
@endphp
<div class="space-y-6">

    {{-- Tab Bar --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-2xl w-fit">
        @php $tabs = [
            ['id'=>'link',     'label'=>__('Link & Dokumen'), 'count'=>count($links) + count($files)],
            ['id'=>'template', 'label'=>__('Template Pesan'), 'count'=>count($templates)],
        ]; @endphp
        @foreach($tabs as $tab)
        <button type="button" onclick="switchTab('{{ $tab['id'] }}')" id="tab-btn-{{ $tab['id'] }}"
            class="tab-btn px-5 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            {{ $tab['label'] }}
            <span class="text-[10px] font-bold opacity-50">{{ $tab['count'] }}</span>
        </button>
        @endforeach
    </div>

    {{-- ── TAB: Link & Dokumen ── --}}
    <div id="tab-link" class="tab-panel space-y-6">

        {{-- Links --}}
        <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <div>
                    <h2 class="font-bold text-sm">{{ __('Link Penting') }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('Google Drive, katalog produk, proposal online, dll.') }}</p>
                </div>
                <button type="button" onclick="openModal('modal-add-link')"
                    class="flex items-center gap-1.5 bg-black text-white px-3 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Tambah Link') }}
                </button>
            </div>
            @if(count($links) > 0)
            <div class="divide-y divide-gray-50">
                @foreach($links as $link)
                @php $meta = $tm($link['type']); @endphp
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50/50 transition-all">
                    <div class="w-8 h-8 rounded-xl {{ $meta['bg'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 {{ $meta['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold truncate">{{ $link['title'] }}</p>
                            @if($link['type'])<span class="text-[10px] font-bold {{ $meta['text'] }} {{ $meta['bg'] }} px-2 py-0.5 rounded-full flex-shrink-0">{{ $types[$link['type']] ?? $link['type'] }}</span>@endif
                        </div>
                        <a href="{{ $link['url'] }}" target="_blank" class="text-xs text-gray-400 hover:text-black transition-all truncate block">{{ $link['url'] }}</a>
                        @if($link['notes'])<p class="text-xs text-gray-400 mt-0.5 italic">{{ $link['notes'] }}</p>@endif
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <a href="{{ $link['url'] }}" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Buka') }}">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <button type="button" onclick="copyToClipboard('{{ $link['url'] }}', this)" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Salin') }}">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                        <form action="{{ route('bisnis.docs.destroy', $link['id']) }}" method="POST" class="contents">
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
            <div class="py-10 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <p class="text-sm">{{ __('Belum ada link tersimpan') }}</p>
            </div>
            @endif
        </div>

        {{-- Files --}}
        <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <div>
                    <h2 class="font-bold text-sm">{{ __('File Tersimpan') }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('Proposal, kontrak, invoice, company profile (maks. 10 MB)') }}</p>
                </div>
                <button type="button" onclick="openModal('modal-upload-file')"
                    class="flex items-center gap-1.5 bg-black text-white px-3 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    {{ __('Upload File') }}
                </button>
            </div>
            @if(count($files) > 0)
            <div class="divide-y divide-gray-50">
                @foreach($files as $f)
                @php $meta = $tm($f['type']); @endphp
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50/50 transition-all">
                    <div class="w-8 h-8 rounded-xl {{ $meta['bg'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 {{ $meta['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold truncate">{{ $f['title'] }}</p>
                            @if($f['type'])<span class="text-[10px] font-bold {{ $meta['text'] }} {{ $meta['bg'] }} px-2 py-0.5 rounded-full flex-shrink-0">{{ $types[$f['type']] ?? $f['type'] }}</span>@endif
                        </div>
                        <p class="text-xs text-gray-400 truncate">{{ $f['original_name'] }} · {{ $fmtSize($f['size']) }}</p>
                        @if($f['notes'])<p class="text-xs text-gray-400 mt-0.5 italic">{{ $f['notes'] }}</p>@endif
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <a href="{{ route('bisnis.docs.download', $f['id']) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Unduh') }}">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                        <form action="{{ route('bisnis.docs.destroy', $f['id']) }}" method="POST" class="contents">
                            @csrf @method('DELETE')
                            <button type="button" onclick="askDelete(this, '{{ __('Hapus file ini?') }}')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all" title="{{ __('Hapus') }}">
                                <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <p class="text-sm">{{ __('Belum ada file terupload') }}</p>
            </div>
            @endif
        </div>
    </div>{{-- end tab-link --}}

    {{-- ── TAB: Template Pesan ── --}}
    <div id="tab-template" class="tab-panel space-y-4" style="display:none">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <p class="text-sm text-gray-500">{{ __('Simpan template email penawaran, pesan WhatsApp, atau follow-up yang sering kamu pakai.') }}</p>
            <button type="button" onclick="openModal('modal-add-tpl')"
                class="flex items-center gap-1.5 bg-black text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('Buat Template') }}
            </button>
        </div>

        @php
            $catMeta = [
                'email'     => ['bg'=>'bg-blue-50','text'=>'text-blue-700'],
                'penawaran' => ['bg'=>'bg-indigo-50','text'=>'text-indigo-700'],
                'whatsapp'  => ['bg'=>'bg-green-50','text'=>'text-green-700'],
                'followup'  => ['bg'=>'bg-amber-50','text'=>'text-amber-700'],
                'lainnya'   => ['bg'=>'bg-gray-100','text'=>'text-gray-600'],
            ];
        @endphp
        @if(count($templates) > 0)
        <div class="space-y-3">
            @foreach($templates as $tpl)
            @php $cm = $catMeta[$tpl['category']] ?? $catMeta['lainnya']; $catLabel = $tplCategories[$tpl['category']] ?? ($tpl['category'] ?: 'Lainnya'); @endphp
            <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden">
                {{-- Header (clickable) --}}
                <div class="flex items-start justify-between gap-3 px-5 py-4 cursor-pointer" onclick="toggleTpl('{{ $tpl['id'] }}')">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-bold">{{ $tpl['title'] }}</p>
                            <span class="text-[10px] font-bold {{ $cm['text'] }} {{ $cm['bg'] }} px-2 py-0.5 rounded-full">{{ $catLabel }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ __('Diperbarui') }} {{ date('j M Y', strtotime($tpl['updated_at'])) }}</p>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" onclick="event.stopPropagation(); copyToClipboard(this.dataset.content, this)" data-content="{{ $tpl['content'] }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Salin Teks') }}">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                        <button type="button" onclick='event.stopPropagation(); openEditTpl(@json($tpl))'
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Edit') }}">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('bisnis.docs.destroy', $tpl['id']) }}" method="POST" class="contents">
                            @csrf @method('DELETE')
                            <button type="button" onclick="event.stopPropagation(); askDelete(this, '{{ __('Hapus template ini?') }}')"
                                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all" title="{{ __('Hapus') }}">
                                <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        <svg id="tpl-chevron-{{ $tpl['id'] }}" class="w-4 h-4 text-gray-400 transition-transform ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>
                {{-- Body (collapsible) --}}
                <div id="tpl-body-{{ $tpl['id'] }}" class="hidden border-t border-gray-50">
                    <div class="px-5 py-4">
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap font-sans leading-relaxed bg-gray-50 rounded-xl p-4">{{ $tpl['content'] }}</pre>
                    </div>
                    <div class="px-5 pb-4">
                        <button type="button" onclick="copyToClipboard(this.dataset.content, this)" data-content="{{ $tpl['content'] }}"
                            class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            {{ __('Salin Teks') }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-2xl border border-gray-50 py-16 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="font-bold text-sm">{{ __('Belum ada template') }}</p>
            <p class="text-xs mt-1">{{ __('Buat template email penawaran atau pesan WhatsApp') }}</p>
            <button type="button" onclick="openModal('modal-add-tpl')" class="mt-4 bg-black text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">{{ __('Buat Template Pertama') }}</button>
        </div>
        @endif
    </div>{{-- end tab-template --}}

</div>

{{-- ── Modals ── --}}
<div id="modal-add-link" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-add-link')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Tambah Link') }}</h2>
            <button type="button" onclick="closeModal('modal-add-link')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('bisnis.docs.link') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Judul') }} *</label>
                <input type="text" name="title" maxlength="255" required placeholder="{{ __('cth: Katalog Produk 2026') }}"
                    class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">URL *</label>
                <input type="url" name="url" maxlength="500" required placeholder="https://..."
                    class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Kategori') }}</label>
                <select name="type" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                    @foreach($types as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Catatan') }}</label>
                <input type="text" name="notes" maxlength="300" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-add-link')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">{{ __('Batal') }}</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-upload-file" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-upload-file')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Upload File') }}</h2>
            <button type="button" onclick="closeModal('modal-upload-file')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('bisnis.docs.file') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center cursor-pointer hover:border-black hover:bg-gray-50 transition-all" onclick="document.getElementById('biz-file-input').click()">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <p class="text-sm font-bold text-gray-600" id="biz-file-label">{{ __('Klik untuk pilih file') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('PDF, DOC, XLS, JPG, PNG — maks. 10 MB') }}</p>
                <input type="file" id="biz-file-input" name="file" class="hidden" required
                    onchange="document.getElementById('biz-file-label').textContent = this.files[0]?.name ?? '{{ __('Klik untuk pilih file') }}'">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Judul') }}</label>
                <input type="text" name="title" maxlength="255" placeholder="{{ __('Kosongkan untuk pakai nama file asli') }}"
                    class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Kategori') }}</label>
                <select name="type" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                    @foreach($types as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-upload-file')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">{{ __('Batal') }}</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Upload') }}</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-add-tpl" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-add-tpl')">
    <div class="bg-white rounded-3xl w-full max-w-lg my-8">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Buat Template') }}</h2>
            <button type="button" onclick="closeModal('modal-add-tpl')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('bisnis.docs.template.store') }}" class="p-6 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Judul Template') }} *</label>
                <input type="text" name="title" maxlength="255" required placeholder="{{ __('cth: Email Penawaran Jasa') }}"
                    class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Kategori') }}</label>
                <select name="category" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                    @foreach($tplCategories as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach
                </select>
            </div>
            <textarea name="content" rows="8" required placeholder="{{ __('Tulis isi template... cth: Halo Bapak/Ibu, perkenalkan kami dari...') }}"
                class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-black resize-none transition-all"></textarea>
            <button type="submit" class="w-full py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Simpan Template') }}</button>
        </form>
    </div>
</div>

<div id="modal-edit-tpl" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-edit-tpl')">
    <div class="bg-white rounded-3xl w-full max-w-lg my-8">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Edit Template') }}</h2>
            <button type="button" onclick="closeModal('modal-edit-tpl')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="" id="editTplForm" class="p-6 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Judul Template') }} *</label>
                <input type="text" name="title" maxlength="255" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Kategori') }}</label>
                <select name="category" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                    @foreach($tplCategories as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach
                </select>
            </div>
            <textarea name="content" rows="8" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-black resize-none transition-all"></textarea>
            <button type="submit" class="w-full py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Perbarui Template') }}</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function switchTab(id) {
    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('bg-white','shadow-sm','text-black'); b.classList.add('text-gray-500'); });
    document.getElementById('tab-' + id).style.display = '';
    const btn = document.getElementById('tab-btn-' + id);
    btn.classList.add('bg-white','shadow-sm','text-black'); btn.classList.remove('text-gray-500');
    localStorage.setItem('bisnis_docs_tab', id);
}
document.addEventListener('DOMContentLoaded', () => switchTab(localStorage.getItem('bisnis_docs_tab') || 'link'));

function openModal(id){ document.getElementById(id).classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeModal(id){ document.getElementById(id).classList.add('hidden'); document.body.style.overflow=''; }
function toggleTpl(id){
    const body = document.getElementById('tpl-body-' + id);
    const chev = document.getElementById('tpl-chevron-' + id);
    const open = body.classList.toggle('hidden');
    if (chev) chev.style.transform = open ? '' : 'rotate(180deg)';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { document.querySelectorAll('[id^="modal-"]').forEach(m => m.classList.add('hidden')); document.body.style.overflow=''; } });

function copyToClipboard(text, btn) {
    navigator.clipboard?.writeText(text || '').then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => btn.innerHTML = orig, 1500);
    });
}
function openEditTpl(t) {
    const f = document.getElementById('editTplForm');
    f.action = '{{ url('bisnis/dokumen/template') }}/' + t.id;
    f.querySelector('[name="title"]').value = t.title ?? '';
    f.querySelector('[name="category"]').value = t.category ?? 'email';
    f.querySelector('[name="content"]').value = t.content ?? '';
    const sel = f.querySelector('[name="category"]');
    if (sel._csRefresh) sel._csRefresh();
    openModal('modal-edit-tpl');
}
</script>
@endpush
@endsection
