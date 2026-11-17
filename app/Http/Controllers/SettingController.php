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
        return view('settings.index');
    }

    //Simpan pengaturan umum, login, alert, fonnte, favicon 
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name'             => 'required|string|max:100',
            'login_title'          => 'required|string|max:150',
            'login_subtitle'       => 'required|string|max:200',
            'login_branding_title' => 'required|string|max:200',
            'login_branding_desc'  => 'required|string|max:300',
            'favicon'              => 'nullable|image|mimes:png,ico,jpg,jpeg|max:512',
            'fonnte_token'         => 'nullable|string|max:255',
            'fonnte_admin_wa'      => 'nullable|string|max:20',
            'fonnte_group_wa'      => 'nullable|string|max:100',
            'fonnte_admin_group_id' => 'nullable|string|max:100',
            'alert_cuti_habis'     => 'required|string|max:255',
            'alert_melebihi_sisa'  => 'required|string|max:255',
            'alert_sisa_menipis'   => 'required|string|max:255',
        ]);

        // Upload favicon
        if ($request->hasFile('favicon')) {
            $old = Setting::get('favicon');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('favicon')->store('favicon', 'public');
            Setting::set('favicon', $path);
        }

        // Simpan semua setting teks
        foreach (
            [
                'app_name',
                'login_title',
                'login_subtitle',
                'login_branding_title',
                'login_branding_desc',
                'fonnte_token',
                'fonnte_admin_wa',
                'fonnte_group_wa',
                'fonnte_admin_group_id',
                'alert_cuti_habis',
                'alert_melebihi_sisa',
                'alert_sisa_menipis',
            ] as $key
        ) {
            Setting::set($key, $validated[$key] ?? null);
        }

        // Update .env Fonnte agar langsung terbaca
        $this->updateEnv([
            'FONNTE_TOKEN'          => $validated['fonnte_token'] ?? '',
            'FONNTE_ADMIN_WA'       => $validated['fonnte_admin_wa'] ?? '',
            'FONNTE_GROUP_WA'       => $validated['fonnte_group_wa'] ?? '',
            'FONNTE_ADMIN_GROUP_ID' => $validated['fonnte_admin_group_id'] ?? '',
        ]);

        Artisan::call('config:clear');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
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

        return back()->with('success', 'Database berhasil diimpor. Backup lama tersimpan sebagai ' . basename($backupPath));
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
