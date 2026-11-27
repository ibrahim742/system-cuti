<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(\App\Models\Setting::get('favicon'))
    <link rel="icon" type="image/png" href="{{ Storage::url(\App\Models\Setting::get('favicon')) }}">
    @endif
    <title>{{ \App\Models\Setting::get('app_name') }} — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
                    colors: {
                        navy: { 900: '#0A1628', 800: '#0F2038' },
                        brand: { 400: '#22C3C9', 500: '#0E8A8F', 600: '#0B7378', 700: '#095C60' }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* cahaya lembut di belakang card supaya jaringan tidak terlalu ramai di tengah */
        .glow::before {
            content: "";
            position: fixed; inset: 0; z-index: 1; pointer-events: none;
            background: radial-gradient(ellipse 45% 55% at 50% 50%, rgba(10,22,40,.85), rgba(10,22,40,0) 100%);
        }
    </style>
</head>
<body class="glow bg-navy-900 font-sans antialiased min-h-screen flex items-center justify-center p-4 text-slate-800">

    @php
        $logo = \App\Models\Setting::get('logo');
        $appName = \App\Models\Setting::get('app_name') ?: 'Sistem Cuti';
    @endphp

    {{-- Latar: jaringan saraf --}}
    <canvas id="network" class="fixed inset-0 w-full h-full z-0" aria-hidden="true"></canvas>

    {{-- Card portrait --}}
    <main class="relative z-10 w-full max-w-sm bg-white rounded-2xl shadow-2xl shadow-black/40 px-8 py-10 sm:px-10">

        <div class="flex flex-col items-center text-center mb-8">
            @if($logo)
                <img src="{{ Storage::url($logo) }}" alt="{{ $appName }}" class="h-14 w-auto mb-4">
            @else
                <div class="w-14 h-14 rounded-xl bg-navy-900 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14l2 2 4-4"/>
                    </svg>
                </div>
            @endif
            <h1 class="text-xl font-bold text-slate-900">{{ \App\Models\Setting::get('login_title') ?: $appName }}</h1>
            <p class="text-slate-500 text-sm mt-1">{{ \App\Models\Setting::get('login_subtitle') }}</p>
        </div>

        @if($errors->any())
            <div role="alert" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm flex items-start gap-2">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="/login" class="space-y-5">
            @csrf

            {{-- Input Email (Gaya Google Floating Label + Hover Tipis) --}}
            <div class="relative">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder=" "
                       class="peer w-full px-3.5 py-3 text-sm text-slate-800 bg-transparent border border-slate-300 rounded-lg hover:border-slate-400 focus:outline-none focus:border-brand-500 transition-colors">
                <label for="email"
                       class="absolute left-3 top-0 -translate-y-1/2 bg-white px-1 text-xs font-medium text-slate-500 pointer-events-none transition-all duration-150
                              peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-placeholder-shown:font-normal peer-placeholder-shown:text-slate-400
                              peer-focus:top-0 peer-focus:text-xs peer-focus:font-medium peer-focus:text-brand-600">
                    Email
                </label>
            </div>

            {{-- Input Password (Gaya Google Floating Label + Hover Tipis) --}}
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       placeholder=" "
                       class="peer w-full pl-3.5 pr-11 py-3 text-sm text-slate-800 bg-transparent border border-slate-300 rounded-lg hover:border-slate-400 focus:outline-none focus:border-brand-500 transition-colors">
                <label for="password"
                       class="absolute left-3 top-0 -translate-y-1/2 bg-white px-1 text-xs font-medium text-slate-500 pointer-events-none transition-all duration-150
                              peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-placeholder-shown:font-normal peer-placeholder-shown:text-slate-400
                              peer-focus:top-0 peer-focus:text-xs peer-focus:font-medium peer-focus:text-brand-600">
                    Password
                </label>
                <button type="button" id="togglePassword" aria-label="Tampilkan password"
                        class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600 focus:outline-none focus-visible:text-brand-600">
                    <svg id="eyeOn" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/>
                        <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                    </svg>
                    <svg id="eyeOff" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.6 6a9.6 9.6 0 011.4-.1c6 0 9.5 6.1 9.5 6.1a16 16 0 01-3.2 3.9M6.6 7.6A15.7 15.7 0 002.5 12S6 18.5 12 18.5c1.4 0 2.7-.3 3.8-.8M9.9 9.9a3 3 0 004.2 4.2"/>
                    </svg>
                </button>
            </div>

            <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-500">
                Masuk
            </button>
        </form>
    </main>

    <script>
        // Tampilkan / sembunyikan password
        (function () {
            var btn = document.getElementById('togglePassword');
            var input = document.getElementById('password');
            var on = document.getElementById('eyeOn');
            var off = document.getElementById('eyeOff');
            btn.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                on.classList.toggle('hidden', show);
                off.classList.toggle('hidden', !show);
                btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            });
        })();

        // Latar jaringan saraf (node + garis penghubung)
        (function () {
            var canvas = document.getElementById('network');
            var ctx = canvas.getContext('2d');
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var nodes = [], w = 0, h = 0, dpr = Math.min(window.devicePixelRatio || 1, 2);
            var mouse = { x: -9999, y: -9999 };
            var LINK = 150;

            function resize() {
                w = window.innerWidth; h = window.innerHeight;
                canvas.width = w * dpr; canvas.height = h * dpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                var count = Math.max(30, Math.min(90, Math.round(w * h / 18000)));
                nodes = [];
                for (var i = 0; i < count; i++) {
                    nodes.push({
                        x: Math.random() * w, y: Math.random() * h,
                        vx: (Math.random() - .5) * .35, vy: (Math.random() - .5) * .35,
                        r: Math.random() * 1.6 + 1
                    });
                }
            }

            function draw() {
                ctx.clearRect(0, 0, w, h);
                var i, j, a, b, dx, dy, d;
                for (i = 0; i < nodes.length; i++) {
                    a = nodes[i];
                    if (!reduce) {
                        a.x += a.vx; a.y += a.vy;
                        if (a.x < 0 || a.x > w) a.vx *= -1;
                        if (a.y < 0 || a.y > h) a.vy *= -1;
                    }
                    for (j = i + 1; j < nodes.length; j++) {
                        b = nodes[j];
                        dx = a.x - b.x; dy = a.y - b.y; d = Math.sqrt(dx * dx + dy * dy);
                        if (d < LINK) {
                            ctx.strokeStyle = 'rgba(34,195,201,' + (0.35 * (1 - d / LINK)) + ')';
                            ctx.lineWidth = 1;
                            ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
                        }
                    }
                    // garis ke kursor
                    dx = a.x - mouse.x; dy = a.y - mouse.y; d = Math.sqrt(dx * dx + dy * dy);
                    if (d < 180) {
                        ctx.strokeStyle = 'rgba(120,220,225,' + (0.5 * (1 - d / 180)) + ')';
                        ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(mouse.x, mouse.y); ctx.stroke();
                    }
                    ctx.fillStyle = 'rgba(140,230,235,.85)';
                    ctx.beginPath(); ctx.arc(a.x, a.y, a.r, 0, Math.PI * 2); ctx.fill();
                }
                if (!reduce) requestAnimationFrame(draw);
            }

            window.addEventListener('resize', function () { resize(); if (reduce) draw(); });
            window.addEventListener('mousemove', function (e) { mouse.x = e.clientX; mouse.y = e.clientY; });
            window.addEventListener('mouseleave', function () { mouse.x = mouse.y = -9999; });
            resize(); draw();
        })();
    </script>
</body>
</html>