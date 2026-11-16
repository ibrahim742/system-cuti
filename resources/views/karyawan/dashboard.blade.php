<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Karyawan</h1>
                <p class="text-gray-500 text-sm">Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-red-600 hover:underline">Logout</button>
            </form>
        </div>

        {{-- Kartu Sisa Cuti --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-500 text-sm">Sisa Cuti Tahunan</p>
                <p class="text-4xl font-bold text-blue-600">
                    {{ auth()->user()->sisa_cuti_tahunan }}
                    <span class="text-lg font-normal text-gray-500">hari</span>
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-500 text-sm">Cuti Terpakai Bulan Ini</p>
                <p class="text-4xl font-bold text-orange-500">
                    {{ $cutiBulanIni }}
                    <span class="text-lg font-normal text-gray-500">hari</span>
                </p>
            </div>
        </div>

        {{-- Form Pengajuan --}}
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Ajukan Cuti</h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('cuti.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" required
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Alasan (opsional)</label>
                    <input type="text" name="alasan"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-3">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>

        {{-- Riwayat --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Riwayat Pengajuan</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-gray-600">
                        <tr>
                            <th class="py-2">Tanggal</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $c)
                            <tr class="border-b">
                                <td class="py-2">{{ $c->tanggal_mulai->format('d/m/Y') }} - {{ $c->tanggal_selesai->format('d/m/Y') }}</td>
                                <td>{{ $c->jumlah_hari }} hari</td>
                                <td>
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $c->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $c->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $c->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $c->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-gray-500">Belum ada pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $riwayat->links() }}</div>
        </div>

    </div>
</body>
</html>
