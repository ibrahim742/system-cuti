<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — Sistem Cuti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-3xl mx-auto py-6 sm:py-10 px-4">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Profil Saya</h1>
                <p class="text-xs sm:text-sm text-slate-500">Kelola foto profil dan password Anda</p>
            </div>
            <a href="{{ url()->previous() !== url('/profile') ? url()->previous() : '/' }}"
               class="inline-flex items-center gap-1 sm:gap-2 text-xs sm:text-sm text-slate-600 hover:text-slate-800 bg-white sm:bg-transparent px-3 py-2 sm:p-0 rounded-lg shadow-sm sm:shadow-none border border-slate-200 sm:border-0 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- Preview Foto Besar --}}
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 px-6 py-8 sm:py-10 flex flex-col items-center text-center">
                <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full overflow-hidden bg-white/20 border-4 border-white shadow-lg flex items-center justify-center">
                    @if($user->foto_profile)
                        <img src="{{ Storage::url($user->foto_profile) }}" alt="Foto Profil" class="w-full h-full object-cover">
                    @else
                        <span class="text-white text-3xl sm:text-4xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <h2 class="mt-4 text-lg sm:text-xl font-semibold text-white">{{ $user->name }}</h2>
                <p class="text-blue-100 text-xs sm:text-sm mt-1">{{ $user->email }}</p>
                <span class="mt-3 inline-flex px-3 py-1 rounded-full text-[10px] sm:text-xs font-medium bg-white/20 text-white uppercase tracking-wide">
                    {{ str_replace('_', ' ', $user->role) }}
                </span>
            </div>

            {{-- Form --}}
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-5 sm:space-y-6">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-xs sm:text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Foto --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Foto Profil</label>
                    <input type="file" name="foto_profile" accept="image/*"
                           class="w-full text-xs sm:text-sm text-slate-600 file:mr-3 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded-lg file:border-0 file:text-xs sm:file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition border sm:border-0 border-slate-200 rounded-lg">
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-1.5">Format: jpg, jpeg, png, webp. Maksimal 1 MB.</p>
                </div>

                {{-- Info akun (read-only) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-500 mb-1.5">Nama</label>
                        <input type="text" value="{{ $user->name }}" disabled
                               class="w-full px-3 py-2 sm:py-2.5 border border-slate-200 rounded-lg text-slate-500 bg-slate-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-500 mb-1.5">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full px-3 py-2 sm:py-2.5 border border-slate-200 rounded-lg text-slate-500 bg-slate-50 text-sm">
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-5 sm:pt-6">
                    <h3 class="text-sm sm:text-base font-semibold text-slate-800 mb-1">Ganti Password</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
                            <input type="password" name="password" minlength="6"
                                   class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" minlength="6"
                                   class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-2">
                    <a href="{{ url()->previous() }}"
                       class="w-full sm:w-auto text-center px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>

    @if(session('success'))
    <script>
        Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#2563eb' });
    </script>
    @endif
    @if($errors->any())
    <script>
        Swal.fire({ icon: 'error', title: 'Gagal', text: @json($errors->first()), confirmButtonColor: '#dc2626' });
    </script>
    @endif

    <script>
        document.querySelector('input[name="foto_profile"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const maxSize = 1024 * 1024; // 1 MB dalam bytes
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: `Ukuran foto Anda ${(file.size / 1024 / 1024).toFixed(2)} MB, melebihi batas 1 MB.`,
                    confirmButtonColor: '#dc2626',
                });
                e.target.value = ''; // Reset input
            }
        });
    </script>
</body>
</html>
