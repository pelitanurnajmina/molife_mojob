@extends('layouts.app')
@section('title', __('Tugas Bisnis'))
@section('page-title', __('Tugas Bisnis'))
@section('breadcrumb', __('Bisnis') . ' › ' . __('Tugas'))

@section('content')
<div class="space-y-4 md:space-y-6">

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h3 class="font-bold">{{ __('Papan Tugas Semua Bisnis') }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Satu pandangan untuk tugas dari semua proyekmu, plus tugas umum di luar proyek. Seret kartu untuk mengubah status.') }}</p>
        </div>
        <button type="button" onclick="openTaskModal()"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-black text-white text-xs font-bold hover:bg-gray-800 transition-all flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            {{ __('Buat Tugas') }}
        </button>
    </div>

    @php
        $taskCols = [
            'todo'     => ['label' => __('To-do'),      'dot' => 'bg-gray-300'],
            'progress' => ['label' => __('Dikerjakan'), 'dot' => 'bg-amber-400'],
            'review'   => ['label' => __('Review'),     'dot' => 'bg-violet-400'],
            'done'     => ['label' => __('Selesai'),    'dot' => 'bg-emerald-500'],
        ];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
        @foreach($taskCols as $st => $meta)
        <div class="bg-gray-50 rounded-2xl p-3 border border-gray-100 transition-all"
             ondragover="taskDragOver(event, this)"
             ondragleave="this.classList.remove('ring-2','ring-gray-300')"
             ondrop="dropTask(event, '{{ $st }}', this)">
            <div class="flex items-center justify-between px-1 mb-2.5">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-gray-500">
                    <span class="w-2 h-2 rounded-full {{ $meta['dot'] }}"></span>{{ $meta['label'] }}
                </span>
                <span id="cnt-{{ $st }}" class="text-[10px] font-bold text-gray-400">{{ count($tasks[$st] ?? []) }}</span>
            </div>
            <div id="col-{{ $st }}" class="space-y-2 min-h-[70px]">
                @foreach($tasks[$st] ?? [] as $t)
                <div id="task-{{ $t['id'] }}" draggable="true"
                     ondragstart="taskDragStart(event, {{ $t['id'] }})" ondragend="taskDragEnd()"
                     onclick='openTaskModal(@json($t))'
                     class="task-card bg-white rounded-xl border border-gray-100 p-3 cursor-grab active:cursor-grabbing hover:border-gray-300 hover:shadow-sm transition-all">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        @include('pages.bisnis.collab._priority_chip', ['p' => $t['priority']])
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full {{ $t['project'] ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500' }}">{{ $t['project'] ?? __('Umum') }}</span>
                        @if($t['due_label'])
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full inline-flex items-center gap-1 {{ $t['overdue'] ? 'bg-red-50 text-red-500' : 'bg-gray-50 border border-gray-100 text-gray-400' }}">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $t['due_label'] }}
                        </span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-gray-800 leading-snug">{{ $t['title'] }}</p>
                    @if($t['note'])
                    <p class="text-[11px] text-gray-400 mt-1 leading-relaxed line-clamp-2">{{ $t['note'] }}</p>
                    @endif
                    <div class="mt-2.5 flex items-center justify-between">
                        @if($t['assignee'])
                        <span class="inline-flex items-center gap-1.5 min-w-0">
                            <span class="w-5 h-5 rounded-full bg-gray-900 text-white text-[9px] font-bold flex items-center justify-center flex-shrink-0">{{ strtoupper(substr($t['assignee'], 0, 1)) }}</span>
                            <span class="text-[11px] font-bold text-gray-500 truncate">{{ $t['assignee'] }}</span>
                        </span>
                        @else
                        <span class="text-[10px] font-bold text-gray-300">{{ __('Belum di-assign') }}</span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-gray-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 13h5"/></svg>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="openTaskModal(null, '{{ $st }}')"
                class="w-full mt-2 py-2 rounded-xl border border-dashed border-gray-200 text-[11px] font-bold text-gray-400 hover:border-gray-400 hover:text-gray-600 transition-all">
                + {{ __('Tambah') }}
            </button>
        </div>
        @endforeach
    </div>

</div>

{{-- ── Modal tugas (buat/edit) ── --}}
<div id="modal-task" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('modal-task')">
    <div class="bg-white rounded-3xl w-full max-w-md">
        <div class="flex items-start justify-between gap-3 px-6 pt-6 pb-4 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-lg leading-tight" id="taskModalTitle">{{ __('Buat Tugas') }}</h2>
                <p class="text-xs text-gray-400 mt-1">{{ __('Tugas proyek terlihat oleh anggota proyek itu; tugas umum hanya untukmu.') }}</p>
            </div>
            <button type="button" onclick="closeModal('modal-task')" class="w-8 h-8 -mr-1.5 -mt-1 flex items-center justify-center rounded-lg text-gray-400 hover:text-black hover:bg-gray-100 transition-all flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" action="" id="taskForm" class="px-6 pt-5 pb-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Judul Tugas') }} <span class="text-red-400">*</span></label>
                    <input type="text" name="title" maxlength="200" required placeholder="{{ __('cth: Follow up klien PT Maju Jaya') }}"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Catatan') }}</label>
                    <textarea name="note" rows="2" maxlength="500" placeholder="{{ __('Detail singkat (opsional)') }}"
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-black focus:bg-white resize-none transition-all"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Proyek') }}</label>
                        <select name="business_product_id" id="taskProject" onchange="refreshTaskAssignees()"
                            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                            <option value="">{{ __('Umum (tanpa proyek)') }}</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Status') }}</label>
                        <select name="status" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                            <option value="todo">{{ __('To-do') }}</option>
                            <option value="progress">{{ __('Dikerjakan') }}</option>
                            <option value="review">{{ __('Review') }}</option>
                            <option value="done">{{ __('Selesai') }}</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Prioritas') }}</label>
                        <select name="priority" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                            <option value="low">{{ __('Rendah') }}</option>
                            <option value="normal" selected>{{ __('Normal') }}</option>
                            <option value="high">{{ __('Tinggi') }}</option>
                            <option value="urgent">{{ __('Urgent') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Assign ke') }}</label>
                        <select name="assignee_id" id="taskAssignee"
                            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-all">
                            <option value="">{{ __('Tidak di-assign') }}</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('Tenggat') }}</label>
                        <div class="relative">
                            <input type="date" name="due_date" placeholder="{{ __('Pilih tanggal') }}"
                                class="w-full pl-3 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-black focus:bg-white transition-all">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full mt-6 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all" id="taskSubmit">{{ __('Simpan') }}</button>
        </form>
        <form method="POST" action="" id="taskDeleteForm" class="hidden px-6 pb-5 -mt-2 text-center">
            @csrf @method('DELETE')
            <button type="button" onclick="askDelete(this, '{{ __('Hapus tugas ini?') }}')"
                class="text-[11px] font-bold text-gray-400 hover:text-red-500 transition-all">{{ __('Hapus tugas ini') }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id){ document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeModal(id){ document.getElementById(id).classList.add('hidden'); document.body.style.overflow = ''; }

/* ── Drag & drop antar kolom ── */
var TASK_URL = '{{ url('bisnis/tugas') }}';
var TASK_CSRF = document.querySelector('meta[name="csrf-token"]').content;
var ASSIGNEES_BY_PROJECT = @json($assigneesByProject);
var dragTaskId = null, justDragged = false;

function taskDragStart(e, id){
    dragTaskId = id; justDragged = true;
    e.dataTransfer.effectAllowed = 'move';
    try { e.dataTransfer.setData('text/plain', String(id)); } catch(err) {}
}
function taskDragEnd(){ setTimeout(() => { justDragged = false; }, 150); }
function taskDragOver(e, col){ e.preventDefault(); col.classList.add('ring-2', 'ring-gray-300'); }

async function dropTask(e, status, col){
    e.preventDefault();
    col.classList.remove('ring-2', 'ring-gray-300');
    if (!dragTaskId) return;

    const card = document.getElementById('task-' + dragTaskId);
    const from = card.parentElement;
    const id = dragTaskId; dragTaskId = null;
    if (from.id === 'col-' + status) return;

    document.getElementById('col-' + status).appendChild(card);
    refreshTaskCounts();
    try {
        const r = await fetch(TASK_URL + '/' + id + '/status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': TASK_CSRF },
            body: JSON.stringify({ status }),
        });
        if (!r.ok) throw 0;
    } catch(err) {
        from.appendChild(card);
        refreshTaskCounts();
        if (window.showMojobToast) showMojobToast('{{ __('Gagal menyimpan status. Coba lagi.') }}');
    }
}

function refreshTaskCounts(){
    ['todo', 'progress', 'review', 'done'].forEach(s => {
        document.getElementById('cnt-' + s).textContent = document.getElementById('col-' + s).querySelectorAll('.task-card').length;
    });
}

/* ── Assignee mengikuti proyek terpilih ── */
function refreshTaskAssignees(keepId){
    const project = document.getElementById('taskProject').value;
    const sel = document.getElementById('taskAssignee');
    const opsi = ASSIGNEES_BY_PROJECT[project] || {};
    sel.innerHTML = '<option value="">{{ __('Tidak di-assign') }}</option>';
    Object.keys(opsi).forEach(uid => {
        const o = new Option(opsi[uid], uid);
        sel.add(o);
    });
    sel.value = (keepId && opsi[keepId]) ? String(keepId) : '';
    if (sel._csRefresh) sel._csRefresh();
}

/* ── Modal tugas ── */
function openTaskModal(t, presetStatus){
    if (justDragged) return;

    const f = document.getElementById('taskForm');
    const del = document.getElementById('taskDeleteForm');
    const isEdit = !!t;

    if (isEdit) {
        const col = document.getElementById('task-' + t.id)?.parentElement?.id;
        if (col) t.status = col.replace('col-', '');
    }

    document.getElementById('taskModalTitle').textContent = isEdit ? '{{ __('Ubah Tugas') }}' : '{{ __('Buat Tugas') }}';
    document.getElementById('taskSubmit').textContent = isEdit ? '{{ __('Perbarui') }}' : '{{ __('Simpan') }}';
    f.action = isEdit ? TASK_URL + '/' + t.id : TASK_URL;
    f.querySelector('[name="title"]').value = t?.title ?? '';
    f.querySelector('[name="note"]').value = t?.note ?? '';

    const pr = document.getElementById('taskProject');
    pr.value = t?.project_id ?? '';
    if (pr._csRefresh) pr._csRefresh();
    refreshTaskAssignees(t?.assignee_id ?? '');

    const st = f.querySelector('[name="status"]');
    st.value = t?.status ?? presetStatus ?? 'todo';
    if (st._csRefresh) st._csRefresh();

    const pri = f.querySelector('[name="priority"]');
    pri.value = t?.priority ?? 'normal';
    if (pri._csRefresh) pri._csRefresh();

    const dd = f.querySelector('[name="due_date"]');
    if (dd._flatpickr) dd._flatpickr.setDate(t?.due_date || null, false); else dd.value = t?.due_date ?? '';

    del.classList.toggle('hidden', !isEdit);
    if (isEdit) del.action = TASK_URL + '/' + t.id;

    openModal('modal-task');
}
</script>
@endpush
