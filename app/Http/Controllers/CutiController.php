<?php

namespace App\Http\Controllers;

use App\Jobs\SendFonnteNotification;
use App\Models\Cuti;
use App\Models\Setting;
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

    /** Dashboard Super Admin */
    public function dashboardSuperAdmin()
    {
        $totalUsers = \App\Models\User::count();
        $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count();
        $totalAdmin = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->count();
        $pending = Cuti::where('status', 'pending')->count();
        $approved = Cuti::where('status', 'approved')->count();
        $rejected = Cuti::where('status', 'rejected')->count();

        $cutiTerbaru = Cuti::with('user')->latest()->take(5)->get();

        return view('super-admin.dashboard', compact(
            'totalUsers',
            'totalKaryawan',
            'totalAdmin',
            'pending',
            'approved',
            'rejected',
            'cutiTerbaru'
        ));
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
            $pesanHabis = Setting::get('alert_cuti_habis') ?? 'Sisa cuti Anda habis. Silakan hubungi BOS.';
            return back()->withErrors(['msg' => $pesanHabis]);
        }

        // Aturan 2: durasi melebihi sisa
        if ($durasi > $user->sisa_cuti_tahunan) {
            $pesanMelebihi = Setting::get('alert_melebihi_sisa') ?? 'Durasi cuti melebihi sisa cuti Anda.';
            return back()->withErrors([
                'msg' => "{$pesanMelebihi} (Durasi: {$durasi} hari, sisa: {$user->sisa_cuti_tahunan} hari)"
            ]);
        }

        Cuti::create([
            'user_id'         => $user->id,
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari'     => $durasi,
            'alasan'          => $validated['alasan'] ?? null,
        ]);

        // Hitung sisa setelah pengajuan ini (estimasi, belum dipotong)
        $sisaSetelahPengajuan = $user->sisa_cuti_tahunan - $durasi;

        // Susun pesan
        $pesan = "📋 Pengajuan cuti baru\n"
            . "Nama: {$user->name}\n"
            . "Tanggal: {$validated['tanggal_mulai']} s/d {$validated['tanggal_selesai']}\n"
            . "Durasi: {$durasi} hari\n"
            . "Sisa cuti saat ini: {$user->sisa_cuti_tahunan} hari\n"
            . "Status: Menunggu persetujuan";

        // Tambahkan warning jika sisa cuti menipis atau habis setelah pengajuan
        if ($sisaSetelahPengajuan <= 0) {
            $pesan .= "\n\n🚫 PERINGATAN: Setelah cuti ini disetujui, sisa cuti {$user->name} akan HABIS. Silakan hubungi BOS.";
        } elseif ($sisaSetelahPengajuan <= 2) {
            $pesan .= "\n\n⚠️ PERINGATAN: Setelah cuti ini disetujui, sisa cuti {$user->name} tinggal {$sisaSetelahPengajuan} hari.";
        }

        // 1. Kirim ke karyawan
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch($user->no_wa, $pesan);
        }

        // 2. Kirim ke admin
        $adminWa = Setting::get('fonnte_admin_wa') ?? config('services.fonnte.admin_wa');
        if (!empty($adminWa)) {
            SendFonnteNotification::dispatch($adminWa, $pesan);
        }

        // 3. Kirim ke grup
        $groupWa = Setting::get('fonnte_group_wa')
            ?? config('services.fonnte.group_wa')
            ?? Setting::get('fonnte_admin_group_id')
            ?? config('services.fonnte.admin_group_id');
        if (!empty($groupWa)) {
            SendFonnteNotification::dispatch($groupWa, $pesan);
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
                . "Sisa cuti tersisa: {$user->sisa_cuti_tahunan} hari";

            // Tambahkan warning sesuai kondisi sisa cuti
            if ($user->sisa_cuti_tahunan <= 0) {
                $pesanApprove .= "\n\n🚫 PERINGATAN: Sisa cuti {$user->name} sudah HABIS. Tidak bisa mengajukan cuti lagi. Silakan hubungi BOS.";
            } elseif ($user->sisa_cuti_tahunan <= 2) {
                $pesanApprove .= "\n\n⚠️ PERINGATAN: Sisa cuti {$user->name} tinggal {$user->sisa_cuti_tahunan} hari. Segera hubungi BOS jika ingin mengajukan cuti tambahan.";
            }

            // 1. Ke karyawan
            if (!empty($user->no_wa)) {
                SendFonnteNotification::dispatch($user->no_wa, $pesanApprove);
            }

            // 2. Ke admin
            $adminWa = Setting::get('fonnte_admin_wa') ?? config('services.fonnte.admin_wa');
            if (!empty($adminWa)) {
                SendFonnteNotification::dispatch($adminWa, $pesanApprove);
            }

            // 3. Ke grup
            $groupWa = Setting::get('fonnte_group_wa')
                ?? config('services.fonnte.group_wa')
                ?? Setting::get('fonnte_admin_group_id')
                ?? config('services.fonnte.admin_group_id');
            if (!empty($groupWa)) {
                SendFonnteNotification::dispatch($groupWa, $pesanApprove);
            }
        });

        return back()->with('success', 'Cuti disetujui.');
    }

    /** Reject Cuti */
    public function reject(Cuti $cuti)
    {
        $cuti->update(['status' => 'rejected']);

        $user = $cuti->user;

        // Pesan penolakan (sertakan sisa cuti agar karyawan tahu posisinya)
        $pesanReject = "❌ Pengajuan cuti ditolak\n"
            . "Nama: {$user->name}\n"
            . "Tanggal: {$cuti->tanggal_mulai->format('d/m/Y')} s/d {$cuti->tanggal_selesai->format('d/m/Y')}\n"
            . "Durasi: {$cuti->jumlah_hari} hari\n"
            . "Sisa cuti Anda tetap: {$user->sisa_cuti_tahunan} hari";

        // 1. Ke karyawan
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch($user->no_wa, $pesanReject);
        }

        // 2. Ke admin
        $adminWa = Setting::get('fonnte_admin_wa') ?? config('services.fonnte.admin_wa');
        if (!empty($adminWa)) {
            SendFonnteNotification::dispatch($adminWa, $pesanReject);
        }

        // 3. Ke grup
        $groupWa = Setting::get('fonnte_group_wa')
            ?? config('services.fonnte.group_wa')
            ?? Setting::get('fonnte_admin_group_id')
            ?? config('services.fonnte.admin_group_id');
        if (!empty($groupWa)) {
            SendFonnteNotification::dispatch($groupWa, $pesanReject);
        }

        return back()->with('success', 'Cuti ditolak.');
    }
}
