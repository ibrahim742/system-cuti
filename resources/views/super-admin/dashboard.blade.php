@extends('super-admin.layout')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard Super Admin')
@section('page-subtitle', 'Ringkasan sistem cuti')

@section('content')

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
        {{-- Total Akun --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs sm:text-sm font-medium text-slate-500 line-clamp-1">Total Akun</p>
            <p class="text-xl sm:text-2xl font-bold text-slate-800 mt-1 sm:mt-2">{{ $totalUsers }}</p>
            <p class="text-[10px] sm:text-xs text-slate-400 mt-1">{{ $totalKaryawan }} karyawan · {{ $totalAdmin }} admin</p>
        </div>

        {{-- Menunggu --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs sm:text-sm font-medium text-slate-500 line-clamp-1">Menunggu Persetujuan</p>
            <p class="text-xl sm:text-2xl font-bold text-amber-600 mt-1 sm:mt-2">{{ $pending }}</p>
            <p class="text-[10px] sm:text-xs text-slate-400 mt-1">pengajuan pending</p>
        </div>

        {{-- Cuti Bulan Ini --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs sm:text-sm font-medium text-slate-500 line-clamp-1">Cuti Bulan Ini</p>
            <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-1 sm:mt-2">{{ $cutiBulanIni }} <span class="text-xs sm:text-sm font-normal text-slate-400">hari</span></p>
            <p class="text-[10px] sm:text-xs text-slate-400 mt-1">cuti disetujui</p>
        </div>

        {{-- Belum Cuti --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5 shadow-sm">
            <p class="text-xs sm:text-sm font-medium text-slate-500 line-clamp-1">Belum Pernah Cuti</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600 mt-1 sm:mt-2">{{ $belumCutiCount }} <span class="text-xs sm:text-sm font-normal text-slate-400">orang</span></p>
            <p class="text-[10px] sm:text-xs text-slate-400 mt-1">karyawan</p>
        </div>
    </div>

    {{-- AKSI CEPAT --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 lg:gap-6">
        <a href="{{ route('super-admin.users.index') }}"
           class="group bg-white rounded-xl border border-slate-200 p-4 sm:p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm sm:text-base font-semibold text-slate-800">Kelola Akun</p>
                <p class="text-xs sm:text-sm text-slate-500 line-clamp-1">Tambah, edit, hapus akun karyawan & admin</p>
            </div>
        </a>
        <a href="{{ route('admin.dashboard') }}"
           class="group bg-white rounded-xl border border-slate-200 p-4 sm:p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm sm:text-base font-semibold text-slate-800">Persetujuan Cuti</p>
                <p class="text-xs sm:text-sm text-slate-500 line-clamp-1">Tinjau dan proses pengajuan cuti</p>
            </div>
        </a>
    </div>

    {{-- CUTI TERBARU --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200">
            <h2 class="text-base sm:text-lg font-semibold text-slate-800">Pengajuan Terbaru</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">5 pengajuan terakhir dari semua karyawan</p>
        </div>

        {{-- Area Scroll di HP --}}
        <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-xs sm:text-sm text-left min-w-[700px]">
                <thead class="bg-slate-50 text-slate-600 text-[10px] sm:text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Nama</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Durasi</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Alasan</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cutiTerbaru as $c)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-slate-100 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-semibold text-slate-600 shrink-0">
                                        {{ strtoupper(substr($c->user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800 truncate max-w-[120px] sm:max-w-none">{{ $c->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-700 whitespace-nowrap">
                                {{ $c->tanggal_mulai->format('d M Y') }} — {{ $c->tanggal_selesai->format('d M Y') }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-700 whitespace-nowrap">{{ $c->jumlah_hari }} hari</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-600 max-w-[150px] sm:max-w-xs truncate" title="{{ $c->alasan ?? 'Tidak ada alasan' }}">
                                {{ $c->alasan ?? '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                @if($c->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @elseif($c->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium bg-red-50 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada pengajuan cuti.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABEL BELUM PERNAH CUTI (COLLAPSIBLE) --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-6">
        <details class="group">
            {{-- Bagian yang selalu tampil (Card Header) --}}
            <summary class="px-4 sm:px-6 py-4 sm:py-5 cursor-pointer flex items-center justify-between hover:bg-slate-50 transition list-none">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-slate-800">Karyawan Belum Pernah Cuti</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 hidden sm:block">Klik untuk melihat daftar karyawan yang belum pernah mengambil cuti</p>
                </div>

                {{-- Tombol Panah / Ikon --}}
                <div class="flex items-center justify-center w-8 h-8 sm:w-auto sm:h-auto sm:px-3 sm:py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg group-open:bg-red-50 group-open:text-red-600 transition">
                    <svg class="w-4 h-4 transition-transform duration-300 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </summary>

            {{-- Bagian yang disembunyikan (Tabel Data) --}}
            <div class="border-t border-slate-200">
                <div class="overflow-x-auto custom-scroll" style="max-height: 300px;">
                    <table class="w-full text-xs sm:text-sm text-left min-w-[500px]">
                        <thead class="bg-slate-50 text-slate-600 text-[10px] sm:text-xs uppercase tracking-wide sticky top-0 z-10">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 font-semibold">Nama Karyawan</th>
                                <th class="px-4 sm:px-6 py-3 font-semibold">Email</th>
                                <th class="px-4 sm:px-6 py-3 font-semibold">Sisa Cuti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse(\App\Models\User::where('role', 'karyawan')->whereDoesntHave('cutis', function($q){ $q->where('status', 'approved'); })->get() as $u)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 sm:px-6 py-3 sm:py-4 flex items-center gap-2 sm:gap-3">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-slate-100 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-semibold text-slate-600 shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-slate-800 truncate">{{ $u->name }}</span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-600">{{ $u->email }}</td>
                                    <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-600 whitespace-nowrap">{{ $u->sisa_cuti_tahunan }} / {{ $u->jatah_cuti_tahunan }} hari</td>
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

@endsection
