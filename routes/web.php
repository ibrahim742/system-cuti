<?php

use App\Http\Controllers\CutiController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman utama → arahkan ke login atau dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->role === 'karyawan' ? 'karyawan.dashboard' : 'admin.dashboard')
        : redirect('/login');
});

// Halaman login sederhana
Route::get('/login', function () {
    return view('auth.login-simple');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route(
            Auth::user()->role === 'karyawan' ? 'karyawan.dashboard' : 'admin.dashboard'
        );
    }

    return back()->withErrors(['email' => 'Email atau password salah.']);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Route yang dilindungi auth
Route::middleware(['auth'])->group(function () {
    Route::middleware('role:karyawan')->group(function () {
        Route::get('/dashboard', [CutiController::class, 'dashboardKaryawan'])->name('karyawan.dashboard');
        Route::post('/cuti/ajukan', [CutiController::class, 'store'])->name('cuti.store');
    });

    Route::middleware('role:admin,super_admin')->group(function () {
        Route::get('/admin/dashboard', [CutiController::class, 'dashboardAdmin'])->name('admin.dashboard');
        Route::post('/cuti/{cuti}/approve', [CutiController::class, 'approve'])->name('cuti.approve');
        Route::post('/cuti/{cuti}/reject', [CutiController::class, 'reject'])->name('cuti.reject');
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('super-admin/users', UserController::class)->names([
            'index' => 'super-admin.users.index',
            'create' => 'super-admin.users.create',
            'store' => 'super-admin.users.store',
            'edit' => 'super-admin.users.edit',
            'update' => 'super-admin.users.update',
            'destroy' => 'super-admin.users.destroy',
        ])->except(['show']);
    });
});
