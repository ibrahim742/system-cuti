@extends('super-admin.layout')

@section('title', 'Tambah Akun')
@section('page-title', 'Tambah Akun')
@section('page-subtitle', 'Buat akun karyawan, admin, atau super admin baru')

@section('content')

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-semibold text-slate-800">Form Tambah Akun</h2>
                <a href="{{ route('super-admin.users.index') }}" class="inline-flex items-center gap-1 text-xs sm:text-sm text-slate-600 hover:text-slate-800 bg-slate-50 sm:bg-transparent px-2.5 py-1.5 sm:p-0 rounded-md sm:rounded-none border sm:border-0 border-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
            </div>
            <form action="{{ route('super-admin.users.store') }}" method="POST" class="p-4 sm:p-6 space-y-4 sm:space-y-5">
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

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">No. WhatsApp</label>
                    <input type="text" name="no_wa" value="{{ old('no_wa') }}" placeholder="6281234567890"
                           class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-1">Format: 628xxx (tanpa + dan tanpa spasi)</p>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Role</label>
                    <select name="role" required
                            class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>

                {{-- Grid menumpuk di HP, berdampingan di Desktop --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Jatah Cuti Tahunan</label>
                        <input type="number" name="jatah_cuti_tahunan" value="{{ old('jatah_cuti_tahunan', 12) }}" required min="0"
                               class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Sisa Cuti Tahunan</label>
                        <input type="number" name="sisa_cuti_tahunan" value="{{ old('sisa_cuti_tahunan', 12) }}" required min="0"
                               class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-2">
                    <a href="{{ route('super-admin.users.index') }}"
                       class="w-full sm:w-auto text-center px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
