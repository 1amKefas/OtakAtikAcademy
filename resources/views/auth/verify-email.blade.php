<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - OtakAtik Academy</title>
    
    {{-- Hapus CDN, Ganti dengan Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center font-sans">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full text-center">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Verifikasi Email Anda</h2>
        
        @if (session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <p class="text-gray-600 mb-6">
            Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan ke email Anda.
        </p>

        {{-- [BARU] Tombol Shortcut ke Gmail & Login --}}
        <div class="flex flex-col gap-3 mb-6">
            <a href="https://mail.google.com" target="_blank" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/></svg>
                Buka Gmail
            </a>
            
            <a href="{{ route('login') }}" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition">
                Masuk ke Halaman Login
            </a>
        </div>
        {{-- [END BARU] --}}

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded w-full transition">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">
                Logout
            </button>
        </form>
    </div>
</body>
</html>