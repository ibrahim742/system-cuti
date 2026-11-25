<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Cuti;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendFonnteNotification;

class ArsipCutiBulanan extends Command
{
    protected $signature = 'cuti:arsipkan';
    protected $description = 'Arsipkan riwayat cuti bulan lalu ke PDF dan kirim ke WA';

    public function handle()
    {
        $bulanLalu = now()->subMonth()->month;
        $tahunLalu = now()->subMonth()->year;
        $namaBulan = now()->subMonth()->translatedFormat('F Y');

        $users = User::where('role', 'karyawan')->get();

        foreach ($users as $user) {
            $riwayat = Cuti::where('user_id', $user->id)
                ->whereMonth('tanggal_mulai', $bulanLalu)
                ->whereYear('tanggal_mulai', $tahunLalu)
                ->get();

            if ($riwayat->isEmpty()) continue;

            // 1. Buat PDF
            $pdf = Pdf::loadView('pdf.riwayat-cuti', compact('user', 'riwayat', 'bulanLalu', 'tahunLalu', 'namaBulan'));
            $fileName = 'arsip-cuti/' . $user->id . '-' . $tahunLalu . '-' . $bulanLalu . '.pdf';
            
            // Simpan PDF ke storage
            Storage::disk('public')->put($fileName, $pdf->output());
            
            // URL untuk diakses (Pastikan sudah menjalankan php artisan storage:link)
            $pdfUrl = asset('storage/' . $fileName);

            // 2. Simpan path PDF ke database (Update semua cuti di bulan itu)
            Cuti::whereIn('id', $riwayat->pluck('id'))->update(['arsip_pdf' => $fileName]);

            // 3. Kirim Link PDF ke WA Karyawan
            if (!empty($user->no_wa)) {
                $pesan = "📄 *Arsip Cuti Bulan {$namaBulan}*\n\n"
                    . "Halo {$user->name}, berikut adalah link unduhan rekap cuti Anda bulan lalu:\n"
                    . "{$pdfUrl}\n\n"
                    . "Riwayat cuti di dashboard Anda akan dibersihkan untuk bulan baru.";
                SendFonnteNotification::dispatch($user->no_wa, $pesan);
            }
        }

        $this->info('Arsip cuti bulanan selesai diproses.');
    }
}