@extends('blog.layout')

@section('seo_title', 'Blog Molife — Tips Hidup Teratur, Produktif & Konsisten')
@section('seo_description', 'Artikel praktis seputar membangun kebiasaan baik, konsistensi ibadah, mengatur keuangan, produktivitas, dan karier. Belajar merapikan hidupmu bersama Molife.')
@section('canonical', route('blog.index'))

@php
    $cmap = [
        'green'  => ['from'=>'#E7F4EC','to'=>'#d6efe0','txt'=>'#16A34A'],
        'blue'   => ['from'=>'#E8EEFC','to'=>'#dbe6fb','txt'=>'#3B6FF6'],
        'violet' => ['from'=>'#EEEAFC','to'=>'#e4dcfb','txt'=>'#7C5CF0'],
        'orange' => ['from'=>'#FBEEDD','to'=>'#f8e4c9','txt'=>'#F5871F'],
        'rose'   => ['from'=>'#FCE8EC','to'=>'#fbdce3','txt'=>'#EF4D6A'],
    ];
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Blog',
    'name' => 'Blog Molife',
    'url' => route('blog.index'),
    'description' => 'Tips praktis seputar kebiasaan baik, ibadah, keuangan, produktivitas, dan karier.',
    'blogPost' => collect($posts)->map(fn($p, $slug) => [
        '@type' => 'BlogPosting',
        'headline' => $p['title'],
        'url' => route('blog.show', $slug),
        'datePublished' => $p['date'],
        'author' => ['@type' => 'Organization', 'name' => $p['author']],
    ])->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<main class="blog-wrap">

    <div class="blog-hero">
        <div class="blog-eyebrow">Blog Molife</div>
        <h1>Cara <span class="serif">merapikan hidupmu,</span> satu kebiasaan setiap hari.</h1>
        <p>Tips praktis seputar ibadah, kebiasaan, keuangan, produktivitas, dan karier , topik yang bisa kamu lacak dan perbaiki langsung di Molife.</p>
    </div>

    <div class="blog-grid">
        @foreach($posts as $slug => $p)
        @php $c = $cmap[$p['color']] ?? $cmap['blue']; @endphp
        <a class="bcard" href="{{ route('blog.show', $slug) }}">
            <div class="bcard-cover" style="background:linear-gradient(135deg,{{ $c['from'] }},{{ $c['to'] }})">{{ $p['emoji'] }}</div>
            <div class="bcard-body">
                <div class="bcard-tags">
                    <span class="bchip" style="background:{{ $c['from'] }};color:{{ $c['txt'] }}">{{ $p['category'] }}</span>
                    <span style="font-size:12px;color:var(--gray-2)">{{ $p['read'] }}</span>
                </div>
                <h2>{{ $p['title'] }}</h2>
                <p class="bcard-excerpt">{{ $p['excerpt'] }}</p>
                <span class="bcard-more">Baca selengkapnya
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                </span>
            </div>
        </a>
        @endforeach
    </div>

    <div class="blog-cta">
        <h2>Berhenti sekadar membaca tips.</h2>
        <p>Lacak ibadah, kebiasaan, keuangan, dan kariermu dalam satu dasbor. Ubah niat baik jadi sistem yang berjalan sendiri.</p>
        <a class="btn btn-light btn-lg" style="margin-top:24px" href="{{ route('register') }}">Mulai Sekarang
            <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
    </div>

</main>
@endsection
