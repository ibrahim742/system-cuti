<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'app_name'             => 'Sistem Cuti',
            'login_title'          => 'Selamat datang kembali',
            'login_subtitle'       => 'Masuk ke akun Anda untuk melanjutkan',
            'login_branding_title' => 'Kelola cuti karyawan dengan mudah dan transparan.',
            'login_branding_desc'  => 'Ajukan, setujui, dan pantau sisa cuti dalam satu platform. Notifikasi otomatis langsung ke WhatsApp.',
            'favicon'              => null,
            'fonnte_token'         => null,
            'fonnte_admin_wa'      => null,
            'fonnte_group_wa'      => null,
            'fonnte_admin_group_id' => null,
            'alert_cuti_habis'     => 'Sisa cuti Anda habis. Silakan hubungi BOS.',
            'alert_melebihi_sisa'  => 'Durasi cuti melebihi sisa cuti Anda.',
            'alert_sisa_menipis'   => 'Sisa cuti Anda tinggal sedikit. Segera hubungi BOS jika ingin mengajukan cuti tambahan.',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
