@extends('super-admin.layout')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Kelola branding, login, alert, Fonnte, dan database')

@section('content')

    @php
        // Optimasi: Ambil semua data dari database HANYA SEKALI di awal
        $app_name = \App\Models\Setting::get('app_name');
        $favicon = \App\Models\Setting::get('favicon');
        $login_title = \App\Models\Setting::get('login_title');
        $login_subtitle = \App\Models\Setting::get('login_subtitle');
        $login_branding_title = \App\Models\Setting::get('login_branding_title');
        $login_branding_desc = \App\Models\Setting::get('login_branding_desc');
        $alert_cuti_habis = \App\Models\Setting::get('alert_cuti_habis');
        $alert_melebihi_sisa = \App\Models\Setting::get('alert_melebihi_sisa');
        $alert_sisa_menipis = \App\Models\Setting::get('alert_sisa_menipis');
        $fonnte_token = \App\Models\Setting::get('fonnte_token');
        $fonnte_admin_wa = \App\Models\Setting::get('fonnte_admin_wa');
        $fonnte_group_wa = \App\Models\Setting::get('fonnte_group_wa');
        $fonnte_admin_group_id = \App\Models\Setting::get('fonnte_admin_group_id');
    @endphp

    {{-- GRID KARTU PENGATURAN (4 KARTU) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6 mb-10">

        {{-- KARTU 1: BRANDING --}}
        <div onclick="openModal('brandingModal')" class="cursor-pointer group bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm hover:shadow-md hover:border-blue-300 transition-all flex flex-col items-center text-center relative overflow-hidden">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-2">Branding & Favicon</h2>
            <p class="text-xs sm:text-sm text-slate-500 line-clamp-2">Ubah nama aplikasi dan ikon favicon yang tampil di browser.</p>
        </div>

        {{-- KARTU 2: TEKS LOGIN --}}
        <div onclick="openModal('loginModal')" class="cursor-pointer group bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm hover:shadow-md hover:border-purple-300 transition-all flex flex-col items-center text-center relative overflow-hidden">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-2">Teks Halaman Login</h2>
            <p class="text-xs sm:text-sm text-slate-500 line-clamp-2">Sesuaikan judul, subjudul, dan deskripsi pada halaman login.</p>
        </div>

        {{-- KARTU 3: ALERT --}}
        <div onclick="openModal('alertModal')" class="cursor-pointer group bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm hover:shadow-md hover:border-amber-300 transition-all flex flex-col items-center text-center relative overflow-hidden">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-2">Pesan Alert</h2>
            <p class="text-xs sm:text-sm text-slate-500 line-clamp-2">Atur pesan peringatan (pop-up) saat cuti habis atau melebihi batas.</p>
        </div>

        {{-- KARTU 4: FONNTE --}}
        <div onclick="openModal('fonnteModal')" class="cursor-pointer group bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col items-center text-center relative overflow-hidden">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-2">Integrasi Fonnte</h2>
            <p class="text-xs sm:text-sm text-slate-500 line-clamp-2">Konfigurasi Token API dan nomor WhatsApp untuk notifikasi.</p>
        </div>
    </div>

    {{-- MANAJEMEN DATABASE (HANYA 2 KARTU) --}}
    <div class="mt-8">
        {{-- Judul Utama --}}
        <div class="mb-4">
            <h2 class="text-lg sm:text-xl font-bold text-slate-800">Manajemen Database</h2>
            <p class="text-xs sm:text-sm text-slate-500">Backup dan restore data aplikasi (SQLite)</p>
        </div>

        {{-- 2 Card Database --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">

            {{-- Card 1: Export --}}
            <a href="{{ route('super-admin.settings.export-db') }}" class="group bg-slate-100 hover:bg-slate-200 rounded-2xl border border-slate-200 p-6 sm:p-8 flex flex-col items-center justify-center text-center transition-all cursor-pointer">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-slate-200 text-slate-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-slate-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-1">Export Database</h3>
                <p class="text-xs sm:text-sm text-slate-500">Klik untuk mengunduh backup</p>
            </a>

            {{-- Card 2: Import --}}
            <div class="group relative bg-slate-100 hover:bg-slate-200 rounded-2xl border border-slate-200 p-6 sm:p-8 flex flex-col items-center justify-center text-center transition-all cursor-pointer overflow-hidden">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-slate-200 text-slate-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-slate-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-1">Import Database</h3>
                <p class="text-xs sm:text-sm text-slate-500">Klik untuk mengunggah file .sqlite</p>

                {{-- Form tersembunyi untuk klik otomatis --}}
                <form action="{{ route('super-admin.settings.import-db') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Peringatan! Tindakan ini akan MENGGANTI seluruh data saat ini dengan file database yang diunggah. Lanjutkan?')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    @csrf
                    <input type="file" name="database_file" accept=".sqlite,.db" required class="w-full h-full cursor-pointer" onchange="this.form.submit()">
                </form>
            </div>

        </div>
    </div>

    {{-- ================= MODALS ================= --}}

    {{-- MODAL: BRANDING --}}
    <div id="brandingModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50 shrink-0">
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Edit Branding</h2>
                <button type="button" onclick="closeModal('brandingModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('super-admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-4 overflow-y-auto custom-scroll">
                @csrf
                {{-- Hidden inputs --}}
                <input type="hidden" name="login_title" value="{{ $login_title }}">
                <input type="hidden" name="login_subtitle" value="{{ $login_subtitle }}">
                <input type="hidden" name="login_branding_title" value="{{ $login_branding_title }}">
                <input type="hidden" name="login_branding_desc" value="{{ $login_branding_desc }}">
                <input type="hidden" name="alert_cuti_habis" value="{{ $alert_cuti_habis }}">
                <input type="hidden" name="alert_melebihi_sisa" value="{{ $alert_melebihi_sisa }}">
                <input type="hidden" name="alert_sisa_menipis" value="{{ $alert_sisa_menipis }}">
                <input type="hidden" name="fonnte_token" value="{{ $fonnte_token }}">
                <input type="hidden" name="fonnte_admin_wa" value="{{ $fonnte_admin_wa }}">
                <input type="hidden" name="fonnte_group_wa" value="{{ $fonnte_group_wa }}">
                <input type="hidden" name="fonnte_admin_group_id" value="{{ $fonnte_admin_group_id }}">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ $app_name }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Favicon <span class="text-slate-400 font-normal">(Maks 512 KB)</span></label>
                    @if($favicon)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ Storage::url($favicon) }}" class="w-10 h-10 object-contain border rounded" alt="Favicon">
                            <span class="text-xs text-slate-500">Favicon saat ini</span>
                        </div>
                    @endif
                    <input type="file" name="favicon" accept=".png,.ico,.jpg,.jpeg" class="w-full text-xs sm:text-sm text-slate-600 file:mr-3 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded-lg file:border-0 file:text-xs sm:file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition border sm:border-0 border-slate-200 rounded-lg">
                </div>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-2 shrink-0">
                    <button type="button" onclick="closeModal('brandingModal')" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition text-sm">Batal</button>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: TEKS LOGIN --}}
    <div id="loginModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50 shrink-0">
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Edit Teks Login</h2>
                <button type="button" onclick="closeModal('loginModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('super-admin.settings.update') }}" method="POST" class="p-5 sm:p-6 space-y-4 overflow-y-auto custom-scroll">
                @csrf
                <input type="hidden" name="app_name" value="{{ $app_name }}">
                <input type="hidden" name="alert_cuti_habis" value="{{ $alert_cuti_habis }}">
                <input type="hidden" name="alert_melebihi_sisa" value="{{ $alert_melebihi_sisa }}">
                <input type="hidden" name="alert_sisa_menipis" value="{{ $alert_sisa_menipis }}">
                <input type="hidden" name="fonnte_token" value="{{ $fonnte_token }}">
                <input type="hidden" name="fonnte_admin_wa" value="{{ $fonnte_admin_wa }}">
                <input type="hidden" name="fonnte_group_wa" value="{{ $fonnte_group_wa }}">
                <input type="hidden" name="fonnte_admin_group_id" value="{{ $fonnte_admin_group_id }}">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Form Login</label>
                    <input type="text" name="login_title" value="{{ $login_title }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Subjudul Form Login</label>
                    <input type="text" name="login_subtitle" value="{{ $login_subtitle }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Branding (Panel Kiri)</label>
                    <input type="text" name="login_branding_title" value="{{ $login_branding_title }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi Branding</label>
                    <textarea name="login_branding_desc" rows="3" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">{{ $login_branding_desc }}</textarea>
                </div>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-2 shrink-0">
                    <button type="button" onclick="closeModal('loginModal')" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition text-sm">Batal</button>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: ALERT --}}
    <div id="alertModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50 shrink-0">
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Edit Pesan Alert</h2>
                <button type="button" onclick="closeModal('alertModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('super-admin.settings.update') }}" method="POST" class="p-5 sm:p-6 space-y-4 overflow-y-auto custom-scroll">
                @csrf
                <input type="hidden" name="app_name" value="{{ $app_name }}">
                <input type="hidden" name="login_title" value="{{ $login_title }}">
                <input type="hidden" name="login_subtitle" value="{{ $login_subtitle }}">
                <input type="hidden" name="login_branding_title" value="{{ $login_branding_title }}">
                <input type="hidden" name="login_branding_desc" value="{{ $login_branding_desc }}">
                <input type="hidden" name="fonnte_token" value="{{ $fonnte_token }}">
                <input type="hidden" name="fonnte_admin_wa" value="{{ $fonnte_admin_wa }}">
                <input type="hidden" name="fonnte_group_wa" value="{{ $fonnte_group_wa }}">
                <input type="hidden" name="fonnte_admin_group_id" value="{{ $fonnte_admin_group_id }}">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pesan Cuti Habis</label>
                    <input type="text" name="alert_cuti_habis" value="{{ $alert_cuti_habis }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pesan Melebihi Sisa</label>
                    <input type="text" name="alert_melebihi_sisa" value="{{ $alert_melebihi_sisa }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pesan Sisa Menipis</label>
                    <input type="text" name="alert_sisa_menipis" value="{{ $alert_sisa_menipis }}" required class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-2 shrink-0">
                    <button type="button" onclick="closeModal('alertModal')" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition text-sm">Batal</button>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: FONNTE --}}
    <div id="fonnteModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50 shrink-0">
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Edit Konfigurasi Fonnte</h2>
                <button type="button" onclick="closeModal('fonnteModal')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('super-admin.settings.update') }}" method="POST" class="p-5 sm:p-6 space-y-4 overflow-y-auto custom-scroll">
                @csrf
                <input type="hidden" name="app_name" value="{{ $app_name }}">
                <input type="hidden" name="login_title" value="{{ $login_title }}">
                <input type="hidden" name="login_subtitle" value="{{ $login_subtitle }}">
                <input type="hidden" name="login_branding_title" value="{{ $login_branding_title }}">
                <input type="hidden" name="login_branding_desc" value="{{ $login_branding_desc }}">
                <input type="hidden" name="alert_cuti_habis" value="{{ $alert_cuti_habis }}">
                <input type="hidden" name="alert_melebihi_sisa" value="{{ $alert_melebihi_sisa }}">
                <input type="hidden" name="alert_sisa_menipis" value="{{ $alert_sisa_menipis }}">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Token Fonnte</label>
                    <input type="text" name="fonnte_token" value="{{ $fonnte_token }}" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                {{-- Menjadi 1 kolom di HP, 2 kolom di Desktop --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">WA Admin</label>
                        <input type="text" name="fonnte_admin_wa" value="{{ $fonnte_admin_wa }}" placeholder="628xxx" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">WA Grup</label>
                        <input type="text" name="fonnte_group_wa" value="{{ $fonnte_group_wa }}" placeholder="xxxx@g.us" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Group ID Admin</label>
                    <input type="text" name="fonnte_admin_group_id" value="{{ $fonnte_admin_group_id }}" placeholder="xxxx@g.us" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-2 shrink-0">
                    <button type="button" onclick="closeModal('fonnteModal')" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition text-sm">Batal</button>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    // Tutup modal via klik backdrop
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // Tutup modal via Esc
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.fixed.inset-0').forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    closeModal(modal.id);
                }
            });
        }
    });
</script>
@endpush
