@extends('layouts.app')
@section('title', 'Persiapan Melamar')
@section('page-title', 'Persiapan Melamar')
@section('breadcrumb', 'Persiapan')

@section('content')
<div class="space-y-6">

    {{-- Tab Bar --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-2xl w-fit">
        @php
            $tabs = [
                ['id' => 'link',     'label' => 'Link & Dokumen', 'count' => count($links) + count($files)],
                ['id' => 'template', 'label' => 'Template Pesan', 'count' => count($templates)],
            ];
        @endphp
        @foreach($tabs as $tab)
        <button type="button"
                onclick="switchTab('{{ $tab['id'] }}')"
                id="tab-btn-{{ $tab['id'] }}"
                class="tab-btn px-5 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            {{ $tab['label'] }}
            <span class="text-[10px] font-bold opacity-50 tab-count-{{ $tab['id'] }}">{{ $tab['count'] }}</span>
        </button>
        @endforeach
    </div>

    {{-- ============================================================ --}}
    {{-- TAB: Link & Dokumen                                          --}}
    {{-- ============================================================ --}}
    <div id="tab-link" class="tab-panel space-y-6">

        {{-- Links Section --}}
        <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <div>
                    <h2 class="font-bold text-sm">Link Penting</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Portfolio, LinkedIn, GitHub, job board, dll.</p>
                </div>
                <button type="button" onclick="openModal('modal-add-link')"
                        class="flex items-center gap-1.5 bg-black text-white px-3 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Tambah Link
                </button>
            </div>

            @if(count($links) > 0)
            <div class="divide-y divide-gray-50">
                @php
                    $linkTypeMeta = [
                        'cv'        => ['label' => 'CV / Resume',    'bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        'portfolio' => ['label' => 'Portfolio',       'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        'linkedin'  => ['label' => 'LinkedIn',        'bg' => 'bg-sky-50',    'text' => 'text-sky-700',    'icon' => 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z'],
                        'github'    => ['label' => 'GitHub',          'bg' => 'bg-gray-100',  'text' => 'text-gray-700',   'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                        'referral'  => ['label' => 'Referral',        'bg' => 'bg-green-50',  'text' => 'text-green-700',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        'jobsite'   => ['label' => 'Job Board',       'bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        'other'     => ['label' => 'Lainnya',         'bg' => 'bg-gray-50',   'text' => 'text-gray-600',   'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
                    ];
                @endphp
                @foreach($links as $link)
                @php $meta = $linkTypeMeta[$link['type']] ?? $linkTypeMeta['other']; @endphp
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50/50 transition-all group">
                    <div class="w-8 h-8 rounded-xl {{ $meta['bg'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 {{ $meta['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $meta['icon'] }}"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold truncate">{{ $link['name'] }}</p>
                            <span class="text-[10px] font-bold {{ $meta['text'] }} {{ $meta['bg'] }} px-2 py-0.5 rounded-full flex-shrink-0">{{ $meta['label'] }}</span>
                        </div>
                        <a href="{{ $link['url'] }}" target="_blank"
                           class="text-xs text-gray-400 hover:text-black transition-all truncate block">
                           {{ $link['url'] }}
                        </a>
                        @if($link['notes'])
                        <p class="text-xs text-gray-400 mt-0.5 italic">{{ $link['notes'] }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all">
                        <a href="{{ $link['url'] }}" target="_blank"
                           class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                           title="Buka link">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <button type="button"
                                onclick="copyToClipboard('{{ $link['url'] }}', this)"
                                class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                                title="Salin link">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                        <form action="{{ route('persiapan.link.destroy', $link['id']) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button"
                                    onclick="askDelete(this, 'Hapus link \'{{ addslashes($link['name']) }}\'?')"
                                    class="p-1.5 rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <p class="text-sm">Belum ada link tersimpan</p>
            </div>
            @endif
        </div>

        {{-- Files Section --}}
        <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <div>
                    <h2 class="font-bold text-sm">File Tersimpan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">CV, cover letter, portofolio, sertifikat, dll. (maks. 10 MB)</p>
                </div>
                <button type="button" onclick="openModal('modal-upload-file')"
                        class="flex items-center gap-1.5 bg-black text-white px-3 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Upload File
                </button>
            </div>

            @if(count($files) > 0)
            @php
                $fileTypeMeta = [
                    'cv'           => ['label' => 'CV / Resume',     'bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'ext_bg' => 'bg-blue-100'],
                    'cover_letter' => ['label' => 'Cover Letter',    'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'ext_bg' => 'bg-indigo-100'],
                    'portfolio'    => ['label' => 'Portfolio',        'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'ext_bg' => 'bg-purple-100'],
                    'certificate'  => ['label' => 'Sertifikat',      'bg' => 'bg-green-50',  'text' => 'text-green-700',  'ext_bg' => 'bg-green-100'],
                    'other'        => ['label' => 'Lainnya',         'bg' => 'bg-gray-50',   'text' => 'text-gray-600',   'ext_bg' => 'bg-gray-100'],
                ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4">
                @foreach($files as $file)
                @php
                    $meta = $fileTypeMeta[$file['type']] ?? $fileTypeMeta['other'];
                    $ext  = strtoupper(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                    $sizeMb = $file['size'] > 1048576
                        ? round($file['size'] / 1048576, 1) . ' MB'
                        : round($file['size'] / 1024, 0) . ' KB';
                @endphp
                <div class="flex items-center gap-3 p-3.5 border border-gray-100 rounded-2xl hover:border-gray-200 transition-all group">
                    <div class="w-10 h-10 {{ $meta['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="text-[10px] font-black {{ $meta['text'] }}">{{ $ext }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate">{{ $file['name'] }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[10px] font-bold {{ $meta['text'] }} {{ $meta['bg'] }} px-1.5 py-0.5 rounded-full">{{ $meta['label'] }}</span>
                            <span class="text-[10px] text-gray-400">{{ $sizeMb }}</span>
                        </div>
                        @if($file['notes'])
                        <p class="text-[10px] text-gray-400 mt-0.5 truncate italic">{{ $file['notes'] }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('persiapan.file.download', $file['id']) }}"
                           class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                           title="Unduh">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                        <form action="{{ route('persiapan.file.destroy', $file['id']) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button"
                                    onclick="askDelete(this, 'Hapus file \'{{ addslashes($file['name']) }}\'?')"
                                    class="p-1.5 rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm">Belum ada file terupload</p>
            </div>
            @endif
        </div>

    </div>{{-- end tab-link --}}

    {{-- ============================================================ --}}
    {{-- TAB: Template Pesan                                          --}}
    {{-- ============================================================ --}}
    <div id="tab-template" class="tab-panel space-y-4" style="display:none">

        {{-- Add button --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Simpan template kata pengantar, body email, atau pesan LinkedIn yang sering kamu pakai.
            </p>
            <button type="button" onclick="openModal('modal-add-template')"
                    class="flex-shrink-0 flex items-center gap-1.5 bg-black text-white px-3 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Buat Template
            </button>
        </div>

        @if(count($templates) > 0)
        @php
            $catMeta = [
                'email'        => ['label' => 'Body Email',      'bg' => 'bg-blue-50',   'text' => 'text-blue-700'],
                'cover_letter' => ['label' => 'Cover Letter',    'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700'],
                'linkedin'     => ['label' => 'LinkedIn',        'bg' => 'bg-sky-50',    'text' => 'text-sky-700'],
                'whatsapp'     => ['label' => 'WhatsApp',        'bg' => 'bg-green-50',  'text' => 'text-green-700'],
                'other'        => ['label' => 'Lainnya',         'bg' => 'bg-gray-50',   'text' => 'text-gray-600'],
            ];
        @endphp
        <div class="space-y-3">
            @foreach($templates as $tpl)
            @php $cat = $catMeta[$tpl['category']] ?? $catMeta['other']; @endphp
            <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden" id="tpl-{{ $tpl['id'] }}">
                {{-- Header --}}
                <div class="flex items-start justify-between gap-3 px-5 py-4 cursor-pointer"
                     onclick="toggleTemplate('{{ $tpl['id'] }}')">
                    <div class="flex items-center gap-3 min-w-0">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-bold">{{ $tpl['title'] }}</p>
                                <span class="text-[10px] font-bold {{ $cat['text'] }} {{ $cat['bg'] }} px-2 py-0.5 rounded-full">{{ $cat['label'] }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Diperbarui {{ date('j M Y', strtotime($tpl['updated_at'])) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button"
                                onclick="event.stopPropagation(); copyTemplate('{{ addslashes(str_replace(["\r", "\n"], ['', '\n'], $tpl['content'])) }}')"
                                class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                                title="Salin ke clipboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                        <button type="button"
                                onclick="event.stopPropagation(); openEditTemplate('{{ $tpl['id'] }}', {{ json_encode($tpl) }})"
                                class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 hover:text-black transition-all"
                                title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('persiapan.template.destroy', $tpl['id']) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button"
                                    onclick="event.stopPropagation(); askDelete(this, 'Hapus template \'{{ addslashes($tpl['title']) }}\'?')"
                                    class="p-2 rounded-xl hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        <svg id="tpl-chevron-{{ $tpl['id'] }}" class="w-4 h-4 text-gray-400 transition-transform ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>

                {{-- Content (collapsible) --}}
                <div id="tpl-body-{{ $tpl['id'] }}" class="hidden border-t border-gray-50">
                    <div class="px-5 py-4">
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap font-sans leading-relaxed bg-gray-50 rounded-xl p-4">{{ $tpl['content'] }}</pre>
                    </div>
                    <div class="px-5 pb-4">
                        <button type="button"
                                onclick="copyTemplate('{{ addslashes(str_replace(["\r", "\n"], ['', '\n'], $tpl['content'])) }}')"
                                class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Salin Teks
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-2xl border border-gray-50 py-16 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <p class="font-bold text-sm">Belum ada template tersimpan</p>
            <p class="text-xs mt-1">Buat template kata pengantar, body email, atau pesan LinkedIn</p>
            <button type="button" onclick="openModal('modal-add-template')"
                    class="mt-4 bg-black text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                Buat Template Pertama
            </button>
        </div>
        @endif

    </div>{{-- end tab-template --}}

</div>{{-- end content --}}

{{-- ============================================================ --}}
{{-- MODALS                                                       --}}
{{-- ============================================================ --}}

{{-- Modal: Tambah Link --}}
<div id="modal-add-link" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this)closeModal('modal-add-link')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">Tambah Link</h2>
            <button type="button" onclick="closeModal('modal-add-link')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('persiapan.link.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Nama / Label *</label>
                <input type="text" name="name" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all"
                       placeholder="CV di Google Drive, Portfolio Website, dll." required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">URL *</label>
                <input type="url" name="url" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all"
                       placeholder="https://..." required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Kategori</label>
                <select name="type" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                    <option value="cv">CV / Resume</option>
                    <option value="portfolio">Portfolio</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="github">GitHub</option>
                    <option value="referral">Referral / Rekomendasi</option>
                    <option value="jobsite">Job Board</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Catatan</label>
                <input type="text" name="notes" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all"
                       placeholder="Contoh: versi terbaru per Mei 2025">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-add-link')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Upload File --}}
<div id="modal-upload-file" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this)closeModal('modal-upload-file')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">Upload File</h2>
            <button type="button" onclick="closeModal('modal-upload-file')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('persiapan.file.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            {{-- Drop zone --}}
            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center cursor-pointer hover:border-black hover:bg-gray-50 transition-all"
                 onclick="document.getElementById('file-input').click()">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <p class="text-sm font-bold text-gray-600" id="file-label">Klik untuk pilih file</p>
                <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, PNG — maks. 10 MB</p>
                <input type="file" id="file-input" name="file"
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                       class="hidden"
                       onchange="document.getElementById('file-label').textContent = this.files[0]?.name ?? 'Klik untuk pilih file'" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Nama Tampilan</label>
                <input type="text" name="name" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all"
                       placeholder="Kosongkan untuk pakai nama file asli">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Tipe File</label>
                <select name="type" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                    <option value="cv">CV / Resume</option>
                    <option value="cover_letter">Cover Letter</option>
                    <option value="portfolio">Portfolio</option>
                    <option value="certificate">Sertifikat</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Catatan</label>
                <input type="text" name="notes" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all"
                       placeholder="Contoh: versi terbaru, pakai untuk posisi senior">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-upload-file')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">Upload</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Buat Template --}}
<div id="modal-add-template" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this)closeModal('modal-add-template')">
    <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-50 flex-shrink-0">
            <h2 class="font-bold text-lg">Buat Template Baru</h2>
            <button type="button" onclick="closeModal('modal-add-template')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('persiapan.template.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Judul Template *</label>
                    <input type="text" name="title" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all"
                           placeholder="Email Lamaran — Formal" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Kategori</label>
                    <select name="category" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="email">Body Email</option>
                        <option value="cover_letter">Cover Letter</option>
                        <option value="linkedin">Pesan LinkedIn</option>
                        <option value="whatsapp">Pesan WhatsApp</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold text-gray-500">Isi Template *</label>
                    <span id="add-char-count" class="text-[10px] text-gray-400">0 karakter</span>
                </div>
                <textarea name="content" rows="12"
                          class="w-full px-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all resize-none font-mono leading-relaxed"
                          placeholder="Tulis template kata pengantar di sini...

Contoh:
Yth. Bapak/Ibu HRD [Nama Perusahaan],

Saya [Nama Anda], seorang [Profesi] dengan pengalaman [X] tahun...

Hormat saya,
[Nama Anda]"
                          oninput="document.getElementById('add-char-count').textContent = this.value.length + ' karakter'"
                          required></textarea>
            </div>
            <div class="flex gap-3 pt-2 flex-shrink-0">
                <button type="button" onclick="closeModal('modal-add-template')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">Simpan Template</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Template --}}
<div id="modal-edit-template" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this)closeModal('modal-edit-template')">
    <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-50 flex-shrink-0">
            <h2 class="font-bold text-lg">Edit Template</h2>
            <button type="button" onclick="closeModal('modal-edit-template')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-template-form" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Judul Template *</label>
                    <input type="text" id="edit-tpl-title" name="title" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Kategori</label>
                    <select id="edit-tpl-category" name="category" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="email">Body Email</option>
                        <option value="cover_letter">Cover Letter</option>
                        <option value="linkedin">Pesan LinkedIn</option>
                        <option value="whatsapp">Pesan WhatsApp</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold text-gray-500">Isi Template *</label>
                    <span id="edit-char-count" class="text-[10px] text-gray-400">0 karakter</span>
                </div>
                <textarea id="edit-tpl-content" name="content" rows="12"
                          class="w-full px-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all resize-none font-mono leading-relaxed"
                          oninput="document.getElementById('edit-char-count').textContent = this.value.length + ' karakter'"
                          required></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-template')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
/* ---------- Tab switching ---------- */
function switchTab(id) {
    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-white', 'shadow-sm', 'text-black');
        b.classList.add('text-gray-500');
    });
    document.getElementById('tab-' + id).style.display = '';
    const btn = document.getElementById('tab-btn-' + id);
    btn.classList.add('bg-white', 'shadow-sm', 'text-black');
    btn.classList.remove('text-gray-500');
    localStorage.setItem('persiapan_tab', id);
}

// Init tab on load
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('persiapan_tab') || 'link';
    switchTab(saved);
});

/* ---------- Modals ---------- */
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="modal-"]').forEach(m => m.classList.add('hidden'));
        document.body.style.overflow = '';
    }
});

/* ---------- Copy to clipboard ---------- */
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => btn.innerHTML = orig, 1500);
    });
}

function copyTemplate(escaped) {
    const text = escaped.replace(/\\n/g, '\n');
    navigator.clipboard.writeText(text).then(() => {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-24 md:bottom-6 left-1/2 -translate-x-1/2 bg-black text-white px-5 py-3 rounded-2xl text-sm font-bold shadow-xl z-50 animate-fade-in';
        toast.textContent = '✓ Template tersalin ke clipboard';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}

/* ---------- Template collapsible ---------- */
function toggleTemplate(id) {
    const body    = document.getElementById('tpl-body-' + id);
    const chevron = document.getElementById('tpl-chevron-' + id);
    if (body.classList.contains('hidden')) {
        body.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        body.classList.add('hidden');
        chevron.style.transform = '';
    }
}

/* ---------- Edit template modal ---------- */
function openEditTemplate(id, data) {
    document.getElementById('edit-template-form').action = '{{ url("/persiapan/template") }}/' + id;
    document.getElementById('edit-tpl-title').value    = data.title    || '';
    document.getElementById('edit-tpl-category').value = data.category || 'email';
    window.refreshSelect && window.refreshSelect('edit-tpl-category');
    document.getElementById('edit-tpl-content').value  = data.content  || '';
    document.getElementById('edit-char-count').textContent = (data.content || '').length + ' karakter';
    openModal('modal-edit-template');
}
</script>
@endpush
@endsection
