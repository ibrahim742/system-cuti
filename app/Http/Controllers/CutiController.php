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

        // HANYA TAMPILKAN RIWAYAT BULAN INI (Data lama otomatis hilang dari layar)
        $query = $user->cutis()
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun);

        if ($tanggal) {
            $query->whereDate('tanggal_mulai', $tanggal);
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

        // Ambil data pending beserta TOTAL HARI cuti user tersebut di bulan yang diajukan
        $pending = Cuti::with('user')->where('status', 'pending')->latest()->get()->map(function ($cuti) {
            $totalHariBulanIni = Cuti::where('user_id', $cuti->user_id)
                ->whereIn('status', ['pending', 'approved'])
                ->whereMonth('tanggal_mulai', $cuti->tanggal_mulai->month)
                ->whereYear('tanggal_mulai', $cuti->tanggal_mulai->year)
                ->sum('jumlah_hari');

            $cuti->total_hari_bulan_ini = $totalHariBulanIni;
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
        
        // FITUR BARU: Ambil Data Arsip PDF
        $arsipCuti = Cuti::with('user')
            ->whereNotNull('arsip_pdf')
            ->latest()
            ->take(10)
            ->get();

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
            'arsipCuti', // Tambahan fitur PDF
            'bulan',
            'tahun'
        ));
    }

        /** Halaman Arsip Cuti Karyawan (Khusus Super Admin / Admin) */
    public function arsipKaryawan(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $arsip = Cuti::with('user')
            ->whereNotNull('arsip_pdf')
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.arsip.index', compact('arsip', 'bulan', 'tahun'));
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
        $mulai = Carbon::parse($validated['tanggal_mulai']);
        $selesai = Carbon::parse($validated['tanggal_selesai']);
        $durasi = $mulai->diffInDays($selesai) + 1;

        // Aturan 1: cuti tahunan habis
        if ($user->sisa_cuti_tahunan <= 0) {
            $pesanHabis = Setting::get('alert_cuti_habis') ?? 'Sisa cuti Anda habis. Silakan hubungi BOS.';
            return back()->withErrors(['msg' => $pesanHabis]);
        }

        // Aturan 2: durasi melebihi sisa cuti tahunan
        if ($durasi > $user->sisa_cuti_tahunan) {
            $pesanMelebihi = Setting::get('alert_melebihi_sisa') ?? 'Durasi cuti melebihi sisa cuti Anda.';
            return back()->withErrors([
                'msg' => "{$pesanMelebihi} (Durasi: {$durasi} hari, sisa: {$user->sisa_cuti_tahunan} hari)"
            ]);
        }

        // Hitung TOTAL HARI cuti (pending + approved) di BULAN yang sama
        $totalHariBulanIni = Cuti::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereMonth('tanggal_mulai', $mulai->month)
            ->whereYear('tanggal_mulai', $mulai->year)
            ->sum('jumlah_hari');

        $totalHariSetelahPengajuan = $totalHariBulanIni + $durasi;

        // Simpan Pengajuan
        Cuti::create([
            'user_id'         => $user->id,
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari'     => $durasi,
            'alasan'          => $validated['alasan'] ?? null,
        ]);

        // POTONG KUOTA TAHUNAN LANGSUNG SAAT PENGAJUAN
        $user->decrement('sisa_cuti_tahunan', $durasi);

        // --- SUSUN PESAN NOTIFIKASI WA ---
        $sisaSetelahPengajuan = $user->sisa_cuti_tahunan;

        $pesan = "📋 Pengajuan cuti baru\n"
            . "Nama: {$user->name}\n"
            . "Tanggal: {$validated['tanggal_mulai']} s/d {$validated['tanggal_selesai']}\n"
            . "Durasi: {$durasi} hari\n"
            . "Status: Menunggu persetujuan\n\n"
            . "📊 Informasi Cuti:\n"
            . "- Total cuti bulan ini: {$totalHariSetelahPengajuan} hari (Maks 3)\n"
            . "- Sisa cuti tahunan: {$sisaSetelahPengajuan} hari\n";

        // Logika Peringatan Berdasarkan Total Hari
        $statusPeringatan = '';
        if ($totalHariSetelahPengajuan > 3) {
            $pesan .= "\n🚫 PERINGATAN KERAS: Pengajuan ini membuat total cuti bulan ini menjadi {$totalHariSetelahPengajuan} hari! Karyawan telah MELEBIHI batas maksimal (3 hari per bulan).";
            $statusPeringatan = 'danger';
        } elseif ($totalHariSetelahPengajuan == 3) {
            $pesan .= "\n⚠️ PERINGATAN: Pengajuan ini membuat total cuti bulan ini menjadi 3 hari. Anda telah mencapai batas maksimal bulan ini.";
            $statusPeringatan = 'warning';
        }

        // Peringatan sisa cuti tahunan
        if ($sisaSetelahPengajuan <= 0) {
            $pesan .= "\n🚫 PERINGATAN: Sisa cuti tahunan Anda sudah HABIS.";
        } elseif ($sisaSetelahPengajuan <= 2) {
            $pesan .= "\n⚠️ PERINGATAN: Sisa cuti tahunan Anda tinggal {$sisaSetelahPengajuan} hari.";
        }

        // 1. Kirim ke Karyawan
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch($user->no_wa, $pesan);
        }

        // 2. Kirim ke Admin
        $adminWa = Setting::get('fonnte_admin_wa') ?? config('services.fonnte.admin_wa');
        if (!empty($adminWa)) {
            SendFonnteNotification::dispatch($adminWa, $pesan);
        }

        // 3. Kirim ke Grup
        $groupWa = Setting::get('fonnte_group_wa')
            ?? config('services.fonnte.group_wa')
            ?? Setting::get('fonnte_admin_group_id')
            ?? config('services.fonnte.admin_group_id');
        if (!empty($groupWa)) {
            SendFonnteNotification::dispatch($groupWa, $pesan);
        }

        // Tampilkan peringatan di sistem (SweetAlert)
        if ($statusPeringatan === 'danger') {
            return back()->withErrors(['msg' => "PERINGATAN KERAS: Total cuti Anda bulan ini menjadi {$totalHariSetelahPengajuan} hari. Anda sudah melebihi batas maksimal 3 hari per bulan!"]);
        } elseif ($statusPeringatan === 'warning') {
            return back()->with('warning', "PERINGATAN: Total cuti Anda bulan ini tepat 3 hari. Anda sudah mencapai batas maksimal bulan ini.");
        }

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    /** Batalkan Cuti (Oleh Karyawan) */
    public function cancel(Cuti $cuti)
    {
        // Pastikan yang membatalkan adalah pemilik cuti tersebut
        if ($cuti->user_id !== auth()->id()) {
            return back()->withErrors(['msg' => 'Anda tidak berhak membatalkan cuti ini.']);
        }

        // Hanya cuti dengan status pending yang bisa dibatalkan
        if ($cuti->status !== 'pending') {
            return back()->withErrors(['msg' => 'Hanya cuti yang belum disetujui yang dapat dibatalkan.']);
        }

        $user = auth()->user();

        // KEMBALIKAN KUOTA (REFUND) KARENA DIBATALKAN
        $user->increment('sisa_cuti_tahunan', $cuti->jumlah_hari);

        // Ubah status menjadi rejected atau batalkan (kita gunakan rejected agar terhitung tidak aktif)
        $cuti->update(['status' => 'cancelled']); 

        // --- SUSUN PESAN NOTIFIKASI WA ---
        $pesanCancel = "🚫 Pembatalan Cuti\n"
            . "Nama: {$user->name}\n"
            . "Tanggal: {$cuti->tanggal_mulai->format('d/m/Y')} s/d {$cuti->tanggal_selesai->format('d/m/Y')}\n"
            . "Durasi: {$cuti->jumlah_hari} hari\n"
            . "Status: Dibatalkan oleh Karyawan\n"
            . "Sisa cuti dikembalikan menjadi: {$user->sisa_cuti_tahunan} hari";

        // 1. Ke Karyawan
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch($user->no_wa, $pesanCancel);
        }

        // 2. Ke Admin
        $adminWa = Setting::get('fonnte_admin_wa') ?? config('services.fonnte.admin_wa');
        if (!empty($adminWa)) {
            SendFonnteNotification::dispatch($adminWa, $pesanCancel);
        }

        // 3. Ke Grup
        $groupWa = Setting::get('fonnte_group_wa')
            ?? config('services.fonnte.group_wa')
            ?? Setting::get('fonnte_admin_group_id')
            ?? config('services.fonnte.admin_group_id');
        if (!empty($groupWa)) {
            SendFonnteNotification::dispatch($groupWa, $pesanCancel);
        }

        return back()->with('success', 'Pengajuan cuti berhasil dibatalkan. Sisa cuti Anda telah dikembalikan.');
    }

    /** Approve Cuti */
    public function approve(Cuti $cuti)
    {
        if ($cuti->status !== 'pending') {
            return back()->withErrors(['msg' => 'Cuti ini sudah diproses.']);
        }

        // HANYA UBAH STATUS (Kuota sudah dipotong saat pengajuan)
        $cuti->update(['status' => 'approved']);
        
        $user = $cuti->user;

        // --- SUSUN PESAN NOTIFIKASI WA ---
        $pesanApprove = "✅ Cuti disetujui\n"
            . "Nama: {$user->name}\n"
            . "Tanggal: {$cuti->tanggal_mulai->format('d/m/Y')} s/d {$cuti->tanggal_selesai->format('d/m/Y')}\n"
            . "Durasi: {$cuti->jumlah_hari} hari\n"
            . "Sisa cuti tersisa: {$user->sisa_cuti_tahunan} hari";

        if ($user->sisa_cuti_tahunan <= 0) {
            $pesanApprove .= "\n\n🚫 PERINGATAN: Sisa cuti {$user->name} sudah HABIS. Silakan hubungi BOS.";
        } elseif ($user->sisa_cuti_tahunan <= 2) {
            $pesanApprove .= "\n\n⚠️ PERINGATAN: Sisa cuti {$user->name} tinggal {$user->sisa_cuti_tahunan} hari.";
        }

        // 1. Ke Karyawan
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch($user->no_wa, $pesanApprove);
        }

        // 2. Ke Admin
        $adminWa = Setting::get('fonnte_admin_wa') ?? config('services.fonnte.admin_wa');
        if (!empty($adminWa)) {
            SendFonnteNotification::dispatch($adminWa, $pesanApprove);
        }

        // 3. Ke Grup
        $groupWa = Setting::get('fonnte_group_wa')
            ?? config('services.fonnte.group_wa')
            ?? Setting::get('fonnte_admin_group_id')
            ?? config('services.fonnte.admin_group_id');
        if (!empty($groupWa)) {
            SendFonnteNotification::dispatch($groupWa, $pesanApprove);
        }

        return back()->with('success', 'Cuti disetujui.');
    }

    /** Reject Cuti */
    public function reject(Cuti $cuti)
    {
        if ($cuti->status !== 'pending') {
            return back()->withErrors(['msg' => 'Cuti ini sudah diproses.']);
        }

        $user = $cuti->user;

        // KEMBALIKAN KUOTA (REFUND) KARENA DITOLAK
        $user->increment('sisa_cuti_tahunan', $cuti->jumlah_hari);

        // Ubah status
        $cuti->update(['status' => 'rejected']);

        // --- SUSUN PESAN NOTIFIKASI WA ---
        $pesanReject = "❌ Pengajuan cuti ditolak\n"
            . "Nama: {$user->name}\n"
            . "Tanggal: {$cuti->tanggal_mulai->format('d/m/Y')} s/d {$cuti->tanggal_selesai->format('d/m/Y')}\n"
            . "Durasi: {$cuti->jumlah_hari} hari\n"
            . "Sisa cuti Anda dikembalikan menjadi: {$user->sisa_cuti_tahunan} hari";

        // 1. Ke Karyawan
        if (!empty($user->no_wa)) {
            SendFonnteNotification::dispatch($user->no_wa, $pesanReject);
        }

        // 2. Ke Admin
        $adminWa = Setting::get('fonnte_admin_wa') ?? config('services.fonnte.admin_wa');
        if (!empty($adminWa)) {
            SendFonnteNotification::dispatch($adminWa, $pesanReject);
        }

        // 3. Ke Grup
        $groupWa = Setting::get('fonnte_group_wa')
            ?? config('services.fonnte.group_wa')
            ?? Setting::get('fonnte_admin_group_id')
            ?? config('services.fonnte.admin_group_id');
        if (!empty($groupWa)) {
            SendFonnteNotification::dispatch($groupWa, $pesanReject);
        }

        return back()->with('success', 'Cuti ditolak. Sisa cuti karyawan telah dikembalikan.');
    }
}