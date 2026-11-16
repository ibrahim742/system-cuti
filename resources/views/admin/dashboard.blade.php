<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
                <p class="text-gray-500 text-sm">Masuk sebagai: {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
            </div>
            <div class="flex items-center gap-4">
                @if(auth()->user()->role === 'super_admin')
                    <a href="{{ route('super-admin.users.index') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        Kelola Akun
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-red-600 hover:underline">Logout</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Pengajuan Menunggu Persetujuan</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-gray-600">
                        <tr>
                            <th class="py-2">Nama</th>
                            <th>Tanggal</th>
                            <th>Durasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pending as $c)
                            <tr class="border-b">
                                <td class="py-2">{{ $c->user->name }}</td>
                                <td>{{ $c->tanggal_mulai->format('d/m/Y') }} - {{ $c->tanggal_selesai->format('d/m/Y') }}</td>
                                <td>{{ $c->jumlah_hari }} hari</td>
                                <td class="space-x-2">
                                    <form action="{{ route('cuti.approve', $c) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">Setujui</button>
                                    </form>
                                    <form action="{{ route('cuti.reject', $c) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-gray-500">Tidak ada pengajuan pending.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
