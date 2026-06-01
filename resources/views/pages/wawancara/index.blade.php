@extends('layouts.app')
@section('title', 'Wawancara')
@section('page-title', 'Wawancara')
@section('breadcrumb', 'Wawancara')

@section('content')
<div class="space-y-6">

    {{-- Header + Add button --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-gray-400 text-sm">
                {{ count($upcoming) }} mendatang · {{ count($completed) }} selesai
            </p>
        </div>
        <button type="button" onclick="openModal('modal-add')"
                class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Wawancara
        </button>
    </div>

    {{-- Upcoming --}}
    <div>
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-3">Mendatang</h2>
        @forelse($upcoming as $iv)
        @php
            $d = new DateTime($iv['date']);
            $idMonths = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $typeLabel = ['video' => 'Video Call', 'phone' => 'Telepon', 'onsite' => 'On-site'][$iv['type']] ?? $iv['type'];
            $typeIcon  = ['video' => 'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                          'phone' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
                          'onsite'=> 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'][$iv['type']] ?? '';
        @endphp
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-gray-50 mb-3">
            <div class="flex items-start gap-4">
                {{-- Date tile --}}
                <div class="flex-shrink-0 w-14 h-14 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $idMonths[(int)$d->format('n')] }}</span>
                    <span class="text-2xl font-bold leading-none">{{ $d->format('j') }}</span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 flex-wrap">
                        <div>
                            <p class="font-bold text-sm">{{ $iv['position'] }}</p>
                            <p class="text-gray-500 text-sm">{{ $iv['company'] }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="flex items-center gap-1 text-xs text-gray-500 bg-gray-50 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $typeIcon }}"/></svg>
                                {{ $typeLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-400">
                        <span>{{ date('H:i', strtotime($iv['time'])) }}</span>
                        @if($iv['round'])
                        <span>· {{ $iv['round'] }}</span>
                        @endif
                        @if($iv['location'])
                        <span>· {{ $iv['location'] }}</span>
                        @endif
                        @if($iv['interviewer'])
                        <span>· {{ $iv['interviewer'] }}</span>
                        @endif
                    </div>

                    @if($iv['notes'])
                    <p class="mt-2 text-xs text-gray-400 bg-gray-50 rounded-xl px-3 py-2">{{ $iv['notes'] }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-50">
                <form action="{{ route('wawancara.complete', $iv['id']) }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 rounded-xl text-xs font-bold hover:bg-green-100 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tandai Selesai
                    </button>
                </form>
                <button type="button" onclick="openEdit('{{ $iv['id'] }}', {{ json_encode($iv) }})"
                        class="px-3 py-1.5 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-100 transition-all">
                    Edit
                </button>
                <form action="{{ route('wawancara.destroy', $iv['id']) }}" method="POST"
                      onsubmit="return confirm('Hapus wawancara ini?')" class="ml-auto">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 text-gray-300 hover:text-red-500 rounded-xl text-xs font-bold hover:bg-red-50 transition-all">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-10 text-center text-gray-400 border border-gray-50">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-bold">Belum ada wawancara terjadwal</p>
        </div>
        @endforelse
    </div>

    {{-- Completed --}}
    @if(count($completed) > 0)
    <div>
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-3">Sudah Selesai</h2>
        @foreach($completed as $iv)
        @php
            $d = new DateTime($iv['date']);
            $typeLabel = ['video' => 'Video Call', 'phone' => 'Telepon', 'onsite' => 'On-site'][$iv['type']] ?? $iv['type'];
        @endphp
        <div class="bg-white rounded-2xl p-4 border border-gray-50 mb-3 opacity-60">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-gray-50 rounded-xl border border-gray-100 flex flex-col items-center justify-center">
                    <span class="text-[9px] font-bold text-gray-300 uppercase">{{ $d->format('M') }}</span>
                    <span class="text-xl font-bold leading-none text-gray-400">{{ $d->format('j') }}</span>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-sm text-gray-500 line-through">{{ $iv['position'] }}</p>
                    <p class="text-gray-400 text-xs">{{ $iv['company'] }} · {{ $typeLabel }}</p>
                </div>
                <span class="flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full font-bold">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Selesai
                </span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- Modal Tambah --}}
<div id="modal-add" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-add')">
    <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-50">
            <h2 class="font-bold text-lg">Tambah Wawancara</h2>
            <button type="button" onclick="closeModal('modal-add')" class="text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('wawancara.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Perusahaan *</label>
                    <input type="text" name="company" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Posisi *</label>
                    <input type="text" name="position" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Tanggal *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Waktu *</label>
                    <input type="time" name="time" value="09:00" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Jenis</label>
                    <select name="type" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="video">Video Call</option>
                        <option value="phone">Telepon</option>
                        <option value="onsite">On-site</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Putaran</label>
                    <input type="text" name="round" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="HR Screening">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Lokasi / Link</label>
                    <input type="text" name="location" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Zoom / Kantor Jakarta">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Pewawancara</label>
                    <input type="text" name="interviewer" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" placeholder="Nama HR">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all resize-none" placeholder="Persiapan, pertanyaan yang ingin ditanyakan..."></textarea>
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
            <h2 class="font-bold text-lg">Edit Wawancara</h2>
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
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Tanggal *</label>
                    <input type="date" id="edit-date" name="date" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Waktu *</label>
                    <input type="time" id="edit-time" name="time" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Jenis</label>
                    <select id="edit-type" name="type" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                        <option value="video">Video Call</option>
                        <option value="phone">Telepon</option>
                        <option value="onsite">On-site</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Putaran</label>
                    <input type="text" id="edit-round" name="round" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Lokasi / Link</label>
                    <input type="text" id="edit-location" name="location" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Pewawancara</label>
                    <input type="text" id="edit-interviewer" name="interviewer" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
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
    document.getElementById('edit-form').action = '/wawancara/' + id;
    document.getElementById('edit-company').value    = data.company ?? '';
    document.getElementById('edit-position').value   = data.position ?? '';
    document.getElementById('edit-date').value        = data.date ?? '';
    document.getElementById('edit-time').value        = data.time ?? '09:00';
    document.getElementById('edit-type').value        = data.type ?? 'video';
    document.getElementById('edit-round').value       = data.round ?? '';
    document.getElementById('edit-location').value    = data.location ?? '';
    document.getElementById('edit-interviewer').value = data.interviewer ?? '';
    document.getElementById('edit-notes').value       = data.notes ?? '';
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
