{{-- Favicon tab browser (satu sumber untuk semua halaman yang memuat partial ini).
     Jangan deklarasikan ulang di halaman masing-masing supaya tidak saling beda versi. --}}
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon.png') }}?v=3">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon.png') }}?v=3">
<link rel="shortcut icon" href="{{ asset('images/icon.png') }}?v=3">

{{-- PWA: ikon home screen (Android via manifest, iPhone via apple-touch-icon) + warna tema --}}
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#111827">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/icon-180.png') }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Molife">
