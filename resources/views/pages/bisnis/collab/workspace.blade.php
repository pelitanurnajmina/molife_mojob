@extends('layouts.app')
@section('title', $product->name . ' · ' . ($isOwner ? 'Folder Proyek' : 'Kolaborasi'))
@section('page-title', $product->name)
@section('breadcrumb', 'Bisnis › ' . ($isOwner ? 'Folder Proyek' : 'Kolaborasi') . ' › ' . $product->name)

@section('content')
@php $rp = fn($n) => 'Rp ' . number_format((int) $n, 0, ',', '.'); @endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Header proyek ── --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gray-50 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-2xl bg-gray-900 text-white flex items-center justify-center flex-shrink-0 text-lg font-black">
                {{ strtoupper(substr($product->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h2 class="font-bold text-lg text-gray-900 truncate">{{ $product->name }}</h2>
                <p class="text-xs text-gray-400">
                    {{ __('Workspace kolaborasi') }} · {{ __('milik') }} <span class="font-bold text-gray-500">{{ $ownerName }}</span>
                    @if(count($members))
                    · {{ count($members) }} {{ __('kolaborator') }}: {{ implode(', ', array_slice($members, 0, 4)) }}{{ count($members) > 4 ? '…' : '' }}
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0 self-start sm:self-auto">
            @if($isOwner)
            <button type="button" onclick="openModal('modal-invite')"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-black text-white text-xs font-bold hover:bg-gray-800 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                {{ __('Undang Partner') }}{{ count($collabRows) ? ' (' . count($collabRows) . ')' : '' }}
            </button>
            @endif
            <a href="{{ $isOwner ? route('bisnis.deals') : route('kolaborasi.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-500 hover:border-gray-400 hover:text-black transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                {{ $isOwner ? __('Ke Menu Bisnis') : __('Semua Kolaborasi') }}
            </a>
        </div>
    </div>

    {{-- ── Statistik proyek ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none">{{ $total }}</p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Total Proposal') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $thisMonthCount }} {{ __('bulan ini') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl md:text-3xl font-black text-gray-900 leading-none">{{ $winRate }}<span class="text-base text-gray-400">%</span></p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Win Rate') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $counts['won'] }} {{ __('deal dari') }} {{ $closed }} {{ __('closing') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="text-base md:text-lg font-black text-gray-900 leading-tight truncate">{{ $rp($pipelineValue) }}</p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Nilai Pipeline') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $active }} {{ __('proposal aktif') }}</p>
        </div>
        <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-5 border border-gray-50">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-base md:text-lg font-black text-gray-900 leading-tight truncate">{{ $rp($wonValue) }}</p>
            <p class="text-[11px] font-bold text-gray-500 mt-1.5">{{ __('Nilai Deal') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ __('dari proposal menang') }}</p>
        </div>
    </div>

    {{-- ── Pipeline per status ── --}}
    <div class="dash-card bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <h3 class="font-bold mb-4">{{ __('Pipeline') }} {{ $product->name }}</h3>
        <div class="space-y-3">
            @foreach($statuses as $key => $meta)
            @php $cnt = $counts[$key] ?? 0; $pct = $total > 0 ? round($cnt / $total * 100) : 0; @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-bold text-gray-700">{{ $meta['label'] }}</span>
                    <span class="text-xs font-bold text-gray-500">{{ $cnt }}</span>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" style="width:{{ $pct }}%;background:{{ $meta['hex'] }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Tab: Proposal & Klien / Template Pesan ── --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-2xl w-fit">
        <button type="button" id="tabBtnDeals" onclick="switchWsTab('deals')"
            class="ws-tab-btn px-5 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            {{ __('Proposal & Klien') }}
            <span class="text-[10px] font-bold opacity-50">{{ count($deals) }}</span>
        </button>
        <button type="button" id="tabBtnTpl" onclick="switchWsTab('tpl')"
            class="ws-tab-btn px-5 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
            {{ __('Template Pesan') }}
            <span class="text-[10px] font-bold opacity-50">{{ count($templates) }}</span>
        </button>
    </div>

    {{-- ── Proposal / klien ── --}}
    <div id="panelDeals" class="dash-card bg-white rounded-2xl md:rounded-3xl border border-gray-50 overflow-hidden">
        <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-50">
            <div>
                <h3 class="font-bold">{{ __('Proposal & Klien') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Semua proposal untuk proyek ini. Kolaborator bisa menambah & mengubah.') }}</p>
            </div>
            <button type="button" onclick="openModal('modal-add-deal')"
                class="px-4 py-2.5 rounded-xl bg-black text-white text-xs font-bold hover:bg-gray-800 transition-all flex-shrink-0">
                + {{ __('Tambah') }}
            </button>
        </div>
        @if(count($deals) === 0)
        <p class="text-center text-gray-400 text-sm py-10">{{ __('Belum ada proposal untuk proyek ini.') }}</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Klien') }}</th>
                        <th class="text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden md:table-cell">{{ __('Nilai') }}</th>
                        <th class="text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Status') }}</th>
                        <th class="text-left px-5 py-3.5 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden md:table-cell whitespace-nowrap">{{ __('Tanggal') }}</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deals as $d)
                    @php $meta = $statuses[$d['status']] ?? ['label' => $d['status'], 'tw' => 'gray']; @endphp
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-all">
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-gray-800">{{ $d['client_name'] }}</p>
                            @if($d['industry'])<p class="text-[11px] text-gray-400">{{ $d['industry'] }}</p>@endif
                        </td>
                        <td class="px-5 py-3.5 hidden md:table-cell font-bold text-gray-600 whitespace-nowrap">{{ $d['value'] ? $rp($d['value']) : '-' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-{{ $meta['tw'] }}-100 text-{{ $meta['tw'] }}-700">{{ $meta['label'] }}</span>
                        </td>
                        <td class="px-5 py-3.5 hidden md:table-cell text-gray-500 whitespace-nowrap">{{ $d['proposal_date'] ?: '-' }}</td>
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <button type="button" onclick='openEditDeal(@json($d))' class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('kolaborasi.deal.destroy', [$product->id, $d['id']]) }}" class="inline m-0">
                                @csrf @method('DELETE')
                                <button type="button" onclick="askDelete(this, '{{ __('Hapus proposal ini?') }}')" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-gray-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Template pesan (accordion, isi penuh saat diklik) ── --}}
    <div id="panelTpl" class="hidden space-y-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h3 class="font-bold">{{ __('Template Pesan') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Template email/WA/penawaran khusus proyek ini. Klik untuk lihat isi lengkap.') }}</p>
            </div>
            <button type="button" onclick="openTplModal()"
                class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-black text-white text-xs font-bold hover:bg-gray-800 transition-all flex-shrink-0">
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
        @if(count($templates) === 0)
        <div class="bg-white rounded-2xl border border-gray-50 py-16 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="font-bold text-sm">{{ __('Belum ada template untuk proyek ini.') }}</p>
            <p class="text-xs mt-1">{{ __('Buat template email penawaran atau pesan WhatsApp') }}</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($templates as $t)
            @php $cm = $catMeta[$t['category']] ?? $catMeta['lainnya']; $catLabel = $tplCategories[$t['category']] ?? ($t['category'] ?: 'Lainnya'); @endphp
            <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden">
                {{-- Header (clickable) --}}
                <div class="flex items-start justify-between gap-3 px-5 py-4 cursor-pointer" onclick="toggleWsTpl('{{ $t['id'] }}')">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-bold">{{ $t['title'] }}</p>
                            <span class="text-[10px] font-bold {{ $cm['text'] }} {{ $cm['bg'] }} px-2 py-0.5 rounded-full">{{ $catLabel }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ __('Diperbarui') }} {{ date('j M Y', strtotime($t['updated_at'])) }}</p>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" onclick='event.stopPropagation(); copyTpl(this, @json($t['content']))'
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Salin') }}">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                        <button type="button" onclick='event.stopPropagation(); openTplModal(@json($t))'
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-black transition-all" title="{{ __('Edit') }}">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form method="POST" action="{{ route('kolaborasi.template.destroy', [$product->id, $t['id']]) }}" class="contents">
                            @csrf @method('DELETE')
                            <button type="button" onclick="event.stopPropagation(); askDelete(this, '{{ __('Hapus template ini?') }}')"
                                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all" title="{{ __('Hapus') }}">
                                <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        <svg id="ws-tpl-chevron-{{ $t['id'] }}" class="w-4 h-4 text-gray-400 transition-transform ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>
                {{-- Body (collapsible) --}}
                <div id="ws-tpl-body-{{ $t['id'] }}" class="hidden border-t border-gray-50">
                    <div class="px-5 py-4">
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap font-sans leading-relaxed bg-gray-50 rounded-xl p-4">{{ $t['content'] }}</pre>
                    </div>
                    <div class="px-5 pb-4">
                        <button type="button" onclick='copyTpl(this, @json($t['content']))'
                            class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            {{ __('Salin Teks') }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- ── Modal undang partner (khusus owner) ── --}}
@if($isOwner)
<div id="modal-invite" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-invite')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg">{{ __('Undang Partner') }} · {{ $product->name }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('Bisa kelola proposal & template proyek ini, plus lihat statistiknya. Tanpa perlu langganan.') }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-invite')" class="text-gray-400 hover:text-black flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6">
            <div class="space-y-2 mb-4 max-h-56 overflow-y-auto">
                @forelse($collabRows as $c)
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
                <p class="text-center text-gray-400 text-sm py-4">{{ __('Belum ada partner. Undang lewat email di bawah.') }}</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('bisnis.collab.invite', $product->id) }}" class="flex items-center gap-2 border-t border-gray-50 pt-4">
                @csrf
                <input type="email" name="email" maxlength="255" required placeholder="{{ __('email@rekan-kamu.com') }}"
                    class="flex-1 min-w-0 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all flex-shrink-0">{{ __('Undang') }}</button>
            </form>
            @error('email')
            <p class="text-xs font-bold text-red-500 mt-2">{{ $message }}</p>
            @enderror
            <p class="text-[10px] text-gray-400 mt-2">{{ __('Mereka akan menerima email berisi link untuk bergabung.') }}</p>
        </div>
    </div>
</div>
@endif

{{-- ── Modal tambah proposal ── --}}
<div id="modal-add-deal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-add-deal')">
    <div class="bg-white rounded-3xl w-full max-w-md my-8">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Tambah Proposal') }} · {{ $product->name }}</h2>
            <button type="button" onclick="closeModal('modal-add-deal')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('kolaborasi.deal.store', $product->id) }}" class="p-6">
            @csrf
            @include('pages.bisnis.collab._deal_fields', ['statuses' => $statuses])
            <button type="submit" class="w-full mt-2 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Simpan') }}</button>
        </form>
    </div>
</div>

{{-- ── Modal edit proposal ── --}}
<div id="modal-edit-deal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-edit-deal')">
    <div class="bg-white rounded-3xl w-full max-w-md my-8">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Ubah Proposal') }}</h2>
            <button type="button" onclick="closeModal('modal-edit-deal')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="" id="editDealForm" class="p-6">
            @csrf
            @include('pages.bisnis.collab._deal_fields', ['statuses' => $statuses])
            <button type="submit" class="w-full mt-2 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Perbarui') }}</button>
        </form>
    </div>
</div>

{{-- ── Modal template (tambah/edit) ── --}}
<div id="modal-tpl" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('modal-tpl')">
    <div class="bg-white rounded-3xl w-full max-w-md my-8">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg" id="tplModalTitle">{{ __('Template Baru') }}</h2>
            <button type="button" onclick="closeModal('modal-tpl')" class="text-gray-400 hover:text-black"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="" id="tplForm" class="p-6 space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Judul') }} <span class="text-red-400">*</span></label>
                <input type="text" name="title" maxlength="255" required placeholder="{{ __('cth: Penawaran awal WA') }}"
                    class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Kategori') }}</label>
                <select name="category" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                    @foreach($tplCategories as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Isi Template') }} <span class="text-red-400">*</span></label>
                <textarea name="content" rows="7" required placeholder="{{ __('Halo kak, perkenalkan saya dari...') }}"
                    class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-black focus:bg-white resize-none transition-all"></textarea>
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all" id="tplSubmit">{{ __('Simpan') }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id){ document.getElementById(id).classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeModal(id){ document.getElementById(id).classList.add('hidden'); document.body.style.overflow=''; }

/* ── Tab Proposal / Template (pilihan diingat per proyek) ── */
var WS_TAB_KEY = 'wsTab:{{ $product->id }}';
function switchWsTab(tab){
    document.getElementById('panelDeals').classList.toggle('hidden', tab !== 'deals');
    document.getElementById('panelTpl').classList.toggle('hidden', tab !== 'tpl');
    document.querySelectorAll('.ws-tab-btn').forEach(b => { b.classList.remove('bg-white', 'shadow-sm', 'text-black'); b.classList.add('text-gray-500'); });
    const btn = document.getElementById(tab === 'tpl' ? 'tabBtnTpl' : 'tabBtnDeals');
    btn.classList.add('bg-white', 'shadow-sm', 'text-black'); btn.classList.remove('text-gray-500');
    try { localStorage.setItem(WS_TAB_KEY, tab); } catch(e) {}
}
switchWsTab((() => { try { return localStorage.getItem(WS_TAB_KEY) === 'tpl' ? 'tpl' : 'deals'; } catch(e) { return 'deals'; } })());

function openEditDeal(d){
    const f = document.getElementById('editDealForm');
    f.action = '{{ url('kolaborasi/' . $product->id . '/proposal') }}/' + d.id;
    ['client_name','industry','address','contact','notes'].forEach(k => f.querySelector('[name="'+k+'"]').value = d[k] ?? '');
    f.querySelector('[name="value"]').value = d.value || '';
    f.querySelector('[name="status"]').value = d.status ?? 'lead';
    f.querySelector('[name="proposal_date"]').value = d.proposal_date ?? '';
    const st = f.querySelector('[name="status"]');
    if (st._csRefresh) st._csRefresh();
    openModal('modal-edit-deal');
}

function openTplModal(t){
    const f = document.getElementById('tplForm');
    const isEdit = !!t;
    document.getElementById('tplModalTitle').textContent = isEdit ? '{{ __('Ubah Template') }}' : '{{ __('Template Baru') }}';
    document.getElementById('tplSubmit').textContent = isEdit ? '{{ __('Perbarui') }}' : '{{ __('Simpan') }}';
    f.action = isEdit
        ? '{{ url('kolaborasi/' . $product->id . '/template') }}/' + t.id
        : '{{ route('kolaborasi.template.store', $product->id) }}';
    f.querySelector('[name="title"]').value = t?.title ?? '';
    f.querySelector('[name="content"]').value = t?.content ?? '';
    const cat = f.querySelector('[name="category"]');
    cat.value = t?.category ?? 'whatsapp';
    if (cat._csRefresh) cat._csRefresh();
    openModal('modal-tpl');
}

function toggleWsTpl(id){
    const body = document.getElementById('ws-tpl-body-' + id);
    const chev = document.getElementById('ws-tpl-chevron-' + id);
    const nowHidden = body.classList.toggle('hidden');
    if (chev) chev.style.transform = nowHidden ? '' : 'rotate(180deg)';
}

function copyTpl(btn, text){
    navigator.clipboard.writeText(text).then(() => {
        if (window.showMojobToast) showMojobToast('{{ __('Template disalin.') }}');
    });
}
</script>
@endpush
