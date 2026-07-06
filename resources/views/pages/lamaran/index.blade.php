@extends('layouts.app')
@section('title', __('Lamaran Kerja'))
@section('page-title', __('Lamaran Kerja'))
@section('breadcrumb', 'Lamaran')

@section('content')
<div class="space-y-6">

    {{-- ── Plan limit banner (Freemium only) ── --}}
    @if($isFreemium && $lamaranLimit)
    @php
        $used = $total;
        $remaining = max(0, $lamaranLimit - $used);
    @endphp
    @if($atLimit)
    <x-upgrade-banner
        title="{{ __('Sudah :n/:max lamaran', ['n' => $used, 'max' => $lamaranLimit]) }}"
        message="{{ __('Upgrade ke Plus untuk lamaran tanpa batas dan fitur lainnya.') }}"
        cta="{{ __('Upgrade ke Plus') }}"
        variant="warning" />
    @elseif($remaining <= 3)
    <x-upgrade-banner
        title="{{ __('Tersisa :n lamaran lagi', ['n' => $remaining]) }}"
        message="{{ __('Paket Freemium dibatasi :max lamaran. Upgrade ke Plus untuk tanpa batas.', ['max' => $lamaranLimit]) }}"
        cta="{{ __('Lihat Plan') }}" />
    @endif
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statCards = [
                ['label' => 'Total',           'value' => $total,                                    'bg' => 'bg-gray-50',   'text' => 'text-gray-800'],
                ['label' => __('Aktif'),       'value' => $active,                                   'bg' => 'bg-blue-50',   'text' => 'text-blue-800'],
                ['label' => __('Wawancara'),   'value' => $counts['interview'],                      'bg' => 'bg-indigo-50', 'text' => 'text-indigo-800'],
                ['label' => __('Tawaran'),     'value' => $counts['offer'] + $counts['hired'],        'bg' => 'bg-green-50',  'text' => 'text-green-800'],
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-2xl p-4 border border-gray-50">
            <p class="text-xs text-gray-500 font-medium">{{ $card['label'] }}</p>
            <p class="text-3xl font-bold mt-1">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter + Add --}}
    <div class="flex flex-wrap items-center gap-2">
        @php
            $filters = [
                'all'       => ['label' => __('Semua'),     'count' => $total],
                'wishlist'  => ['label' => 'Wishlist',      'count' => $counts['wishlist'] ?? 0],
                'applied'   => ['label' => __('Dikirim'),   'count' => $counts['applied']],
                'review'    => ['label' => 'Review',        'count' => $counts['review']],
                'interview' => ['label' => 'Interview',     'count' => $counts['interview']],
                'offer'     => ['label' => __('Tawaran'),   'count' => $counts['offer']],
                'hired'     => ['label' => __('Diterima'),  'count' => $counts['hired']],
                'rejected'  => ['label' => __('Ditolak'),   'count' => $counts['rejected']],
            ];
        @endphp
        @foreach($filters as $key => $f)
        <a href="{{ route('lamaran.index', ['status' => $key]) }}"
           class="px-3 py-1.5 rounded-full text-xs font-bold border transition-all
                  {{ $filterStatus === $key ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
            {{ $f['label'] }}
            <span class="ml-1 {{ $filterStatus === $key ? 'opacity-60' : 'text-gray-400' }}">{{ $f['count'] }}</span>
        </a>
        @endforeach

        <div class="flex-1"></div>

        <button type="button" onclick="openModal('modal-impor-lamaran')" title="{{ __('Impor dari CSV / Excel') }}"
                class="hidden md:flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V7a3 3 0 013-3h10a3 3 0 013 3v1m-4 4l-4-4m0 0l-4 4m4-4v12"/></svg>
            {{ __('Impor') }}
        </button>

        @php $_isPro = \App\Support\Profile::isPro(); @endphp
        <a href="{{ $_isPro ? route('lamaran.export') : route('settings.langganan') }}"
           class="hidden md:flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all
                  {{ $_isPro ? 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' : 'bg-gray-50 border border-gray-100 text-gray-400 hover:bg-gray-100' }}"
           title="{{ $_isPro ? '' : __('Hanya tersedia di paket Pro') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            {{ __('Ekspor CSV') }}
            @if(!$_isPro)<span class="text-[9px] bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded-full">PRO</span>@endif
        </a>
        @if($atLimit)
        <a href="{{ route('settings.langganan') }}"
           class="flex items-center gap-2 bg-orange-500 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-600 transition-all"
           title="{{ __('Sudah mencapai batas, upgrade ke Plus') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            {{ __('Upgrade ke Plus') }}
        </a>
        @else
        <button type="button" onclick="openModal('modal-add')"
                class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('Tambah Lamaran') }}
        </button>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden">
        @if(count($apps) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Perusahaan') }}</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">{{ __('Posisi') }}</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden lg:table-cell">{{ __('Tipe Pekerjaan') }}</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden lg:table-cell">{{ __('Channel') }}</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden md:table-cell">{{ __('Lokasi') }}</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden xl:table-cell">{{ __('Tanggal') }}</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $typeLabels = [
                            'fulltime'   => __('Full-time'),
                            'parttime'   => __('Part-time'),
                            'internship' => __('Internship'),
                            'freelance'  => __('Freelance'),
                            'contract'   => __('Kontrak'),
                        ];
                        $typeColors = [
                            'fulltime'   => 'bg-blue-50 text-blue-600',
                            'parttime'   => 'bg-indigo-50 text-indigo-600',
                            'internship' => 'bg-purple-50 text-purple-600',
                            'freelance'  => 'bg-amber-50 text-amber-600',
                            'contract'   => 'bg-gray-100 text-gray-600',
                        ];
                        $channelMeta = [
                            'linkedin'    => ['label' => 'LinkedIn',                    'color' => 'bg-blue-600 text-white'],
                            'jobstreet'   => ['label' => 'Jobstreet',                   'color' => 'bg-orange-500 text-white'],
                            'glints'      => ['label' => 'Glints',                      'color' => 'bg-teal-500 text-white'],
                            'upwork'      => ['label' => 'Upwork',                      'color' => 'bg-green-600 text-white'],
                            'fiverr'      => ['label' => 'Fiverr',                      'color' => 'bg-emerald-500 text-white'],
                            'kontrakhub'  => ['label' => 'Kontrakhub',                  'color' => 'bg-violet-600 text-white'],
                            'email'       => ['label' => __('Email'),                   'color' => 'bg-gray-600 text-white'],
                            'referral'    => ['label' => __('Referral'),                'color' => 'bg-amber-500 text-white'],
                            'website'     => ['label' => __('Website'),                 'color' => 'bg-slate-500 text-white'],
                            'other'       => ['label' => __('Lainnya'),                 'color' => 'bg-gray-400 text-white'],
                        ];
                    @endphp
                    @foreach($apps as $app)
                    @php
                        $statusLabels = [
                            'wishlist'  => 'Wishlist',
                            'applied'   => __('Dikirim'),
                            'review'    => 'Review',
                            'interview' => 'Interview',
                            'offer'     => __('Tawaran'),
                            'hired'     => __('Diterima'),
                            'rejected'  => __('Ditolak'),
                        ];
                        $jt = $app['job_type'] ?? '';
                        $ch = $app['channel'] ?? '';
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                                    {{ strtoupper(substr($app['company'], 0, 2)) }}
                                </div>
                                <span class="font-semibold text-sm">{{ $app['company'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $app['position'] }}</td>
                        <td class="px-4 py-4 hidden lg:table-cell">
                            @if($jt)
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $typeColors[$jt] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $typeLabels[$jt] ?? $jt }}
                            </span>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 hidden lg:table-cell whitespace-nowrap">
                            @if($ch)
                            <span class="inline-block whitespace-nowrap text-[10px] font-bold px-2 py-1 rounded-full {{ $channelMeta[$ch]['color'] ?? 'bg-gray-400 text-white' }}">
                                {{ $channelMeta[$ch]['label'] ?? $ch }}
                            </span>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-gray-400 text-xs hidden md:table-cell">{{ $app['location'] ?? '—' }}</td>
                        <td class="px-4 py-4 text-gray-400 text-xs hidden xl:table-cell">
                            {{ $app['applied_date'] ? date('j M Y', strtotime($app['applied_date'])) : '—' }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold pill-{{ $app['status'] ?? 'applied' }}">
                                {{ $statusLabels[$app['status']] ?? $app['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="flex items-center gap-1 justify-end">
                                @if(!empty($app['job_url']))
                                <a href="{{ $app['job_url'] }}" target="_blank"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-all"
                                   title="{{ __('Buka lowongan') }}">
                                    <svg class="w-3.5 h-3.5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                @endif
                                <button type="button"
                                        onclick="openEdit('{{ $app['id'] }}', {{ json_encode($app) }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-all"
                                        title="Edit">
                                    <svg class="w-3.5 h-3.5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('lamaran.destroy', $app['id']) }}" method="POST" class="contents">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="askDelete(this, '{{ __('Hapus lamaran ke :company?', ['company' => addslashes($app['company'])]) }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all"
                                            title="{{ __('Hapus') }}">
                                        <svg class="w-3.5 h-3.5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-20 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <p class="font-bold text-sm">{{ __('Belum ada lamaran') }}</p>
            <p class="text-xs mt-1">{{ __('Mulai catat lamaran kerja pertamamu') }}</p>
            <button type="button" onclick="openModal('modal-add')"
                    class="mt-4 bg-black text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                {{ __('Tambah Lamaran') }}
            </button>
        </div>
        @endif
    </div>


</div>

{{-- Modal Tambah --}}
<div id="modal-add" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-add')">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Tambah Lamaran') }}</h2>
            <button type="button" onclick="closeModal('modal-add')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('lamaran.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Perusahaan') }} *</label>
                    <input type="text" name="company" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Gojek" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Posisi') }} *</label>
                    <input type="text" name="position" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Backend Engineer" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Lokasi') }}</label>
                    <input type="text" name="location" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Jakarta / Remote">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Rentang Gaji') }}</label>
                    <input type="text" name="salary" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Rp 15–20 jt">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tanggal Melamar') }} *</label>
                    <input type="date" name="applied_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tipe Pekerjaan') }}</label>
                    <select name="job_type" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="">{{ __('Pilih tipe...') }}</option>
                        <option value="fulltime">{{ __('Full-time') }}</option>
                        <option value="parttime">{{ __('Part-time') }}</option>
                        <option value="internship">{{ __('Internship') }}</option>
                        <option value="freelance">{{ __('Freelance') }}</option>
                        <option value="contract">{{ __('Kontrak') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="wishlist">Wishlist</option>
                        <option value="applied">{{ __('Dikirim') }}</option>
                        <option value="review">Review</option>
                        <option value="interview">Interview</option>
                        <option value="offer">{{ __('Tawaran') }}</option>
                        <option value="hired">{{ __('Diterima') }}</option>
                        <option value="rejected">{{ __('Ditolak') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Channel') }}</label>
                    <select name="channel" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="">{{ __('Pilih channel...') }}</option>
                        <optgroup label="Job Portal">
                            <option value="linkedin">LinkedIn</option>
                            <option value="jobstreet">Jobstreet</option>
                            <option value="glints">Glints</option>
                        </optgroup>
                        <optgroup label="Freelance">
                            <option value="upwork">Upwork</option>
                            <option value="fiverr">Fiverr</option>
                            <option value="kontrakhub">Kontrakhub</option>
                        </optgroup>
                        <optgroup label="{{ __('Lainnya') }}">
                            <option value="email">{{ __('Email Langsung') }}</option>
                            <option value="referral">{{ __('Kenalan / Referral') }}</option>
                            <option value="website">{{ __('Website Perusahaan') }}</option>
                            <option value="other">{{ __('Lainnya') }}</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('URL Lowongan') }}</label>
                    <input type="url" name="job_url" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="https://...">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Catatan') }}</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all resize-none" placeholder="{{ __('Nama recruiter, tech stack, dll.') }}"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-add')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">{{ __('Batal') }}</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-edit')">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">{{ __('Edit Lamaran') }}</h2>
            <button type="button" onclick="closeModal('modal-edit')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Perusahaan') }} *</label>
                    <input type="text" id="edit-company" name="company" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Posisi') }} *</label>
                    <input type="text" id="edit-position" name="position" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Lokasi') }}</label>
                    <input type="text" id="edit-location" name="location" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Rentang Gaji') }}</label>
                    <input type="text" id="edit-salary" name="salary" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tanggal Melamar') }} *</label>
                    <input type="date" id="edit-applied_date" name="applied_date" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tipe Pekerjaan') }}</label>
                    <select id="edit-job_type" name="job_type" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="">{{ __('Pilih tipe...') }}</option>
                        <option value="fulltime">{{ __('Full-time') }}</option>
                        <option value="parttime">{{ __('Part-time') }}</option>
                        <option value="internship">{{ __('Internship') }}</option>
                        <option value="freelance">{{ __('Freelance') }}</option>
                        <option value="contract">{{ __('Kontrak') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Status</label>
                    <select id="edit-status" name="status" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="wishlist">Wishlist</option>
                        <option value="applied">{{ __('Dikirim') }}</option>
                        <option value="review">Review</option>
                        <option value="interview">Interview</option>
                        <option value="offer">{{ __('Tawaran') }}</option>
                        <option value="hired">{{ __('Diterima') }}</option>
                        <option value="rejected">{{ __('Ditolak') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Channel') }}</label>
                    <select id="edit-channel" name="channel" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="">{{ __('Pilih channel...') }}</option>
                        <optgroup label="Job Portal">
                            <option value="linkedin">LinkedIn</option>
                            <option value="jobstreet">Jobstreet</option>
                            <option value="glints">Glints</option>
                        </optgroup>
                        <optgroup label="Freelance">
                            <option value="upwork">Upwork</option>
                            <option value="fiverr">Fiverr</option>
                            <option value="kontrakhub">Kontrakhub</option>
                        </optgroup>
                        <optgroup label="{{ __('Lainnya') }}">
                            <option value="email">{{ __('Email Langsung') }}</option>
                            <option value="referral">{{ __('Kenalan / Referral') }}</option>
                            <option value="website">{{ __('Website Perusahaan') }}</option>
                            <option value="other">{{ __('Lainnya') }}</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('URL Lowongan') }}</label>
                    <input type="url" id="edit-job_url" name="job_url" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Catatan') }}</label>
                <textarea id="edit-notes" name="notes" rows="3" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">{{ __('Batal') }}</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">{{ __('Simpan Perubahan') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal impor lamaran --}}
<div id="modal-impor-lamaran" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-impor-lamaran')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-start justify-between gap-3 p-6 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg leading-tight">{{ __('Impor Lamaran') }}</h2>
                <p class="text-xs text-gray-400 mt-1">{{ __('Pindahkan catatan lamaran lamamu ke molife dalam sekali unggah.') }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-impor-lamaran')" class="w-8 h-8 -mr-1.5 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6">
            <p class="text-xs text-gray-400 mb-3">{{ __('Format CSV atau Excel (.xlsx), maksimal 500 baris. Kolom: Perusahaan, Posisi, Tipe, Channel, Lokasi, Gaji, Tanggal Melamar, Status, URL, Catatan. Status boleh ditulis bebas (Dilamar, Interview, Ditolak, dll).') }}</p>
            <a href="{{ route('lamaran.import.template') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-black transition-all mb-3">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ __('Unduh file contoh') }}
            </a>
            <form method="POST" action="{{ route('lamaran.import') }}" enctype="multipart/form-data" class="space-y-2">
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
    </div>
</div>

@push('scripts')
<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
function openEdit(id, data) {
    document.getElementById('edit-form').action = '{{ url("/lamaran") }}/' + id;
    document.getElementById('edit-company').value      = data.company ?? '';
    document.getElementById('edit-position').value     = data.position ?? '';
    document.getElementById('edit-location').value     = data.location ?? '';
    document.getElementById('edit-salary').value       = data.salary ?? '';
    document.getElementById('edit-applied_date').value = data.applied_date ?? '';
    document.getElementById('edit-job_url').value      = data.job_url ?? '';
    document.getElementById('edit-notes').value        = data.notes ?? '';
    document.getElementById('edit-status').value   = data.status   ?? 'applied';
    document.getElementById('edit-job_type').value = data.job_type ?? '';
    document.getElementById('edit-channel').value  = data.channel  ?? '';
    openModal('modal-edit');
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal('modal-add');
        closeModal('modal-edit');
    }
});
</script>
@endpush
@endsection
