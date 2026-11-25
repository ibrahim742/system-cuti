<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoResetCuti
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $tahunSekarang = now()->year;
            $tahunTerakhir = $user->tahun_cuti_terakhir ?? $tahunSekarang;

            // Cek jika role adalah karyawan DAN tahun terakhir reset bukan tahun ini
            if ($user->role === 'karyawan' && $tahunTerakhir < $tahunSekarang) {
                $user->update([
                    'sisa_cuti_tahunan'   => $user->jatah_cuti_tahunan,
                    'tahun_cuti_terakhir' => $tahunSekarang,
                ]);
            }
        }

        return $next($request);
    }
}