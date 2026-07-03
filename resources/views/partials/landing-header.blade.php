<header id="hdr">
  <div class="wrap nav">
    <a class="logo" href="{{ route('landing') }}"><img src="{{ asset('images/logo.png') }}" alt="molife" style="height:30px;width:auto;display:block"></a>
    <nav class="nav-links">
      <a href="{{ route('landing') }}#masalah">Masalah</a>
      <a href="{{ route('landing') }}#fitur">Fitur</a>
      <a href="{{ route('landing') }}#cara">Cara Kerja</a>
      <a href="{{ route('landing') }}#harga">Harga</a>
      <a href="{{ route('blog.index') }}">Blog</a>
      <a href="{{ route('landing') }}#faq">FAQ</a>
    </nav>
    <div class="nav-right">
      <a class="masuk" href="{{ route('login') }}">Masuk</a>
      <a class="btn btn-dark btn-sm" href="{{ route('register') }}">Mulai Sekarang</a>
      <button class="menu-toggle" aria-label="Menu"><svg width="24" height="24" fill="none" stroke="#101116" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
    </div>
  </div>
</header>
