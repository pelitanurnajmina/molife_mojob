@extends('layouts.app')
@section('title', 'Journal')
@section('page-title', 'Journal')
@section('breadcrumb', 'Life › Journal')

@section('content')
@php $c = $meta['color']; @endphp
<div class="space-y-4 md:space-y-6">

    {{-- ── Hero ── --}}
    <div class="bg-gradient-to-br from-{{ $c }}-600 to-purple-700 rounded-2xl md:rounded-3xl p-6 md:p-8 text-white relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -right-2 bottom-0 w-24 h-24 bg-white/10 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between gap-3 mb-2">
                <span class="inline-block text-[10px] font-bold bg-white/20 px-2.5 py-1 rounded-full">{{ $meta['label'] }}</span>
                @if($streak > 0)
                <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/15 px-3 py-1.5 rounded-full">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ $streak }} {{ __('hari') }}
                </span>
                @endif
            </div>
            <h2 class="text-2xl md:text-3xl font-bold mb-1">{{ __('Tulis journal hari ini') }}</h2>
            <p class="text-sm text-white/80 max-w-lg">{{ $meta['desc'] }}</p>
        </div>
    </div>

    {{-- Editing a past day notice --}}
    @if($editDate !== $today)
    <div class="flex items-center justify-between gap-3 bg-amber-50 border border-amber-100 rounded-2xl px-4 py-3">
        <p class="text-xs font-bold text-amber-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            {{ __('Mengedit entri') }} {{ date('j F Y', strtotime($editDate)) }}
        </p>
        <a href="{{ route('journal') }}" class="text-xs font-bold text-amber-700 hover:underline">{{ __('Kembali ke hari ini') }}</a>
    </div>
    @endif

    {{-- ── Guided form (single frame) ── --}}
    <form method="POST" action="{{ route('journal.store') }}">
        @csrf
        <input type="hidden" name="template" value="{{ $template }}">
        <input type="hidden" name="date" value="{{ $editDate }}">

        <div class="bg-white rounded-2xl md:rounded-3xl border border-gray-50 overflow-hidden">
            @foreach($meta['fields'] as $f)
            <div class="p-4 md:p-6 {{ $loop->first ? '' : 'border-t border-gray-100' }}">
                <div class="flex items-start gap-3 mb-2">
                    <div class="w-9 h-9 rounded-xl bg-{{ $c }}-50 text-{{ $c }}-600 flex items-center justify-center flex-shrink-0">
                        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-gray-800">{{ $f['label'] }}</h3>
                        <p class="text-xs text-gray-400 leading-relaxed mt-0.5">{{ $f['help'] }}</p>
                    </div>
                </div>
                <textarea name="content[{{ $f['key'] }}]" rows="{{ $f['rows'] }}"
                    placeholder="{{ $f['placeholder'] }}"
                    class="w-full mt-1 px-3.5 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm leading-relaxed outline-none focus:border-{{ $c }}-400 focus:bg-white resize-none transition-all">{{ $today_content[$f['key']] ?? '' }}</textarea>
            </div>
            @endforeach

            {{-- Footer inside the same frame --}}
            <div class="flex items-center justify-between gap-3 p-4 md:p-6 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-400">{{ __('Disimpan untuk') }} {{ date('l, j F Y', strtotime($editDate)) }}</p>
                <button type="submit" class="px-6 py-3 rounded-xl bg-black text-white text-sm font-bold hover:bg-gray-800 transition-all">
                    {{ __('Simpan Journal') }}
                </button>
            </div>
        </div>
    </form>

    {{-- ── History ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <h3 class="font-bold mb-1">{{ __('Riwayat Journal') }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ __('Baca lagi perjalanan & manifestasimu.') }}</p>

        @php $fieldLabels = collect($meta['fields'])->pluck('label', 'key'); @endphp
        @forelse($history as $h)
        @php $isToday = $h['date'] === $today; @endphp
        <details class="group border-b border-gray-50 last:border-0 py-2.5" {{ $loop->first ? 'open' : '' }}>
            <summary class="flex items-center gap-3 cursor-pointer list-none">
                <div class="w-8 h-8 rounded-lg bg-{{ $c }}-50 text-{{ $c }}-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <span class="flex-1 text-sm font-bold text-gray-700">{{ date('l, j F Y', strtotime($h['date'])) }}</span>
                @if($isToday)<span class="text-[9px] font-bold text-{{ $c }}-600 bg-{{ $c }}-50 px-2 py-0.5 rounded-full">{{ __('Hari ini') }}</span>@endif
                <svg class="w-4 h-4 text-gray-300 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <div class="mt-3 pl-1 space-y-3">
                {{-- Entri lama bisa berupa teks polos, bukan array field --}}
                @foreach((is_array($h['content']) ? $h['content'] : ['journal' => $h['content']]) as $key => $val)
                @if(trim((string) $val) !== '')
                <div>
                    <p class="text-[11px] font-bold text-{{ $c }}-600 uppercase tracking-wide mb-0.5">{{ $fieldLabels[$key] ?? $key }}</p>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $val }}</p>
                </div>
                @endif
                @endforeach

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-2">
                    <a href="{{ route('journal', ['date' => $h['date']]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-700 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ __('Edit') }}
                    </a>
                    <form method="POST" action="{{ route('journal.destroy', $h['id']) }}" class="m-0">
                        @csrf @method('DELETE')
                        <button type="button" onclick="askDelete(this, '{{ __('Hapus entri journal ini? Tindakan ini tidak bisa dibatalkan.') }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-red-500 hover:bg-red-50 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            {{ __('Hapus') }}
                        </button>
                    </form>
                </div>
            </div>
        </details>
        @empty
        <div class="text-center py-8">
            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">{{ __('Belum ada journal') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Mulai tulis entri pertamamu di atas.') }}</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
