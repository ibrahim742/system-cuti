<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024', // 1024 KB = 1 MB
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'foto_profile.image' => 'File harus berupa gambar.',
            'foto_profile.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'foto_profile.max' => 'Ukuran foto maksimal 1 MB.',
            'foto_profile.uploaded' => 'Gagal upload foto. Pastikan ukuran tidak melebihi 1 MB.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        // Upload foto
        if ($request->hasFile('foto_profile')) {
            // Hapus foto lama jika ada
            if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }

            $path = $request->file('foto_profile')->store('profile-photos', 'public');
            $user->foto_profile = $path;
        }

        // Ganti password
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    
}
