<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Akun</h1>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:underline self-center">← Dashboard</a>
                <a href="{{ route('super-admin.users.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">+ Tambah Akun</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white rounded-lg shadow p-6 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b text-gray-600">
                    <tr>
                        <th class="py-2">Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Jatah</th>
                        <th>Sisa</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr class="border-b">
                            <td class="py-2">{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                <span class="px-2 py-1 rounded text-xs bg-gray-100">{{ $u->role }}</span>
                            </td>
                            <td>{{ $u->jatah_cuti_tahunan }}</td>
                            <td>{{ $u->sisa_cuti_tahunan }}</td>
                            <td class="text-right space-x-2">
                                <a href="{{ route('super-admin.users.edit', $u) }}"
                                   class="text-blue-600 hover:underline text-xs">Edit</a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('super-admin.users.destroy', $u) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-4 text-gray-500">Belum ada akun.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>

    </div>
</body>
</html>
