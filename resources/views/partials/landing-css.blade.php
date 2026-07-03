@verbatim
<style>
  /* Smooth cross-page transitions (no white flash between navigations) */
  @view-transition { navigation: auto; }
  ::view-transition-old(root), ::view-transition-new(root) { animation-duration: .18s; }
  :root{
    --bg:#F5F6F8;
    --ink:#101116;
    --ink-2:#23242B;
    --gray:#62656E;
    --gray-2:#9A9CA4;
    --line:#E8E9EC;
    --line-2:#F0F1F3;
    --card:#FFFFFF;
    --green:#16A34A; --green-soft:#E7F4EC;
    --blue:#3B6FF6;  --blue-soft:#E8EEFC;
    --violet:#7C5CF0;--violet-soft:#EEEAFC;
    --orange:#F5871F;--orange-soft:#FBEEDD;
    --rose:#EF4D6A; --rose-soft:#FCE8EC;
    --yellow:#F4C430;
    --shadow-xl:0 40px 80px -36px rgba(18,18,40,.20), 0 12px 30px -18px rgba(18,18,40,.12);
    --shadow-md:0 20px 44px -28px rgba(18,18,40,.22), 0 4px 14px -8px rgba(18,18,40,.08);
    --shadow-sm:0 10px 28px -22px rgba(18,18,40,.20);
    --r-xl:28px; --r-lg:22px; --r-md:18px; --r-sm:14px; --pill:999px;
    --maxw:1180px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  html{scroll-behavior:smooth}
  body{
    font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:var(--ink);
    background:
      radial-gradient(1100px 640px at 82% -6%, #FFFFFF 0%, rgba(255,255,255,0) 58%),
      radial-gradient(900px 700px at 4% 4%, #FBFAFF 0%, rgba(251,250,255,0) 52%),
      linear-gradient(180deg,#FAFAFB 0%, #F4F5F7 100%);
    -webkit-font-smoothing:antialiased;line-height:1.5;overflow-x:hidden;
  }
  a{color:inherit;text-decoration:none}
  .wrap{max-width:var(--maxw);margin:0 auto;padding:0 28px}
  .serif{font-family:'Instrument Serif',Georgia,serif;font-style:italic;font-weight:400}
  .eyebrow{font-size:12.5px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}

  /* ---------- buttons ---------- */
  .btn{position:relative;overflow:hidden;display:inline-flex;align-items:center;justify-content:center;gap:8px;
    font-weight:600;font-size:15px;line-height:1;padding:15px 26px;border-radius:var(--pill);
    border:1px solid transparent;cursor:pointer;white-space:nowrap;
    transition:transform .18s ease, box-shadow .25s ease, background .2s ease, border-color .2s ease}
  .btn::after{content:"";position:absolute;top:0;left:-140%;width:55%;height:100%;pointer-events:none;
    background:linear-gradient(110deg,transparent,rgba(255,255,255,.30),transparent);
    transform:skewX(-18deg);transition:left .65s cubic-bezier(.3,.7,.3,1)}
  .btn:hover::after{left:160%}
  .btn-dark{background:var(--ink);color:#fff}
  .btn-dark:hover{transform:translateY(-2px);box-shadow:0 16px 30px -16px rgba(16,17,22,.55)}
  .btn-ghost{background:rgba(255,255,255,.7);color:var(--ink);border-color:var(--line)}
  .btn-ghost::after{background:linear-gradient(110deg,transparent,rgba(124,92,240,.10),transparent)}
  .btn-ghost:hover{background:#fff;border-color:#d9dade;transform:translateY(-2px)}
  .btn-sm{padding:12px 20px;font-size:14px}
  .btn-lg{padding:17px 30px;font-size:16px}

  /* ---------- header ---------- */
  header{position:sticky;top:0;z-index:60;
    background:rgba(245,246,248,.94);
    border-bottom:1px solid transparent;transition:border-color .3s ease}
  header.scrolled{border-color:var(--line)}
  .nav{display:flex;align-items:center;justify-content:space-between;height:78px}
  .logo{display:flex;align-items:center;gap:11px;font-weight:700;font-size:21px;letter-spacing:-.02em}
  .logo svg{width:27px;height:27px;flex:none;color:var(--ink)}
  .nav-links{display:flex;gap:34px;font-size:15px;color:var(--ink-2);font-weight:500}
  .nav-links a{opacity:.8;transition:opacity .2s}
  .nav-links a:hover{opacity:1}
  .nav-right{display:flex;align-items:center;gap:16px}
  .masuk{font-weight:600;font-size:15px;opacity:.85}
  .masuk:hover{opacity:1}
  .menu-toggle{display:none;background:none;border:none;cursor:pointer;padding:8px}

  /* ---------- section base ---------- */
  .section{padding:104px 0}
  .section-sm{padding:72px 0}
  .head{max-width:680px}
  .head.center{margin-inline:auto;text-align:center}
  .head .eyebrow{color:var(--violet)}
  .title{margin-top:16px;font-size:clamp(31px,4vw,46px);line-height:1.05;letter-spacing:-.03em;font-weight:800}
  .title .serif{color:#7A7C84;font-size:1.04em}
  .sub{margin-top:18px;font-size:18px;line-height:1.62;color:var(--gray);max-width:580px}
  .head.center .sub{margin-inline:auto}

  /* ---------- hero ---------- */
  .hero{position:relative;padding:64px 0 92px;overflow:hidden}
  .hero::before{content:"";position:absolute;inset:-12% -6% auto -6%;height:128%;z-index:0;pointer-events:none;
    background:
      radial-gradient(38% 48% at 80% 26%, rgba(124,92,240,.18), transparent 72%),
      radial-gradient(36% 44% at 93% 62%, rgba(59,111,246,.15), transparent 72%),
      radial-gradient(40% 46% at 9% 24%, rgba(22,163,74,.13), transparent 72%),
      radial-gradient(34% 40% at 26% 88%, rgba(245,135,31,.09), transparent 72%)}
  .hero::after{content:"";position:absolute;inset:0;z-index:0;pointer-events:none;opacity:.6;
    background-image:radial-gradient(rgba(16,17,22,.05) 1px, transparent 1.4px);background-size:24px 24px;
    -webkit-mask-image:radial-gradient(125% 80% at 50% 0%, #000 30%, transparent 75%);
    mask-image:radial-gradient(125% 80% at 50% 0%, #000 30%, transparent 75%)}
  .hero-grid{position:relative;z-index:1;display:grid;grid-template-columns:minmax(0,1.02fr) minmax(0,.98fr);gap:52px;align-items:center}
  .pill-badge{display:inline-flex;align-items:center;gap:9px;padding:8px 16px 8px 13px;border:1px solid var(--line);
    border-radius:var(--pill);background:rgba(255,255,255,.72);font-size:12.5px;font-weight:600;
    letter-spacing:.1em;text-transform:uppercase;color:var(--gray)}
  .pill-badge svg{width:15px;height:15px;color:var(--violet)}
  h1.h1{margin-top:24px;font-size:clamp(44px,6.1vw,80px);line-height:.99;letter-spacing:-.035em;font-weight:800}
  h1.h1 .serif{font-size:1.02em;background:linear-gradient(102deg,#16A34A 4%,#3B6FF6 48%,#7C5CF0 96%);
    -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:transparent}
  .hero-sub{margin-top:24px;max-width:530px;font-size:18.5px;line-height:1.62;color:var(--gray)}
  .hero-cta{display:flex;gap:12px;margin-top:34px;flex-wrap:wrap}
  .hero-cta .btn-lg{padding:16px 25px}
  .btn-arrow{width:18px;height:18px;margin-left:3px;transition:transform .22s ease}
  .btn:hover .btn-arrow{transform:translateX(4px)}
  .cluster::before{content:"";position:absolute;inset:-14% -8%;z-index:0;pointer-events:none;border-radius:50%;
    background:radial-gradient(50% 50% at 50% 42%, rgba(124,92,240,.16), rgba(59,111,246,.10) 45%, transparent 72%);
    filter:blur(8px)}
  .cluster-inner{position:relative;z-index:1}
  .proof{display:flex;align-items:center;gap:14px;margin-top:34px}
  .avatars{display:flex}
  .avatars .av{width:37px;height:37px;border-radius:50%;border:2.5px solid var(--bg);margin-left:-11px;
    display:flex;align-items:center;justify-content:center;flex:none}
  .avatars .av:first-child{margin-left:0}
  .avatars .av svg{width:20px;height:20px;color:#fff}
  .av1{background:linear-gradient(135deg,#7C5CF0,#A38EF3)}
  .av2{background:linear-gradient(135deg,#16A34A,#41C97B)}
  .av3{background:linear-gradient(135deg,#F5871F,#F7A24E)}
  .proof p{font-size:14px;color:var(--gray)}
  .proof b{color:var(--ink);font-weight:700}

  /* ---------- hero mock cluster ---------- */
  .cluster{position:relative;min-height:474px}
  .card{background:var(--card);border:1px solid var(--line-2);border-radius:var(--r-lg);box-shadow:var(--shadow-md)}
  .cluster{perspective:1500px}
  .cluster-inner{transform-style:preserve-3d;transition:transform .5s cubic-bezier(.2,.7,.2,1);will-change:transform;position:relative;height:100%}

  .hero-app{position:relative;z-index:1;width:336px;margin-left:auto;background:#fff;border:1px solid var(--line);
    border-radius:28px;box-shadow:var(--shadow-xl);padding:16px}
  .ha-head{display:flex;align-items:center;justify-content:space-between;padding:5px 7px 14px}
  .ha-brand{display:flex;align-items:center;gap:7px;font-weight:700;font-size:15px}
  .ha-brand .logo-mark{width:18px;height:18px}
  .ha-streak{font-size:11px;font-weight:700;color:#C9710F;background:var(--orange-soft);padding:6px 11px;border-radius:99px;white-space:nowrap}
  .ha-score{color:#fff;border-radius:18px;padding:18px 20px;overflow:hidden;
    background:radial-gradient(150% 130% at 12% 105%, rgba(46,200,130,.18), transparent 52%), var(--ink)}
  .ha-rings{padding:16px 7px 5px}

  .score-card{position:absolute;z-index:4;left:0;top:104px;width:256px;color:#fff;overflow:hidden;
    background:radial-gradient(150% 130% at 12% 105%, rgba(46,200,130,.16), rgba(0,0,0,0) 52%), var(--ink);
    border-radius:var(--r-lg);padding:24px 26px 24px;box-shadow:var(--shadow-xl)}
  .score-card .eyebrow{color:#85868F}
  .score-row{display:flex;align-items:baseline;gap:11px;margin-top:12px;flex-wrap:nowrap}
  .score-row .n{font-size:62px;font-weight:800;letter-spacing:-.04em;line-height:.9}
  .score-row .d{font-size:13px;font-weight:600;color:#54D88A;white-space:nowrap}
  .score-spark{margin-top:16px;height:48px;width:100%;display:block}
  .spark-cap{margin-top:8px;font-size:11px;font-weight:500;color:#7C7D86}

  .rings-card{position:absolute;z-index:5;right:0;top:132px;width:248px;padding:18px;box-shadow:var(--shadow-xl)}
  .rings-head{font-size:13px;font-weight:700;color:var(--ink);margin-bottom:4px}
  .rings-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px}
  .ringbox{display:flex;flex-direction:column;align-items:center;text-align:center;gap:6px;padding:11px 6px;border-radius:14px}
  .ringbox.g{background:var(--green-soft)} .ringbox.b{background:var(--blue-soft)}
  .ringbox.v{background:var(--violet-soft)} .ringbox.o{background:var(--orange-soft)}
  .ring{width:36px;height:36px;flex:none;transform:rotate(-90deg)}
  .ring .track{fill:none;stroke:rgba(0,0,0,.07);stroke-width:6}
  .ring .fill{fill:none;stroke-width:6;stroke-linecap:round;stroke-dasharray:113;stroke-dashoffset:113;
    transition:stroke-dashoffset 1.1s cubic-bezier(.2,.7,.2,1)}
  .in .ring .fill{stroke-dashoffset:calc(113 - 113 * var(--p) / 100)}
  .ringbox.g .fill{stroke:var(--green)} .ringbox.b .fill{stroke:var(--blue)}
  .ringbox.v .fill{stroke:var(--violet)} .ringbox.o .fill{stroke:var(--orange)}
  .ringbox .lab{display:flex;flex-direction:column;align-items:center;line-height:1.15}
  .ringbox .lab b{font-size:16px;font-weight:800;letter-spacing:-.02em}
  .ringbox .lab span{font-size:10.5px;color:var(--gray);font-weight:600}

  .streak-card{position:absolute;z-index:6;left:0;top:0;display:flex;align-items:center;gap:13px;
    padding:14px 18px;border-radius:var(--r-md);box-shadow:var(--shadow-md)}
  .streak-ico{width:42px;height:42px;border-radius:12px;background:var(--green-soft);display:flex;align-items:center;justify-content:center}
  .streak-ico svg{width:22px;height:22px;color:var(--green)}
  .streak-card b{font-size:21px;font-weight:800;letter-spacing:-.02em;display:block;line-height:1}
  .streak-card span{font-size:12px;color:var(--gray);font-weight:500}

  .insight-card{position:absolute;z-index:6;left:-18px;bottom:30px;width:240px;padding:15px 17px;border-radius:18px;
    box-shadow:var(--shadow-xl);background:#fff;border:1px solid var(--line)}
  .insight-card .eyebrow{font-size:10.5px;color:var(--gray-2);letter-spacing:.14em}
  .insight-row{display:flex;align-items:center;gap:11px;margin-top:11px}
  .insight-row .i{width:30px;height:30px;border-radius:9px;flex:none;display:flex;align-items:center;justify-content:center}
  .insight-row .i.o{background:var(--orange-soft)} .insight-row .i.o svg{color:var(--orange)}
  .insight-row .i.v{background:var(--violet-soft)} .insight-row .i.v svg{color:var(--violet)}
  .insight-row .i svg{width:16px;height:16px}
  .insight-row p{font-size:13px;color:var(--ink-2);font-weight:500;line-height:1.35}

  /* ---------- problem ---------- */
  .problem{background:linear-gradient(180deg,rgba(255,255,255,0) 0%,#fff 9%,#FAFAFB 100%);border-bottom:1px solid var(--line)}
  .problem .head .eyebrow{color:var(--rose)}
  .pain-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-top:54px}
  .pain{display:flex;gap:18px;padding:26px 26px;background:var(--card);border:1px solid var(--line-2);border-radius:var(--r-md);box-shadow:var(--shadow-sm)}
  .pain-ico{width:46px;height:46px;border-radius:13px;flex:none;display:flex;align-items:center;justify-content:center;background:var(--rose-soft)}
  .pain-ico svg{width:23px;height:23px;color:var(--rose)}
  .pain h3{font-size:18px;font-weight:700;letter-spacing:-.01em}
  .pain p{margin-top:7px;font-size:15px;line-height:1.58;color:var(--gray)}
  .turn{margin-top:46px;text-align:center;font-size:clamp(20px,2.4vw,27px);font-weight:700;letter-spacing:-.02em;line-height:1.35}
  .turn .serif{color:var(--violet);font-size:1.08em}

  /* ---------- solution / dashboard window ---------- */
  .sol-grid{display:grid;grid-template-columns:minmax(0,.92fr) minmax(0,1.08fr);gap:56px;align-items:center;margin-top:8px}
  .sol-head{display:flex;align-items:flex-end;justify-content:space-between;gap:48px;flex-wrap:wrap;margin-top:8px}
  .sol-head-l{flex:1 1 500px}
  .sol-head .sub{flex:1 1 360px;max-width:480px;margin-top:0}
  .sol-points{margin:36px 0 42px;display:grid;grid-template-columns:repeat(3,1fr);gap:32px}
  .sol-point{display:flex;gap:14px;align-items:flex-start}
  .sol-point .b{width:26px;height:26px;border-radius:8px;flex:none;display:flex;align-items:center;justify-content:center;background:var(--ink);margin-top:1px}
  .sol-point .b svg{width:15px;height:15px;color:#fff}
  .sol-point h4{font-size:16.5px;font-weight:700}
  .sol-point p{font-size:14.5px;color:var(--gray);line-height:1.55;margin-top:3px}

  .window{border-radius:var(--r-xl);background:#fff;border:1px solid var(--line);box-shadow:var(--shadow-xl);overflow:hidden}
  .win-bar{display:flex;align-items:center;gap:7px;padding:13px 16px;border-bottom:1px solid var(--line-2);background:#FCFCFD}
  .win-bar i{width:11px;height:11px;border-radius:50%;background:#E2E3E7;display:block}
  .win-bar i:nth-child(1){background:#FF6159} .win-bar i:nth-child(2){background:#FFBD2E} .win-bar i:nth-child(3){background:#28C840}
  .win-bar .url{margin-left:12px;font-size:12px;color:var(--gray-2);background:#fff;border:1px solid var(--line-2);
    padding:5px 12px;border-radius:8px;font-weight:500}
  .win-body{display:grid;grid-template-columns:188px 1fr}
  .win-side{border-right:1px solid var(--line-2);padding:18px 14px;background:#fff}
  .win-brand{display:flex;align-items:center;gap:8px;font-weight:700;font-size:16px;margin-bottom:18px;padding:0 4px}
  .win-brand svg{width:19px;height:19px}
  .win-cap{font-size:10px;font-weight:700;letter-spacing:.14em;color:var(--gray-2);margin:14px 6px 8px}
  .win-nav{display:flex;align-items:center;gap:10px;padding:9px 11px;border-radius:10px;font-size:13.5px;font-weight:500;color:var(--ink-2)}
  .win-nav svg{width:16px;height:16px;color:var(--gray);flex:none}
  .win-nav.active{background:var(--ink);color:#fff} .win-nav.active svg{color:#fff}
  .win-main{padding:22px 24px;background:#FAFBFC;display:flex;flex-direction:column}
  .win-h{font-size:20px;font-weight:800;letter-spacing:-.02em}
  .win-h small{display:block;font-size:12px;color:var(--gray-2);font-weight:500;margin-top:2px}
  .win-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:18px}
  .wstat{display:flex;align-items:center;gap:13px;background:#fff;border:1px solid var(--line-2);border-radius:var(--r-sm);padding:14px 15px}
  .wstat .ic{width:38px;height:38px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex:none}
  .wstat .ic svg{width:18px;height:18px;color:#fff}
  .wstat .meta{min-width:0}
  .wstat .v{font-size:19px;font-weight:800;letter-spacing:-.02em;white-space:nowrap}
  .wstat .v small{font-size:11px;font-weight:600;color:var(--gray-2)}
  .wstat .l{font-size:11.5px;color:var(--gray);font-weight:500;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .win-insights{margin-top:14px;display:flex;flex-direction:column;gap:8px}
  .wins{display:flex;align-items:center;gap:10px;padding:11px 13px;border-radius:11px;font-size:12.5px;font-weight:500;color:var(--ink-2)}
  .wins .t{margin-left:auto;font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px}
  .wins.green{background:var(--green-soft)} .wins.green .t{color:var(--green)}
  .wins.violet{background:var(--violet-soft)} .wins.violet .t{color:var(--violet)}
  .wins .di{width:24px;height:24px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex:none}
  .wins .di svg{width:14px;height:14px}
  .win-chart{margin-top:10px;background:#fff;border:1px solid var(--line-2);border-radius:14px;padding:15px 18px 14px;flex:1 1 auto;display:flex;flex-direction:column;min-height:138px}
  .wc-head{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:13px;flex:none}
  .wc-title{font-size:13px;font-weight:700;color:var(--ink)}
  .wc-sub{font-size:11px;font-weight:600;color:var(--green)}
  .wc-bars{display:flex;align-items:flex-end;gap:9px;flex:1 1 auto;min-height:74px}
  .wcb{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;gap:7px;height:100%}
  .wcb i{display:block;width:100%;max-width:26px;border-radius:6px 6px 4px 4px;background:linear-gradient(180deg,#34C77B,#8BE3B4)}
  .wcb span{font-size:10px;color:var(--gray-2);font-weight:600}

  /* ---------- modules ---------- */
  .mod-grid{display:flex;flex-wrap:wrap;justify-content:center;gap:20px;margin-top:54px}
  .mod-grid .mod{flex:0 1 calc((100% - 40px) / 3)}
  .steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:54px}
  .mod{background:var(--card);border:1px solid var(--line-2);border-radius:var(--r-lg);padding:26px 24px 24px;
    box-shadow:var(--shadow-sm);transition:transform .25s ease, box-shadow .3s ease;overflow:hidden}
  .mod:hover{transform:translateY(-5px);box-shadow:var(--shadow-md)}
  .mod-tag{display:inline-block;font-size:10.5px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;
    padding:4px 9px;border-radius:6px;margin-bottom:14px}
  .tag-life{background:var(--green-soft);color:var(--green)}
  .tag-karir{background:var(--blue-soft);color:var(--blue)}
  .tag-finance{background:var(--orange-soft);color:#C9710F}
  .tag-bisnis{background:#E6F4F1;color:#0F766E}
  .tag-core{background:var(--violet-soft);color:var(--violet)}
  .mod h3{font-size:19px;font-weight:700;letter-spacing:-.01em}
  .mod p{margin-top:9px;font-size:14.5px;line-height:1.56;color:var(--gray)}
  .mod-viz{margin-top:18px;height:112px;border:1px solid var(--line-2);border-radius:var(--r-sm);background:#FCFCFD;
    display:flex;align-items:center;justify-content:center;padding:12px;overflow:hidden}

  /* mini visuals */
  .mini-pomo{width:78px;height:78px;transform:rotate(-90deg)}
  .mini-pomo .tr{fill:none;stroke:#E9EAEE;stroke-width:5}
  .mini-pomo .fl{fill:none;stroke:var(--green);stroke-width:5;stroke-linecap:round;stroke-dasharray:163;stroke-dashoffset:48}
  .pomo-wrap{position:relative;display:flex;align-items:center;justify-content:center}
  .pomo-wrap .t{position:absolute;font-size:13px;font-weight:800;letter-spacing:-.02em}
  .dots{display:flex;gap:5px;flex-wrap:wrap;width:100%;justify-content:center}
  .dots i{width:13px;height:13px;border-radius:4px;background:#EDEEF1;display:block}
  .moodrow{display:flex;gap:6px}
  .moodweek{display:flex;gap:9px;align-items:center;justify-content:center;flex-wrap:wrap}
  .moodweek .mf{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center}
  .moodweek .mf svg{width:19px;height:19px}
  .moodweek .mf.good{background:var(--green-soft)} .moodweek .mf.good svg{color:var(--green)}
  .moodweek .mf.ok{background:var(--violet-soft)} .moodweek .mf.ok svg{color:var(--violet)}
  .moodweek .mf.low{background:var(--blue-soft)} .moodweek .mf.low svg{color:var(--blue)}
  .moodrow i{width:15px;height:15px;border-radius:5px;display:block}
  .bars{display:flex;align-items:flex-end;gap:7px;height:64px}
  .bars i{width:13px;border-radius:4px 4px 0 0;display:block}
  .pipe{width:100%;display:flex;flex-direction:column;gap:6px}
  .pipe-row{display:flex;align-items:center;gap:9px;font-size:10px;color:var(--gray);font-weight:600}
  .pipe-row .nm{width:52px;flex:none}
  .pipe-row .tk{flex:1;height:6px;border-radius:99px;background:#EDEEF1;overflow:hidden}
  .pipe-row .tk i{display:block;height:100%;border-radius:99px}
  .pipe-row .ct{width:16px;flex:none;text-align:right;color:var(--ink);font-weight:800;font-size:10px}
  .fin{display:flex;gap:9px;width:100%}
  .fin .c{flex:1;border-radius:10px;padding:9px 10px}
  .fin .c .l{font-size:9.5px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;opacity:.8}
  .fin .c .v{font-size:14px;font-weight:800;margin-top:2px}
  .savings{width:100%;display:flex;flex-direction:column;gap:7px}
  .savings .sv-top{display:flex;justify-content:space-between;align-items:baseline;font-size:11.5px;font-weight:600;color:var(--gray);margin-bottom:4px;line-height:1.05}
  .savings .sv-top b{color:var(--ink);font-weight:800;font-size:12px}
  .savings .sv-bar{height:6px;border-radius:99px;background:#EDEEF1;overflow:hidden}
  .savings .sv-bar i{display:block;height:100%;border-radius:99px}
  .tasklist{width:100%;display:flex;flex-direction:column;gap:7px}
  .tasklist .ti{display:flex;align-items:center;gap:9px;font-size:12px;color:var(--ink-2);font-weight:500}
  .tasklist .cb{width:16px;height:16px;border-radius:5px;border:1.6px solid #D6D7DC;flex:none}
  .tasklist .ti.done .cb{background:var(--green);border-color:var(--green);display:flex;align-items:center;justify-content:center}
  .tasklist .ti.done .cb svg{width:11px;height:11px;color:#fff}
  .tasklist .ti.done span{color:var(--gray-2);text-decoration:line-through}
  .appchips{display:flex;flex-wrap:wrap;gap:7px;justify-content:center;align-content:center}
  .appchips .ac{font-size:11px;font-weight:700;padding:6px 11px;border-radius:99px;white-space:nowrap}
  .appchips .ac.sent{background:#EFF0F2;color:#6A6A72}
  .appchips .ac.review{background:var(--orange-soft);color:#C9710F}
  .appchips .ac.intv{background:var(--blue-soft);color:var(--blue)}
  .appchips .ac.offer{background:var(--green-soft);color:var(--green)}
  .tpl{width:100%;display:flex;flex-direction:column;gap:10px}
  .tpr{display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:600;color:var(--ink-2)}
  .tpr .tn{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .tpr .tt{font-size:9.5px;font-weight:700;padding:3px 8px;border-radius:99px;flex:none}
  .tpr .tt.b{background:var(--blue-soft);color:var(--blue)}
  .tpr .tt.v{background:var(--violet-soft);color:var(--violet)}
  .tpr .tt.n{background:#EFF0F2;color:#6A6A72}

  /* ---------- modular toggle section ---------- */
  .modular{background:var(--ink);color:#fff}
  .modular .wrap{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.05fr);gap:56px;align-items:center}
  .modular .eyebrow{color:#9F8CF7}
  .modular .title{color:#fff}
  .modular .title .serif{color:#B7A8FA}
  .modular .sub{color:#A9AAB4}
  .toggle-panel{background:#17181E;border:1px solid #26272F;border-radius:var(--r-xl);padding:14px;box-shadow:var(--shadow-xl)}
  .tpanel-head{display:flex;align-items:center;gap:12px;padding:12px 14px 16px}
  .tpanel-head .ic{width:40px;height:40px;border-radius:11px;background:#21222A;display:flex;align-items:center;justify-content:center}
  .tpanel-head .ic svg{width:20px;height:20px;color:#B7B8C0}
  .tpanel-head b{font-size:16px;font-weight:700}
  .tpanel-head span{display:block;font-size:12px;color:#80828C;font-weight:500;margin-top:1px}
  .trow{display:flex;align-items:center;gap:14px;padding:13px 14px;border-radius:13px;transition:background .2s}
  .trow:hover{background:#1D1E25}
  .trow .meta b{font-size:14.5px;font-weight:600}
  .trow .meta span{display:block;font-size:12px;color:#80828C;margin-top:1px}
  .sw{margin-left:auto;width:42px;height:24px;border-radius:99px;background:#33343D;flex:none;position:relative;transition:background .25s;cursor:pointer}
  .sw::after{content:"";position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:left .25s}
  .sw.on{background:#fff} .sw.on::after{left:21px;background:var(--ink)}

  /* ---------- stats band ---------- */
  .stats .head .eyebrow{color:var(--green)}
  .heatwrap{margin-top:50px;background:var(--card);border:1px solid var(--line-2);border-radius:var(--r-xl);box-shadow:var(--shadow-md);padding:30px 30px 34px}
  .heat-title{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
  .heat-title b{font-size:17px;font-weight:700}
  .legend{display:flex;gap:14px;font-size:12px;color:var(--gray);font-weight:600;align-items:center}
  .legend i{display:inline-block;width:12px;height:12px;border-radius:4px;margin-right:5px;vertical-align:-1px}
  .heatrow{margin-top:22px}
  .heatrow .rl{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;letter-spacing:.1em;color:var(--gray);text-transform:uppercase;margin-bottom:9px}
  .heatrow .rl svg{width:15px;height:15px}
  .cells{display:grid;grid-template-columns:repeat(30,1fr);gap:5px}
  .cells i{aspect-ratio:1/1;border-radius:5px;background:#EFF0F2;display:block}
  .heat-foot{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:30px}
  .hf{border-radius:var(--r-md);padding:18px;text-align:center}
  .hf.g{background:var(--green-soft)} .hf.b{background:var(--blue-soft)} .hf.v{background:var(--violet-soft)}
  .hf .v{font-size:28px;font-weight:800;letter-spacing:-.03em}
  .hf.g .v{color:var(--green)} .hf.b .v{color:var(--blue)} .hf.v .v{color:var(--violet)}
  .hf .l{font-size:12.5px;color:var(--gray);font-weight:600;margin-top:3px}

  /* ---------- testimonials ---------- */
  .tg{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:54px}
  .tc{background:var(--card);border:1px solid var(--line-2);border-radius:var(--r-lg);padding:28px 26px;box-shadow:var(--shadow-sm)}
  .stars{display:flex;gap:3px;color:var(--yellow);margin-bottom:15px}
  .stars svg{width:16px;height:16px}
  .tc blockquote{font-size:15.5px;line-height:1.6;color:var(--ink-2);font-weight:500}
  .tp{display:flex;align-items:center;gap:12px;margin-top:22px}
  .tp .tav{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;
    color:#fff;font-weight:700;font-size:14px;letter-spacing:.02em;flex:none}
  .tav1{background:linear-gradient(135deg,#7C5CF0,#A38EF3)}
  .tav2{background:linear-gradient(135deg,#16A34A,#41C97B)}
  .tav3{background:linear-gradient(135deg,#3B6FF6,#6E9BFA)}
  .tp b{display:block;font-size:14.5px;font-weight:700}
  .tp span{font-size:13px;color:var(--gray-2)}

  /* ---------- pricing ---------- */
  .pricing{background:linear-gradient(180deg,#FAFAFB,#fff);border-top:1px solid var(--line)}
  .price-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;max-width:860px;margin:54px auto 0}
  .price{background:var(--card);border:1px solid var(--line);border-radius:var(--r-xl);padding:34px 32px;box-shadow:var(--shadow-sm);position:relative;display:flex;flex-direction:column}
  .price.feat{border-color:var(--ink);box-shadow:var(--shadow-xl)}
  .price-badge{position:absolute;top:-13px;right:28px;background:var(--ink);color:#fff;font-size:11px;font-weight:700;
    letter-spacing:.08em;text-transform:uppercase;padding:6px 14px;border-radius:var(--pill)}
  .price h3{font-size:18px;font-weight:700}
  .price .amt{margin-top:14px;display:flex;align-items:baseline;gap:6px;flex-wrap:nowrap}
  .price .amt .n{font-size:42px;font-weight:800;letter-spacing:-.03em;white-space:nowrap}
  .price .amt .per{font-size:14px;color:var(--gray);font-weight:500;white-space:nowrap}
  .price .desc{margin-top:8px;font-size:14px;color:var(--gray);line-height:1.5}
  .price ul{list-style:none;margin:24px 0 28px;display:flex;flex-direction:column;gap:13px}
  .price li{display:flex;gap:11px;font-size:14.5px;color:var(--ink-2);line-height:1.45}
  .price li svg{width:18px;height:18px;flex:none;color:var(--green);margin-top:1px}
  .price .btn{width:100%;margin-top:auto}
  .price-note{text-align:center;font-size:13.5px;color:var(--gray-2);margin-top:26px}
  .dur-wrap{text-align:center}
  .dur-seg{display:inline-flex;flex-wrap:wrap;justify-content:center;gap:4px;background:#fff;border:1px solid var(--line);
    border-radius:var(--pill);padding:5px;margin:46px auto 0;box-shadow:var(--shadow-sm)}
  .dur-seg button{border:none;background:none;cursor:pointer;font-family:inherit;font-weight:600;font-size:14px;
    color:var(--gray);padding:12px 22px;border-radius:var(--pill);transition:color .2s, background .2s;white-space:nowrap}
  .dur-seg button:hover{color:var(--ink)}
  .dur-seg button.active{background:var(--ink);color:#fff}
  #planPrice,#planPer,#planTitle,#planDesc{transition:opacity .18s ease}

  /* ---------- referral band ---------- */
  .ref{padding:0 0 var(--gap,0)}
  .ref-box{background:linear-gradient(110deg,#F5871F,#F26419);border-radius:var(--r-xl);padding:42px 44px;color:#fff;
    display:flex;align-items:center;justify-content:space-between;gap:28px;flex-wrap:wrap;position:relative;overflow:hidden}
  .ref-box::before{content:"";position:absolute;right:-60px;top:-60px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.12)}
  .ref-box::after{content:"";position:absolute;right:60px;bottom:-90px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.08)}
  .ref-box > *{position:relative}
  .ref-box .eyebrow{color:rgba(255,255,255,.85)}
  .ref-box h3{font-size:clamp(24px,3vw,34px);font-weight:800;letter-spacing:-.02em;margin-top:10px;max-width:560px}
  .ref-box p{margin-top:10px;font-size:15.5px;color:rgba(255,255,255,.9);max-width:520px}
  .ref-box .btn-light{background:#fff;color:#D9640F}
  .ref-box .btn-light:hover{transform:translateY(-2px)}

  /* ---------- faq ---------- */
  .faq{background:linear-gradient(180deg,#fff,#FAFAFB);border-top:1px solid var(--line)}
  .faq-list{max-width:760px;margin:50px auto 0}
  details{border-bottom:1px solid var(--line);padding:4px 0}
  summary{list-style:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:20px;
    padding:22px 4px;font-size:17.5px;font-weight:600;letter-spacing:-.01em}
  summary::-webkit-details-marker{display:none}
  .chev{width:22px;height:22px;flex:none;color:var(--gray-2);transition:transform .25s ease}
  details[open] .chev{transform:rotate(45deg);color:var(--violet)}
  details p{padding:0 4px 24px;font-size:15.5px;line-height:1.62;color:var(--gray);max-width:660px}

  /* ---------- final cta ---------- */
  .cta{padding:0 0 110px}
  .cta-box{background:var(--ink);border-radius:34px;padding:78px 56px;text-align:center;position:relative;overflow:hidden}
  .cta-box::before{content:"";position:absolute;inset:0;background:radial-gradient(620px 320px at 50% -28%, rgba(124,92,240,.42), rgba(124,92,240,0) 70%)}
  .cta-box > *{position:relative}
  .cta-box h2{font-size:clamp(30px,4.2vw,48px);line-height:1.06;letter-spacing:-.03em;font-weight:800;color:#fff;max-width:660px;margin-inline:auto}
  .cta-box h2 .serif{color:#B7A8FA}
  .cta-box p{margin-top:18px;font-size:18px;color:#AEAFB8;max-width:500px;margin-inline:auto}
  .cta-box .btn-light{background:#fff;color:var(--ink);margin-top:34px}
  .cta-box .btn-light:hover{transform:translateY(-2px);box-shadow:0 18px 36px -16px rgba(0,0,0,.5)}
  .cta-note{margin-top:16px;font-size:13.5px;color:#7C7D86}

  /* ---------- footer ---------- */
  footer{border-top:1px solid var(--line);padding:46px 0 54px}
  .foot{display:flex;align-items:flex-start;justify-content:space-between;gap:30px;flex-wrap:wrap}
  .foot-brand{max-width:280px}
  .foot-brand p{margin-top:12px;font-size:13.5px;color:var(--gray);line-height:1.55}
  .foot-cols{display:flex;gap:56px;flex-wrap:wrap}
  .foot-col h5{font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--gray-2);margin-bottom:14px}
  .foot-col a{display:block;font-size:14px;color:var(--gray);margin-bottom:10px}
  .foot-col a:hover{color:var(--ink)}
  .foot-copy{margin-top:38px;padding-top:24px;border-top:1px solid var(--line);font-size:13px;color:var(--gray-2)}

  /* ---------- motion ---------- */
  .rise{opacity:0;transform:translateY(18px);animation:rise .8s cubic-bezier(.2,.7,.2,1) both}
  .d1{animation-delay:.05s}.d2{animation-delay:.13s}.d3{animation-delay:.21s}.d4{animation-delay:.29s}.d5{animation-delay:.37s}
  @keyframes rise{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
  [data-reveal]{opacity:0;transform:translateY(22px);transition:opacity .55s ease, transform .6s cubic-bezier(.2,.7,.2,1)}
  [data-reveal].in{opacity:1;transform:none}
  .score-card{animation:popIn .7s cubic-bezier(.2,.8,.2,1) .15s both}
  .rings-card{animation:popIn .7s cubic-bezier(.2,.8,.2,1) .32s both}
  .streak-card{animation:popIn .65s cubic-bezier(.2,.8,.2,1) .5s both, floatY 7s ease-in-out 1.6s infinite}
  .insight-card{animation:popIn .7s cubic-bezier(.2,.8,.2,1) .62s both, floatY 8s ease-in-out 2s infinite}
  @keyframes popIn{from{opacity:0;transform:translateY(22px) scale(.96)}to{opacity:1;transform:none}}
  @keyframes floatY{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}

  @media (prefers-reduced-motion:reduce){
    *{animation:none!important}
    html{scroll-behavior:auto}
    .rise,.score-card,.rings-card,.streak-card,.insight-card{opacity:1!important;transform:none!important}
    [data-reveal]{opacity:1!important;transform:none!important;filter:none!important;transition:none!important}
    .in .ring .fill{transition:none!important}
    .cluster-inner{transition:none!important}
  }

  /* ---------- responsive ---------- */
  @media(max-width:1080px){
    .hero-grid,.sol-grid,.modular .wrap{grid-template-columns:1fr;gap:56px}
    .sol-points{grid-template-columns:1fr;gap:18px;margin:28px 0 34px}
    .cluster{max-width:560px;margin:0 auto}
    .tg{grid-template-columns:1fr 1fr}
    .mod-grid .mod{flex-basis:calc((100% - 20px) / 2)}
    .pain-grid{grid-template-columns:1fr 1fr}
  }
  @media(max-width:760px){
    .wrap{padding:0 20px}
    .nav-links,.masuk{display:none}
    .menu-toggle{display:block}
    .section{padding:74px 0}.section-sm{padding:56px 0}
    .hero{padding:44px 0 60px}
    .steps-grid,.tg,.pain-grid,.price-grid,.heat-foot{grid-template-columns:1fr}
    .mod-grid .mod{flex-basis:100%}
    .hero-cta{flex-direction:column;align-items:stretch}
    .hero-cta .btn{width:100%}
    .cluster{min-height:0}
    .score-card,.rings-card,.streak-card,.insight-card{position:relative;inset:auto;left:auto;right:auto;top:auto;bottom:auto;width:auto;margin-bottom:14px}
    .hero-app{width:auto;max-width:380px;margin:0 auto}
    .insight-card{max-width:380px;margin:16px auto 0;box-shadow:var(--shadow-md)}
    .win-body{grid-template-columns:1fr}
    .win-side{display:none}
    .win-stats{grid-template-columns:1fr 1fr}
    .cells{grid-template-columns:repeat(15,1fr)}
    .ref-box,.cta-box{padding:40px 24px}
    .foot{flex-direction:column}
  }
  @media(max-width:440px){ .win-stats{grid-template-columns:1fr} }
  @media(max-width:560px){
    .dur-seg{display:grid;grid-template-columns:1fr 1fr;gap:6px;width:100%;max-width:300px;border-radius:20px}
    .dur-seg button{width:100%;text-align:center;padding:12px 10px}
    .price .amt .n{font-size:34px}
  }
  /* ---------- hero (bold typographic style) ---------- */
  .hero-new{position:relative;z-index:1;padding-top:6px}
  .hn-top{display:flex;align-items:flex-start;justify-content:flex-end;gap:24px}
  .hn-stats{display:flex;gap:clamp(28px,4vw,56px)}
  .hn-num{font-size:clamp(30px,3.6vw,50px);font-weight:800;letter-spacing:-.03em;line-height:1;color:var(--ink)}
  .hn-lab{font-size:14px;color:var(--gray);margin-top:7px;font-weight:500}

  .play-cta{display:inline-flex;align-items:center;gap:15px;text-decoration:none}
  .play-label{font-weight:800;font-size:18px;color:var(--ink);letter-spacing:-.01em;line-height:1.15;text-align:right}
  .play-orb{position:relative;width:64px;height:64px;border-radius:50%;flex:none;display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,#22C56F,#15A04A);box-shadow:0 14px 30px rgba(22,163,74,.34);transition:transform .2s ease}
  .play-orb svg{width:22px;height:22px;color:#fff;margin-left:3px;position:relative;z-index:1}
  .play-cta:hover .play-orb{transform:scale(1.06)}
  .play-orb::before,.play-orb::after{content:"";position:absolute;inset:0;border-radius:50%;border:2px solid rgba(22,163,74,.5);animation:playPulse 2.8s cubic-bezier(.2,.6,.3,1) infinite}
  .play-orb::after{animation-delay:1.4s}
  @keyframes playPulse{0%{transform:scale(1);opacity:.55}80%{opacity:0}100%{transform:scale(2.7);opacity:0}}
  @media(prefers-reduced-motion:reduce){.play-orb::before,.play-orb::after{animation:none;opacity:0}}

  .hn-pill{display:inline-flex;align-items:center;gap:11px;background:#fff;border:1px solid var(--line);
    border-radius:99px;padding:6px 17px 6px 7px;box-shadow:var(--shadow-sm);margin-top:clamp(6px,1.6vw,18px);
    font-size:14px;font-weight:600;color:var(--ink)}
  .hn-cta{display:flex;gap:12px;flex:none}
  .hn-pill .avatars{margin:0}
  .hn-pill-txt b{font-weight:800}

  .hn-title{margin-top:24px;font-weight:800;font-size:clamp(44px,6.8vw,86px);line-height:.96;letter-spacing:-.04em;color:var(--ink)}
  .dom-bubbles{display:inline-flex;vertical-align:middle;margin:0 .16em -.05em 0}
  .dbub{width:.86em;height:.86em;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;
    margin-left:-.24em;box-shadow:0 .12em .26em rgba(0,0,0,.22), inset 0 .04em .08em rgba(255,255,255,.5)}
  .dbub:first-child{margin-left:0}
  .dbub svg{width:.46em;height:.46em;color:#fff}
  .dbub.g{background:radial-gradient(circle at 34% 28%,#5BE38C,#16A34A)}
  .dbub.b{background:radial-gradient(circle at 34% 28%,#6BB1FB,#2563EB)}
  .dbub.v{background:radial-gradient(circle at 34% 28%,#B79CFB,#7C3AED)}
  .dbub.o{background:radial-gradient(circle at 34% 28%,#FFB36B,#EA580C)}

  .hn-subbar{display:flex;align-items:center;justify-content:space-between;gap:36px;margin-top:clamp(32px,5vw,52px);
    border:1px solid var(--line);border-radius:22px;padding:23px 28px;background:rgba(255,255,255,.55)}
  .hn-subbar p{font-size:clamp(15px,1.35vw,18px);color:var(--gray);line-height:1.6;max-width:680px;margin:0}
  .hn-subbar .btn{flex:none}

  @media(max-width:820px){
    .hn-top{flex-direction:column-reverse;align-items:flex-start;gap:26px}
    .play-cta{align-self:flex-start}
    .hn-subbar{flex-direction:column;align-items:flex-start;gap:20px}
    .hn-subbar .btn{width:100%}
    .hn-cta{flex-direction:column;width:100%}
  }
  /* ---------- scroll Life Score motif ---------- */
  .scroll-score{position:fixed;right:22px;bottom:22px;z-index:60;display:flex;align-items:center;gap:11px;
    background:rgba(255,255,255,.95);
    border:1px solid var(--line);border-radius:99px;padding:7px 17px 7px 7px;box-shadow:var(--shadow-md);
    opacity:0;transform:translateY(14px) scale(.95);transition:opacity .5s ease, transform .5s ease;pointer-events:none}
  .scroll-score.show{opacity:1;transform:none}
  .scroll-score svg{width:42px;height:42px;transform:rotate(-90deg);flex:none}
  .ss-track{fill:none;stroke:#E9EAEE;stroke-width:5}
  .ss-fill{fill:none;stroke:var(--green);stroke-width:5;stroke-linecap:round;stroke-dasharray:119.4;stroke-dashoffset:119.4;transition:stroke .4s ease}
  .ss-meta{line-height:1.04}
  .ss-meta b{font-size:18px;font-weight:800;letter-spacing:-.02em;color:var(--ink);font-variant-numeric:tabular-nums}
  .ss-meta span{display:block;font-size:10.5px;color:var(--gray);font-weight:600;letter-spacing:.01em}
  @media(max-width:620px){.scroll-score{display:none}}
  @media(prefers-reduced-motion:reduce){.scroll-score{transition:none}.ss-fill{transition:none}}

  /* ---------- Cara Kerja scrollytelling ---------- */
  .scrolly{display:grid;grid-template-columns:.92fr 1.08fr;gap:64px;align-items:start;margin-top:44px}
  .scrolly-vis{position:sticky;top:16vh;height:68vh;display:flex;align-items:center;justify-content:center}
  .loop{position:relative;width:330px;max-width:80%;aspect-ratio:1;transform:translateZ(0);backface-visibility:hidden}
  .loop-svg{width:100%;height:100%;display:block;overflow:visible}
  .loop-track{fill:none;stroke:#E9EAEE;stroke-width:6}
  .loop-prog{fill:none;stroke:var(--green);stroke-width:6;stroke-linecap:round;stroke-dasharray:741.4;stroke-dashoffset:741.4;transition:stroke-dashoffset .7s cubic-bezier(.4,0,.2,1)}
  .loop[data-active="0"] .loop-prog{stroke-dashoffset:494}
  .loop[data-active="1"] .loop-prog{stroke-dashoffset:247}
  .loop[data-active="2"] .loop-prog{stroke-dashoffset:0}
  .ln-dot{fill:#fff;stroke:#D7D9DF;stroke-width:2.5;transition:fill .4s ease,stroke .4s ease,transform .4s cubic-bezier(.2,.8,.2,1);transform-box:fill-box;transform-origin:center}
  .ln-lab{fill:var(--gray-2);font-size:12px;font-weight:700;transition:fill .4s ease;font-family:'Plus Jakarta Sans',sans-serif}
  .lnode.on .ln-dot{fill:var(--green);stroke:var(--green)}
  .lnode.on .ln-lab{fill:var(--ink)}
  .lnode.cur .ln-dot{transform:scale(1.32)}
  .loop-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;pointer-events:none}
  .lc-num{font-size:62px;line-height:1;color:var(--violet)}
  .lc-score{font-size:12.5px;color:var(--gray);font-weight:600}
  .lc-score b{font-size:20px;color:var(--green);font-weight:800;font-variant-numeric:tabular-nums}
  .scrolly-steps{display:flex;flex-direction:column}
  .sstep{min-height:66vh;display:flex;flex-direction:column;justify-content:center;opacity:.32;transform:translateY(10px);transition:opacity .5s ease,transform .5s ease}
  .sstep.active{opacity:1;transform:none}
  .ss-n{font-size:46px;line-height:1;color:var(--violet);margin-bottom:8px}
  .sstep h3{font-size:27px;margin-bottom:11px;letter-spacing:-.01em}
  .sstep p{font-size:16px;color:var(--gray);max-width:440px;line-height:1.65}
  @media(max-width:860px){
    .scrolly{grid-template-columns:1fr;gap:8px;margin-top:30px}
    .scrolly-vis{position:static;height:auto;margin:0 auto 22px}
    .loop{width:236px}
    .sstep{min-height:auto;opacity:1;transform:none;padding:22px 0;border-top:1px solid var(--line-2)}
    .sstep:first-child{border-top:none}
    .ss-n{font-size:38px}
  }
  @media(prefers-reduced-motion:reduce){.loop-prog,.ln-dot,.sstep{transition:none}.sstep{opacity:1;transform:none}}
</style>
@endverbatim
