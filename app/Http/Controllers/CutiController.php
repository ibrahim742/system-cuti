<?php

namespace App\Http\Controllers;

use App\Jobs\SendFonnteNotification;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CutiController extends Controller
{
    /** Dashboard Karyawan */
    public function dashboardKaryawan()
    {
        $user = auth()->user();

        $cutiBulanIni = $user->cutis()
            ->where('status', 'approved')
            ->whereMonth('tanggal_mulai', now()->month)
            ->whereYear('tanggal_mulai', now()->year)
            ->sum('jumlah_hari');

        $riwayat = $user->cutis()->latest()->paginate(10);

        return view('karyawan.dashboard', compact('user', 'cutiBulanIni', 'riwayat'));
    }

    /** Ajukan Cuti */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai'    => 'required|date|after_or_equal:today',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'           => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $durasi = Carbon::parse($validated['tanggal_mulai'])
            ->diffInDays(Carbon::parse($validated['tanggal_selesai'])) + 1;

        // Aturan 1: cuti habis
        if ($user->sisa_cuti_tahunan <= 0) {
            return back()->withErrors(['msg' => 'Sisa cuti Anda habis. Silakan hubungi BOS.']);
        }

        // Aturan 2: durasi melebihi sisa
        if ($durasi > $user->sisa_cuti_tahunan) {
            return back()->withErrors([
                'msg' => "Durasi cuti ({$durasi} hari) melebihi sisa cuti Anda ({$user->sisa_cuti_tahunan} hari)."
            ]);
        }

        Cuti::create([
            'user_id'         => $user->id,
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari'     => $durasi,
            'alasan'          => $validated['alasan'] ?? null,
        ]);

        // Pesan notifikasi
        $pesan = "📋 Pengajuan cuti baru\n"
            . "Nama: {$user->name}\n"
            . "Tanggal: {$validated['tanggal_mulai']} s/d {$validated['tanggal_selesai']}\n"
            . "Durasi: {$durasi} hari\n"
            . "Status: Menunggu persetujuan";

        // 1. Kirim ke WA karyawan yang mengajukan
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch($user->no_wa, $pesan);
        }

        // 2. Kirim ke grup WA admin
        $adminGroupId = config('services.fonnte.admin_group_id');
        if (!empty($adminGroupId)) {
            SendFonnteNotification::dispatch($adminGroupId, $pesan);
        }

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    /** Dashboard Admin */
    public function dashboardAdmin()
    {
        $pending = Cuti::with('user')->where('status', 'pending')->latest()->get();
        return view('admin.dashboard', compact('pending'));
    }

    /** Approve Cuti */
    public function approve(Cuti $cuti)
    {
        if ($cuti->status !== 'pending') {
            return back()->withErrors(['msg' => 'Cuti ini sudah diproses.']);
        }

        DB::transaction(function () use ($cuti) {
            $user = $cuti->user;

            if ($cuti->jumlah_hari > $user->sisa_cuti_tahunan) {
                throw new \Exception('Sisa cuti karyawan tidak mencukupi.');
            }

            $user->decrement('sisa_cuti_tahunan', $cuti->jumlah_hari);
            $cuti->update(['status' => 'approved']);

            // Pesan persetujuan
            $pesanApprove = "✅ Cuti disetujui\n"
                . "Nama: {$user->name}\n"
                . "Tanggal: {$cuti->tanggal_mulai->format('d/m/Y')} s/d {$cuti->tanggal_selesai->format('d/m/Y')}\n"
                . "Durasi: {$cuti->jumlah_hari} hari\n"
                . "Sisa cuti: {$user->sisa_cuti_tahunan} hari";

            // Kirim ke WA karyawan yang cutinya disetujui
            if (!empty($user->no_wa)) {
                SendFonnteNotification::dispatch($user->no_wa, $pesanApprove);
            }

            // Peringatan jika sisa cuti menipis (ke karyawan + grup admin)
            $user->refresh();
            if ($user->sisa_cuti_tahunan > 0 && $user->sisa_cuti_tahunan <= 2) {
                $pesanPeringatan = "⚠️ Peringatan: sisa cuti {$user->name} tinggal {$user->sisa_cuti_tahunan} hari.";

                if (!empty($user->no_wa)) {
                    SendFonnteNotification::dispatch($user->no_wa, $pesanPeringatan);
                }

                $adminGroupId = config('services.fonnte.admin_group_id');
                if (!empty($adminGroupId)) {
                    SendFonnteNotification::dispatch($adminGroupId, $pesanPeringatan);
                }
            }
        });

        return back()->with('success', 'Cuti disetujui.');
    }

    /** Reject Cuti */
    public function reject(Cuti $cuti)
    {
        $cuti->update(['status' => 'rejected']);

        // Notifikasi penolakan ke karyawan
        $user = $cuti->user;
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch(
                $user->no_wa,
                "❌ Pengajuan cuti ditolak\n"
                    . "Nama: {$user->name}\n"
                    . "Tanggal: {$cuti->tanggal_mulai->format('d/m/Y')} s/d {$cuti->tanggal_selesai->format('d/m/Y')}\n"
                    . "Durasi: {$cuti->jumlah_hari} hari"
            );
        }

        return back()->with('success', 'Cuti ditolak.');
    }
}
