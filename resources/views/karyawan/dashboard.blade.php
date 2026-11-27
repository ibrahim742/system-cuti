<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(\App\Models\Setting::get('favicon'))
    <link rel="icon" type="image/png" href="{{ Storage::url(\App\Models\Setting::get('favicon')) }}">
    @endif
    <title>{{ \App\Models\Setting::get('app_name') }} — Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

    {{-- Sidebar (Mobile & Desktop) --}}
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
            {{-- Tombol Close (Hanya di HP) --}}
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-1">
            <a href="{{ route('karyawan.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-600 text-white font-medium text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
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
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
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

    {{-- Overlay Latar Belakang (Hanya untuk HP) --}}
    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden"></div>

    {{-- Main --}}
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
                    <h1 class="text-base sm:text-lg font-semibold text-slate-800">Dashboard Karyawan</h1>
                    <p class="text-xs text-slate-500 hidden sm:block">Selamat datang, {{ auth()->user()->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Tombol Ajukan Cuti --}}
                <button type="button" onclick="openCutiModal()"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium px-3 sm:px-4 py-2 rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="sm:hidden">Ajukan</span>
                    <span class="hidden sm:inline">Ajukan Cuti</span>
                </button>

                {{-- Ikon Profil --}}
                <a href="{{ route('profile.show') }}" title="Profil Saya"
                   class="w-9 h-9 sm:w-10 sm:h-10 rounded-full overflow-hidden bg-slate-200 flex items-center justify-center hover:ring-2 hover:ring-blue-400 transition">
                    @if(auth()->user()->foto_profile)
                        <img src="{{ Storage::url(auth()->user()->foto_profile) }}" alt="Profil" class="w-full h-full object-cover">
                    @else
                        <span class="text-slate-600 font-semibold text-xs sm:text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                </a>
            </div>
        </header>

        <main class="p-4 lg:p-8 space-y-6 flex-1">
            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Sisa Cuti Tahunan</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">
                                {{ auth()->user()->sisa_cuti_tahunan }}
                                <span class="text-base font-medium text-slate-400">hari</span>
                            </p>
                        </div>
                        <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Cuti Terpakai Bulan Ini</p>
                            <p class="text-3xl font-bold text-slate-800 mt-2">
                                {{ $cutiBulanIni }}
                                <span class="text-base font-medium text-slate-400">hari</span>
                            </p>
                        </div>
                        <div class="w-11 h-11 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Riwayat Pengajuan</h2>
                        <p class="text-sm text-slate-500 mt-0.5 hidden sm:block">Berdasarkan filter yang dipilih</p>
                    </div>

                    {{-- FORM FILTER --}}
                    <form method="GET" action="{{ route('karyawan.dashboard') }}" class="flex flex-wrap items-center gap-2 text-sm w-full sm:w-auto">
                        <input type="month" name="bulan_month" value="{{ $tahun }}-{{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}"
                               onchange="document.getElementById('tahunInput').value = this.value.split('-')[0]; document.getElementById('bulanInput').value = this.value.split('-')[1]; this.form.submit();"
                               class="border border-slate-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 text-sm w-full sm:w-auto">

                        <input type="hidden" name="tahun" id="tahunInput" value="{{ $tahun }}">
                        <input type="hidden" name="bulan" id="bulanInput" value="{{ $bulan }}">

                        <span class="text-slate-400 hidden sm:block">atau</span>

                        <input type="date" name="tanggal" value="{{ $tanggal }}"
                               onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 text-sm w-full sm:w-auto">

                        @if(request('tanggal') || request('bulan'))
                            <a href="{{ route('karyawan.dashboard') }}" class="text-red-500 hover:text-red-700 text-xs font-medium ml-2 whitespace-nowrap mt-2 sm:mt-0">Reset</a>
                        @endif
                    </form>
                </div>

                {{-- Area scroll tabel --}}
                <div class="custom-scroll overflow-x-auto" style="max-height: 400px;">
                    <table class="w-full text-sm text-left min-w-[600px]">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide sticky top-0 z-10">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 sm:px-6 py-3 font-semibold">Durasi</th>
                                <th class="px-4 sm:px-6 py-3 font-semibold">Alasan</th>
                                <th class="px-4 sm:px-6 py-3 font-semibold">Status</th>
                                <th class="px-4 sm:px-6 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($riwayat as $c)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 sm:px-6 py-4 text-slate-700 whitespace-nowrap">
                                        {{ $c->tanggal_mulai->format('d M Y') }} — {{ $c->tanggal_selesai->format('d M Y') }}
                                    </td>

                                    <td class="px-4 sm:px-6 py-4 text-slate-700 whitespace-nowrap">
                                        {{ $c->jumlah_hari }} hari
                                    </td>

                                    <td class="px-4 sm:px-6 py-4 text-slate-600 max-w-[150px] sm:max-w-xs truncate" title="{{ $c->alasan ?? 'Tidak ada alasan' }}">
                                        {{ $c->alasan ?? '-' }}
                                    </td>

                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        @if($c->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                            </span>
                                        @elseif($c->status === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                            </span>
                                        @elseif($c->status === 'cancelled')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Dibatalkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 sm:px-6 py-4 text-right whitespace-nowrap">
                                        @if($c->status === 'pending')
                                            <form action="{{ route('cuti.cancel', $c) }}" method="POST" class="inline"
                                                  data-confirm="{{ \App\Models\Setting::get('alert_konfirmasi_batal_cuti') ?: 'Apakah Anda yakin ingin membatalkan pengajuan cuti ini? Sisa cuti akan dikembalikan.' }}"
                                                  onsubmit="return confirm(this.dataset.confirm)">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700 px-2 py-1 rounded hover:bg-red-50 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Batalkan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Belum ada pengajuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($riwayat->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-200">{{ $riwayat->links() }}</div>
                @endif
            </div>
        </main>
    </div>

    {{-- MODAL: Ajukan Cuti --}}
    <div id="cutiModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Ajukan Cuti</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Sisa cuti Anda: <span class="font-semibold text-blue-600">{{ auth()->user()->sisa_cuti_tahunan }} hari</span></p>
                </div>
                <button type="button" onclick="closeCutiModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('cuti.store') }}" method="POST" class="p-6 space-y-4" id="cutiForm">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Alasan <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="text" name="alasan" placeholder="Misal: keperluan keluarga"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeCutiModal()"
                            class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SweetAlert --}}
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            confirmButtonColor: '#2563eb',
        });
    </script>
    @endif
    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: @json(\App\Models\Setting::get('alert_judul_gagal_cuti') ?: 'Tidak Bisa Mengajukan Cuti'),
            text: @json($errors->first()),
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#dc2626',
        }).then(() => { openCutiModal(); });
    </script>
    @endif
    @if(session('warning'))
    <script>
        Swal.fire({
            icon: 'warning',
            title: @json(\App\Models\Setting::get('alert_judul_batas_bulanan_pas') ?: 'Peringatan Batas Cuti'),
            text: @json(session('warning')),
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#f59e0b',
        });
    </script>
    @endif

    {{-- Script Modal & Sidebar Toggle --}}
    <script>
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

        function openCutiModal() {
            const modal = document.getElementById('cutiModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeCutiModal() {
            const modal = document.getElementById('cutiModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        document.getElementById('cutiModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCutiModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCutiModal();
        });

        document.getElementById('cutiForm')?.addEventListener('submit', function(e) {
            const sisaCuti = {{ auth()->user()->sisa_cuti_tahunan }};
            const alertHabis = @json(\App\Models\Setting::get('alert_cuti_habis') ?: 'Sisa cuti Anda habis.');
            const alertTanggalTidakValid = @json(\App\Models\Setting::get('alert_tanggal_tidak_valid') ?: 'Tanggal selesai tidak boleh sebelum tanggal mulai.');
            const alertMelebihiSisa = @json(\App\Models\Setting::get('alert_melebihi_sisa') ?: 'Durasi cuti Anda {durasi} hari, sedangkan sisa cuti Anda hanya {sisa} hari.');
            const mulai = document.getElementById('tanggal_mulai').value;
            const selesai = document.getElementById('tanggal_selesai').value;

            if (!mulai || !selesai) return;

            const tglMulai = new Date(mulai);
            const tglSelesai = new Date(selesai);

            if (tglSelesai < tglMulai) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Tanggal Tidak Valid', text: alertTanggalTidakValid, confirmButtonColor: '#f59e0b' });
                return;
            }

            const diffTime = Math.abs(tglSelesai - tglMulai);
            const durasi = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (sisaCuti <= 0) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Cuti Habis', text: alertHabis, confirmButtonColor: '#dc2626' });
                return;
            }

            if (durasi > sisaCuti) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Melebihi Sisa Cuti',
                    text: alertMelebihiSisa.replace('{durasi}', durasi).replace('{sisa}', sisaCuti),
                    confirmButtonColor: '#dc2626'
                });
                return;
            }
        });
    </script>
</body>
</html>