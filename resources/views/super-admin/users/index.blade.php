@extends('super-admin.layout')

@section('title', 'Kelola Akun')
@section('page-title', 'Kelola Akun')
@section('page-subtitle', 'Tambah, edit, dan hapus akun karyawan & admin')

@section('content')

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200 flex items-center justify-between gap-2">
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-slate-800">Daftar Akun</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $users->total() }} akun terdaftar</p>
            </div>
            <button type="button" onclick="openCreateModal()"
                    class="inline-flex items-center gap-1.5 sm:gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium px-3 sm:px-4 py-2 rounded-lg transition shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="sm:hidden">+ Tambah</span>
                <span class="hidden sm:inline">Tambah Akun</span>
            </button>
        </div>

        {{-- Area Tabel dengan Scroll Horizontal di HP --}}
        <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-xs sm:text-sm text-left min-w-[800px]">
                <thead class="bg-slate-50 text-slate-600 text-[10px] sm:text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Nama</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Email</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">No. WA</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Role</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Sisa Cuti</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold">Status</th>
                        <th class="px-4 sm:px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-slate-100 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-semibold text-slate-600 shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800 truncate max-w-[120px] sm:max-w-none">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-600">{{ $u->email }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-600 whitespace-nowrap">{{ $u->no_wa ?? '—' }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                @if($u->role === 'super_admin')
                                    <span class="inline-flex px-2 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium bg-purple-50 text-purple-700">Super Admin</span>
                                @elseif($u->role === 'admin')
                                    <span class="inline-flex px-2 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium bg-blue-50 text-blue-700">Admin</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium bg-slate-100 text-slate-700">Karyawan</span>
                                @endif
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-slate-600 whitespace-nowrap">{{ $u->sisa_cuti_tahunan }} / {{ $u->jatah_cuti_tahunan }} hari</td>

                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                @if($u->is_locked)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium bg-red-100 text-red-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Terkunci
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex items-center justify-end gap-1 sm:gap-2">
                                    @if($u->is_locked)
                                        {{-- TOMBOL BUKA KUNCI --}}
                                        <form action="{{ route('super-admin.users.unlock', $u) }}" method="POST" class="inline" onsubmit="return confirm('Buka kunci akun {{ $u->name }}?')">
                                            @csrf
                                            <button class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-medium text-amber-600 hover:text-amber-700 px-1.5 sm:px-2 py-1 rounded hover:bg-amber-50 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                Buka
                                            </button>
                                        </form>
                                    @else
                                        {{-- TOMBOL EDIT & HAPUS --}}
                                        <button type="button"
                                                onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->no_wa ?? '' }}', '{{ $u->role }}', {{ $u->jatah_cuti_tahunan }}, {{ $u->sisa_cuti_tahunan }})"
                                                class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-medium text-blue-600 hover:text-blue-700 px-1.5 sm:px-2 py-1 rounded hover:bg-blue-50 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('super-admin.users.destroy', $u) }}" method="POST"
                                                  onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-medium text-red-600 hover:text-red-700 px-1.5 sm:px-2 py-1 rounded hover:bg-red-50 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada akun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-slate-200">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- ================= MODAL TAMBAH AKUN ================= --}}
    <div id="createModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 shrink-0">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-800">Tambah Akun</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Buat akun karyawan, admin, atau super admin baru</p>
                </div>
                <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('super-admin.users.store') }}" method="POST" class="p-5 sm:p-6 space-y-4 overflow-y-auto custom-scroll">
                @csrf

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

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-2 shrink-0">
                    <button type="button" onclick="closeCreateModal()"
                            class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL EDIT AKUN ================= --}}
    <div id="editModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 shrink-0">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-800">Edit Akun</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui data akun</p>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="editForm" method="POST" class="p-5 sm:p-6 space-y-4 overflow-y-auto custom-scroll">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="editName" required
                           class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" id="editEmail" required
                           class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">No. WhatsApp</label>
                    <input type="text" name="no_wa" id="editNoWa" placeholder="6281234567890"
                           class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-1">Format: 628xxx (tanpa + dan tanpa spasi)</p>
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">
                        Password Baru <span class="text-slate-400 font-normal">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password" minlength="6"
                           class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Role</label>
                    <select name="role" id="editRole" required
                            class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="karyawan">Karyawan</option>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Jatah Cuti</label>
                        <input type="number" name="jatah_cuti_tahunan" id="editJatah" required min="0"
                               class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5">Sisa Cuti</label>
                        <input type="number" name="sisa_cuti_tahunan" id="editSisa" required min="0"
                               class="w-full px-3 py-2 sm:py-2.5 border border-slate-300 rounded-lg text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-2 shrink-0">
                    <button type="button" onclick="closeEditModal()"
                            class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                        Perbarui Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// ===== Modal Tambah =====
function openCreateModal() {
    const modal = document.getElementById('createModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
    const modal = document.getElementById('createModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// ===== Modal Edit =====
function openEditModal(id, name, email, noWa, role, jatah, sisa) {
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');

    form.action = `/super-admin/users/${id}`;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editNoWa').value = noWa;
    document.getElementById('editRole').value = role;
    document.getElementById('editJatah').value = jatah;
    document.getElementById('editSisa').value = sisa;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// Klik backdrop untuk tutup
document.getElementById('createModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});
document.getElementById('editModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Esc untuk tutup
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeCreateModal(); closeEditModal(); }
});
</script>
@endpush
