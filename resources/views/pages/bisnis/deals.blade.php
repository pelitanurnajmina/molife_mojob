@extends('layouts.app')
@section('title', 'Proposal & Klien')
@section('page-title', __('Proposal & Klien'))
@section('breadcrumb', 'Bisnis › Proposal & Klien')

@section('content')
@php $rp = fn($n) => 'Rp ' . number_format((int) $n, 0, ',', '.'); @endphp
<div class="space-y-4 md:space-y-6">

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('bisnis.deals') }}" class="flex items-center gap-2 flex-1">
            <input type="hidden" name="status" value="{{ $filterStatus }}">
            <div class="relative flex-1 max-w-xs">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari klien / proyek / bidang...') }}"
                    class="w-full pl-9 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="w-44">
                <select name="product" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                    <option value="all" {{ $filterProduct === 'all' ? 'selected' : '' }}>{{ __('Semua proyek') }}</option>
                    @foreach($products as $p)
                    <option value="{{ $p }}" {{ $filterProduct === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button type="button" onclick="openModal('modal-impor')" title="{{ __('Impor / Ekspor Data') }}"
                class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/></svg>
                <span class="hidden lg:inline">{{ __('Impor') }}</span>
            </button>
            <button type="button" onclick="openModal('modal-products')"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                {{ __('Kelola Proyek') }}
            </button>
            <button type="button" onclick="openModal('modal-add')"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Tambah Proposal') }}
            </button>
        </div>
    </div>

    {{-- ── Folder proyek (kolaborasi per proyek) ── --}}
    @if(count($productRows))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($productRows as $pr)
        @php
            $nDeals  = (int) ($dealsPerProduct[$pr->name] ?? 0);
            $nCollab = $pr->collaborators->count();
        @endphp
        <div onclick="location.href='{{ route('kolaborasi.workspace', $pr->id) }}'"
            class="cursor-pointer bg-white rounded-2xl border border-gray-100 p-4 transition-all hover:border-gray-900 hover:shadow-sm group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-50 text-gray-500 border border-gray-100 group-hover:bg-gray-900 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $pr->name }}</p>
                    <p class="text-[11px] text-gray-400">{{ $nDeals }} {{ __('proposal') }} · {{ $nCollab }} {{ __('kolaborator') }}</p>
                </div>
                <button type="button" onclick="event.stopPropagation(); openModal('modal-collab-{{ $pr->id }}')" title="{{ __('Undang kolaborator') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl {{ $nCollab ? 'bg-gray-50 border border-gray-200 text-gray-600' : 'bg-black text-white' }} text-[11px] font-bold hover:opacity-80 transition-all flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    {{ __('Kolaborator') }}
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Status filter chips --}}
    <div class="flex flex-wrap gap-2">
        @php $chip = fn($active) => $active ? 'bg-black text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-300'; @endphp
        <a href="{{ route('bisnis.deals', ['status' => 'all', 'product' => $filterProduct, 'q' => request('q')]) }}"
            class="text-xs font-bold px-3.5 py-2 rounded-xl transition-all {{ $chip($filterStatus === 'all') }}">{{ __('Semua') }} ({{ $total }})</a>
        @foreach($statuses as $key => $meta)
        <a href="{{ route('bisnis.deals', ['status' => $key, 'product' => $filterProduct, 'q' => request('q')]) }}"
            class="text-xs font-bold px-3.5 py-2 rounded-xl transition-all {{ $chip($filterStatus === $key) }}">{{ $meta['label'] }} ({{ $counts[$key] ?? 0 }})</a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl md:rounded-3xl border border-gray-50 overflow-hidden">
        @if(count($deals) === 0)
        <div class="text-center py-14 px-4">
            <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">{{ __('Belum ada proposal') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Klik "Tambah Proposal" untuk mulai mencatat.') }}</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="w-full text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Klien') }}</th>
                        <th class="text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden md:table-cell whitespace-nowrap">{{ __('Proyek') }}</th>
                        <th class="text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden lg:table-cell whitespace-nowrap">{{ __('Bidang') }}</th>
                        <th class="text-right px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400 whitespace-nowrap">{{ __('Nilai') }}</th>
                        <th class="text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400 whitespace-nowrap">{{ __('Status') }}</th>
                        <th class="text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden xl:table-cell whitespace-nowrap">{{ __('Tanggal') }}</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deals as $d)
                    @php
                        $m = $statuses[$d['status']] ?? ['label'=>$d['status'],'tw'=>'gray'];
                        $contact = trim((string) $d['contact']);
                        $hasContact = $contact !== '' && !in_array($contact, ['-', '—']);
                    @endphp
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-all align-middle">
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-800">{{ $d['client_name'] }}</p>
                            @if($hasContact)<p class="text-xs text-gray-400 mt-0.5">{{ $contact }}</p>@endif
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-gray-600 whitespace-nowrap">{{ $d['product'] ?: '—' }}</td>
                        <td class="px-5 py-4 hidden lg:table-cell text-gray-500 whitespace-nowrap">{{ $d['industry'] ?: '—' }}</td>
                        <td class="px-5 py-4 text-right font-bold text-gray-700 whitespace-nowrap">{{ $d['value'] > 0 ? $rp($d['value']) : '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-block whitespace-nowrap text-[10px] font-bold px-2.5 py-1 rounded-full bg-{{ $m['tw'] }}-100 text-{{ $m['tw'] }}-700">{{ $m['label'] }}</span>
                        </td>
                        <td class="px-5 py-4 hidden xl:table-cell text-gray-500 whitespace-nowrap">{{ $d['proposal_date'] ? date('j M Y', strtotime($d['proposal_date'])) : '—' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" onclick='openView(@json($d))' title="{{ __('Lihat detail') }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button type="button" onclick='openEdit(@json($d))'
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('bisnis.destroy', $d['id']) }}" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="askDelete(this, '{{ __('Hapus proposal/klien ini?') }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ── Add / Edit modal ── --}}
<div id="modal-add" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-add')">
    <div class="bg-white rounded-3xl w-full max-w-lg my-8">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Tambah Proposal') }}</h2>
            <button type="button" onclick="closeModal('modal-add')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('bisnis.store') }}" class="p-6">
            @csrf
            @include('pages.bisnis._form', ['statuses' => $statuses, 'd' => null])
            <button type="submit" class="w-full mt-2 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Simpan') }}</button>
        </form>
    </div>
</div>

<div id="modal-edit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-edit')">
    <div class="bg-white rounded-3xl w-full max-w-lg my-8">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Edit Proposal') }}</h2>
            <button type="button" onclick="closeModal('modal-edit')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="" id="editForm" class="p-6">
            @csrf
            @include('pages.bisnis._form', ['statuses' => $statuses, 'd' => null, 'edit' => true])
            <button type="submit" class="w-full mt-2 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Perbarui') }}</button>
        </form>
    </div>
</div>

{{-- Manage products modal --}}
<div id="modal-products" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-products')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg">{{ __('Kelola Proyek') }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Proyek/jasa yang kamu tawarkan ke klien.') }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-products')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6">
            <div class="space-y-2 mb-4">
                @forelse($productRows as $pr)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0 text-xs font-bold">{{ strtoupper(substr($pr->name, 0, 1)) }}</div>
                    <span class="flex-1 text-sm font-bold text-gray-800 truncate">{{ $pr->name }}</span>
                    <a href="{{ route('kolaborasi.workspace', $pr->id) }}" title="{{ __('Buka folder proyek (kolaborasi, template, statistik)') }}"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-black hover:bg-gray-200 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('bisnis.product.destroy', $pr->id) }}" class="m-0">
                        @csrf @method('DELETE')
                        <button type="button" onclick="askDelete(this, '{{ __('Hapus proyek ini? Proposal lama tidak terpengaruh.') }}')" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-gray-200 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm py-4">{{ __('Belum ada proyek.') }}</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('bisnis.product.store') }}" class="flex items-center gap-2 border-t border-gray-50 pt-4">
                @csrf
                <input type="text" name="name" maxlength="100" required placeholder="{{ __('cth: Camemo, Molife, Hastacode...') }}"
                    class="flex-1 min-w-0 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all flex-shrink-0">{{ __('Tambah') }}</button>
            </form>
        </div>
    </div>
</div>

{{-- Modal impor / ekspor --}}
<div id="modal-impor" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-impor')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-start justify-between gap-3 p-6 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg leading-tight">{{ __('Impor / Ekspor Data') }}</h2>
                <p class="text-xs text-gray-400 mt-1">{{ __('Pindahkan data proposal & klien lamamu ke molife, atau unduh datamu.') }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-impor')" class="w-8 h-8 -mr-1.5 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 space-y-5">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">{{ __('Impor dari file') }}</p>
                <p class="text-xs text-gray-400 mb-3">{{ __('Format CSV atau Excel (.xlsx), maksimal 500 baris. Kolom: Nama Klien, Bidang, Alamat, Kontak, Channel, Proyek, Nilai, Status, Tanggal Proposal, Catatan. Proyek baru dibuat otomatis.') }}</p>
                <a href="{{ route('bisnis.import.template') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-black transition-all mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    {{ __('Unduh file contoh') }}
                </a>
                <form method="POST" action="{{ route('bisnis.import') }}" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <label class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:border-gray-300 transition-all">
                        <span class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-700 flex-shrink-0">{{ __('Pilih File') }}</span>
                        <span class="fileName text-xs text-gray-400 truncate flex-1">{{ __('Belum ada file dipilih') }}</span>
                        <input type="file" name="file" accept=".csv,.xlsx,text/csv" required class="hidden"
                            onchange="this.closest('label').querySelector('.fileName').textContent = this.files[0] ? this.files[0].name : '{{ __('Belum ada file dipilih') }}'">
                    </label>
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-black text-white text-xs font-bold hover:bg-gray-800 transition-all">{{ __('Impor Sekarang') }}</button>
                </form>
                @error('file')
                <p class="text-xs font-bold text-red-500 mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="border-t border-gray-50 pt-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">{{ __('Ekspor') }}</p>
                <a href="{{ route('bisnis.export') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:border-gray-400 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    {{ __('Unduh semua data (CSV)') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal lihat detail (read-only) ── --}}
<div id="modal-view" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-view')">
    <div class="bg-white rounded-3xl w-full max-w-md my-8">
        <div class="flex items-start justify-between gap-3 px-6 pt-6 pb-4 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg leading-tight" id="viewClient">—</h2>
                <p class="text-xs text-gray-400 mt-1">{{ __('Detail proposal & klien') }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-view')" class="w-8 h-8 -mr-1.5 -mt-1 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="px-6 pt-5 pb-6">
            <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Status') }}</p>
                    <span id="viewStatus" class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">—</span>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Nilai Proposal') }}</p>
                    <p id="viewValue" class="text-sm font-bold text-gray-800">—</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Proyek Kita') }}</p>
                    <p id="viewProduct" class="text-sm font-medium text-gray-700">—</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Bidang Klien') }}</p>
                    <p id="viewIndustry" class="text-sm font-medium text-gray-700">—</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Awal Komunikasi') }}</p>
                    <p id="viewChannel" class="text-sm font-medium text-gray-700">—</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Tanggal Proposal') }}</p>
                    <p id="viewDate" class="text-sm font-medium text-gray-700">—</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Narahubung') }}</p>
                    <p id="viewContact" class="text-sm font-medium text-gray-700 break-words">—</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Alamat Klien') }}</p>
                    <p id="viewAddress" class="text-sm font-medium text-gray-700 break-words">—</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50">
                <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-1">{{ __('Respon Klien / Catatan') }}</p>
                <p id="viewNotes" class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">—</p>
            </div>
            <button type="button" onclick="closeModal('modal-view'); openEdit(viewCurrent)"
                class="w-full mt-6 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-bold text-gray-700 hover:border-gray-400 transition-all">{{ __('Edit Data Ini') }}</button>
        </div>
    </div>
</div>

{{-- Modal kolaborator per proyek --}}
@foreach($productRows as $pr)
<div id="modal-collab-{{ $pr->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-collab-{{ $pr->id }}')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg">{{ __('Kolaborator') }} · {{ $pr->name }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Bisa kelola proposal & template proyek ini, plus lihat statistiknya. Tanpa perlu langganan.') }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-collab-{{ $pr->id }}')" class="text-gray-400 hover:text-black flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6">
            <div class="space-y-2 mb-4 max-h-56 overflow-y-auto">
                @forelse($pr->collaborators as $c)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0 text-xs font-bold">{{ strtoupper(substr($c->email, 0, 1)) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ $c->email }}</p>
                        @if($c->status === 'active')
                        <p class="text-[10px] font-bold text-green-600">{{ __('Aktif') }}{{ $c->accepted_at ? ' · ' . $c->accepted_at->translatedFormat('j M Y') : '' }}</p>
                        @else
                        <p class="text-[10px] font-bold text-amber-600">{{ __('Menunggu diterima') }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('bisnis.collab.remove', $c->id) }}" class="m-0">
                        @csrf @method('DELETE')
                        <button type="button" onclick="askDelete(this, '{{ __('Hapus kolaborator ini? Dia langsung kehilangan akses ke proyek ini.') }}')"
                            class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-gray-200 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm py-4">{{ __('Belum ada kolaborator. Undang lewat email di bawah.') }}</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('bisnis.collab.invite', $pr->id) }}" class="flex items-center gap-2 border-t border-gray-50 pt-4">
                @csrf
                <input type="email" name="email" maxlength="255" required placeholder="{{ __('email@rekan-kamu.com') }}"
                    class="flex-1 min-w-0 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all flex-shrink-0">{{ __('Undang') }}</button>
            </form>
            <div class="flex items-center justify-between mt-3">
                <p class="text-[10px] text-gray-400">{{ __('Mereka menerima email berisi link bergabung.') }}</p>
                <a href="{{ route('kolaborasi.workspace', $pr->id) }}" class="text-[10px] font-bold text-gray-500 hover:text-black transition-all whitespace-nowrap">{{ __('Buka workspace') }} ›</a>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
function openModal(id){ document.getElementById(id).classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeModal(id){ document.getElementById(id).classList.add('hidden'); document.body.style.overflow=''; }
function openEdit(d){
    const f = document.getElementById('editForm');
    f.action = '{{ url('bisnis/proposal') }}/' + d.id;
    f.querySelector('[name="client_name"]').value = d.client_name ?? '';
    f.querySelector('[name="industry"]').value = d.industry ?? '';
    f.querySelector('[name="address"]').value = d.address ?? '';
    f.querySelector('[name="contact"]').value = d.contact ?? '';
    f.querySelector('[name="value"]').value = d.value || '';
    f.querySelector('[name="status"]').value = d.status ?? 'lead';
    // Kolom tanggal dikelola flatpickr: set lewat instance-nya agar tampilan ikut terisi.
    const pd = f.querySelector('[name="proposal_date"]');
    if (pd._flatpickr) pd._flatpickr.setDate(d.proposal_date || null, false); else pd.value = d.proposal_date ?? '';
    f.querySelector('[name="notes"]').value = d.notes ?? '';
    const ch = f.querySelector('[name="channel"]');
    ch.value = d.channel ?? '';
    if (ch._csRefresh) ch._csRefresh();
    // product: ensure the saved value exists as an option even if deleted later
    const ps = f.querySelector('[name="product"]');
    if (d.product && ![...ps.options].some(o => o.value === d.product)) {
        ps.add(new Option(d.product, d.product));
    }
    ps.value = d.product ?? '';
    if (f.querySelector('[name="status"]')._csRefresh) f.querySelector('[name="status"]')._csRefresh();
    if (ps._csRefresh) ps._csRefresh();
    openModal('modal-edit');
}

/* ── Mode lihat (read-only) ── */
var DEAL_STATUSES = @json($statuses);
var DEAL_CHANNELS = @json(array_map(fn($l) => __($l), \App\Services\BusinessService::CHANNELS));
var viewCurrent = null;

function openView(d){
    viewCurrent = d;
    const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
    const set = (id, v) => document.getElementById(id).textContent = (v === null || v === undefined || String(v).trim() === '') ? '—' : v;

    set('viewClient', d.client_name);
    set('viewValue', d.value > 0 ? rp(d.value) : null);
    set('viewProduct', d.product);
    set('viewIndustry', d.industry);
    set('viewChannel', d.channel ? (DEAL_CHANNELS[d.channel] ?? d.channel) : null);
    set('viewDate', d.proposal_date ? new Date(d.proposal_date + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : null);
    set('viewContact', d.contact);
    set('viewAddress', d.address);
    set('viewNotes', d.notes);

    const m = DEAL_STATUSES[d.status] || { label: d.status, tw: 'gray' };
    const badge = document.getElementById('viewStatus');
    badge.textContent = m.label;
    badge.className = 'inline-block text-[10px] font-bold px-2.5 py-1 rounded-full bg-' + m.tw + '-100 text-' + m.tw + '-700';

    openModal('modal-view');
}

</script>
@endpush
@endsection
