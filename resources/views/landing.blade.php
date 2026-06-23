<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>molife · Satu aplikasi. Seluruh kehidupanmu.</title>
<meta name="description" content="Sholat, olahraga, mood, tugas, karier, dan keuangan dalam satu dasbor yang tenang. molife merangkum seluruh hidupmu jadi satu Life Score yang jujur." />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">
@verbatim
<style>
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
</head>
<body>

<!-- ============ HEADER ============ -->
<header id="hdr">
  <div class="wrap nav">
    <a class="logo" href="{{ route('landing') }}"><img src="{{ asset('images/logo.png') }}" alt="molife" style="height:30px;width:auto;display:block"></a>
    <nav class="nav-links">
      <a href="#masalah">Masalah</a>
      <a href="#fitur">Fitur</a>
      <a href="#cara">Cara Kerja</a>
      <a href="#harga">Harga</a>
      <a href="#faq">FAQ</a>
    </nav>
    <div class="nav-right">
      <a class="masuk" href="{{ route('login') }}">Masuk</a>
      <a class="btn btn-dark btn-sm" href="{{ route('register') }}">Mulai Sekarang</a>
      <button class="menu-toggle" aria-label="Menu"><svg width="24" height="24" fill="none" stroke="#101116" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
    </div>
  </div>
</header>

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
          <span class="mod-tag tag-bisnis">Bisnis</span>
          <h3>Proposal &amp; Klien</h3>
          <p>Catat tiap proposal yang dikirim, data klien, produk yang ditawarkan, nilai, dan respon mereka dalam satu pipeline.</p>
          <div class="mod-viz"><div class="pipe"><div class="pipe-row"><span class="nm">Prospek</span><span class="tk"><i style="width:100%;background:#7C5CF0"></i></span><span class="ct">9</span></div><div class="pipe-row"><span class="nm">Terkirim</span><span class="tk"><i style="width:67%;background:#6B7280"></i></span><span class="ct">6</span></div><div class="pipe-row"><span class="nm">Negosiasi</span><span class="tk"><i style="width:44%;background:#F5871F"></i></span><span class="ct">4</span></div><div class="pipe-row"><span class="nm">Deal</span><span class="tk"><i style="width:22%;background:#16A34A"></i></span><span class="ct">2</span></div></div></div>
        </div>

        <div class="mod" data-reveal>
          <span class="mod-tag tag-bisnis">Bisnis</span>
          <h3>Analitik &amp; Dokumen Bisnis</h3>
          <p>Pantau win rate, nilai pipeline, dan bidang klien teratas. Simpan link, file, dan template penawaran di satu tempat.</p>
          <div class="mod-viz"><div class="fin"><div class="c" style="background:#E6F4F1"><div class="l" style="color:#0F766E">Win Rate</div><div class="v" style="color:#0F766E">38%</div></div><div class="c" style="background:var(--blue-soft)"><div class="l" style="color:var(--blue)">Pipeline</div><div class="v" style="color:var(--blue)">24jt</div></div><div class="c" style="background:var(--ink);color:#fff"><div class="l">Deal</div><div class="v">9jt</div></div></div></div>
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
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Semua tracker: sholat, olahraga, pomodoro, mood, tugas</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Career Hub &amp; pelacak lamaran lengkap</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Bisnis: proposal, klien, dokumen &amp; analitik</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Keuangan: pemasukan, pengeluaran, anggaran, tabungan</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Statistik 30 hari, insight, &amp; Life Score harian</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Export data ke CSV</li>
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
<footer>
  <div class="wrap">
    <div class="foot">
      <div class="foot-brand">
        <a class="logo" href="{{ route('landing') }}"><img src="{{ asset('images/logo.png') }}" alt="molife" style="height:30px;width:auto;display:block"></a>
        <p>Satu aplikasi untuk seluruh kehidupanmu: ibadah, kesehatan, mental, karier, dan keuangan dalam satu dasbor yang tenang.</p>
      </div>
      <div class="foot-cols">
        <div class="foot-col"><h5>Produk</h5><a href="#fitur">Fitur</a><a href="#harga">Harga</a><a href="#cara">Cara Kerja</a><a href="#">Referral</a></div>
        <div class="foot-col"><h5>Perusahaan</h5><a href="#">Tentang</a><a href="#">Blog</a><a href="#">Kontak</a><a href="#">Karier</a></div>
        <div class="foot-col"><h5>Legal</h5><a href="#">Privasi</a><a href="#">Ketentuan</a><a href="#">Keamanan</a></div>
      </div>
    </div>
    <div class="foot-copy">© 2026 molife. Dibuat untuk hidup yang lebih teratur.</div>
  </div>
</footer>

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
    '6':  {label:'6 Bulan', price:49000,  per:'/ 6 bulan',  badge:'Hemat 26%',       desc:'Setara ± Rp 8.200/bulan. Lebih hemat dibanding bayar tiap bulan.'},
    '12': {label:'1 Tahun', price:89000,  per:'/ 1 tahun',  badge:'Hemat 33%',       desc:'Setara ± Rp 7.400/bulan. Harga per bulan paling murah.'}
  };
  const durSeg = document.getElementById('durSeg');
  function setPlan(k){
    const p = PLANS[k]; if(!p) return;
    const set=(id,v)=>{const e=document.getElementById(id); if(e) e.textContent=v;};
    set('planTitle','molife · '+p.label);
    set('planPrice','Rp '+p.price.toLocaleString('id-ID'));
    set('planPer',p.per);
    set('planDesc',p.desc);
    set('planBadge',p.badge);
    set('planCta','Ambil Akses '+p.label);
    [...durSeg.children].forEach(b=>b.classList.toggle('active', b.dataset.plan===k));
  }
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
