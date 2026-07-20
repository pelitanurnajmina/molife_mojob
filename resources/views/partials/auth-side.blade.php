{{-- Shared brand panel for auth pages: pastel gradient + animated molife progress-ring logo --}}
<style>
@keyframes mlRingFill { 0% { stroke-dashoffset:540.35; } 55% { stroke-dashoffset:54; } 100% { stroke-dashoffset:540.35; } }
@keyframes mlRingSpin { to { transform: rotate(360deg); } }
@keyframes mlGlow     { 0%,100% { opacity:.45; transform:scale(1); } 50% { opacity:.8; transform:scale(1.08); } }
.auth-panel{ background:linear-gradient(135deg,#f4f1fb 0%, #fbf4f8 40%, #f0f5fc 74%, #f5f4fb 100%); }
.ml-stage{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; z-index:1; }
.ml-glow{ position:absolute; width:clamp(220px,22vw,320px); aspect-ratio:1; border-radius:50%;
    background:radial-gradient(circle, rgba(124,92,240,.22), rgba(124,92,240,0) 70%); filter:blur(10px);
    animation: mlGlow 5s ease-in-out infinite; }
.ml-ring{ position:relative; width:clamp(176px,17vw,232px); aspect-ratio:1;
    filter: drop-shadow(0 20px 40px rgba(124,92,240,.35)); }
.ml-ring .rot{ transform-box: fill-box; transform-origin:center; animation: mlRingSpin 9s linear infinite; }
.ml-ring .prog{ stroke-dasharray:540.35; stroke-dashoffset:540.35;
    animation: mlRingFill 3.8s cubic-bezier(.65,0,.35,1) infinite; }
.ml-cap{ margin-top:26px; font-size:12px; font-weight:600; letter-spacing:.02em; color:rgba(60,40,90,.55); }
@media (prefers-reduced-motion: reduce){ .ml-ring .rot,.ml-ring .prog,.ml-glow{ animation:none !important; } .ml-ring .prog{ stroke-dashoffset:135; } }
</style>

<div class="auth-panel hidden lg:flex w-1/2 relative overflow-hidden p-10 m-3 rounded-3xl">
    {{-- animated progress-ring logo --}}
    <div class="ml-stage">
        <div class="ml-glow"></div>
        <svg class="ml-ring" viewBox="0 0 200 200" fill="none">
            <defs>
                <linearGradient id="mlGrad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%"  stop-color="#10b981"/>
                    <stop offset="38%" stop-color="#3b82f6"/>
                    <stop offset="72%" stop-color="#8b5cf6"/>
                    <stop offset="100%" stop-color="#f59e0b"/>
                </linearGradient>
            </defs>
            <circle cx="100" cy="100" r="86" stroke="rgba(255,255,255,.55)" stroke-width="15"/>
            <g class="rot">
                <circle class="prog" cx="100" cy="100" r="86" stroke="url(#mlGrad)" stroke-width="15"
                    stroke-linecap="round" transform="rotate(-90 100 100)"/>
            </g>
        </svg>
        <p class="ml-cap">{{ __('Hidup terus berprogress.') }}</p>
    </div>

    {{-- content --}}
    <div class="relative z-10 flex flex-col justify-between w-full text-gray-900">
        <a href="{{ route('landing') }}" class="inline-block">
            <img src="{{ asset('images/logo.png') }}" alt="molife" class="h-9 w-auto">
        </a>

        <div>
            <h1 class="text-4xl xl:text-[42px] font-extrabold leading-[1.06] tracking-tight">
                {!! __('Satu aplikasi,<br>seluruh hidupmu.') !!}
            </h1>
            <p class="text-gray-600 text-sm leading-relaxed mt-4 max-w-sm">
                {{ __('Sholat, olahraga, mood, tugas, karier, bisnis, dan keuangan dalam satu Life Score yang jujur, dirangkum tiap hari.') }}
            </p>
        </div>
    </div>
</div>
