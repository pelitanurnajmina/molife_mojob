<!DOCTYPE html>
<html lang="id" style="background-color:#FAFAFB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ── SEO ── --}}
    <title>@yield('seo_title') · Molife</title>
    <meta name="description" content="@yield('seo_description')">
    @hasSection('seo_keywords')<meta name="keywords" content="@yield('seo_keywords')">@endif
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Molife">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="Molife">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('seo_title') · Molife">
    <meta property="og:description" content="@yield('seo_description')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('seo_title') · Molife">
    <meta name="twitter:description" content="@yield('seo_description')">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">

    {{-- Shared landing design system --}}
    @include('partials.landing-css')

    @stack('head')

    @verbatim
    <style>
        /* ── Blog-specific (built on landing variables) ── */
        .blog-wrap{max-width:1080px;margin:0 auto;padding:0 28px}
        .post-wrap{max-width:740px;margin:0 auto;padding:0 28px}
        .blog-hero{text-align:center;padding:72px 0 40px;max-width:680px;margin:0 auto}
        .blog-hero h1{font-size:clamp(34px,6vw,56px);font-weight:800;letter-spacing:-.03em;line-height:1.04}
        .blog-hero p{color:var(--gray);font-size:18px;line-height:1.6;margin-top:16px}
        .blog-eyebrow{font-size:12.5px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--gray-2)}

        .blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;padding-bottom:24px}
        @media(max-width:900px){.blog-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:600px){.blog-grid{grid-template-columns:1fr}}
        .bcard{display:flex;flex-direction:column;background:var(--card);border:1px solid var(--line);border-radius:var(--r-lg);overflow:hidden;
            box-shadow:var(--shadow-sm);transition:transform .25s ease, box-shadow .25s ease}
        .bcard:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
        .bcard-cover{height:150px;display:flex;align-items:center;justify-content:center;font-size:52px}
        .bcard-body{padding:20px;display:flex;flex-direction:column;flex:1}
        .bcard-tags{display:flex;align-items:center;gap:8px;margin-bottom:10px}
        .bchip{font-size:11px;font-weight:700;padding:3px 9px;border-radius:var(--pill)}
        .bcard-body h2,.bcard-body h3{font-size:17px;font-weight:700;line-height:1.3;letter-spacing:-.01em}
        .bcard-excerpt{color:var(--gray);font-size:14px;line-height:1.55;margin-top:8px;flex:1}
        .bcard-more{display:inline-flex;align-items:center;gap:6px;font-weight:700;font-size:14px;color:var(--ink);margin-top:16px}
        .bcard:hover .bcard-more svg{transform:translateX(3px)}
        .bcard-more svg{width:16px;height:16px;transition:transform .2s}

        /* breadcrumb */
        .bcrumb{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--gray-2);padding-top:34px;margin-bottom:22px}
        .bcrumb a:hover{color:var(--ink)}

        /* post header */
        .post-head h1{font-size:clamp(30px,5vw,44px);font-weight:800;letter-spacing:-.03em;line-height:1.08;margin-top:14px}
        .post-meta{display:flex;align-items:center;gap:12px;margin-top:22px;color:var(--gray)}
        .post-avatar{width:38px;height:38px;border-radius:50%;background:var(--ink);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px}
        .post-cover{height:clamp(170px,30vw,240px);border-radius:var(--r-xl);display:flex;align-items:center;justify-content:center;font-size:72px;margin:34px 0 40px}

        /* prose */
        .prose{color:#2b2d36;font-size:17.5px;line-height:1.78}
        .prose>p{margin:0 0 1.3rem}
        .prose .lead{font-size:1.18rem;color:#3a3c45;line-height:1.7;margin-bottom:1.6rem}
        .prose h2{font-size:1.7rem;font-weight:800;letter-spacing:-.02em;margin:2.6rem 0 .9rem;color:var(--ink)}
        .prose h3{font-size:1.22rem;font-weight:700;margin:1.9rem 0 .55rem;color:var(--ink)}
        .prose ul,.prose ol{margin:0 0 1.3rem;padding-left:1.35rem}
        .prose li{margin:.45rem 0}
        .prose ul li{list-style:disc}
        .prose ol li{list-style:decimal}
        .prose strong{font-weight:700;color:var(--ink)}
        .prose a{color:var(--blue);font-weight:600;text-decoration:underline;text-underline-offset:2px}
        .prose blockquote{border-left:3px solid var(--ink);padding:.4rem 0 .4rem 1.2rem;margin:1.8rem 0;font-style:italic;color:#41434c;font-size:1.05rem}

        /* CTA blocks */
        .blog-cta{margin:64px 0;border-radius:var(--r-xl);background:var(--ink);color:#fff;padding:48px 32px;text-align:center}
        .blog-cta h2{font-size:clamp(24px,4vw,32px);font-weight:800;letter-spacing:-.02em}
        .blog-cta p{color:rgba(255,255,255,.72);margin:12px auto 0;max-width:560px;line-height:1.6}
        .post-cta{margin:48px 0;border:1px solid var(--line);background:var(--card);border-radius:var(--r-xl);padding:30px;text-align:center;box-shadow:var(--shadow-sm)}
        .post-cta p.t{font-size:1.15rem;font-weight:800;letter-spacing:-.01em}
        .post-cta p.s{font-size:.92rem;color:var(--gray);margin:8px auto 0;max-width:440px;line-height:1.55}

        .related-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
        @media(max-width:700px){.related-grid{grid-template-columns:1fr}}
    </style>
    @endverbatim
</head>
<body>

@include('partials.landing-header')

@yield('content')

@include('partials.landing-footer')

<script>
    var hdr = document.getElementById('hdr');
    if (hdr) addEventListener('scroll', function(){ hdr.classList.toggle('scrolled', scrollY > 8); }, {passive:true});
</script>
</body>
</html>
