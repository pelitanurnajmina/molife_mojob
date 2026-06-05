@extends('layouts.app')
@section('title', $meta['label'])
@section('page-title', $meta['title'])
@section('breadcrumb', 'Life › ' . $meta['label'])

@section('content')
@php $c = $meta['color']; @endphp
<div class="space-y-4 md:space-y-6 max-w-3xl">

    {{-- ── Streak hero ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-6 md:p-10 text-center relative overflow-hidden border border-gray-50">
        <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-{{ $c }}-50 to-transparent"></div>
        <div class="relative">
            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-{{ $c }}-100 text-{{ $c }}-600 flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $meta['icon'] }}"/></svg>
            </div>
            <p class="text-7xl md:text-8xl font-black text-{{ $c }}-500 leading-none">{{ $stats['streak'] }}</p>
            <p class="text-sm font-bold text-gray-400 mt-2">{{ $meta['unit'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('Mulai sejak') }} {{ date('j M Y', strtotime($stats['start_date'])) }}</p>

            @if($stats['next'])
            <div class="max-w-xs mx-auto mt-6">
                <div class="flex justify-between text-[11px] font-bold text-gray-400 mb-1">
                    <span>{{ $stats['streak'] }} {{ __('hari') }}</span>
                    <span>{{ __('Target') }} {{ $stats['next'] }}</span>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-{{ $c }}-500 h-full rounded-full transition-all" style="width:{{ $stats['next'] > 0 ? min(100, round(($stats['streak'] / $stats['next']) * 100)) : 0 }}%"></div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5">{{ __('Tinggal :n hari lagi menuju milestone berikutnya', ['n' => $stats['to_next']]) }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('quit.relapse', $type) }}" class="mt-6 inline-flex" id="relapseForm">
                @csrf
                <button type="button" onclick="askDelete(this, '{{ __('Yakin reset streak? Tetap semangat, kamu bisa mulai lagi.') }}')"
                    class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">
                    {{ $meta['relapse'] }}
                </button>
            </form>
        </div>
    </div>

    {{-- ── Stats row ── --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-{{ $c }}-600">{{ $stats['best'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Rekor Terlama') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['streak'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Streak Saat Ini') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center border border-gray-50">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['relapses'] }}</p>
            <p class="text-[10px] text-gray-400 font-bold mt-1">{{ __('Total Reset') }}</p>
        </div>
    </div>

    {{-- ── Milestones ── --}}
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <h3 class="font-bold mb-4">{{ __('Milestone') }}</h3>
        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
            @foreach($stats['milestones'] as $m)
            @php $reached = $stats['streak'] >= $m; @endphp
            <div class="rounded-xl p-3 text-center {{ $reached ? 'bg-'.$c.'-50 border border-'.$c.'-200' : 'bg-gray-50 border border-transparent' }}">
                <p class="text-lg font-bold {{ $reached ? 'text-'.$c.'-600' : 'text-gray-300' }}">{{ $m }}</p>
                <p class="text-[9px] font-bold {{ $reached ? 'text-'.$c.'-500' : 'text-gray-300' }}">{{ __('hari') }}</p>
                @if($reached)
                <svg class="w-3.5 h-3.5 mx-auto mt-1 text-{{ $c }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── History ── --}}
    @if(count($history) > 0)
    <div class="bg-white rounded-2xl md:rounded-3xl p-4 md:p-8 border border-gray-50">
        <h3 class="font-bold mb-4">{{ __('Riwayat Reset') }}</h3>
        <div class="space-y-2">
            @foreach($history as $h)
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-700">{{ date('l, j F Y', strtotime($h['date'])) }}</p>
                    @if($h['note'])<p class="text-xs text-gray-400">{{ $h['note'] }}</p>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
