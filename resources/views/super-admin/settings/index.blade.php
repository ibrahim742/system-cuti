@extends('super-admin.layout')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Pilih menu pengaturan di bawah untuk mengedit')

@section('content')
@php
    $alertFields = [
        'Cuti & Batas Bulanan' => [
            'alert_judul_batas_bulanan_pas' => ['label' => 'Judul Alert Peringatan Batas Cuti (Kuning)', 'default' => 'Peringatan Batas Cuti', 'type' => 'text'],
            'alert_judul_gagal_cuti' => ['label' => 'Judul Alert Gagal / Melebihi Batas Cuti (Merah)', 'default' => 'Tidak Bisa Mengajukan Cuti', 'type' => 'text'],
            'alert_batas_bulanan_pas' => ['label' => 'Pesan Alert Cuti Tepat 3 Hari (Kuning)', 'default' => 'PERINGATAN: Total cuti Anda bulan ini tepat 3 hari. Anda sudah mencapai batas maksimal bulan ini.'],
            'alert_batas_bulanan_lebih' => ['label' => 'Pesan Alert Cuti Melebihi 3 Hari (Merah) — variabel: {total}', 'default' => 'PERINGATAN KERAS: Total cuti Anda bulan ini menjadi {total} hari. Anda sudah melebihi batas maksimal 3 hari per bulan!'],
            'alert_cuti_berhasil' => ['label' => 'Pengajuan Cuti Berhasil', 'default' => 'Pengajuan cuti berhasil dikirim.'],
            'alert_cuti_habis' => ['label' => 'Sisa Cuti Habis', 'default' => 'Sisa cuti Anda habis. Silakan hubungi BOS.'],
            'alert_melebihi_sisa' => ['label' => 'Durasi Melebihi Sisa Cuti — variabel: {durasi}, {sisa}', 'default' => 'Durasi cuti Anda {durasi} hari, sedangkan sisa cuti Anda hanya {sisa} hari.'],
            'alert_sisa_menipis' => ['label' => 'Peringatan Sisa Cuti Menipis', 'default' => 'Sisa cuti tahunan Anda tinggal sedikit.'],
            'alert_tanggal_tidak_valid' => ['label' => 'Tanggal Selesai Sebelum Tanggal Mulai', 'default' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.'],
            'alert_konfirmasi_batal_cuti' => ['label' => 'Konfirmasi Batalkan Cuti (Popup)', 'default' => 'Apakah Anda yakin ingin membatalkan pengajuan cuti ini? Sisa cuti akan dikembalikan.'],
            'alert_cuti_dibatalkan' => ['label' => 'Cuti Berhasil Dibatalkan', 'default' => 'Pengajuan cuti berhasil dibatalkan. Sisa cuti Anda telah dikembalikan.'],
            'alert_batal_bukan_pemilik' => ['label' => 'Gagal Batal (Bukan Pemilik)', 'default' => 'Anda tidak berhak membatalkan cuti ini.'],
            'alert_batal_bukan_pending' => ['label' => 'Gagal Batal (Sudah Diproses)', 'default' => 'Hanya cuti yang belum disetujui yang dapat dibatalkan.'],
            'alert_cuti_sudah_diproses' => ['label' => 'Cuti Sudah Diproses Admin', 'default' => 'Cuti ini sudah diproses.'],
            'alert_cuti_disetujui' => ['label' => 'Cuti Disetujui', 'default' => 'Cuti disetujui.'],
            'alert_cuti_ditolak' => ['label' => 'Cuti Ditolak', 'default' => 'Cuti ditolak. Sisa cuti karyawan telah dikembalikan.'],
        ],
        'Login' => [
            'alert_login_terkunci' => ['label' => 'Akun Terkunci (Salah Password 5x)', 'default' => 'Akun Anda telah dikunci karena salah password 5 kali. Silakan hubungi Super Admin untuk membuka kunci.'],
            'alert_login_salah_sisa' => ['label' => 'Password Salah dengan Sisa Percobaan — variabel: {sisa}', 'default' => 'Email atau password salah. Sisa percobaan: {sisa}x.'],
            'alert_login_gagal' => ['label' => 'Email / Password Salah (Umum)', 'default' => 'Email atau password salah.'],
        ],
        'Kelola Akun & Profil' => [
            'alert_akun_ditambahkan' => ['label' => 'Akun Berhasil Ditambahkan', 'default' => 'Akun berhasil ditambahkan.'],
            'alert_akun_diperbarui' => ['label' => 'Akun Berhasil Diperbarui', 'default' => 'Akun berhasil diperbarui.'],
            'alert_akun_dihapus' => ['label' => 'Akun Berhasil Dihapus', 'default' => 'Akun berhasil dihapus.'],
            'alert_hapus_akun_sendiri' => ['label' => 'Gagal Hapus Akun Sendiri', 'default' => 'Anda tidak bisa menghapus akun sendiri.'],
            'alert_akun_dibuka' => ['label' => 'Kunci Akun Dibuka — variabel: {nama}', 'default' => 'Akun {nama} berhasil dibuka kuncinya.'],
            'alert_reset_cuti_massal' => ['label' => 'Reset Cuti Massal Berhasil', 'default' => 'Sisa cuti seluruh karyawan berhasil di-reset ke jatah awal tahun ini.'],
            'alert_profil_diperbarui' => ['label' => 'Profil Berhasil Diperbarui', 'default' => 'Profil berhasil diperbarui.'],
            'alert_foto_terlalu_besar' => ['label' => 'Foto Profil Terlalu Besar (JS) — variabel: {ukuran}', 'default' => 'Ukuran foto Anda {ukuran} MB, melebihi batas 1 MB.'],
            'alert_foto_bukan_gambar' => ['label' => 'Validasi Foto Bukan Gambar', 'default' => 'File harus berupa gambar.'],
            'alert_foto_format_salah' => ['label' => 'Validasi Format Foto Salah', 'default' => 'Format gambar harus jpg, jpeg, png, atau webp.'],
            'alert_foto_maksimal' => ['label' => 'Validasi Ukuran Foto Maksimal', 'default' => 'Ukuran foto maksimal 1 MB.'],
            'alert_foto_gagal_upload' => ['label' => 'Validasi Gagal Upload Foto', 'default' => 'Gagal upload foto. Pastikan ukuran tidak melebihi 1 MB.'],
            'alert_password_minimal' => ['label' => 'Validasi Password Minimal', 'default' => 'Password minimal 6 karakter.'],
            'alert_password_konfirmasi' => ['label' => 'Validasi Konfirmasi Password', 'default' => 'Konfirmasi password tidak cocok.'],
        ],
    ];

    $activeSection = old('section', session('active_section', ''));
@endphp

{{-- GRID MENU IKON --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    {{-- Ikon 1: Umum & Login --}}
    <button type="button" onclick="openSettingPanel('umum')" id="card-umum"
            class="setting-card group bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-blue-400 transition text-left flex flex-col items-center text-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-800">Umum & Login</p>
            <p class="text-xs text-slate-500 mt-0.5">Nama app, favicon, teks login</p>
        </div>
    </button>

    {{-- Ikon 2: WhatsApp Fonnte --}}
    <button type="button" onclick="openSettingPanel('fonnte')" id="card-fonnte"
            class="setting-card group bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-emerald-400 transition text-left flex flex-col items-center text-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-800">WhatsApp (Fonnte)</p>
            <p class="text-xs text-slate-500 mt-0.5">Token API, nomor & grup WA</p>
        </div>
    </button>

    {{-- Ikon 3: Pesan Alert --}}
    <button type="button" onclick="openSettingPanel('alert')" id="card-alert"
            class="setting-card group bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-amber-400 transition text-left flex flex-col items-center text-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-800">Pesan Alert</p>
            <p class="text-xs text-slate-500 mt-0.5">Edit semua teks notifikasi</p>
        </div>
    </button>

    {{-- Ikon 4: Backup & Restore Database --}}
    <button type="button" onclick="openSettingPanel('database')" id="card-database"
            class="setting-card group bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:border-purple-400 transition text-left flex flex-col items-center text-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-800">Backup Database</p>
            <p class="text-xs text-slate-500 mt-0.5">Export & import SQLite</p>
        </div>
    </button>
</div>

{{-- PANEL 1: UMUM & LOGIN --}}
<div id="panel-umum" class="setting-panel hidden bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-5">
        <div>
            <h2 class="text-base font-semibold text-slate-800">Umum & Halaman Login</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Identitas aplikasi, favicon, dan teks halaman login</p>
        </div>
        <button type="button" onclick="closeAllPanels()" class="text-xs font-medium text-slate-500 hover:text-slate-800 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 transition">
            Tutup
        </button>
    </div>

    <form action="{{ route('super-admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <input type="hidden" name="section" value="umum">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Aplikasi</label>
                <input type="text" name="app_name" required
                       value="{{ old('app_name', \App\Models\Setting::get('app_name') ?: 'Sistem Cuti') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Favicon (PNG/ICO/JPG, maks 512 KB)</label>
                <input type="file" name="favicon" accept=".png,.ico,.jpg,.jpeg"
                       class="w-full text-sm text-slate-600 border border-slate-300 rounded-lg p-1.5">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul Login</label>
                <input type="text" name="login_title" required
                       value="{{ old('login_title', \App\Models\Setting::get('login_title') ?: 'Selamat Datang') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Subjudul Login</label>
                <input type="text" name="login_subtitle" required
                       value="{{ old('login_subtitle', \App\Models\Setting::get('login_subtitle') ?: 'Silakan masuk ke akun Anda') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul Branding Login</label>
                <input type="text" name="login_branding_title" required
                       value="{{ old('login_branding_title', \App\Models\Setting::get('login_branding_title') ?: 'Sistem Pengajuan Cuti') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Branding Login</label>
                <input type="text" name="login_branding_desc" required
                       value="{{ old('login_branding_desc', \App\Models\Setting::get('login_branding_desc') ?: 'Kelola pengajuan dan persetujuan cuti karyawan dengan mudah.') }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Simpan Umum & Login
            </button>
        </div>
    </form>
</div>

{{-- PANEL 2: WHATSAPP FONNTE --}}
<div id="panel-fonnte" class="setting-panel hidden bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-5">
        <div>
            <h2 class="text-base font-semibold text-slate-800">Notifikasi WhatsApp (Fonnte)</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Konfigurasi token API dan target penerima notifikasi</p>
        </div>
        <button type="button" onclick="closeAllPanels()" class="text-xs font-medium text-slate-500 hover:text-slate-800 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 transition">
            Tutup
        </button>
    </div>

    <form action="{{ route('super-admin.settings.update') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="section" value="fonnte">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Fonnte Token</label>
                <input type="text" name="fonnte_token"
                       value="{{ old('fonnte_token', \App\Models\Setting::get('fonnte_token')) }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nomor WA Admin</label>
                <input type="text" name="fonnte_admin_wa"
                       value="{{ old('fonnte_admin_wa', \App\Models\Setting::get('fonnte_admin_wa')) }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Group WA (ID Grup)</label>
                <input type="text" name="fonnte_group_wa"
                       value="{{ old('fonnte_group_wa', \App\Models\Setting::get('fonnte_group_wa')) }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Admin Group ID (Opsional)</label>
                <input type="text" name="fonnte_admin_group_id"
                       value="{{ old('fonnte_admin_group_id', \App\Models\Setting::get('fonnte_admin_group_id')) }}"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Simpan Pengaturan WA
            </button>
        </div>
    </form>
</div>

{{-- PANEL 3: PESAN ALERT --}}
<div id="panel-alert" class="setting-panel hidden bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-5">
        <div>
            <h2 class="text-base font-semibold text-slate-800">Pengaturan Pesan Alert</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Biarkan variabel seperti <code>{total}</code>, <code>{durasi}</code>, atau <code>{sisa}</code> agar terisi otomatis.</p>
        </div>
        <button type="button" onclick="closeAllPanels()" class="text-xs font-medium text-slate-500 hover:text-slate-800 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 transition">
            Tutup
        </button>
    </div>

    <form action="{{ route('super-admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="section" value="alert">

        @foreach($alertFields as $group => $fields)
            <div class="border-t border-slate-200 pt-4 first:border-t-0 first:pt-0 space-y-4">
                <h3 class="text-sm font-semibold text-amber-600">{{ $group }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($fields as $key => $info)
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">{{ $info['label'] }}</label>
                            @if(($info['type'] ?? 'textarea') === 'text')
                                <input type="text" name="{{ $key }}"
                                       value="{{ old($key, \App\Models\Setting::get($key) ?: $info['default']) }}"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-amber-500">
                            @else
                                <textarea name="{{ $key }}" rows="2"
                                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-amber-500">{{ old($key, \App\Models\Setting::get($key) ?: $info['default']) }}</textarea>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Simpan Pesan Alert
            </button>
        </div>
    </form>
</div>

{{-- PANEL 4: BACKUP & RESTORE DATABASE --}}
<div id="panel-database" class="setting-panel hidden bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-5">
        <div>
            <h2 class="text-base font-semibold text-slate-800">Backup & Restore Database</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Ekspor atau impor file database</p>
        </div>
        <button type="button" onclick="closeAllPanels()" class="text-xs font-medium text-slate-500 hover:text-slate-800 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 transition">
            Tutup
        </button>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('super-admin.settings.export-db') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
            Download Backup Database
        </a>

        <form action="{{ route('super-admin.settings.import-db') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
            @csrf
            <input type="file" name="database_file" accept=".sqlite,.db" required
                   class="text-xs sm:text-sm text-slate-600 border border-slate-300 rounded-lg p-1.5">
            <button type="submit"
                    onclick="return confirm('Yakin ingin mengimpor database baru? Database lama akan di-backup otomatis.')"
                    class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">
                Import Database
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSettingPanel(name) {
        document.querySelectorAll('.setting-panel').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.setting-card').forEach(el => {
            el.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500', 'bg-slate-50');
        });

        const panel = document.getElementById('panel-' + name);
        const card = document.getElementById('card-' + name);

        if (panel) {
            panel.classList.remove('hidden');
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        if (card) {
            card.classList.add('ring-2', 'ring-blue-500', 'border-blue-500', 'bg-slate-50');
        }
    }

    function closeAllPanels() {
        document.querySelectorAll('.setting-panel').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.setting-card').forEach(el => {
            el.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500', 'bg-slate-50');
        });
    }

    @if(!empty($activeSection))
        document.addEventListener('DOMContentLoaded', function () {
            openSettingPanel(@json($activeSection));
        });
    @endif
</script>
@endpush