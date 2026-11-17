@extends('super-admin.layout')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard Super Admin')
@section('page-subtitle', 'Ringkasan sistem cuti')

@section('content')

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Akun</p>
            <p class="text-2xl font-bold text-slate-800 mt-2">{{ $totalUsers }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $totalKaryawan }} karyawan · {{ $totalAdmin }} admin</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Menunggu</p>
            <p class="text-2xl font-bold text-amber-600 mt-2">{{ $pending }}</p>
            <p class="text-xs text-slate-400 mt-1">pengajuan pending</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Disetujui</p>
            <p class="text-2xl font-bold text-emerald-600 mt-2">{{ $approved }}</p>
            <p class="text-xs text-slate-400 mt-1">total cuti disetujui</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Ditolak</p>
            <p class="text-2xl font-bold text-red-600 mt-2">{{ $rejected }}</p>
            <p class="text-xs text-slate-400 mt-1">total cuti ditolak</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
        <a href="{{ route('super-admin.users.index') }}"
           class="group bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800">Kelola Akun</p>
                <p class="text-sm text-slate-500">Tambah, edit, hapus akun karyawan & admin</p>
            </div>
        </a>
        <a href="{{ route('admin.dashboard') }}"
           class="group bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800">Persetujuan Cuti</p>
                <p class="text-sm text-slate-500">Tinjau dan proses pengajuan cuti</p>
            </div>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-base font-semibold text-slate-800">Pengajuan Terbaru</h2>
            <p class="text-sm text-slate-500 mt-0.5">5 pengajuan terakhir dari semua karyawan</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Nama</th>
                        <th class="px-6 py-3 font-semibold">Tanggal</th>
                        <th class="px-6 py-3 font-semibold">Durasi</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cutiTerbaru as $c)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-xs font-semibold text-slate-600">
                                        {{ strtoupper(substr($c->user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $c->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                {{ $c->tanggal_mulai->format('d M Y') }} — {{ $c->tanggal_selesai->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $c->jumlah_hari }} hari</td>
                            <td class="px-6 py-4">
                                @if($c->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @elseif($c->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada pengajuan cuti.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
