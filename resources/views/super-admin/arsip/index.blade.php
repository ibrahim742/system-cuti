@extends('super-admin.layout')

@section('title', 'Arsip PDF Cuti')
@section('page-title', 'Arsip PDF Cuti')
@section('page-subtitle', 'Daftar rekap cuti karyawan yang telah diarsipkan')

@section('content')

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        
        {{-- Header Tabel & Filter --}}
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-slate-800">Daftar Arsip</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $arsip->total() }} file arsip ditemukan</p>
            </div>

            {{-- Filter Bulan & Tahun --}}
            <form method="GET" action="{{ route('super-admin.arsip.index') }}" class="flex flex-wrap items-center gap-2 text-sm w-full sm:w-auto">
                <input type="month" name="bulan_month" value="{{ $tahun }}-{{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}"
                       onchange="document.getElementById('tahunInput').value = this.value.split('-')[0]; document.getElementById('bulanInput').value = this.value.split('-')[1]; this.form.submit();"
                       class="border border-slate-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 text-sm w-full sm:w-auto">

                <input type="hidden" name="tahun" id="tahunInput" value="{{ $tahun }}">
                <input type="hidden" name="bulan" id="bulanInput" value="{{ $bulan }}">
                
                @if(request('bulan') || request('tahun'))
                    <a href="{{ route('super-admin.arsip.index') }}" class="text-red-500 hover:text-red-700 text-xs font-medium ml-2 mt-2 sm:mt-0">Reset</a>
                @endif
            </form>
        </div>

        {{-- Area Tabel --}}
        <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-xs sm:text-sm text-left min-w-[600px]">
                <thead class="bg-slate-50 text-slate-600 text-[10px] sm:text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Nama Karyawan</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Bulan Arsip</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Tanggal Diarsipkan</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($arsip as $a)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-slate-100 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-semibold text-slate-600 shrink-0">
                                        {{ strtoupper(substr($a->user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800 truncate max-w-[120px] sm:max-w-none">{{ $a->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-700 whitespace-nowrap">
                                {{ $a->tanggal_mulai->format('F Y') }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-600 whitespace-nowrap">
                                {{ $a->updated_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-right whitespace-nowrap">
                                <a href="{{ asset('storage/' . $a->arsip_pdf) }}" target="_blank" 
                                   class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition border border-blue-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Belum ada arsip pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($arsip->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-slate-200">{{ $arsip->links() }}</div>
        @endif
    </div>

@endsection