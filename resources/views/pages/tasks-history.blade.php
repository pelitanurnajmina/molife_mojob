@extends('layouts.app')
@section('title', __('Riwayat Task'))
@section('page-title', __('Riwayat Task'))
@section('breadcrumb', 'Tasks › ' . __('Riwayat'))

@section('content')
<div class="space-y-4 md:space-y-6">

    <a href="{{ route('tasks') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-400 hover:text-black transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        {{ __('Kembali ke Tasks') }}
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        {{-- Daily history --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-base md:text-lg font-bold mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('Riwayat Harian') }}
            </h3>
            <p class="text-xs text-gray-400 mb-4">{{ __('30 hari terakhir') }}</p>
            @forelse($dailyHistory as $h)
            @php $pct = $h['total'] > 0 ? round($h['done']/$h['total']*100) : 0; @endphp
            <details class="group border-b border-gray-50 last:border-0 py-2.5">
                <summary class="flex items-center gap-3 cursor-pointer list-none">
                    <span class="flex-1 text-sm font-bold text-gray-700">{{ $h['label'] }}</span>
                    <span class="text-xs font-bold {{ $pct === 100 ? 'text-green-600' : 'text-gray-500' }}">{{ $h['done'] }}/{{ $h['total'] }}</span>
                    <div class="w-16 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $pct === 100 ? 'bg-green-500' : 'bg-black' }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="mt-2 pl-1 space-y-1">
                    @foreach($h['tasks'] as $t)
                    <div class="flex items-center gap-2 text-xs">
                        <svg class="w-3.5 h-3.5 flex-shrink-0 {{ $t['done'] ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $t['done'] ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
                        <span class="{{ $t['done'] ? 'text-gray-400 line-through' : 'text-gray-600' }}">{{ $t['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </details>
            @empty
            <p class="text-center text-gray-400 text-sm py-8">{{ __('Belum ada riwayat harian') }}</p>
            @endforelse
        </div>

        {{-- Weekly history --}}
        <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8">
            <h3 class="text-base md:text-lg font-bold mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ __('Riwayat Mingguan') }}
            </h3>
            <p class="text-xs text-gray-400 mb-4">{{ __('12 minggu terakhir') }}</p>
            @forelse($weeklyHistory as $h)
            @php $pct = $h['total'] > 0 ? round($h['done']/$h['total']*100) : 0; @endphp
            <details class="group border-b border-gray-50 last:border-0 py-2.5">
                <summary class="flex items-center gap-3 cursor-pointer list-none">
                    <span class="flex-1 text-sm font-bold text-gray-700">{{ $h['label'] }}</span>
                    <span class="text-xs font-bold {{ $pct === 100 ? 'text-green-600' : 'text-gray-500' }}">{{ $h['done'] }}/{{ $h['total'] }}</span>
                    <div class="w-16 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $pct === 100 ? 'bg-green-500' : 'bg-black' }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="mt-2 pl-1 space-y-1">
                    @foreach($h['tasks'] as $t)
                    <div class="flex items-center gap-2 text-xs">
                        <svg class="w-3.5 h-3.5 flex-shrink-0 {{ $t['done'] ? 'text-green-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $t['done'] ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
                        <span class="{{ $t['done'] ? 'text-gray-400 line-through' : 'text-gray-600' }}">{{ $t['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </details>
            @empty
            <p class="text-center text-gray-400 text-sm py-8">{{ __('Belum ada riwayat mingguan') }}</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
