@extends('layouts.app')
@section('title', 'Tasks & Notes')
@section('page-title', 'Tasks & Notes')
@section('breadcrumb', 'Tasks')

@section('content')
<div class="space-y-4 md:space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">

        {{-- Daily Tasks --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Daily Tasks
            </h3>

            <form method="POST" action="{{ route('tasks.daily.add') }}" class="mb-6" data-pg>
                @csrf
                <div class="flex items-center gap-1.5 mb-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide shrink-0">{{ __('Prioritas') }}:</span>
                    <button type="button" class="priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-gray-200 text-gray-400 bg-white" data-priority="high" onclick="selPri(this)">{{ __('Tinggi') }}</button>
                    <button type="button" class="priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-orange-300 text-orange-500 bg-orange-50" data-priority="medium" onclick="selPri(this)">{{ __('Sedang') }}</button>
                    <button type="button" class="priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-gray-200 text-gray-400 bg-white" data-priority="low" onclick="selPri(this)">{{ __('Rendah') }}</button>
                    <input type="hidden" name="priority" value="medium">
                </div>
                <div class="flex gap-2">
                    <input type="text" name="text" placeholder="Add new daily task..."
                        class="flex-1 p-2.5 md:p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all text-sm" required>
                    <button type="submit" class="bg-black text-white px-4 md:px-6 py-2.5 md:py-3 rounded-xl text-xs md:text-sm font-bold hover:bg-gray-800 transition-all">Add</button>
                </div>
            </form>

            @php
                $priOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
                usort($dailyTodos, fn($a, $b) =>
                    ($a['done'] <=> $b['done']) ?:
                    ($priOrder[$a['priority'] ?? 'medium'] <=> $priOrder[$b['priority'] ?? 'medium'])
                );
                $priBadge = ['high' => 'bg-red-50 text-red-500 border border-red-200', 'medium' => 'bg-orange-50 text-orange-500 border border-orange-200', 'low' => 'bg-gray-100 text-gray-400 border border-gray-200'];
            @endphp
            <div class="space-y-2">
                @forelse($dailyTodos as $todo)
                @php $p = $todo['priority'] ?? 'medium'; @endphp
                <div class="flex items-center gap-3 px-3 md:px-4 py-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all">
                    <form method="POST" action="{{ route('tasks.daily.toggle', $todo['id']) }}" class="flex items-center flex-shrink-0 m-0 leading-none">
                        @csrf
                        <button type="submit" class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all {{ $todo['done'] ? 'bg-black border-black text-white' : 'border-gray-300 hover:border-black' }}">
                            @if($todo['done'])<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                        </button>
                    </form>
                    <span class="flex-1 text-sm leading-none {{ $todo['done'] ? 'line-through text-gray-400' : '' }}">{{ $todo['text'] }}</span>
                    @if(!$todo['done'])
                    <span class="text-[9px] font-bold px-2 py-1 rounded-full shrink-0 leading-none {{ $priBadge[$p] }}">
                        {{ $p === 'high' ? __('Tinggi') : ($p === 'low' ? __('Rendah') : __('Sedang')) }}
                    </span>
                    @endif
                    <form method="POST" action="{{ route('tasks.daily.delete', $todo['id']) }}" class="flex items-center flex-shrink-0 m-0 leading-none">
                        @csrf @method('DELETE')
                        <button type="button" onclick="askDelete(this, '{{ __('Hapus task ini?') }}')" class="w-6 h-6 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-400 hover:bg-gray-200 transition-all">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm py-8">No tasks for today</p>
                @endforelse
            </div>

            @if(count($dailyTodos) > 0)
            <div class="mt-4 pt-4 border-t border-gray-50">
                <div class="flex justify-between text-xs text-gray-500">
                    <span>{{ count(array_filter($dailyTodos, fn($t)=>$t['done'])) }} of {{ count($dailyTodos) }} completed</span>
                    <div class="w-24 bg-gray-100 h-1.5 rounded-full self-center overflow-hidden">
                        <div class="bg-black h-full rounded-full" style="width:{{ count($dailyTodos) > 0 ? (count(array_filter($dailyTodos,fn($t)=>$t['done']))/count($dailyTodos))*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Weekly Tasks --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Weekly Tasks
            </h3>

            <form method="POST" action="{{ route('tasks.weekly.add') }}" class="mb-6" data-pg>
                @csrf
                <div class="flex items-center gap-1.5 mb-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide shrink-0">{{ __('Prioritas') }}:</span>
                    <button type="button" class="priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-gray-200 text-gray-400 bg-white" data-priority="high" onclick="selPri(this)">{{ __('Tinggi') }}</button>
                    <button type="button" class="priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-orange-300 text-orange-500 bg-orange-50" data-priority="medium" onclick="selPri(this)">{{ __('Sedang') }}</button>
                    <button type="button" class="priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-gray-200 text-gray-400 bg-white" data-priority="low" onclick="selPri(this)">{{ __('Rendah') }}</button>
                    <input type="hidden" name="priority" value="medium">
                </div>
                <div class="flex gap-2">
                    <input type="text" name="text" placeholder="Add new weekly task..."
                        class="flex-1 p-2.5 md:p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all text-sm" required>
                    <button type="submit" class="bg-black text-white px-4 md:px-6 py-2.5 md:py-3 rounded-xl text-xs md:text-sm font-bold hover:bg-gray-800 transition-all">Add</button>
                </div>
            </form>

            @php
                usort($weeklyTodos, fn($a, $b) =>
                    ($a['done'] <=> $b['done']) ?:
                    ($priOrder[$a['priority'] ?? 'medium'] <=> $priOrder[$b['priority'] ?? 'medium'])
                );
            @endphp
            <div class="space-y-2">
                @forelse($weeklyTodos as $todo)
                @php $p = $todo['priority'] ?? 'medium'; @endphp
                <div class="flex items-center gap-3 px-3 md:px-4 py-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all">
                    <form method="POST" action="{{ route('tasks.weekly.toggle', $todo['id']) }}" class="flex items-center flex-shrink-0 m-0 leading-none">
                        @csrf
                        <button type="submit" class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all {{ $todo['done'] ? 'bg-black border-black text-white' : 'border-gray-300 hover:border-black' }}">
                            @if($todo['done'])<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                        </button>
                    </form>
                    <span class="flex-1 text-sm leading-none {{ $todo['done'] ? 'line-through text-gray-400' : '' }}">{{ $todo['text'] }}</span>
                    @if(!$todo['done'])
                    <span class="text-[9px] font-bold px-2 py-1 rounded-full shrink-0 leading-none {{ $priBadge[$p] }}">
                        {{ $p === 'high' ? __('Tinggi') : ($p === 'low' ? __('Rendah') : __('Sedang')) }}
                    </span>
                    @endif
                    <form method="POST" action="{{ route('tasks.weekly.delete', $todo['id']) }}" class="flex items-center flex-shrink-0 m-0 leading-none">
                        @csrf @method('DELETE')
                        <button type="button" onclick="askDelete(this, '{{ __('Hapus task ini?') }}')" class="w-6 h-6 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-400 hover:bg-gray-200 transition-all">
                            <svg class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm py-8">No weekly tasks yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Reflection --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            {{ __('Refleksi Harian') }}
        </h3>
        <form method="POST" action="{{ route('tasks.reflection.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div class="p-4 md:p-5 bg-green-50 rounded-2xl">
                <label class="text-xs font-bold text-green-700 flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    {{ __('Apa yang berjalan baik hari ini?') }}
                </label>
                <textarea name="good" placeholder="{{ __('Satu hal positif, kecil pun tidak apa...') }}"
                    class="w-full p-3 bg-white/70 border border-transparent rounded-xl outline-none focus:border-green-400 transition-all text-sm resize-none" rows="4">{{ $reflection['good'] }}</textarea>
            </div>
            <div class="p-4 md:p-5 bg-orange-50 rounded-2xl">
                <label class="text-xs font-bold text-orange-700 flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    {{ __('Apa yang bisa diperbaiki besok?') }}
                </label>
                <textarea name="improve" placeholder="{{ __('Satu kebiasaan / sikap yang ingin kamu naikkan...') }}"
                    class="w-full p-3 bg-white/70 border border-transparent rounded-xl outline-none focus:border-orange-400 transition-all text-sm resize-none" rows="4">{{ $reflection['improve'] }}</textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="bg-black text-white px-6 py-2.5 rounded-xl font-bold hover:bg-gray-800 transition-all text-sm">{{ __('Simpan Refleksi') }}</button>
                @if($reflectionStreak > 0)
                <p class="text-xs text-gray-500 mt-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span><span class="font-bold">{{ $reflectionStreak }}</span> {{ __('dari 7 hari terakhir kamu sudah berefleksi. Pertahankan!') }}</span>
                </p>
                @endif
            </div>
        </form>
    </div>

    {{-- Notes --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
        <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            {{ __('Notes Hari Ini') }}
        </h3>
        <textarea id="noteTextarea" placeholder="{{ __('Tulis catatan untuk hari ini...') }}"
            class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-black focus:bg-white transition-all text-sm resize-none" rows="8">{{ $note }}</textarea>
        <div class="flex items-center justify-between mt-2">
            <p id="noteStatus" class="text-xs text-gray-400">{{ __('Belum ada perubahan') }}</p>
            <button onclick="saveNote()" id="noteSaveBtn"
                class="bg-black text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition-all">
                {{ __('Simpan') }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
/* ── Priority selector ── */
const PRI_ACTIVE = {
    high:   'priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-red-400 text-red-600 bg-red-50',
    medium: 'priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-orange-300 text-orange-500 bg-orange-50',
    low:    'priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-gray-400 text-gray-600 bg-gray-100',
};
const PRI_INACTIVE = 'priority-btn px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all border-gray-200 text-gray-400 bg-white';

function selPri(btn) {
    const form = btn.closest('[data-pg]');
    form.querySelectorAll('.priority-btn').forEach(b => b.className = PRI_INACTIVE);
    btn.className = PRI_ACTIVE[btn.dataset.priority];
    form.querySelector('input[name="priority"]').value = btn.dataset.priority;
}

/* ── Notes ── */
const NOTE_URL  = '{{ route("tasks.note.update") }}';
const CSRF_NOTE = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const NOTE_STRINGS = {
    saving:   '{{ __('Menyimpan...') }}',
    saved:    '{{ __('Tersimpan ✓') }}',
    noChange: '{{ __('Belum ada perubahan') }}',
    unsaved:  '{{ __('Ada perubahan yang belum disimpan') }}',
    failed:   '{{ __('Gagal menyimpan, coba lagi.') }}',
    save:     '{{ __('Simpan') }}',
};

async function saveNote() {
    const btn      = document.getElementById('noteSaveBtn');
    const status   = document.getElementById('noteStatus');
    const textarea = document.getElementById('noteTextarea');

    btn.disabled    = true;
    btn.textContent = NOTE_STRINGS.saving;

    try {
        const res = await fetch(NOTE_URL, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  CSRF_NOTE,
                'Accept':        'application/json',
            },
            body: JSON.stringify({ note: textarea.value }),
        });

        if (!res.ok) throw new Error('failed');

        status.textContent = NOTE_STRINGS.saved;
        status.className   = 'text-xs text-green-600 font-medium';
        setTimeout(() => {
            status.textContent = NOTE_STRINGS.noChange;
            status.className   = 'text-xs text-gray-400';
        }, 3000);
    } catch {
        status.textContent = NOTE_STRINGS.failed;
        status.className   = 'text-xs text-red-500 font-medium';
    } finally {
        btn.disabled    = false;
        btn.textContent = NOTE_STRINGS.save;
    }
}

// Track changes
document.getElementById('noteTextarea')?.addEventListener('input', () => {
    const s = document.getElementById('noteStatus');
    s.textContent = NOTE_STRINGS.unsaved;
    s.className   = 'text-xs text-orange-500 font-medium';
});
</script>
@endpush
@endsection
