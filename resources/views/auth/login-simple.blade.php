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
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl overflow-hidden grid md:grid-cols-2">

        {{-- Panel Kiri: Branding --}}
        <div class="hidden md:flex flex-col justify-between bg-gradient-to-br from-blue-600 to-indigo-700 p-10 text-white">
            <div>
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold tracking-tight">Sistem Cuti</span>
                </div>
                {{-- Branding kiri --}}
                <h1 class="text-3xl font-bold leading-tight mb-4">{{ \App\Models\Setting::get('login_branding_title') }}</h1>
                <p class="text-blue-100 text-sm leading-relaxed">{{ \App\Models\Setting::get('login_branding_desc') }}</p>
            </div>
            <div class="flex items-center gap-3 text-sm text-blue-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Aman & terintegrasi</span>
            </div>
        </div>

        {{-- Panel Kanan: Form Login --}}
        <div class="p-8 md:p-12 flex flex-col justify-center">
            <div class="md:hidden flex items-center gap-3 mb-8">
                <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-lg font-semibold text-slate-800">Sistem Cuti</span>
            </div>

            <h2 class="text-2xl font-bold text-slate-800 mb-1">{{ \App\Models\Setting::get('login_title') }}</h2>
            <p class="text-slate-500 text-sm mb-8">{{ \App\Models\Setting::get('login_subtitle') }}</p>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="nama@perusahaan.com"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition shadow-sm hover:shadow">
                    Masuk
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-200">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-3">Akun uji coba</p>
                <div class="space-y-1.5 text-xs text-slate-500">
                    <p><span class="font-semibold text-slate-700">Super Admin:</span> superadmin@cuti.test / password</p>
                    <p><span class="font-semibold text-slate-700">Admin:</span> admin@cuti.test / password</p>
                    <p><span class="font-semibold text-slate-700">Karyawan:</span> karyawan1@cuti.test / password</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
