<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - {{ $course->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%); }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex h-screen">

    <aside class="sidebar w-64 text-white flex flex-col hidden md:flex flex-shrink-0">
        <div class="p-6 border-b border-blue-500">
            <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-300">Instructor</span></h1>
        </div>
        
        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <li>
                    <a href="/instructor/dashboard" class="flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-blue-500 rounded-lg transition-colors">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/instructor/courses" class="flex items-center gap-3 px-4 py-3 bg-blue-500 rounded-lg text-white">
                        <i class="fas fa-book w-5"></i>
                        <span>My Courses</span>
                    </a>
                </li>
                <li class="pt-4 mt-4 border-t border-blue-500"></li>
                <li>
                    <a href="/course" class="flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-blue-500 rounded-lg transition-colors">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span>Browse Courses</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('instructor.courses') }}" class="text-gray-500 hover:text-gray-800 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <div class="h-6 w-px bg-gray-300"></div>
                <h2 class="text-lg font-bold text-gray-800">Daftar Siswa: {{ $course->title }}</h2>
            </div>
            <div class="text-sm text-gray-500">
                Total Siswa: <b>{{ $students->count() }}</b>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800">Enrolled Students</h3>
                    </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Nama Siswa</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Tanggal Daftar</th>
                                <th class="px-6 py-4">Progress Belajar</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                                <th class="px-6 py-4 text-right">
                                    Time Spent
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($students as $registration)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                            {{ substr($registration->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $registration->user->name }}</p>
                                            <p class="text-xs text-gray-500">ID: #{{ $registration->user->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-sm">
                                    {{ $registration->user->email }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    {{ $registration->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="w-full max-w-xs">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-medium {{ $registration->progress == 100 ? 'text-green-600' : 'text-gray-600' }}">
                                                {{ $registration->progress }}%
                                            </span>
                                            @if($registration->progress == 100)
                                                <span class="text-green-600"><i class="fas fa-check-circle"></i> Selesai</span>
                                            @endif
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ $registration->progress }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-gray-400 hover:text-blue-600 transition" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-user-graduate text-4xl text-gray-300 mb-3"></i>
                                        <p>Belum ada siswa yang mendaftar di kursus ini.</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-bold">
                                        @php
                                            $seconds = $reg->total_learning_seconds;
                                            $hours = floor($seconds / 3600);
                                            $minutes = floor(($seconds % 3600) / 60);
                                        @endphp
                                        
                                        @if($hours > 0)
                                            {{ $hours }}j {{ $minutes }}m
                                        @elseif($minutes > 0)
                                            {{ $minutes }}m
                                        @else
                                            <span class="text-gray-400 text-xs">Baru mulai</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">Total Belajar</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>