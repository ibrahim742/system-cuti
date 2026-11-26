<div align="center">

# 🗓️ System Cuti

**Sistem Manajemen Cuti Karyawan** — Modern, Cepat, dan Mudah Digunakan

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Active-success?style=for-the-badge)]()

</div>

---

## 📖 Tentang Project

**System Cuti** adalah aplikasi berbasis web yang dibangun dengan **Laravel** untuk mempermudah pengelolaan pengajuan dan persetujuan cuti karyawan. Dibuat dengan fokus pada **kemudahan penggunaan**, **kecepatan**, dan **kerapian alur kerja**.

> 💡 Dibuat untuk menyederhanakan proses yang biasanya ribet — dari pengajuan, approval atasan, sampai rekap cuti.

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 🔐 **Autentikasi** | Login multi-role (Admin, Atasan, Karyawan) |
| 📝 **Pengajuan Cuti** | Form pengajuan cuti dengan validasi otomatis |
| ✅ **Approval Workflow** | Persetujuan bertingkat oleh atasan/HRD |
| 📊 **Dashboard** | Statistik cuti & ringkasan data real-time |
| 🗂️ **Riwayat Cuti** | Histori lengkap pengajuan & status |
| 📅 **Kalender Cuti** | Visualisasi jadwal cuti tim |
| 🔔 **Notifikasi** | Pemberitahuan status pengajuan |

---

## 🛠️ Tech Stack

- **Backend:** Laravel 11.x, PHP 8.2+
- **Database:** MySQL / PostgreSQL
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Tools:** Composer, NPM, Vite

---

## 🚀 Instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/system-cuti.git
cd system-cuti

# 2. Install dependency
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env, lalu:
php artisan migrate --seed

# 5. Build asset & jalankan
npm run build
php artisan serve
