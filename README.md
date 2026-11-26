# 📅 Sistem Cuti Karyawan

Sistem Informasi Manajemen Cuti Karyawan berbasis Web yang dibangun menggunakan **Laravel 11** dan **Tailwind CSS**. Aplikasi ini dirancang untuk mempermudah proses pengajuan, persetujuan, dan pemantauan cuti dengan integrasi notifikasi WhatsApp secara otomatis, serta dilengkapi fitur ekspor PDF.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Fonnte](https://img.shields.io/badge/WhatsApp_API-Fonnte-green?style=for-the-badge)

---

## ✨ Fitur Utama

Aplikasi ini memiliki 3 level pengguna (Role-Based Access Control) dengan fitur masing-masing:

### 👨‍💼 1. Karyawan
- Dashboard ringkasan sisa cuti tahunan dan cuti terpakai bulan ini.
- Form pengajuan cuti (tanggal mulai, tanggal selesai, alasan).
- Validasi otomatis batas maksimal cuti (3 hari/bulan).
- Melihat riwayat pengajuan dan status (Pending, Disetujui, Ditolak).
- **Fitur Batalkan Cuti** (khusus status Pending, kuota akan dikembalikan).
- Halaman profil (ganti foto profil & password).

### 🧑‍💻 2. Admin / HRD
- Dashboard persetujuan cuti.
- Melihat daftar pengajuan pending.
- Tombol **Setujui** dan **Tolak** (dengan pengembalian kuota otomatis jika ditolak).
- Melihat indikator peringatan (kuning/merah) jika karyawan mengajukan cuti melebihi batas bulanan.
- Melihat daftar karyawan yang belum pernah mengambil cuti.

### 👑 3. Super Admin
- Semua fitur Admin.
- **Kelola Akun**: Tambah, Edit, Hapus akun (Karyawan/Admin/Super Admin) & Buka Kunci Akun.
- **Reset Cuti Massal**: Mereset sisa cuti seluruh karyawan ke jatah awal tahunan.
- **Pengaturan Sistem**: Mengatur nama aplikasi, favicon, teks login, dan template pesan peringatan.
- **Konfigurasi Fonnte**: Mengatur Token Fonnte, Nomor Admin, dan ID Grup via Dashboard.
- **Arsip PDF**: Mengunduh rekap cuti karyawan bulan sebelumnya (otomatis diarsipkan).

---

## 🛠️ Persyaratan Sistem (Prerequisites)

Sebelum menginstal, pastikan komputer Anda sudah terpasang:
1. **PHP >= 8.2** (Disarankan PHP 8.3)
2. **Composer** (Dependency Manager untuk PHP)
3. **Node.js & NPM** (Untuk build aset frontend/Tailwind)
4. **Database**: MySQL / MariaDB (Disarankan via XAMPP/Laragon) atau SQLite.
5. **Git** (Untuk clone repository).

---

## 🚀 Cara Instalasi (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di komputer Anda:

### 1. Clone Repository
Buka terminal/CMD, lalu jalankan perintah:
```bash
git clone [https://github.com/USERNAME/system-cuti.git](https://github.com/USERNAME/system-cuti.git)
cd system-cuti
```

### 2. Install Dependency & Generate Key
Install seluruh library PHP yang dibutuhkan dan buat application key:
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Konfigurasi Database
Buat database baru di MySQL (misal: `system_cuti`), lalu sesuaikan pengaturan pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=system_cuti
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi Database & Storage Link
Jalankan migrasi tabel beserta data seeder dan hubungkan folder storage:
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### 5. Build Aset Frontend & Jalankan Server
Compile aset Tailwind CSS dan jalankan server lokal Laravel:
```bash
npm install
npm run build
php artisan serve
```

Aplikasi kini dapat diakses melalui browser di `http://127.0.0.1:8000`.

Semoga proyek ini membantu mempermudah pengelolaan cuti dan memberikan nilai tambah bagi tim Anda.
