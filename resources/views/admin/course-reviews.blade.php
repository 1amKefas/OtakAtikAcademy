<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews: {{ $course->title }} - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <div class="sidebar w-64 bg-gray-900 text-white flex flex-col">
            <div class="p-6 border-b border-gray-800">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Admin</span></h1>
            </div>
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li><a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition"><i class="fas fa-arrow-left"></i><span>Back to Dashboard</span></a></li>
                    <li><a href="/admin/courses" class="flex items-center gap-3 px-4 py-3 bg-blue-600 text-white rounded-lg transition"><i class="fas fa-book"></i><span>List Courses</span></a></li>
                </ul>
            </nav>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.courses') }}" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Ulasan Kursus</h1>
                        <p class="text-sm text-gray-500">Mengelola review untuk: <span class="font-semibold text-blue-600">{{ $course->title }}</span></p>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                
                {{-- Stats Ringkas --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Rata-rata Rating</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-3xl font-bold text-gray-800">{{ number_format($reviews->avg('rating') ?? 0, 1) }}</span>
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center text-yellow-500 text-xl">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Ulasan</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $reviews->total() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 text-xl">
                            <i class="fas fa-comment-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">User</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Rating</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase w-1/2">Ulasan</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reviews as $review)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ substr($review->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $review->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $review->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex text-yellow-400 text-xs">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-200' }}"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-700 italic">"{{ $review->review }}"</p>
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-gray-500">
                                    {{ $review->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition" title="Hapus Ulasan (Moderasi)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="far fa-comment-dots text-4xl mb-3 text-gray-300"></i>
                                    <p>Belum ada ulasan untuk kursus ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>