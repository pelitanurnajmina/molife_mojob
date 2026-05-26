@extends('layouts.app')
@section('title', 'Lamaran Kerja')
@section('page-title', 'Lamaran Kerja')
@section('breadcrumb', 'Lamaran')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statCards = [
                ['label' => 'Total',      'value' => $total,               'bg' => 'bg-gray-50',   'text' => 'text-gray-800'],
                ['label' => 'Aktif',      'value' => $active,              'bg' => 'bg-blue-50',   'text' => 'text-blue-800'],
                ['label' => 'Wawancara',  'value' => $counts['interview'], 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-800'],
                ['label' => 'Tawaran',    'value' => $counts['offer'] + $counts['hired'], 'bg' => 'bg-green-50', 'text' => 'text-green-800'],
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
                'all'       => ['label' => 'Semua',    'count' => $total],
                'applied'   => ['label' => 'Dikirim',  'count' => $counts['applied']],
                'review'    => ['label' => 'Review',   'count' => $counts['review']],
                'interview' => ['label' => 'Interview','count' => $counts['interview']],
                'offer'     => ['label' => 'Tawaran',  'count' => $counts['offer']],
                'hired'     => ['label' => 'Diterima', 'count' => $counts['hired']],
                'rejected'  => ['label' => 'Ditolak',  'count' => $counts['rejected']],
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

        <a href="{{ route('lamaran.export') }}"
           class="hidden md:flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Ekspor CSV
        </a>
        <button type="button" onclick="openModal('modal-add')"
                class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Lamaran
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-50 overflow-hidden">
        @if(count($apps) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">Perusahaan</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">Posisi</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden md:table-cell">Lokasi</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden lg:table-cell">Tanggal</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400">Status</th>
                        <th class="text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-gray-400 hidden lg:table-cell">Tahap</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apps as $app)
                    @php
                        $statusLabels = [
                            'applied'   => 'Dikirim',
                            'review'    => 'Review',
                            'interview' => 'Interview',
                            'offer'     => 'Tawaran',
                            'hired'     => 'Diterima',
                            'rejected'  => 'Ditolak',
                        ];
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-all">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                                    {{ strtoupper(substr($app['company'], 0, 2)) }}
                                </div>
                                <span class="font-semibold">{{ $app['company'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-gray-600">{{ $app['position'] }}</td>
                        <td class="px-4 py-4 text-gray-400 text-xs hidden md:table-cell">{{ $app['location'] ?? '—' }}</td>
                        <td class="px-4 py-4 text-gray-400 text-xs hidden lg:table-cell">
                            {{ $app['applied_date'] ? date('j M Y', strtotime($app['applied_date'])) : '—' }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold pill-{{ $app['status'] ?? 'applied' }}">
                                {{ $statusLabels[$app['status']] ?? $app['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-gray-400 text-xs hidden lg:table-cell">{{ $app['stage'] ?? '—' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1 justify-end">
                                @if(!empty($app['job_url']))
                                <a href="{{ $app['job_url'] }}" target="_blank"
                                   class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-all"
                                   title="Buka lowongan">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                @endif
                                <button type="button"
                                        onclick="openEdit('{{ $app['id'] }}', {{ json_encode($app) }})"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-all"
                                        title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('lamaran.destroy', $app['id']) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="askDelete(this, 'Hapus lamaran ke {{ addslashes($app['company']) }}? Data tidak bisa dikembalikan.')"
                                            class="p-1.5 rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-500 transition-all"
                                            title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
            <p class="font-bold text-sm">Belum ada lamaran</p>
            <p class="text-xs mt-1">Mulai catat lamaran kerja pertamamu</p>
            <button type="button" onclick="openModal('modal-add')"
                    class="mt-4 bg-black text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                Tambah Lamaran
            </button>
        </div>
        @endif
    </div>

    {{-- Pipeline visual --}}
    @if($total > 0)
    <div class="bg-white rounded-2xl p-5 border border-gray-50">
        <h3 class="text-sm font-bold mb-4">Pipeline Lamaran</h3>
        @php
            $pipeline = [
                'applied'   => ['label' => 'Dikirim',   'color' => 'bg-gray-400'],
                'review'    => ['label' => 'Review',    'color' => 'bg-amber-400'],
                'interview' => ['label' => 'Interview', 'color' => 'bg-blue-500'],
                'offer'     => ['label' => 'Tawaran',   'color' => 'bg-green-500'],
                'hired'     => ['label' => 'Diterima',  'color' => 'bg-emerald-600'],
                'rejected'  => ['label' => 'Ditolak',   'color' => 'bg-red-400'],
            ];
        @endphp
        <div class="space-y-3">
            @foreach($pipeline as $status => $meta)
            @php $pct = $total > 0 ? round(($counts[$status] / $total) * 100) : 0; @endphp
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 w-20 flex-shrink-0">{{ $meta['label'] }}</span>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="{{ $meta['color'] }} h-full rounded-full transition-all" style="width:{{ $pct }}%"></div>
                </div>
                <span class="text-xs font-bold w-6 text-right">{{ $counts[$status] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Modal Tambah --}}
<div id="modal-add" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-add')">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">Tambah Lamaran</h2>
            <button type="button" onclick="closeModal('modal-add')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('lamaran.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Perusahaan *</label>
                    <input type="text" name="company" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Gojek" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Posisi *</label>
                    <input type="text" name="position" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Backend Engineer" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Lokasi</label>
                    <input type="text" name="location" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Jakarta / Remote">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Rentang Gaji</label>
                    <input type="text" name="salary" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Rp 15–20 jt">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Tanggal Melamar *</label>
                    <input type="date" name="applied_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="applied">Dikirim</option>
                        <option value="review">Review</option>
                        <option value="interview">Interview</option>
                        <option value="offer">Tawaran</option>
                        <option value="hired">Diterima</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">URL Lowongan</label>
                <input type="url" name="job_url" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="https://...">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Tahap Saat Ini</label>
                <input type="text" name="stage" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Menunggu balasan HR">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all resize-none" placeholder="Nama recruiter, tech stack, dll."></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-add')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-edit')">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">Edit Lamaran</h2>
            <button type="button" onclick="closeModal('modal-edit')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Perusahaan *</label>
                    <input type="text" id="edit-company" name="company" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Posisi *</label>
                    <input type="text" id="edit-position" name="position" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Lokasi</label>
                    <input type="text" id="edit-location" name="location" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Rentang Gaji</label>
                    <input type="text" id="edit-salary" name="salary" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Tanggal Melamar *</label>
                    <input type="date" id="edit-applied_date" name="applied_date" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Status</label>
                    <select id="edit-status" name="status" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="applied">Dikirim</option>
                        <option value="review">Review</option>
                        <option value="interview">Interview</option>
                        <option value="offer">Tawaran</option>
                        <option value="hired">Diterima</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">URL Lowongan</label>
                <input type="url" id="edit-job_url" name="job_url" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Tahap Saat Ini</label>
                <input type="text" id="edit-stage" name="stage" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Catatan</label>
                <textarea id="edit-notes" name="notes" rows="3" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit')" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">Simpan Perubahan</button>
            </div>
        </form>
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
    document.getElementById('edit-stage').value        = data.stage ?? '';
    document.getElementById('edit-notes').value        = data.notes ?? '';
    // Set select + refresh custom dropdown label
    document.getElementById('edit-status').value = data.status ?? 'applied';
    window.refreshSelect && window.refreshSelect('edit-status');
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
