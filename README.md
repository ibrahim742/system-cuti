# 📅 Sistem Cuti Karyawan

Sistem Informasi Manajemen Cuti Karyawan berbasis Web yang dibangun menggunakan **Laravel 13** dan **Tailwind CSS**. Aplikasi ini dirancang untuk mempermudah proses pengajuan, persetujuan, dan pemantauan cuti dengan integrasi notifikasi WhatsApp secara otomatis (via Fonnte), pembuatan Arsip PDF, dan pengaturan dinamis langsung dari dashboard Super Admin.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Fonnte](https://img.shields.io/badge/WhatsApp_API-Fonnte-green?style=for-the-badge)

---

## ✨ Fitur Utama

Aplikasi ini memiliki 3 level pengguna (Role-Based Access Control) dengan fitur yang disesuaikan:

### 👨‍💼 1. Karyawan
- **Dashboard Ringkasan**: Melihat sisa cuti tahunan, cuti terpakai bulan ini, dan riwayat pengajuan.
- **Pengajuan Cuti**: Form pengajuan dengan validasi otomatis (maksimal 3 hari/bulan dan sisa cuti tahunan).
- **Pembatalan Cuti**: Karyawan dapat membatalkan pengajuan yang masih berstatus *Pending* (kuota otomatis dikembalikan).
- **Filter Riwayat**: Menampilkan riwayat berdasarkan bulan atau tanggal tertentu.
- **Profil**: Ganti foto profil dan ubah password.
- **Notifikasi WA**: Menerima pesan WhatsApp otomatis saat mengajukan, cuti disetujui, atau ditolak.

### 🧑‍💻 2. Admin / HRD
- **Dashboard Persetujuan**: Melihat daftar pengajuan cuti yang masuk.
- **Indikator Peringatan**: Tanda warna (Kuning/Merah) pada tabel jika karyawan mengajukan cuti melebihi batas bulanan.
- **Approve / Reject**: Tombol persetujuan dan penolakan. Kuota cuti otomatis dikembalikan (refund) jika ditolak.
- **Data Karyawan**: Melihat daftar karyawan yang belum pernah mengambil cuti.
- **Filter Riwayat**: Menyaring riwayat cuti yang sudah disetujui.

### 👑 3. Super Admin
- **Semua fitur Admin**.
- **Kelola Akun (CRUD)**: Tambah, Edit, Hapus akun Karyawan/Admin. Fitur **Buka Kunci Akun** jika karyawan salah password 5x.
- **Reset Cuti Massal**: Mereset sisa cuti seluruh karyawan ke jatah awal tahunan sekaligus.
- **Pengaturan Sistem**: Kustomisasi nama aplikasi, favicon, teks halaman login, dan template pesan peringatan.
- **Integrasi Fonnte**: Mengatur Token WA, Nomor Admin, dan ID Grup langsung dari Dashboard (tanpa edit `.env`).
- **Arsip PDF**: Membuat, menyimpan, dan mengunduh rekap cuti karyawan bulan sebelumnya. Link PDF otomatis dikirim ke WA karyawan.

---

## 🛠️ Persyaratan Sistem (Prerequisites)

Sebelum menginstal, pastikan komputer Anda sudah terpasang:
1. **PHP >= 8.2** (Disarankan PHP 8.3)
2. **Composer**
3. **Node.js & NPM** (Untuk build aset frontend)
4. **Database**: MySQL / MariaDB (Disarankan via XAMPP/Laragon) atau SQLite.
5. **Git**

---

## 🚀 Cara Instalasi (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di komputer Anda:

### 1. Clone Repository
Buka terminal/CMD, lalu jalankan:
```bash
git clone https://github.com/USERNAME/system-cuti.git
cd system-cuti
