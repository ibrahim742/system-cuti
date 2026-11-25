<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(\App\Models\Setting::get('favicon'))
    <link rel="icon" type="image/png" href="{{ Storage::url(\App\Models\Setting::get('favicon')) }}">
    @endif
    <title>{{ \App\Models\Setting::get('app_name') }} — Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

    {{-- Sidebar --}}
    {{-- Defaultnya hidden di HP, muncul saat class 'hidden' dihapus oleh JS --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white flex-col z-50 hidden lg:flex">
        <div class="flex items-center justify-between px-6 h-16 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="font-semibold tracking-tight">{{ \App\Models\Setting::get('app_name', 'Sistem Cuti') }}</span>
            </div>
            {{-- Tombol close sidebar di HP --}}
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-600 text-white font-medium text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Persetujuan Cuti
            </a>
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Dashboard Super Admin
                </a>
                <a href="{{ route('super-admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Kelola Akun
                </a>
            @endif
        </nav>

        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 px-2 py-2">
                <div class="w-9 h-9 bg-slate-700 rounded-full flex items-center justify-center text-sm font-semibold overflow-hidden">
                    @if(auth()->user()->foto_profile)
                        <img src="{{ Storage::url(auth()->user()->foto_profile) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay (Latar gelap saat sidebar dibuka di HP) --}}
    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden"></div>

    {{-- Main Content --}}
    <div class="lg:pl-64 flex-1 flex flex-col min-h-screen">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                {{-- Tombol Hamburger (Hanya di HP) --}}
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 hover:text-slate-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-semibold text-slate-800">Persetujuan Cuti</h1>
                    {{-- Subjudul disembunyikan di HP --}}
                    <p class="text-xs text-slate-500 hidden sm:block">Masuk sebagai {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Ikon Profil --}}
                <a href="{{ route('profile.show') }}" title="Profil Saya"
                   class="w-10 h-10 rounded-full overflow-hidden bg-slate-200 flex items-center justify-center hover:ring-2 hover:ring-blue-400 transition">
                    @if(auth()->user()->foto_profile)
                        <img src="{{ Storage::url(auth()->user()->foto_profile) }}" alt="Profil" class="w-full h-full object-cover">
                    @else
                        <span class="text-slate-600 font-semibold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                </a>
            </div>
        </header>

        <main class="p-4 lg:p-8 space-y-6 flex-1">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- KARTU STATISTIK --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
                {{-- Menunggu Persetujuan --}}
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Menunggu Persetujuan</p>
                            <p class="text-3xl font-bold text-amber-600 mt-2">{{ $pending->count() }}</p>
                        </div>
                        <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Cuti Bulan Ini --}}
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Cuti Disetujui Bulan Ini</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $cutiBulanIni }} <span class="text-sm font-normal text-slate-400">hari</span></p>
                        </div>
                        <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Belum Pernah Cuti --}}
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Karyawan Belum Pernah Cuti</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $belumCuti->count() }} <span class="text-sm font-normal text-slate-400">orang</span></p>
                        </div>
                        <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL PERSETUJUAN --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Pengajuan Menunggu Persetujuan</h2>
                        <p class="text-sm text-slate-500 mt-0.5 hidden sm:block">Tinjau dan proses pengajuan cuti karyawan</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Nama</th>
                                <th class="px-6 py-3 font-semibold hidden sm:table-cell">Tanggal</th>
                                <th class="px-6 py-3 font-semibold">Durasi</th>
                                <th class="px-6 py-3 font-semibold hidden md:table-cell">Alasan</th>
                                <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pending as $c)
                                <tr class="hover:bg-slate-50 transition {{ $c->total_cuti_bulan_ini >= 3 ? 'bg-red-50/50' : '' }}">
                                    <td class="px-6 py-4">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold
            {{ $c->total_hari_bulan_ini > 3 ? 'bg-red-200 text-red-700' : ($c->total_hari_bulan_ini == 3 ? 'bg-amber-200 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
            {{ strtoupper(substr($c->user->name, 0, 1)) }}
        </div>
        <div>
            <span class="font-medium text-slate-800">{{ $c->user->name }}</span>
            
            {{-- BADGE PERINGATAN --}}
            @if($c->total_hari_bulan_ini > 3)
                <div class="text-[10px] font-bold text-red-600 uppercase tracking-wide flex items-center gap-1 mt-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    Melebihi Batas ({!! $c->total_hari_bulan_ini !!} Hari)
                </div>
            @elseif($c->total_hari_bulan_ini == 3)
                <div class="text-[10px] font-bold text-amber-600 uppercase tracking-wide flex items-center gap-1 mt-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    Mencapai Batas (3 Hari)
                </div>
            @endif
        </div>
    </div>
</td>
                                    <td class="px-6 py-4 text-slate-700 whitespace-nowrap hidden sm:table-cell">
                                        {{ $c->tanggal_mulai->format('d M Y') }} — {{ $c->tanggal_selesai->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                            {{ $c->jumlah_hari }} hari
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 max-w-xs truncate hidden md:table-cell" title="{{ $c->alasan ?? 'Tidak ada alasan' }}">
                                        {{ $c->alasan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('cuti.approve', $c) }}" method="POST" class="form-approve">
                                                @csrf
                                                @if($c->total_cuti_bulan_ini >= 3)
                                                    <button type="button" onclick="konfirmasiSetujui(this, '{{ $c->user->name }}')" class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
                                                        Setujui
                                                    </button>
                                                @else
                                                    <button class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
                                                        Setujui
                                                    </button>
                                                @endif
                                            </form>
                                            <form action="{{ route('cuti.reject', $c) }}" method="POST">
                                                @csrf
                                                <button class="inline-flex items-center gap-1.5 bg-slate-400 hover:bg-slate-500 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">Tidak ada pengajuan pending saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TABEL BELUM PERNAH CUTI (COLLAPSIBLE) --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <details class="group">
                    <summary class="px-6 py-5 cursor-pointer flex items-center justify-between hover:bg-slate-50 transition list-none">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Karyawan Belum Pernah Cuti</h2>
                            <p class="text-sm text-slate-500 mt-0.5 hidden sm:block">Klik untuk melihat daftar karyawan yang belum pernah mengambil cuti</p>
                        </div>
                        <div class="flex items-center gap-2 text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg group-open:bg-red-50 group-open:text-red-600 transition">
                            <svg class="w-4 h-4 transition-transform duration-300 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </summary>

                    <div class="border-t border-slate-200">
                        <div class="overflow-x-auto custom-scroll" style="max-height: 300px;">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide sticky top-0 z-10">
                                    <tr>
                                        <th class="px-6 py-3 font-semibold">Nama Karyawan</th>
                                        <th class="px-6 py-3 font-semibold hidden sm:table-cell">Email</th>
                                        <th class="px-6 py-3 font-semibold">Sisa Cuti</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($belumCuti as $u)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 flex items-center gap-3">
                                                <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-xs font-semibold text-slate-600">
                                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                                </div>
                                                <span class="font-medium text-slate-800">{{ $u->name }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 hidden sm:table-cell">{{ $u->email }}</td>
                                            <td class="px-6 py-4 text-slate-600">{{ $u->sisa_cuti_tahunan }} / {{ $u->jatah_cuti_tahunan }} hari</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-slate-400">Semua karyawan sudah pernah mengambil cuti.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle Sidebar HP
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
                overlay.classList.add('hidden');
            }
        }

        function konfirmasiSetujui(btn, nama) {
            Swal.fire({
                title: 'Peringatan Keras!',
                html: `Karyawan <b>${nama}</b> sudah mengambil cuti <b>3 kali atau lebih</b> di bulan ini.<br><br>Apakah Anda yakin ingin menyetujui?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tetap Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        }
    </script>

</body>
</html>
