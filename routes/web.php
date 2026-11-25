<?php

use App\Http\Controllers\CutiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (Tanpa Login)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }
    return auth()->user()->role === 'karyawan'
        ? redirect()->route('karyawan.dashboard')
        : redirect()->route('admin.dashboard');
});

Route::get('/login', function () {
    return view('auth.login-simple');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    // Cek jika akun dikunci
    if ($user && $user->is_locked) {
        return back()->withErrors(['email' => 'Akun Anda telah dikunci karena salah password 5 kali. Silakan hubungi Super Admin untuk membuka kunci.']);
    }

    if (Auth::attempt($credentials)) {
        // Jika berhasil login, reset hitungan salah password
        if ($user) {
            $user->update(['login_attempts' => 0]);
        }

        $request->session()->regenerate();
        return redirect()->route(
            Auth::user()->role === 'karyawan' ? 'karyawan.dashboard' : 'admin.dashboard'
        );
    }

    // Jika GAGAL login
    if ($user) {
        $user->increment('login_attempts');
        $sisaPercobaan = 5 - $user->login_attempts;

        if ($user->login_attempts >= 5) {
            $user->update(['is_locked' => true]);
            return back()->withErrors(['email' => 'Akun Anda telah dikunci karena salah password 5 kali. Silakan hubungi Super Admin.']);
        }

        return back()->withErrors(['email' => "Email atau password salah. Sisa percobaan: {$sisaPercobaan}x."]);
    }

    return back()->withErrors(['email' => 'Email atau password salah.']);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


/*
|--------------------------------------------------------------------------
| Rute Terlindungi (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- Rute Karyawan ---
    Route::middleware('role:karyawan')->group(function () {
        Route::get('/dashboard', [CutiController::class, 'dashboardKaryawan'])->name('karyawan.dashboard');
        Route::post('/cuti/ajukan', [CutiController::class, 'store'])->name('cuti.store');
    });

    // --- Rute Admin & Super Admin ---
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::get('/admin/dashboard', [CutiController::class, 'dashboardAdmin'])->name('admin.dashboard');
        Route::post('/cuti/{cuti}/approve', [CutiController::class, 'approve'])->name('cuti.approve');
        Route::post('/cuti/{cuti}/reject', [CutiController::class, 'reject'])->name('cuti.reject');
    });

    // --- Rute Khusus Super Admin ---
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/super-admin/dashboard', [CutiController::class, 'dashboardSuperAdmin'])->name('super-admin.dashboard');

        // User Management
        Route::resource('super-admin/users', UserController::class)->names([
            'index' => 'super-admin.users.index',
            'create' => 'super-admin.users.create',
            'store' => 'super-admin.users.store',
            'edit' => 'super-admin.users.edit',
            'update' => 'super-admin.users.update',
            'destroy' => 'super-admin.users.destroy',
        ])->except(['show']);

        // Buka Kunci Akun
        Route::post('/super-admin/users/{user}/unlock', [UserController::class, 'unlock'])->name('super-admin.users.unlock');

        // Pengaturan Sistem (Branding, Fonnte, Database)
        Route::get('/super-admin/settings', [SettingController::class, 'index'])->name('super-admin.settings.index');
        Route::post('/super-admin/settings', [SettingController::class, 'update'])->name('super-admin.settings.update');
        Route::get('/super-admin/settings/export-db', [SettingController::class, 'exportDatabase'])->name('super-admin.settings.export-db');
        Route::post('/super-admin/settings/import-db', [SettingController::class, 'importDatabase'])->name('super-admin.settings.import-db');
    });

    // --- Rute Profil (Semua Role) ---
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::post('/super-admin/reset-cuti-massal', [\App\Http\Controllers\UserController::class, 'resetCutiMassal'])->name('super-admin.reset-cuti');

        Route::post('/cuti/{cuti}/cancel', [CutiController::class, 'cancel'])->name('cuti.cancel');

                Route::get('/super-admin/arsip', [CutiController::class, 'arsipKaryawan'])->name('super-admin.arsip.index');

    });
