<!DOCTYPE html>
<html lang="id" style="background-color:#F5F6F8">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('partials.pwa-head')
<title>molife · Satu aplikasi. Seluruh kehidupanmu.</title>
<meta name="description" content="Sholat, olahraga, mood, tugas, karier, dan keuangan dalam satu dasbor yang tenang. molife merangkum seluruh hidupmu jadi satu Life Score yang jujur." />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">
@include('partials.landing-css')
</head>
<body>

<!-- ============ HEADER ============ -->
@include('partials.landing-header')

<main>
  <!-- ============ HERO ============ -->
  <section class="hero">
    <div class="wrap hero-new">
      <span class="hn-pill rise d2">
        <span class="avatars">
          <span class="av av1"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="9.2" r="3.6"/><path d="M5.5 19c0-3.6 2.9-5.7 6.5-5.7s6.5 2.1 6.5 5.7z"/></svg></span><span class="av av2"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="9.2" r="3.6"/><path d="M5.5 19c0-3.6 2.9-5.7 6.5-5.7s6.5 2.1 6.5 5.7z"/></svg></span><span class="av av3"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="9.2" r="3.6"/><path d="M5.5 19c0-3.6 2.9-5.7 6.5-5.7s6.5 2.1 6.5 5.7z"/></svg></span>
        </span>
        <span class="hn-pill-txt"><b>Mari bergabung</b></span>
      </span>

      <h1 class="hn-title rise d3">Satu platform,<br><span class="dom-bubbles" aria-hidden="true"><span class="dbub g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.5A7.5 7.5 0 1 1 11.5 4 6 6 0 0 0 20 14.5z"/></svg></span><span class="dbub b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 7v10M17.5 7v10M9 12h6M4.5 9.5v5M19.5 9.5v5"/></svg></span><span class="dbub v"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M8.5 14c.7.9 2 1.5 3.5 1.5s2.8-.6 3.5-1.5"/><path d="M9 10h.01M15 10h.01"/></svg></span><span class="dbub o"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h7l-1 8 9-12h-7z"/></svg></span></span> seluruh hidupmu.</h1>

      <div class="hn-subbar rise d4">
        <p>Sholat, olahraga, mood, tugas, karier, dan keuangan dalam satu dasbor yang tenang. Bukan sekadar mencatat: <b>satu Life Score</b> harian yang mengubah kebiasaan kecil jadi sistem, bukan sekadar target.</p>
        <div class="hn-cta">
          <a class="btn btn-ghost btn-lg" href="#cara">Lihat Cara Kerja</a>
          <a class="btn btn-dark btn-lg" href="{{ route('register') }}">Rapikan Hidupmu <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
      </div>

    </div>
  </section>

  <!-- ============ PROBLEM ============ -->
  <section class="section problem" id="masalah">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Kenapa hidup terasa berat</div>
        <h2 class="title">Hidupmu nggak berantakan. <span class="serif">Cuma tersebar.</span></h2>
        <p class="sub">Setiap sisi hidupmu hidup di aplikasi yang berbeda, dan tidak satu pun yang pernah melihatmu secara utuh. Inilah yang diam-diam bikin lelah.</p>
      </div>
      <div class="pain-grid">
        <div class="pain" data-reveal>
          <div class="pain-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></div>
          <div><h3>Lima aplikasi, nol gambaran utuh</h3><p>Jadwal sholat di satu app, catatan gym di app lain, mood di jurnal, keuangan di spreadsheet, lamaran kerja di Notion. Kamu sibuk pindah-pindah, tapi tak pernah lihat keseluruhannya.</p></div>
        </div>
        <div class="pain" data-reveal>
          <div class="pain-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M3 4v4h4"/><path d="M12 8v4l2.5 2.5"/></svg></div>
          <div><h3>Konsisten 3 hari, lalu hilang</h3><p>Tanpa satu tempat yang mengingatkan dan menunjukkan progres, streak putus dan kamu nggak sadar kapan tepatnya mulai melempem.</p></div>
        </div>
        <div class="pain" data-reveal>
          <div class="pain-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/><circle cx="12" cy="12" r="4"/></svg></div>
          <div><h3>Sibuk seharian, tapi nggak tahu ke mana</h3><p>Kerja keras tiap hari tanpa bukti progres. Burnout datang diam-diam karena kamu mengukur usaha, bukan arah.</p></div>
        </div>
        <div class="pain" data-reveal>
          <div class="pain-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9 6.8 19.6l1-5.8L3.5 9.2l5.9-.9z"/></svg></div>
          <div><h3>Niat baik tanpa sistem</h3><p>Target ibadah, kesehatan, karier, dan finansial cuma jadi resolusi yang menguap tiap bulan. Bukan karena kamu malas, tapi karena tak ada sistem yang menahannya.</p></div>
        </div>
      </div>
      <p class="turn" data-reveal>Masalahnya bukan kurang niat.<br>Masalahnya kamu belum punya <span class="serif">sistem</span>, cuma niat dan lima aplikasi terpisah.</p>
    </div>
  </section>

  <!-- ============ SOLUTION / DASHBOARD ============ -->
  <section class="section" id="solusi">
    <div class="wrap">
      <div class="sol-head" data-reveal>
        <div class="sol-head-l">
          <div class="eyebrow" style="color:var(--green)">Solusinya</div>
          <h2 class="title">Satu dasbor. <span class="serif">Satu angka</span> yang jujur.</h2>
        </div>
        <p class="sub">molife menarik semua aktivitasmu ke satu layar, lalu menerjemahkannya jadi <b>Life Score</b>: gabungan Spiritual, Health, Mental, dan Produktivitas dalam satu angka 0-100 yang kamu lihat tiap pagi.</p>
      </div>
      <div class="sol-points" data-reveal>
        <div class="sol-point"><span class="b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span><div><h4>Lihat dirimu secara utuh</h4><p>Bukan lagi potongan-potongan terpisah. Satu pandangan, semua sisi hidup.</p></div></div>
        <div class="sol-point"><span class="b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span><div><h4>Tahu persis apa yang sedang turun</h4><p>Skor per domain langsung menunjukkan sisi mana yang butuh perhatian hari ini.</p></div></div>
        <div class="sol-point"><span class="b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span><div><h4>Insight harian dari semua aktivitas</h4><p>molife menghubungkan titik-titiknya dan memberi tahu apa yang penting, tanpa kamu menebak.</p></div></div>
      </div>

      <div class="window" data-reveal>
          <div class="win-bar"><i></i><i></i><i></i><span class="url">app.molife.space/dashboard</span></div>
          <div class="win-body">
            <aside class="win-side">
              <div class="win-brand"><span class="logo-mark"></span> molife</div>
              <div class="win-cap">Dashboard</div>
              <div class="win-nav active"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg> Overview</div>
              <div class="win-cap">Life</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M5 5l1.4 1.4M17.6 17.6L19 19M3 12h2M19 12h2"/></svg> Prayer</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M13 2L4 14h7l-1 8 9-12h-7z"/></svg> Gym</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg> Pomodoro</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5z"/></svg> Meditasi</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M9 14c.8 1 1.8 1.5 3 1.5s2.2-.5 3-1.5"/></svg> Mental</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg> Tasks &amp; Notes</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M5 20V11M12 20V5M19 20v-6"/></svg> Statistik</div>
              <div class="win-cap">Karier</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="6.5" width="17" height="13" rx="2"/><path d="M8.5 6.5V5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v1.5"/></svg> Career Hub</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg> Job Applications</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M13 3v5h5"/></svg> Job Preparation</div>
              <div class="win-cap">Bisnis</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M21 7v6h-6"/></svg> Bisnis Overview</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="4.5" width="17" height="15" rx="2"/><path d="M8 9h8M8 13h5"/></svg> Proposal &amp; Klien</div>
              <div class="win-cap">Finance</div>
              <div class="win-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h3.2a1.5 1.5 0 0 1 0 3H10m0 0h3.2a1.5 1.5 0 0 1 0 3H9.5"/></svg> Keuangan</div>
            </aside>
            <div class="win-main">
              <div class="win-h">Good morning, Mimo! <small>Molife › Dashboard</small></div>
              <div class="win-stats">
                <div class="wstat"><div class="ic" style="background:var(--green)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L4 14h7l-1 8 9-12h-7z"/></svg></div><div class="meta"><div class="v">82<small>%</small></div><div class="l">Life Score</div></div></div>
                <div class="wstat"><div class="ic" style="background:var(--orange)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3c0 3-4 4-4 8a4 4 0 0 0 8 0c0-1.5-1-2.5-1-2.5"/></svg></div><div class="meta"><div class="v">17<small> hari</small></div><div class="l">Prayer Streak</div></div></div>
                <div class="wstat"><div class="ic" style="background:var(--rose)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div><div class="meta"><div class="v">4<small> sesi</small></div><div class="l">Fokus Mingguan</div></div></div>
                <div class="wstat"><div class="ic" style="background:var(--blue)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h3.2a1.5 1.5 0 0 1 0 3H10m0 0h3.2a1.5 1.5 0 0 1 0 3H9.5"/></svg></div><div class="meta"><div class="v">Rp 1,2<small>jt</small></div><div class="l">Saldo Bulanan</div></div></div>
              </div>
              <div class="win-insights">
                <div class="wins green"><span class="di" style="background:#fff"><svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2"><path d="M13 2L4 14h7l-1 8 9-12h-7z"/></svg></span> Streak sholat 17 hari berturut-turut! <span class="t">Life</span></div>
                <div class="wins violet"><span class="di" style="background:#fff"><svg viewBox="0 0 24 24" fill="none" stroke="#7C5CF0" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9 14c.8 1 1.8 1.5 3 1.5s2.2-.5 3-1.5"/></svg></span> Rata-rata mood 7 hari 4/5, stabil. <span class="t">Mental</span></div>
                <div class="wins green"><span class="di" style="background:#fff"><svg viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v10"/></svg></span> Saldo bulan ini positif &amp; terjaga. <span class="t">Finance</span></div>
              </div>
              <div class="win-chart">
                <div class="wc-head"><span class="wc-title">Aktivitas Minggu Ini</span><span class="wc-sub">Konsisten 6 dari 7 hari</span></div>
                <div class="wc-bars">
                  <div class="wcb"><i style="height:55%"></i><span>S</span></div>
                  <div class="wcb"><i style="height:72%"></i><span>S</span></div>
                  <div class="wcb"><i style="height:46%"></i><span>R</span></div>
                  <div class="wcb"><i style="height:88%"></i><span>K</span></div>
                  <div class="wcb"><i style="height:38%"></i><span>J</span></div>
                  <div class="wcb"><i style="height:66%"></i><span>S</span></div>
                  <div class="wcb"><i style="height:94%"></i><span>M</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </section>

  <!-- ============ MODULES / FITUR ============ -->
  <section class="section" id="fitur" style="padding-top:24px">
    <div class="wrap">
      <div class="head center">
        <div class="eyebrow">Yang ada di dalam</div>
        <h2 class="title">Semua sisi hidupmu, <span class="serif">satu per satu.</span></h2>
        <p class="sub">Dari ibadah sampai keuangan dan karier, setiap modul dirancang ringan dan langsung tersambung ke Life Score-mu.</p>
      </div>
      <div class="mod-grid">

        <div class="mod" data-reveal>
          <span class="mod-tag tag-life">Life</span>
          <h3>Sholat &amp; Ibadah</h3>
          <p>Tracker sholat wajib, rawatib &amp; sunnah dengan streak harian yang bikin konsisten terasa ringan.</p>
          <div class="mod-viz"><div class="moodrow" id="viz-sholat"></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-life">Life</span>
          <h3>Olahraga &amp; Gym</h3>
          <p>Catat gym, lari, sepeda, renang, sampai badminton. Pilih olahraga yang kamu jalani, lacak progresnya.</p>
          <div class="mod-viz"><div class="bars" id="viz-gym"></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-life">Life</span>
          <h3>Mood &amp; Mental</h3>
          <p>Mood tracker, energi, dan refleksi harian. Lihat pola perasaanmu sebelum jadi masalah besar.</p>
          <div class="mod-viz"><div class="moodweek">
            <span class="mf good"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="8.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><path d="M8 14.5c1 1.3 2.4 2 4 2s3-.7 4-2"/></svg></span>
            <span class="mf good"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="8.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><path d="M8 14.5c1 1.3 2.4 2 4 2s3-.7 4-2"/></svg></span>
            <span class="mf ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="8.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><path d="M8.5 15h7"/></svg></span>
            <span class="mf low"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="8.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><path d="M8 16.3c1-1.3 2.4-2 4-2s3 .7 4 2"/></svg></span>
            <span class="mf ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="8.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><path d="M8.5 15h7"/></svg></span>
            <span class="mf good"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="8.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><path d="M8 14.5c1 1.3 2.4 2 4 2s3-.7 4-2"/></svg></span>
            <span class="mf good"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="8.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><circle cx="15.5" cy="10" r="1.1" fill="currentColor" stroke="none"/><path d="M8 14.5c1 1.3 2.4 2 4 2s3-.7 4-2"/></svg></span>
          </div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-life">Life</span>
          <h3>Siklus Haid</h3>
          <p>Catat haid, lihat prediksi siklus &amp; masa subur dari rata-ratamu sendiri. Hari haid otomatis jadi hari uzur, streak sholat tak terputus.</p>
          <div class="mod-viz"><div class="fin"><div class="c" style="background:var(--rose-soft)"><div class="l" style="color:var(--rose)">Siklus</div><div class="v" style="color:var(--rose)">28 hr</div></div><div class="c" style="background:#F1EDFD"><div class="l" style="color:var(--violet)">Haid</div><div class="v" style="color:var(--violet)">5 hr</div></div><div class="c" style="background:var(--ink);color:#fff"><div class="l">Berikutnya</div><div class="v">29 Jul</div></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-life">Life</span>
          <h3>Meditasi</h3>
          <p>Timer meditasi layar penuh dengan panduan napas, suara hujan, ombak, atau angin, plus streak harian. 10 menit sehari, pikiran lebih tenang.</p>
          <div class="mod-viz"><div class="pomo-wrap"><svg class="mini-pomo" viewBox="0 0 64 64"><circle class="tr" cx="32" cy="32" r="26"/><circle class="fl" cx="32" cy="32" r="26"/></svg><span class="t">10:00</span></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-life">Life</span>
          <h3>Link Penting</h3>
          <p>Simpan semua link penting beserta fungsinya dalam satu tempat, dari dokumen kerja sampai akun langganan, biar tak perlu cari-cari lagi.</p>
          <div class="mod-viz"><div class="tpl">
            <div class="tpr"><span class="tn">Tagihan Listrik</span><span class="tt b">Link</span></div>
            <div class="tpr"><span class="tn">Langganan Netflix</span><span class="tt v">Link</span></div>
            <div class="tpr"><span class="tn">Folder Foto Keluarga</span><span class="tt n">Link</span></div>
          </div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-core">Insight</span>
          <h3>Pomodoro &amp; Fokus</h3>
          <p>Timer fokus dengan riwayat sesi. Tahu persis berapa menit kamu benar-benar produktif minggu ini.</p>
          <div class="mod-viz"><div class="pomo-wrap"><svg class="mini-pomo" viewBox="0 0 64 64"><circle class="tr" cx="32" cy="32" r="26"/><circle class="fl" cx="32" cy="32" r="26"/></svg><span class="t">25:00</span></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-core">Insight</span>
          <h3>Tasks &amp; Notes</h3>
          <p>Tugas harian, mingguan, dan catatan dalam satu tempat, tanpa pindah ke aplikasi terpisah.</p>
          <div class="mod-viz"><div class="tasklist"><div class="ti done"><span class="cb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span><span>Sholat subuh tepat waktu</span></div><div class="ti done"><span class="cb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span><span>Kirim 1 lamaran</span></div><div class="ti"><span class="cb"></span>Review portfolio</div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-core">Insight</span>
          <h3>Statistik &amp; Life Score</h3>
          <p>Heatmap konsistensi 30 hari, grafik aktivitas, dan satu Life Score yang merangkum semuanya.</p>
          <div class="mod-viz"><div class="dots" id="viz-stat"></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-core">Insight</span>
          <h3>Impor &amp; Ekspor Data</h3>
          <p>Sudah punya catatan lamaran atau data klien di Excel? Unggah sekali, semuanya langsung masuk. Datamu juga bisa diunduh kapan pun.</p>
          <div class="mod-viz"><div class="tpl">
            <div class="tpr"><span class="tn">data-klien-lama.xlsx</span><span class="tt b">34 masuk</span></div>
            <div class="tpr"><span class="tn">lamaran-2026.csv</span><span class="tt v">12 masuk</span></div>
            <div class="tpr"><span class="tn">molife-bisnis.csv</span><span class="tt n">Ekspor</span></div>
          </div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-karir">Karier</span>
          <h3>Career Hub</h3>
          <p>Tetapkan target role, gaji, dan perusahaan impian. Lihat response rate &amp; interview rate-mu naik.</p>
          <div class="mod-viz"><div class="pipe"><div class="pipe-row"><span class="nm">Dikirim</span><span class="tk"><i style="width:100%;background:#3B6FF6"></i></span><span class="ct">12</span></div><div class="pipe-row"><span class="nm">Review</span><span class="tk"><i style="width:67%;background:#F5871F"></i></span><span class="ct">8</span></div><div class="pipe-row"><span class="nm">Interview</span><span class="tk"><i style="width:33%;background:#7C5CF0"></i></span><span class="ct">4</span></div><div class="pipe-row"><span class="nm">Offer</span><span class="tk"><i style="width:17%;background:#14B8A6"></i></span><span class="ct">2</span></div><div class="pipe-row"><span class="nm">Diterima</span><span class="tk"><i style="width:8%;background:#16A34A"></i></span><span class="ct">1</span></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-karir">Karier</span>
          <h3>Pelacak Lamaran</h3>
          <p>Semua lamaran dalam satu tabel: perusahaan, posisi, channel, status. Dari Wishlist sampai Offer.</p>
          <div class="mod-viz"><div class="appchips"><span class="ac sent">Dikirim · 8</span><span class="ac review">Review · 3</span><span class="ac intv">Interview · 2</span><span class="ac offer">Offer · 1</span></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-karir">Karier</span>
          <h3>Persiapan Lamaran</h3>
          <p>Simpan template email, pesan LinkedIn, dokumen, dan latihan interview yang sering dipakai di satu tempat.</p>
          <div class="mod-viz"><div class="tpl"><div class="tpr"><span class="tn">Email Lamaran</span><span class="tt b">Email</span></div><div class="tpr"><span class="tn">Cold Message Recruiter</span><span class="tt v">LinkedIn</span></div><div class="tpr"><span class="tn">Follow Up</span><span class="tt n">Other</span></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-karir">Karier</span>
          <h3>Lowongan Kerja</h3>
          <p>Rekomendasi lowongan dari berbagai sumber dunia yang cocok dengan target role-mu, remote maupun lokal. Eksklusif paket 6 Bulan &amp; 1 Tahun.</p>
          <div class="mod-viz"><div class="tpl">
            <div class="tpr"><span class="tn">Product Designer · Tokopedia</span><span class="tt b">Full Time</span></div>
            <div class="tpr"><span class="tn">Frontend Dev · Remotive</span><span class="tt v">Remote</span></div>
            <div class="tpr"><span class="tn">Data Analyst · Arbeitnow</span><span class="tt n">Baru</span></div>
          </div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-bisnis">Bisnis</span>
          <h3>Proposal &amp; Klien</h3>
          <p>Catat tiap proposal yang dikirim, data klien, proyek yang ditawarkan, nilai, dan respon mereka dalam satu pipeline.</p>
          <div class="mod-viz"><div class="pipe"><div class="pipe-row"><span class="nm">Prospek</span><span class="tk"><i style="width:100%;background:#7C5CF0"></i></span><span class="ct">9</span></div><div class="pipe-row"><span class="nm">Terkirim</span><span class="tk"><i style="width:67%;background:#6B7280"></i></span><span class="ct">6</span></div><div class="pipe-row"><span class="nm">Negosiasi</span><span class="tk"><i style="width:44%;background:#F5871F"></i></span><span class="ct">4</span></div><div class="pipe-row"><span class="nm">Deal</span><span class="tk"><i style="width:22%;background:#16A34A"></i></span><span class="ct">2</span></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-bisnis">Bisnis</span>
          <h3>Analitik &amp; Dokumen Bisnis</h3>
          <p>Pantau win rate, nilai pipeline, dan bidang klien teratas. Simpan link, file, dan template penawaran di satu tempat.</p>
          <div class="mod-viz"><div class="fin"><div class="c" style="background:#E6F4F1"><div class="l" style="color:#0F766E">Win Rate</div><div class="v" style="color:#0F766E">38%</div></div><div class="c" style="background:var(--blue-soft)"><div class="l" style="color:var(--blue)">Pipeline</div><div class="v" style="color:var(--blue)">24jt</div></div><div class="c" style="background:var(--ink);color:#fff"><div class="l">Deal</div><div class="v">9jt</div></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-bisnis">Bisnis</span>
          <h3>Kolaborasi Tim per Proyek</h3>
          <p>Undang partner lewat email untuk mengerjakan satu proyek bareng: proposal, template pesan, dan statistiknya. Kolaborator gratis, tanpa perlu langganan.</p>
          <div class="mod-viz"><div class="tpl">
            <div class="tpr"><span class="tn">Proyek Camemo</span><span class="tt b">2 kolaborator</span></div>
            <div class="tpr"><span class="tn">pelita@gmail.com</span><span class="tt v">Aktif</span></div>
            <div class="tpr"><span class="tn">riko@outlook.com</span><span class="tt n">Diundang</span></div>
          </div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-finance">Finance</span>
          <h3>Keuangan</h3>
          <p>Catat pemasukan, pengeluaran, dan saldo. Tahu persis ke mana uangmu pergi tiap bulan, tanpa ribet.</p>
          <div class="mod-viz"><div class="fin"><div class="c" style="background:var(--green-soft)"><div class="l" style="color:var(--green)">Masuk</div><div class="v" style="color:var(--green)">+1,8jt</div></div><div class="c" style="background:var(--rose-soft)"><div class="l" style="color:var(--rose)">Keluar</div><div class="v" style="color:var(--rose)">-600rb</div></div><div class="c" style="background:var(--ink);color:#fff"><div class="l">Saldo</div><div class="v">1,2jt</div></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-finance">Finance</span>
          <h3>Anggaran</h3>
          <p>Tetapkan budget per kategori, makanan, transportasi, hiburan, lalu pantau yang terpakai vs sisa.</p>
          <div class="mod-viz"><div class="savings"><div class="sv"><div class="sv-top"><span>Makanan</span><b>72%</b></div><div class="sv-bar"><i style="width:72%;background:var(--orange)"></i></div></div><div class="sv"><div class="sv-top"><span>Transportasi</span><b>45%</b></div><div class="sv-bar"><i style="width:45%;background:var(--blue)"></i></div></div><div class="sv"><div class="sv-top"><span>Hiburan</span><b>30%</b></div><div class="sv-bar"><i style="width:30%;background:var(--violet)"></i></div></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-finance">Finance</span>
          <h3>Tabungan &amp; Target</h3>
          <p>Kejar tujuan tabungan, dari dana darurat sampai beli kendaraan, dengan progres yang terlihat jelas.</p>
          <div class="mod-viz"><div class="savings"><div class="sv"><div class="sv-top"><span>Dana Darurat</span><b>65%</b></div><div class="sv-bar"><i style="width:65%;background:var(--green)"></i></div></div><div class="sv"><div class="sv-top"><span>Beli Motor</span><b>30%</b></div><div class="sv-bar"><i style="width:30%;background:var(--blue)"></i></div></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-finance">Finance</span>
          <h3>Scan Struk AI</h3>
          <p>Foto struk belanjamu, AI membaca dan mengisi form transaksinya otomatis: nominal, kategori, tanggal. Eksklusif paket 6 Bulan &amp; 1 Tahun.</p>
          <div class="mod-viz"><div class="tasklist"><div class="ti done"><span class="cb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span><span>Struk difoto</span></div><div class="ti done"><span class="cb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span><span>Rp 61.000 · Makanan terdeteksi</span></div><div class="ti done"><span class="cb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span><span>Form transaksi terisi otomatis</span></div></div></div>
        </div>

      </div>
    </div>
  </section>

  <!-- ============ MODULAR (dark) ============ -->
  <section class="section modular">
    <div class="wrap">
      <div data-reveal>
        <div class="eyebrow">Dibuat sesuai kamu</div>
        <h2 class="title">Nyalakan yang kamu butuh. <span class="serif">Sembunyikan sisanya.</span></h2>
        <p class="sub">Tidak semua orang butuh tracker renang atau Career Hub. molife sepenuhnya modular. Bangun dasbor versimu sendiri, dan data lama tidak akan hilang saat kamu mematikan menu.</p>
        <div class="hero-cta">
          <a class="btn" style="background:#fff;color:var(--ink)" href="{{ route('register') }}">Mulai Sekarang</a>
        </div>
      </div>
      <div class="toggle-panel" data-reveal>
        <div class="tpanel-head">
          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg></div>
          <div><b>Fitur Navigasi</b><span>Aktifkan atau sembunyikan menu dari sidebar</span></div>
        </div>
        <div class="trow"><div class="meta"><b>Sholat</b><span>Tracker sholat wajib, rawatib &amp; sunnah</span></div><span class="sw on"></span></div>
        <div class="trow"><div class="meta"><b>Gym</b><span>Log sesi gym dan kalori terbakar</span></div><span class="sw on"></span></div>
        <div class="trow"><div class="meta"><b>Pomodoro</b><span>Timer fokus &amp; riwayat sesi produktif</span></div><span class="sw on"></span></div>
        <div class="trow"><div class="meta"><b>Meditasi</b><span>Timer meditasi, suara alam &amp; streak harian</span></div><span class="sw on"></span></div>
        <div class="trow"><div class="meta"><b>Siklus Haid</b><span>Prediksi siklus &amp; sinkron hari uzur sholat</span></div><span class="sw on"></span></div>
        <div class="trow"><div class="meta"><b>Lari</b><span>Tracker jarak, pace, dan sesi lari</span></div><span class="sw"></span></div>
        <div class="trow"><div class="meta"><b>Stop Porn</b><span>Streak bebas pornografi &amp; kontrol diri</span></div><span class="sw"></span></div>
        <div class="trow"><div class="meta"><b>Lamaran Kerja</b><span>Kelola lamaran dan status interview</span></div><span class="sw on"></span></div>
      </div>
    </div>
  </section>

  <!-- ============ CARA KERJA ============ -->
  <section class="section" id="cara">
    <div class="wrap">
      <div class="head center">
        <div class="eyebrow">Cara Kerja</div>
        <h2 class="title">Bukan kejar target. <span class="serif">Bangun sistemnya.</span></h2>
        <p class="sub">molife bukan daftar resolusi. Ini sistem kecil yang berulang tiap hari, dan justru pengulangan itulah yang bikin hasil benar-benar menempel.</p>
      </div>
      <div class="scrolly" id="caraScrolly">
        <div class="scrolly-vis">
          <div class="loop" id="caraLoop" data-active="0">
            <svg class="loop-svg" viewBox="0 0 300 300">
              <circle class="loop-track" cx="150" cy="150" r="118"/>
              <circle class="loop-prog" cx="150" cy="150" r="118" transform="rotate(-90 150 150)"/>
              <g class="lnode n0"><circle class="ln-dot" cx="150" cy="32" r="13"/><text class="ln-lab" x="150" y="11" text-anchor="middle">Pilih fokus</text></g>
              <g class="lnode n1"><circle class="ln-dot" cx="252" cy="209" r="13"/><text class="ln-lab" x="258" y="248" text-anchor="middle">Catat</text></g>
              <g class="lnode n2"><circle class="ln-dot" cx="48" cy="209" r="13"/><text class="ln-lab" x="42" y="248" text-anchor="middle">Berputar</text></g>
            </svg>
            <div class="loop-center">
              <span class="lc-num serif" id="lcNum">01</span>
              <span class="lc-score"><b id="lcScore">82</b> Life Score</span>
            </div>
          </div>
        </div>
        <div class="scrolly-steps">
          <div class="sstep active" data-step="0">
            <div class="ss-n serif">01</div>
            <h3>Daftar &amp; pilih fokusmu</h3>
            <p>Buat akunmu, lalu nyalakan modul yang relevan: sholat, olahraga, karier, keuangan, sesukamu. Inilah pondasi sistemmu.</p>
          </div>
          <div class="sstep" data-step="1">
            <div class="ss-n serif">02</div>
            <h3>Catat sekali, lihat semua</h3>
            <p>Setiap aktivitas masuk ke satu dasbor tenang. molife yang menyusun, menghitung, dan merangkumnya jadi satu Life Score.</p>
          </div>
          <div class="sstep" data-step="2">
            <div class="ss-n serif">03</div>
            <h3>Biarkan sistemnya bekerja</h3>
            <p>Life Score naik, streak bertahan, dan tiap hari kamu cukup perbaiki satu hal kecil. Sistem yang berputar, bukan target yang dikejar lalu dilupakan.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ STATS ============ -->
  <section class="section stats" style="background:linear-gradient(180deg,#fff,#FAFAFB);border-top:1px solid var(--line)">
    <div class="wrap">
      <div class="head center">
        <div class="eyebrow">Bukti, bukan tebakan</div>
        <h2 class="title">Lihat dirimu <span class="serif">dalam angka.</span></h2>
        <p class="sub">Konsistensi 30 hari terakhir, terbaca dalam sekali pandang. Inilah yang membuat kebiasaan benar-benar menempel.</p>
      </div>
      <div class="heatwrap" data-reveal>
        <div class="heat-title">
          <b>Konsistensi 30 Hari Terakhir</b>
          <div class="legend"><span><i style="background:var(--green)"></i>Sholat</span><span><i style="background:var(--yellow)"></i>Sebagian</span><span><i style="background:var(--violet)"></i>Mood</span></div>
        </div>
        <div class="heatrow">
          <div class="rl"><svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M5 5l1.4 1.4M17.6 17.6L19 19M3 12h2M19 12h2"/></svg> Sholat</div>
          <div class="cells" id="heat-sholat"></div>
        </div>
        <div class="heatrow">
          <div class="rl"><svg viewBox="0 0 24 24" fill="none" stroke="var(--gray-2)" stroke-width="1.8" stroke-linecap="round"><path d="M13 2L4 14h7l-1 8 9-12h-7z"/></svg> Gym</div>
          <div class="cells" id="heat-gym"></div>
        </div>
        <div class="heatrow">
          <div class="rl"><svg viewBox="0 0 24 24" fill="none" stroke="var(--violet)" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M9 14c.8 1 1.8 1.5 3 1.5s2.2-.5 3-1.5"/></svg> Mood</div>
          <div class="cells" id="heat-mood"></div>
        </div>
        <div class="heat-foot">
          <div class="hf g"><div class="v" data-count="17">17</div><div class="l">Hari Streak Sholat</div></div>
          <div class="hf b"><div class="v" data-count="83">83</div><div class="l">% Hari Aktif</div></div>
          <div class="hf v"><div class="v" data-count="24">24</div><div class="l">Sesi Fokus Bulan Ini</div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ TESTIMONIALS ============ -->
  <section class="section">
    <div class="wrap">
      <div class="head center">
        <div class="eyebrow">Kata mereka</div>
        <h2 class="title">Dipakai orang yang ingin <span class="serif">hidup lebih teratur.</span></h2>
      </div>
      <div class="tg">
        <div class="tc" data-reveal>
          <div class="stars" id="st1"></div>
          <blockquote>“Akhirnya sholat, gym, dan target kerja ada di satu layar. Aku nggak lagi merasa hidupku jalan sendiri-sendiri.”</blockquote>
          <div class="tp"><span class="tav tav1">AR</span><div><b>Anisa Rahmawati</b><span>Product Designer</span></div></div>
        </div>
        <div class="tc" data-reveal>
          <div class="stars" id="st2"></div>
          <blockquote>“Life Score-nya bikin nagih. Sekali lihat, aku langsung tahu sisi mana yang minggu ini aku abaikan.”</blockquote>
          <div class="tp"><span class="tav tav2">RP</span><div><b>Raka Pratama</b><span>Software Engineer</span></div></div>
        </div>
        <div class="tc" data-reveal>
          <div class="stars" id="st3"></div>
          <blockquote>“Tampilannya tenang, nggak bikin overwhelmed. Justru itu yang bikin aku konsisten buka tiap pagi.”</blockquote>
          <div class="tp"><span class="tav tav3">DS</span><div><b>Dimas Saputra</b><span>Mahasiswa S2</span></div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ PRICING ============ -->
  <section class="section pricing" id="harga">
    <div class="wrap">
      <div class="head center">
        <div class="eyebrow" style="color:var(--green)">Harga</div>
        <h2 class="title">Bayar sekali. <span class="serif">Pilih durasimu.</span></h2>
        <p class="sub">Satu kali bayar untuk akses penuh ke semua modul molife. Makin panjang durasi, makin hemat per bulannya. Tanpa langganan otomatis.</p>
      </div>
      <div class="dur-wrap">
        <div class="dur-seg" id="durSeg">
          <button data-plan="1">1 Bulan</button>
          <button data-plan="3" class="active">3 Bulan</button>
          <button data-plan="6">6 Bulan</button>
          <button data-plan="12">1 Tahun</button>
        </div>
      </div>
      <div style="max-width:460px;margin:30px auto 0">
        <div class="price feat" data-reveal>
          <span class="price-badge" id="planBadge">Paling Populer</span>
          <h3 id="planTitle">molife · 3 Bulan</h3>
          <div class="amt"><span class="n" id="planPrice">Rp 29.000</span><span class="per" id="planPer">/ 3 bulan</span></div>
          <p class="desc" id="planDesc">Setara ± Rp 9.700/bulan. Cukup waktu untuk benar-benar membentuk kebiasaan.</p>
          <ul>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Semua tracker: sholat, olahraga, meditasi, siklus haid, mood, tugas</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Career Hub &amp; pelacak lamaran lengkap</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Bisnis: proposal, klien, dokumen, analitik &amp; kolaborasi tim per proyek</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Keuangan: pemasukan, pengeluaran, anggaran, tabungan</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Statistik 30 hari, insight, &amp; Life Score harian</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Impor &amp; ekspor data (Excel/CSV)</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Paket 6 Bulan &amp; 1 Tahun: fitur AI Scan Struk &amp; Lowongan Kerja dunia</li>
          </ul>
          <a class="btn btn-dark" href="{{ route('register') }}" id="planCta">Ambil Akses 3 Bulan</a>
        </div>
      </div>
      <p class="price-note">Akses penuh ke semua modul · Tanpa perpanjangan otomatis · Pembayaran bersifat final</p>
    </div>
  </section>

  <!-- ============ REFERRAL ============ -->
  <section class="section-sm">
    <div class="wrap">
      <div class="ref-box" data-reveal>
        <div>
          <div class="eyebrow">Program Referral</div>
          <h3>Ajak teman, dapat 20% komisi.</h3>
          <p>Setiap teman yang kamu undang dan berlangganan, kamu dapat 20% dari pembayaran mereka, berulang selama mereka aktif.</p>
        </div>
        <a class="btn btn-light btn-lg" href="{{ route('register') }}">Dapatkan Link Referral</a>
      </div>
    </div>
  </section>

  <!-- ============ FAQ ============ -->
  <section class="section faq" id="faq">
    <div class="wrap">
      <div class="head center">
        <div class="eyebrow">FAQ</div>
        <h2 class="title">Masih ada <span class="serif">pertanyaan?</span></h2>
      </div>
      <div class="faq-list">
        <details open>
          <summary>Apa itu Life Score?<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></summary>
          <p>Life Score adalah satu angka 0-100 yang menggabungkan empat sisi hidupmu: Spiritual, Health, Mental, dan Produktivitas. Setiap aktivitas yang kamu catat ikut menggerakkan skor ini, jadi kamu bisa langsung tahu kondisimu hari ini tanpa membuka banyak halaman.</p>
        </details>
        <details>
          <summary>Berapa biayanya dan apa yang saya dapat?<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></summary>
          <p>Satu kali bayar Rp 49.000 untuk 3 bulan akses penuh ke seluruh modul molife: sholat, olahraga, pomodoro, mood, tugas, statistik, Career Hub, Bisnis, keuangan, dan Life Score. Tidak ada langganan otomatis. Setelah 3 bulan, kamu sendiri yang memutuskan untuk memperpanjang.</p>
        </details>
        <details>
          <summary>Apakah pembayaran bisa dikembalikan?<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></summary>
          <p>Karena kamu langsung mendapat akses penuh ke seluruh produk begitu membayar, pembayaran bersifat final. Kami menampilkan setiap fitur secara terbuka di halaman ini supaya kamu bisa memastikan molife cocok untukmu sebelum membeli.</p>
        </details>
        <details>
          <summary>Saya harus pakai semua fiturnya?<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></summary>
          <p>Tidak. molife sepenuhnya modular. Nyalakan hanya modul yang relevan untukmu lewat menu Tampilan &amp; Fitur, dan sembunyikan sisanya. Mematikan sebuah menu tidak menghapus datamu.</p>
        </details>
        <details>
          <summary>Ada tracker sholat dan jadwalnya?<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></summary>
          <p>Ada. molife punya tracker sholat wajib, rawatib, dan sunnah lengkap dengan streak harian. Konsistensimu juga tercermin di Life Score dan statistik 30 hari.</p>
        </details>
        <details>
          <summary>Bisa untuk lamaran kerja dan keuangan juga?<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></summary>
          <p>Bisa. Career Hub membantumu menetapkan target karier dan melacak setiap lamaran dari Wishlist hingga Offer. Modul Keuangan mencatat pemasukan, pengeluaran, anggaran, dan tabungan dalam satu tempat.</p>
        </details>
        <details>
          <summary>Apakah data saya aman dan bisa diakses di mana saja?<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></summary>
          <p>Data kamu terenkripsi, tidak dijual ke pihak ketiga, dan bisa kamu export atau hapus kapan saja. molife berjalan di web dan perangkat seluler dengan sinkronisasi otomatis.</p>
        </details>
      </div>
    </div>
  </section>

  <!-- ============ FINAL CTA ============ -->
  <section class="cta">
    <div class="wrap">
      <div class="cta-box" data-reveal>
        <h2>Hidup yang lebih teratur <span class="serif">dimulai hari ini.</span></h2>
        <p>Satu dasbor untuk ibadah, kesehatan, mental, karier, dan keuanganmu. Akses penuh selama 3 bulan.</p>
        <a class="btn btn-light btn-lg" href="{{ route('register') }}">Ambil Akses 3 Bulan</a>
        <div class="cta-note">Pembayaran sekali · Tanpa perpanjangan otomatis</div>
      </div>
    </div>
  </section>
</main>

<!-- ============ FOOTER ============ -->
@include('partials.landing-footer')

{{-- ── QRIS payment modal (landing) ── --}}
<div id="lpPayModal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(16,17,22,.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:18px" onclick="if(event.target===this)closePay()">
  <div style="background:#fff;border-radius:26px;max-width:384px;width:100%;overflow:hidden;box-shadow:0 50px 90px -34px rgba(0,0,0,.5)">
    {{-- header --}}
    <div style="position:relative;padding:24px 26px 20px;color:#fff;background:linear-gradient(135deg,#101116,#23242B)">
      <div style="position:absolute;right:-34px;top:-34px;width:130px;height:130px;border-radius:50%;background:radial-gradient(circle,rgba(124,92,240,.4),transparent 70%)"></div>
      <button type="button" onclick="closePay()" aria-label="Tutup" style="position:absolute;top:16px;right:16px;width:32px;height:32px;border:none;border-radius:9px;background:rgba(255,255,255,.12);color:#fff;cursor:pointer;font-size:16px;line-height:1">&times;</button>
      <div style="position:relative;font-size:10px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:rgba(255,255,255,.5)">Pembayaran QRIS</div>
      <p style="position:relative;font-size:14px;color:rgba(255,255,255,.7);margin-top:12px">molife · <b id="lpPayPlan" style="color:#fff"></b></p>
      <div id="lpPayAmount" style="position:relative;font-size:30px;font-weight:800;letter-spacing:-.03em;margin-top:2px"></div>
    </div>
    {{-- body --}}
    <div style="padding:22px 26px">
      <div style="text-align:center">
        <div style="position:relative;display:inline-block;padding:14px;border:1px solid #F0F1F3;border-radius:18px;box-shadow:0 12px 30px -24px rgba(0,0,0,.35)">
          <span style="position:absolute;top:8px;left:8px;width:15px;height:15px;border-top:2px solid #101116;border-left:2px solid #101116;border-top-left-radius:5px"></span>
          <span style="position:absolute;top:8px;right:8px;width:15px;height:15px;border-top:2px solid #101116;border-right:2px solid #101116;border-top-right-radius:5px"></span>
          <span style="position:absolute;bottom:8px;left:8px;width:15px;height:15px;border-bottom:2px solid #101116;border-left:2px solid #101116;border-bottom-left-radius:5px"></span>
          <span style="position:absolute;bottom:8px;right:8px;width:15px;height:15px;border-bottom:2px solid #101116;border-right:2px solid #101116;border-bottom-right-radius:5px"></span>
          <img id="lpPayQr" src="" alt="QRIS" width="190" height="190" style="display:block;border-radius:10px">
        </div>
        <p style="font-size:12px;color:#9A9CA4;margin-top:12px">Bayar dari aplikasi apa pun yang mendukung QRIS.</p>
      </div>

      {{-- auto-confirm note --}}
      <div style="display:flex;gap:9px;align-items:flex-start;background:#F6F6F8;border-radius:14px;padding:13px 14px;margin-top:18px">
        <svg style="width:16px;height:16px;flex:none;color:#16A34A;margin-top:1px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p style="font-size:12.5px;color:#62656E;line-height:1.5;margin:0">Setelah pembayaran berhasil, kamu <b style="color:#101116">otomatis diarahkan</b> ke halaman masuk/daftar, lalu langsung ke dashboard dengan akses penuh.</p>
      </div>
    </div>
  </div>
</div>

<svg width="0" height="0" style="position:absolute"><defs><symbol id="ring-mark" viewBox="0 0 32 32"><circle cx="16" cy="16" r="12" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-dasharray="58 18" transform="rotate(-58 16 16)"/></symbol></defs></svg>

  <div class="scroll-score" id="scrollScore" aria-hidden="true">
    <svg viewBox="0 0 44 44"><circle class="ss-track" cx="22" cy="22" r="19"/><circle class="ss-fill" id="ssFill" cx="22" cy="22" r="19"/></svg>
    <div class="ss-meta"><b id="ssNum">0</b><span>Life Progress</span></div>
  </div>

@verbatim
<script>
  // inject logo mark
  document.querySelectorAll('.logo-mark').forEach(el=>{
    el.innerHTML = '<svg viewBox="0 0 32 32" style="width:1em;height:1em;display:block"><use href="#ring-mark"/></svg>';
    el.style.display='inline-flex'; el.style.color='var(--ink)';
  });

  const hdr = document.getElementById('hdr');
  addEventListener('scroll', () => hdr.classList.toggle('scrolled', scrollY > 8), {passive:true});

  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasIO = 'IntersectionObserver' in window;

  // ---- build mini visuals ----
  const star = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9 6.8 19.6l1-5.8L3.5 9.2l5.9-.9z"/></svg>';
  ['st1','st2','st3'].forEach(id=>{const e=document.getElementById(id); if(e) e.innerHTML=star.repeat(5);});

  // sholat mini row (greens + a couple yellows)
  (function(){
    const e=document.getElementById('viz-sholat'); if(!e)return;
    const pat=['g','g','y','g','g','g','y','g','g','g'];
    e.innerHTML=pat.map(c=>`<i style="background:${c==='g'?'var(--green)':'var(--yellow)'}"></i>`).join('');
  })();
  // gym bars
  (function(){
    const e=document.getElementById('viz-gym'); if(!e)return;
    const h=[40,68,52,80,34,60,46];
    e.innerHTML=h.map(v=>`<i style="height:${v}%;background:var(--ink)"></i>`).join('');
  })();
  // stat dots
  (function(){
    const e=document.getElementById('viz-stat'); if(!e)return;
    let html=''; for(let i=0;i<24;i++){const on=Math.random()>.35; html+=`<i style="${on?'background:var(--green)':''}"></i>`;}
    e.innerHTML=html;
  })();
  // pomodoro mini fill animate
  // (uses static dashoffset; fine)

  // ---- heatmap ----
  function heat(id, builder){
    const e=document.getElementById(id); if(!e)return;
    let html='';
    for(let i=0;i<30;i++) html+=`<i style="background:${builder(i)}"></i>`;
    e.innerHTML=html;
  }
  heat('heat-sholat', i=> i<11 ? '#EFF0F2' : (i===17||i===27 ? 'var(--green)' : 'var(--yellow)'));
  heat('heat-gym', ()=> '#EFF0F2');
  heat('heat-mood', i=>{
    const map={15:'#7C5CF0',19:'#7C5CF0',20:'#C9BBF6',21:'#7C5CF0',27:'#C9BBF6'};
    return map[i]||'#EFF0F2';
  });

  // ---- scroll reveal ----
  const reveals = document.querySelectorAll('[data-reveal]');
  if (!reduce && hasIO) {
    const ro = new IntersectionObserver((entries)=>{
      entries.forEach(en=>{
        if(!en.isIntersecting) return;
        const el=en.target;
        const sibs=[...el.parentElement.children].filter(c=>c.hasAttribute('data-reveal'));
        el.style.transitionDelay=(Math.max(0,sibs.indexOf(el))*80)+'ms';
        el.classList.add('in'); ro.unobserve(el);
      });
    },{threshold:.14, rootMargin:'0px 0px -8% 0px'});
    reveals.forEach(el=>ro.observe(el));
  } else { reveals.forEach(el=>el.classList.add('in')); }

  // reveal class for hero cluster rings (animate on load)
  setTimeout(()=>{ document.querySelector('.cluster-inner')?.classList.add('in'); }, 400);

  // ---- count up ----
  function countUp(el){
    const target=parseFloat(el.dataset.count);
    const dur=1200, t0=performance.now();
    function f(now){
      const p=Math.min((now-t0)/dur,1), e=1-Math.pow(1-p,3);
      el.textContent=Math.round(target*e).toLocaleString('id-ID');
      if(p<1) requestAnimationFrame(f);
    }
    requestAnimationFrame(f);
  }
  const counters=document.querySelectorAll('[data-count]');
  if(!reduce && hasIO){
    const co=new IntersectionObserver((ents)=>{
      ents.forEach(en=>{ if(en.isIntersecting){ countUp(en.target); co.unobserve(en.target);} });
    },{threshold:.6});
    counters.forEach(el=>{ el.textContent='0'; co.observe(el); });
  }

  // ---- scroll Life Score growth motif ----
  (function(){
    const w=document.getElementById('scrollScore'), fill=document.getElementById('ssFill'), num=document.getElementById('ssNum');
    if(!w) return;
    const C=119.38, MAX=100; let cur=-1, maxScroll=1, showY=0;
    function calc(){ maxScroll=(document.documentElement.scrollHeight - innerHeight)||1; showY=innerHeight*0.55; }
    function update(){
      const p=Math.min(1, Math.max(0, scrollY/maxScroll));
      const score=Math.round(p*MAX);
      w.classList.toggle('show', scrollY > showY);
      if(score!==cur){
        cur=score;
        if(num) num.textContent=score;
        if(fill) fill.style.strokeDashoffset=(C*(1-score/MAX)).toFixed(1);
      }
    }
    let tick=false;
    addEventListener('scroll',()=>{ if(!tick){ tick=true; requestAnimationFrame(()=>{update();tick=false;}); } },{passive:true});
    addEventListener('resize',()=>{ calc(); update(); },{passive:true});
    addEventListener('load',()=>{ calc(); update(); });
    calc(); update();
  })();

  // ---- Cara Kerja scrollytelling ----
  (function(){
    const scrolly=document.getElementById('caraScrolly'); if(!scrolly) return;
    const loop=document.getElementById('caraLoop');
    const steps=[...scrolly.querySelectorAll('.sstep')];
    const nodes=[...scrolly.querySelectorAll('.lnode')];
    const lcNum=document.getElementById('lcNum'), lcScore=document.getElementById('lcScore');
    const NUMS=['01','02','03'], SCORES=[82,86,92];
    function setActive(i){
      if(loop) loop.dataset.active=i;
      steps.forEach((s,idx)=>s.classList.toggle('active', idx===i));
      nodes.forEach((n,idx)=>{ n.classList.toggle('on', idx<=i); n.classList.toggle('cur', idx===i); });
      if(lcNum) lcNum.textContent=NUMS[i];
      if(lcScore) lcScore.textContent=SCORES[i];
    }
    const desktop=window.matchMedia('(min-width:861px)').matches;
    if(reduce || !hasIO || !desktop){
      steps.forEach(s=>s.classList.add('active'));
      nodes.forEach(n=>n.classList.add('on'));
      if(loop) loop.dataset.active=2;
      if(lcNum) lcNum.textContent='03'; if(lcScore) lcScore.textContent='92';
      return;
    }
    setActive(0);
    const io=new IntersectionObserver((ents)=>{
      ents.forEach(en=>{ if(en.isIntersecting) setActive(+en.target.dataset.step); });
    },{rootMargin:'-45% 0px -45% 0px', threshold:0});
    steps.forEach(s=>io.observe(s));
  })();

  // ---- pricing duration selector ----
  const PLANS = {
    '1':  {label:'1 Bulan', price:11000,  per:'/ 1 bulan',  badge:'Komitmen Ringan', desc:'Komitmen paling ringan untuk mencoba molife sepenuhnya, sekali bayar.'},
    '3':  {label:'3 Bulan', price:29000,  per:'/ 3 bulan',  badge:'Paling Populer',  desc:'Setara ± Rp 9.700/bulan. Cukup waktu untuk benar-benar membentuk kebiasaan.'},
    '6':  {label:'6 Bulan', price:49000,  per:'/ 6 bulan',  badge:'Hemat 26%',       desc:'Setara ± Rp 8.200/bulan. Termasuk fitur AI: Scan Struk & Lowongan Kerja.'},
    '12': {label:'1 Tahun', price:89000,  per:'/ 1 tahun',  badge:'Hemat 33%',       desc:'Setara ± Rp 7.400/bulan. Termasuk fitur AI: Scan Struk & Lowongan Kerja.'}
  };
  const durSeg = document.getElementById('durSeg');
  window.__planKey = '3';
  function setPlan(k){
    const p = PLANS[k]; if(!p) return;
    window.__planKey = k;
    const set=(id,v)=>{const e=document.getElementById(id); if(e) e.textContent=v;};
    set('planTitle','molife · '+p.label);
    set('planPrice','Rp '+p.price.toLocaleString('id-ID'));
    set('planPer',p.per);
    set('planDesc',p.desc);
    set('planBadge',p.badge);
    set('planCta','Ambil Akses '+p.label);
    [...durSeg.children].forEach(b=>b.classList.toggle('active', b.dataset.plan===k));
  }
  window.openPay = function(){
    const p = PLANS[window.__planKey]; if(!p) return;
    document.getElementById('lpPayPlan').textContent = p.label;
    document.getElementById('lpPayAmount').textContent = 'Rp ' + p.price.toLocaleString('id-ID');
    const ref = 'MOLIFE-' + window.__planKey + 'BLN-' + Date.now();
    document.getElementById('lpPayQr').src = 'https://api.qrserver.com/v1/create-qr-code/?size=440x440&margin=0&data=' + encodeURIComponent(ref);
    document.getElementById('lpPayModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  };
  window.closePay = function(){
    document.getElementById('lpPayModal').style.display = 'none';
    document.body.style.overflow = '';
  };
  if(durSeg){
    durSeg.addEventListener('click',e=>{const b=e.target.closest('button'); if(b) setPlan(b.dataset.plan);});
    setPlan('3');
  }

  const inner=document.querySelector('.cluster-inner');
  const hero=document.querySelector('.hero');
  if(inner && hero && !reduce && matchMedia('(pointer:fine)').matches){
    hero.addEventListener('mousemove',(e)=>{
      const r=hero.getBoundingClientRect();
      const x=(e.clientX-r.left)/r.width-.5, y=(e.clientY-r.top)/r.height-.5;
      inner.style.transform=`rotateY(${x*5}deg) rotateX(${-y*5}deg)`;
    });
    hero.addEventListener('mouseleave',()=>{ inner.style.transform=''; });
  }
</script>
@endverbatim
</body>
</html>
