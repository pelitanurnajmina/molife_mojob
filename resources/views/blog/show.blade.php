@extends('blog.layout')

@section('seo_title', $post['title'])
@section('seo_description', $post['description'])
@section('seo_keywords', $post['keywords'])
@section('canonical', route('blog.show', $post['slug']))
@section('og_type', 'article')

@php
    $cmap = [
        'green'  => ['from'=>'#E7F4EC','to'=>'#d6efe0','txt'=>'#16A34A'],
        'blue'   => ['from'=>'#E8EEFC','to'=>'#dbe6fb','txt'=>'#3B6FF6'],
        'violet' => ['from'=>'#EEEAFC','to'=>'#e4dcfb','txt'=>'#7C5CF0'],
        'orange' => ['from'=>'#FBEEDD','to'=>'#f8e4c9','txt'=>'#F5871F'],
        'rose'   => ['from'=>'#FCE8EC','to'=>'#fbdce3','txt'=>'#EF4D6A'],
    ];
    $c = $cmap[$post['color']] ?? $cmap['blue'];
    $published = \Carbon\Carbon::parse($post['date'])->locale('id')->translatedFormat('j F Y');
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post['title'],
    'description' => $post['description'],
    'image' => asset('images/logo.png'),
    'datePublished' => $post['date'],
    'dateModified' => $post['updated'] ?? $post['date'],
    'author' => ['@type' => 'Organization', 'name' => $post['author'], 'url' => route('landing')],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Molife',
        'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo.png')],
    ],
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('blog.show', $post['slug'])],
    'keywords' => $post['keywords'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>route('landing')],
        ['@type'=>'ListItem','position'=>2,'name'=>'Blog','item'=>route('blog.index')],
        ['@type'=>'ListItem','position'=>3,'name'=>$post['title'],'item'=>route('blog.show', $post['slug'])],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<article class="post-wrap">

    <nav class="bcrumb" aria-label="Breadcrumb">
        <a href="{{ route('landing') }}">Beranda</a><span>/</span>
        <a href="{{ route('blog.index') }}">Blog</a><span>/</span>
        <span style="color:var(--gray)">{{ $post['category'] }}</span>
    </nav>

    <header class="post-head">
        <div class="bcard-tags">
            <span class="bchip" style="background:{{ $c['from'] }};color:{{ $c['txt'] }}">{{ $post['category'] }}</span>
            <span style="font-size:13px;color:var(--gray-2)">{{ $post['read'] }} baca</span>
        </div>
        <h1>{{ $post['title'] }}</h1>
        <div class="post-meta">
            <span class="post-avatar">M</span>
            <div>
                <p style="font-weight:700;color:var(--ink)">{{ $post['author'] }}</p>
                <time datetime="{{ $post['date'] }}" style="font-size:13px;color:var(--gray-2)">{{ $published }}</time>
            </div>
        </div>
    </header>

    <div class="post-cover" style="background:linear-gradient(135deg,{{ $c['from'] }},{{ $c['to'] }})">{{ $post['emoji'] }}</div>

    <div class="prose">
        @include('blog.posts.' . $post['slug'])
    </div>

    <div class="post-cta">
        <p class="t">Lacak ini langsung di Molife</p>
        <p class="s">Semua yang kamu baca di sini bisa kamu catat, lacak, dan pantau progresnya dalam satu dasbor.</p>
        <a class="btn btn-dark btn-lg" style="margin-top:20px" href="{{ route('register') }}">Coba Molife Sekarang
            <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
    </div>
</article>

@if(count($related))
<section class="blog-wrap" style="margin-top:64px">
    <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin-bottom:18px">Baca juga</h2>
    <div class="related-grid">
        @foreach($related as $slug => $p)
        @php $rc = $cmap[$p['color']] ?? $cmap['blue']; @endphp
        <a class="bcard" href="{{ route('blog.show', $slug) }}">
            <div class="bcard-cover" style="height:96px;font-size:34px;background:linear-gradient(135deg,{{ $rc['from'] }},{{ $rc['to'] }})">{{ $p['emoji'] }}</div>
            <div class="bcard-body" style="padding:16px">
                <span style="font-size:11px;font-weight:700;color:{{ $rc['txt'] }}">{{ $p['category'] }}</span>
                <h3 style="margin-top:4px">{{ $p['title'] }}</h3>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif
@endsection
