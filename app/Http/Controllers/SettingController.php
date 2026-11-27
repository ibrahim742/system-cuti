<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('super-admin.settings.index');
    }

    /** Simpan pengaturan berdasarkan section (umum, fonnte, alert) */
    public function update(Request $request)
    {
        $section = $request->input('section');

        // 1. Bagian Umum & Login
        if ($section === 'umum') {
            $validated = $request->validate([
                'app_name'             => 'required|string|max:100',
                'login_title'          => 'required|string|max:150',
                'login_subtitle'       => 'required|string|max:200',
                'login_branding_title' => 'required|string|max:200',
                'login_branding_desc'  => 'required|string|max:300',
                'favicon'              => 'nullable|image|mimes:png,ico,jpg,jpeg|max:512',
            ]);

            if ($request->hasFile('favicon')) {
                $old = Setting::get('favicon');
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                $path = $request->file('favicon')->store('favicon', 'public');
                Setting::set('favicon', $path);
            }

            foreach ([
                'app_name',
                'login_title',
                'login_subtitle',
                'login_branding_title',
                'login_branding_desc',
            ] as $key) {
                Setting::set($key, $validated[$key] ?? null);
            }

            return back()
                ->with('success', 'Pengaturan Umum & Login berhasil disimpan.')
                ->with('active_section', 'umum');
        }

        // 2. Bagian WhatsApp (Fonnte)
        if ($section === 'fonnte') {
            $validated = $request->validate([
                'fonnte_token'          => 'nullable|string|max:255',
                'fonnte_admin_wa'       => 'nullable|string|max:20',
                'fonnte_group_wa'       => 'nullable|string|max:100',
                'fonnte_admin_group_id' => 'nullable|string|max:100',
            ]);

            foreach ([
                'fonnte_token',
                'fonnte_admin_wa',
                'fonnte_group_wa',
                'fonnte_admin_group_id',
            ] as $key) {
                Setting::set($key, $validated[$key] ?? null);
            }

            $this->updateEnv([
                'FONNTE_TOKEN'          => $validated['fonnte_token'] ?? '',
                'FONNTE_ADMIN_WA'       => $validated['fonnte_admin_wa'] ?? '',
                'FONNTE_GROUP_WA'       => $validated['fonnte_group_wa'] ?? '',
                'FONNTE_ADMIN_GROUP_ID' => $validated['fonnte_admin_group_id'] ?? '',
            ]);

            Artisan::call('config:clear');

            return back()
                ->with('success', 'Pengaturan WhatsApp (Fonnte) berhasil disimpan.')
                ->with('active_section', 'fonnte');
        }

        // 3. Bagian Pesan Alert
        if ($section === 'alert') {
            $alertKeys = [
                'alert_judul_batas_bulanan_pas',
                'alert_judul_gagal_cuti',
                'alert_cuti_habis',
                'alert_melebihi_sisa',
                'alert_sisa_menipis',
                'alert_cuti_berhasil',
                'alert_tanggal_tidak_valid',
                'alert_batas_bulanan_pas',
                'alert_batas_bulanan_lebih',
                'alert_konfirmasi_batal_cuti',
                'alert_cuti_dibatalkan',
                'alert_batal_bukan_pemilik',
                'alert_batal_bukan_pending',
                'alert_cuti_sudah_diproses',
                'alert_cuti_disetujui',
                'alert_cuti_ditolak',
                'alert_login_terkunci',
                'alert_login_salah_sisa',
                'alert_login_gagal',
                'alert_akun_ditambahkan',
                'alert_akun_diperbarui',
                'alert_akun_dihapus',
                'alert_hapus_akun_sendiri',
                'alert_akun_dibuka',
                'alert_reset_cuti_massal',
                'alert_profil_diperbarui',
                'alert_foto_terlalu_besar',
                'alert_foto_bukan_gambar',
                'alert_foto_format_salah',
                'alert_foto_maksimal',
                'alert_foto_gagal_upload',
                'alert_password_minimal',
                'alert_password_konfirmasi',
            ];

            $rules = [];
            foreach ($alertKeys as $key) {
                $rules[$key] = 'nullable|string|max:500';
            }

            $validated = $request->validate($rules);

            foreach ($alertKeys as $key) {
                if ($request->has($key)) {
                    Setting::set($key, $validated[$key] ?? null);
                }
            }

            return back()
                ->with('success', 'Pengaturan Pesan Alert berhasil disimpan.')
                ->with('active_section', 'alert');
        }

        return back()->withErrors(['msg' => 'Bagian pengaturan tidak dikenali.']);
    }

    /** Export database (SQLite) */
    public function exportDatabase()
    {
        $path = database_path('database.sqlite');

        if (!file_exists($path)) {
            return back()->withErrors(['msg' => 'File database tidak ditemukan.']);
        }

        $filename = 'backup-' . now()->format('Ymd-His') . '.sqlite';

        return response()->download($path, $filename);
    }

    /** Import database (SQLite) */
    public function importDatabase(Request $request)
    {
        $request->validate([
            'database_file' => 'required|file|max:51200', // maks 50 MB
        ], [
            'database_file.required' => 'Pilih file database terlebih dahulu.',
            'database_file.max' => 'Ukuran file maksimal 50 MB.',
        ]);

        $file = $request->file('database_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['sqlite', 'db'])) {
            return back()->withErrors(['msg' => 'File harus berformat .sqlite atau .db']);
        }

        // Backup dulu database lama
        $oldPath = database_path('database.sqlite');
        $backupPath = database_path('database-backup-' . now()->format('Ymd-His') . '.sqlite');
        if (file_exists($oldPath)) {
            copy($oldPath, $backupPath);
        }

        // Timpa dengan file baru
        $file->move(database_path(), 'database.sqlite');

        Artisan::call('config:clear');

        return back()
            ->with('success', 'Database berhasil diimpor. Backup lama tersimpan sebagai ' . basename($backupPath))
            ->with('active_section', 'database');
    }

    /** Helper: update key di .env */
    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $value = $value ?? '';
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);
    }
}