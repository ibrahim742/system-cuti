<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Cuti</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Sistem Cuti</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Masuk
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center mb-2">Akun uji coba:</p>
            <div class="text-xs text-gray-600 space-y-1">
                <p><span class="font-semibold">Super Admin:</span> superadmin@cuti.test / password</p>
                <p><span class="font-semibold">Admin:</span> admin@cuti.test / password</p>
                <p><span class="font-semibold">Karyawan:</span> karyawan1@cuti.test / password</p>
            </div>
        </div>
    </div>
</body>
</html>
