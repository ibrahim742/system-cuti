@extends('super-admin.layout')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Kelola branding, login, alert, Fonnte, dan database')

@section('content')

    <form action="{{ route('super-admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- UMUM --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-base font-semibold text-slate-800">Umum & Branding</h2>
                <p class="text-sm text-slate-500">Nama aplikasi dan favicon</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ \App\Models\Setting::get('app_name') }}" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Favicon</label>
                    @if(\App\Models\Setting::get('favicon'))
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ Storage::url(\App\Models\Setting::get('favicon')) }}" class="w-10 h-10 object-contain border border-slate-200 rounded" alt="Favicon">
                            <span class="text-xs text-slate-500">Favicon saat ini</span>
                        </div>
                    @endif
                    <input type="file" name="favicon" accept=".png,.ico,.jpg,.jpeg"
                           class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                    <p class="text-xs text-slate-500 mt-1">Format: png, ico, jpg. Maks 512 KB.</p>
                </div>
            </div>
        </div>

        {{-- LOGIN --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-base font-semibold text-slate-800">Teks Halaman Login</h2>
                <p class="text-sm text-slate-500">Keterangan yang tampil di halaman login</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Form Login</label>
                    <input type="text" name="login_title" value="{{ \App\Models\Setting::get('login_title') }}" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Subjudul Form Login</label>
                    <input type="text" name="login_subtitle" value="{{ \App\Models\Setting::get('login_subtitle') }}" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Branding (Panel Kiri)</label>
                    <input type="text" name="login_branding_title" value="{{ \App\Models\Setting::get('login_branding_title') }}" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi Branding</label>
                    <textarea name="login_branding_desc" rows="3" required
                              class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ \App\Models\Setting::get('login_branding_desc') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ALERT --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-base font-semibold text-slate-800">Pesan Alert / Pop-up</h2>
                <p class="text-sm text-slate-500">Pesan validasi yang muncul saat karyawan mengajukan cuti</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pesan Cuti Habis</label>
                    <input type="text" name="alert_cuti_habis" value="{{ \App\Models\Setting::get('alert_cuti_habis') }}" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pesan Durasi Melebihi Sisa</label>
                    <input type="text" name="alert_melebihi_sisa" value="{{ \App\Models\Setting::get('alert_melebihi_sisa') }}" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pesan Sisa Cuti Menipis</label>
                    <input type="text" name="alert_sisa_menipis" value="{{ \App\Models\Setting::get('alert_sisa_menipis') }}" required
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>
        </div>

        {{-- FONNTE --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-base font-semibold text-slate-800">Konfigurasi Fonnte (WhatsApp)</h2>
                <p class="text-sm text-slate-500">Token dan nomor tujuan notifikasi</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Token Fonnte</label>
                    <input type="text" name="fonnte_token" value="{{ \App\Models\Setting::get('fonnte_token') }}"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">WA Admin (Personal)</label>
                        <input type="text" name="fonnte_admin_wa" value="{{ \App\Models\Setting::get('fonnte_admin_wa') }}" placeholder="628xxx"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">WA Grup</label>
                        <input type="text" name="fonnte_group_wa" value="{{ \App\Models\Setting::get('fonnte_group_wa') }}" placeholder="xxxx@g.us"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Group ID Admin</label>
                    <input type="text" name="fonnte_admin_group_id" value="{{ \App\Models\Setting::get('fonnte_admin_group_id') }}" placeholder="xxxx@g.us"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                Simpan Semua Pengaturan
            </button>
        </div>
    </form>

    {{-- EXPORT / IMPORT DATABASE --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm mt-6">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-base font-semibold text-slate-800">Database</h2>
            <p class="text-sm text-slate-500">Export (backup) dan import database SQLite</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Export --}}
            <div class="border border-slate-200 rounded-lg p-5">
                <h3 class="font-semibold text-slate-800 mb-1">Export Database</h3>
                <p class="text-sm text-slate-500 mb-4">Unduh file database saat ini sebagai backup.</p>
                <a href="{{ route('super-admin.settings.export-db') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Backup
                </a>
            </div>

            {{-- Import --}}
            <div class="border border-slate-200 rounded-lg p-5">
                <h3 class="font-semibold text-slate-800 mb-1">Import Database</h3>
                <p class="text-sm text-slate-500 mb-4">Upload file <code class="bg-slate-100 px-1 rounded">.sqlite</code> untuk mengganti database. Database lama otomatis dibackup.</p>
                <form action="{{ route('super-admin.settings.import-db') }}" method="POST" enctype="multipart/form-data" class="space-y-3"
                      onsubmit="return confirm('Import database akan MENGGANTI semua data saat ini. Lanjutkan?')">
                    @csrf
                    <input type="file" name="database_file" accept=".sqlite,.db" required
                           class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Database
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection
