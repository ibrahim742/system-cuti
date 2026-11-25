<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /** Daftar akun + modal edit */
    public function index(Request $request)
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(10);

        // Ambil user yang akan diedit jika ada ?edit=ID (opsional, untuk fallback)
        $editUser = null;
        if ($request->filled('edit')) {
            $editUser = User::find($request->edit);
        }

        return view('super-admin.users.index', compact('users', 'editUser'));
    }

    /** Form tambah akun (halaman terpisah) */
    public function create()
    {
        return view('super-admin.users.create');
    }

    /** Simpan akun baru */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_wa' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:karyawan,admin,super_admin',
            'jatah_cuti_tahunan' => 'required|integer|min:0',
            'sisa_cuti_tahunan' => 'required|integer|min:0',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    /** (Fallback) Form edit halaman terpisah — tidak dipakai jika modal aktif */
    public function edit(User $user)
    {
        return view('super-admin.users.edit', compact('user'));
    }

    /** Update akun (dipakai oleh modal edit) */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_wa' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:karyawan,admin,super_admin',
            'jatah_cuti_tahunan' => 'required|integer|min:0',
            'sisa_cuti_tahunan' => 'required|integer|min:0',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    /** Hapus akun */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['msg' => 'Anda tidak bisa menghapus akun sendiri.']);
        }

        if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    public function unlock(User $user)
    {
        $user->update([
            'login_attempts' => 0,
            'is_locked' => false,
        ]);

        return back()->with('success', "Akun {$user->name} berhasil dibuka kuncinya.");
    }

    public function resetCutiMassal()
{
    \App\Models\User::where('role', 'karyawan')->update([
        'sisa_cuti_tahunan' => \Illuminate\Support\Facades\DB::raw('jatah_cuti_tahunan'),
        'tahun_cuti_terakhir' => now()->year,
    ]);

    return back()->with('success', 'Sisa cuti seluruh karyawan berhasil di-reset ke jatah awal tahun ini.');
}
}
