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
    public function dashboardKaryawan(Request $request)
    {
        $user = auth()->user();

        // Filter bulan (default: bulan ini)
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Filter tanggal (opsional)
        $tanggal = $request->input('tanggal');

        // Query dasar riwayat
        $query = $user->cutis();

        if ($tanggal) {
            // Jika filter tanggal spesifik dipilih
            $query->whereDate('tanggal_mulai', $tanggal);
        } else {
            // Jika filter bulanan
            $query->whereMonth('tanggal_mulai', $bulan)
                ->whereYear('tanggal_mulai', $tahun);
        }

        $riwayat = $query->latest()->paginate(10)->withQueryString();

        // Total cuti bulan ini (khusus untuk kartu statistik)
        $cutiBulanIni = $user->cutis()
            ->where('status', 'approved')
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun)
            ->sum('jumlah_hari');

        return view('karyawan.dashboard', compact('user', 'cutiBulanIni', 'riwayat', 'bulan', 'tahun', 'tanggal'));
    }

    /** Dashboard Admin */
    public function dashboardAdmin(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $tanggal = $request->input('tanggal');

        // Ambil data pending beserta jumlah cuti user tersebut di bulan yang diajukan
        $pending = Cuti::with('user')->where('status', 'pending')->latest()->get()->map(function ($cuti) {
            // Hitung total cuti (pending + approved) milik user ini di bulan cuti tersebut
            $cutiBulanIni = Cuti::where('user_id', $cuti->user_id)
                ->whereIn('status', ['pending', 'approved'])
                ->whereMonth('tanggal_mulai', $cuti->tanggal_mulai->month)
                ->whereYear('tanggal_mulai', $cuti->tanggal_mulai->year)
                ->count();

            $cuti->total_cuti_bulan_ini = $cutiBulanIni;
            return $cuti;
        });

        // Filter untuk Total Cuti Disetujui Bulan Ini
        $cutiBulanIni = Cuti::where('status', 'approved')
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun)
            ->sum('jumlah_hari');

        $belumCuti = \App\Models\User::where('role', 'karyawan')
            ->whereDoesntHave('cutis', function ($query) {
                $query->where('status', 'approved');
            })
            ->get();

        // Riwayat keseluruhan untuk filter
        $queryRiwayat = Cuti::with('user')->where('status', 'approved');

        if ($tanggal) {
            $queryRiwayat->whereDate('tanggal_mulai', $tanggal);
        } else {
            $queryRiwayat->whereMonth('tanggal_mulai', $bulan)->whereYear('tanggal_mulai', $tahun);
        }

        $riwayatDisetujui = $queryRiwayat->latest()->paginate(10)->withQueryString();

        return view('admin.dashboard', compact(
            'pending',
            'cutiBulanIni',
            'belumCuti',
            'riwayatDisetujui',
            'bulan',
            'tahun',
            'tanggal'
        ));
    }

    /** Dashboard Super Admin */
    public function dashboardSuperAdmin(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $totalUsers = \App\Models\User::count();
        $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count();
        $totalAdmin = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->count();

        $pending = Cuti::where('status', 'pending')->count();
        $approved = Cuti::where('status', 'approved')->count();
        $rejected = Cuti::where('status', 'rejected')->count();

        // Filter Total Cuti Bulan Ini
        $cutiBulanIni = Cuti::where('status', 'approved')
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun)
            ->sum('jumlah_hari');

        $belumCutiCount = \App\Models\User::where('role', 'karyawan')
            ->whereDoesntHave('cutis', function ($query) {
                $query->where('status', 'approved');
            })
            ->count();

        $cutiTerbaru = Cuti::with('user')->latest()->take(5)->get();

        return view('super-admin.dashboard', compact(
            'totalUsers',
            'totalKaryawan',
            'totalAdmin',
            'pending',
            'approved',
            'rejected',
            'cutiBulanIni',
            'belumCutiCount',
            'cutiTerbaru',
            'bulan',
            'tahun'
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

        // Hitung jumlah pengajuan cuti karyawan ini di bulan yang sama
        $cutiBulanIni = Cuti::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereMonth('tanggal_mulai', Carbon::parse($validated['tanggal_mulai'])->month)
            ->whereYear('tanggal_mulai', Carbon::parse($validated['tanggal_mulai'])->year)
            ->count();

        // Simpan Pengajuan
        Cuti::create([
            'user_id'         => $user->id,
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari'     => $durasi,
            'alasan'          => $validated['alasan'] ?? null,
        ]);

        // --- Susun Pesan Notifikasi WA ---
        $sisaSetelahPengajuan = $user->sisa_cuti_tahunan - $durasi;

        $pesan = "📋 Pengajuan cuti baru\n"
            . "Nama: {$user->name}\n"
            . "Tanggal: {$validated['tanggal_mulai']} s/d {$validated['tanggal_selesai']}\n"
            . "Durasi: {$durasi} hari\n"
            . "Sisa cuti saat ini: {$user->sisa_cuti_tahunan} hari\n"
            . "Status: Menunggu persetujuan";

        // Validasi Notif: Peringatan Batas Bulanan (Cuti ke-2 / lebih)
        if ($cutiBulanIni == 1) { // Ini adalah cuti ke-2 bulan ini
            $pesan .= "\n\n⚠️ PERINGATAN BULANAN: Ini adalah pengajuan cuti ke-2 {$user->name} bulan ini. Maksimal hanya 3 kali cuti per bulan.";
        } elseif ($cutiBulanIni >= 2) { // Ini adalah cuti ke-3 atau lebih
            $pesan .= "\n\n🚫 PERINGATAN KERAS: Pengajuan cuti ini adalah yang ke-" . ($cutiBulanIni + 1) . " kali bulan ini! Karyawan telah MELEBIHI batas maksimal (3x). Harap tinjau dengan ketat.";
        }

        // Peringatan sisa cuti tahunan
        if ($sisaSetelahPengajuan <= 0) {
            $pesan .= "\n\n🚫 PERINGATAN: Setelah cuti ini disetujui, sisa cuti {$user->name} akan HABIS. Silakan hubungi BOS.";
        } elseif ($sisaSetelahPengajuan <= 2) {
            $pesan .= "\n\n⚠️ PERINGATAN: Setelah cuti ini disetujui, sisa cuti {$user->name} tinggal {$sisaSetelahPengajuan} hari.";
        }

        // Kirim WA ke Karyawan
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch($user->no_wa, $pesan);
        }

        // Kirim WA ke Admin
        $adminWa = Setting::get('fonnte_admin_wa') ?? config('services.fonnte.admin_wa');
        if (!empty($adminWa)) {
            SendFonnteNotification::dispatch($adminWa, $pesan);
        }

        // Kirim WA ke Grup
        $groupWa = Setting::get('fonnte_group_wa')
            ?? config('services.fonnte.group_wa')
            ?? Setting::get('fonnte_admin_group_id')
            ?? config('services.fonnte.admin_group_id');
        if (!empty($groupWa)) {
            SendFonnteNotification::dispatch($groupWa, $pesan);
        }

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
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
