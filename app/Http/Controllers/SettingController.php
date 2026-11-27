<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

    /** Export database (MySQL .sql / SQLite .sqlite) */
    public function exportDatabase()
    {
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $path = config('database.connections.sqlite.database') ?: database_path('database.sqlite');

            if (!file_exists($path)) {
                return back()->withErrors(['msg' => 'File database SQLite tidak ditemukan.']);
            }

            return response()->download($path, 'backup-' . now()->format('Ymd-His') . '.sqlite');
        }

        if ($connection === 'mysql') {
            $dbName = config('database.connections.mysql.database');
            $filename = 'backup-' . ($dbName ?: 'mysql') . '-' . now()->format('Ymd-His') . '.sql';

            $sql = "-- Backup Database MySQL: {$dbName}\n";
            $sql .= "-- Tanggal: " . now()->toDateTimeString() . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $sql .= "SET NAMES utf8mb4;\n\n";

            $tables = DB::select('SHOW TABLES');

            foreach ($tables as $tableObj) {
                $table = array_values((array) $tableObj)[0];

                $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
                $createSql = $createResult[0]->{'Create Table'} ?? null;

                if ($createSql) {
                    $sql .= "-- Struktur tabel `{$table}`\n";
                    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= $createSql . ";\n\n";
                }

                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data tabel `{$table}`\n";

                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $columns = array_map(fn($col) => "`{$col}`", array_keys($rowArray));
                        $values = array_map(function ($val) {
                            if (is_null($val)) {
                                return 'NULL';
                            }
                            if (is_bool($val)) {
                                return $val ? '1' : '0';
                            }
                            if (is_int($val) || is_float($val)) {
                                return (string) $val;
                            }
                            return DB::getPdo()->quote((string) $val);
                        }, array_values($rowArray));

                        $sql .= "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }

                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            return response($sql, 200, [
                'Content-Type'        => 'application/sql; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return back()->withErrors(['msg' => 'Koneksi database tidak didukung untuk export otomatis.']);
    }

    /** Import database (SQLite / MySQL .sql) */
    public function importDatabase(Request $request)
    {
        $request->validate([
            'database_file' => 'required|file|max:51200',
        ], [
            'database_file.required' => 'Pilih file database terlebih dahulu.',
            'database_file.max'      => 'Ukuran file maksimal 50 MB.',
        ]);

        $file = $request->file('database_file');
        $ext = strtolower($file->getClientOriginalExtension());
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            if (!in_array($ext, ['sqlite', 'db'])) {
                return back()->withErrors(['msg' => 'File harus berformat .sqlite atau .db']);
            }

            $oldPath = config('database.connections.sqlite.database') ?: database_path('database.sqlite');
            $backupPath = database_path('database-backup-' . now()->format('Ymd-His') . '.sqlite');
            if (file_exists($oldPath)) {
                copy($oldPath, $backupPath);
            }

            $file->move(dirname($oldPath), basename($oldPath));
            Artisan::call('config:clear');

            return back()
                ->with('success', 'Database SQLite berhasil diimpor. Backup lama tersimpan sebagai ' . basename($backupPath))
                ->with('active_section', 'database');
        }

        if ($connection === 'mysql') {
            if ($ext !== 'sql') {
                return back()->withErrors(['msg' => 'Untuk database MySQL, pilih file berformat .sql']);
            }

            $sqlContent = file_get_contents($file->getRealPath());
            if ($sqlContent === false || trim($sqlContent) === '') {
                return back()->withErrors(['msg' => 'File .sql kosong atau tidak dapat dibaca.']);
            }

            DB::unprepared($sqlContent);

            return back()
                ->with('success', 'File backup MySQL (.sql) berhasil diimpor.')
                ->with('active_section', 'database');
        }

        return back()->withErrors(['msg' => 'Koneksi database tidak didukung untuk import.']);
    }

    /** Helper: update key di .env */
    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }

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