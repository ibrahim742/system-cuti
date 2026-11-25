<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Cuti</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Riwayat Cuti Karyawan</h2>
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Periode:</strong> {{ $bulan }}/{{ $tahun }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Durasi</th>
                <th>Alasan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $c)
            <tr>
                <td>{{ $c->tanggal_mulai->format('d/m/Y') }} - {{ $c->tanggal_selesai->format('d/m/Y') }}</td>
                <td>{{ $c->jumlah_hari }} hari</td>
                <td>{{ $c->alasan ?? '-' }}</td>
                <td>{{ $c->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>