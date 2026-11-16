<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-2xl mx-auto py-8 px-4">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Akun</h1>
            <a href="{{ route('super-admin.users.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali</a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('super-admin.users.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">No. WhatsApp</label>
                <input type="text" name="no_wa" value="{{ old('no_wa') }}" placeholder="6281234567890"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Format: 628xxx (tanpa + dan tanpa spasi)</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">Password</label>
                <input type="password" name="password" required minlength="6"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">Role</label>
                <select name="role" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Jatah Cuti Tahunan</label>
                    <input type="number" name="jatah_cuti_tahunan" value="{{ old('jatah_cuti_tahunan', 12) }}" required min="0"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Sisa Cuti Tahunan</label>
                    <input type="number" name="sisa_cuti_tahunan" value="{{ old('sisa_cuti_tahunan', 12) }}" required min="0"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>

    </div>
</body>
</html>
