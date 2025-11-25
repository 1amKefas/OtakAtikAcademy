@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Jelajahi Kursus Kami</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Tingkatkan keahlian Anda dengan materi pembelajaran terbaik dari instruktur berpengalaman.</p>
        </div>

        <!-- Search & Filter Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-10 border border-gray-100">
            <form action="{{ route('course.show') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Search Input -->
                <div class="md:col-span-5">
                    <label for="search" class="sr-only">Cari Kursus</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition duration-150 ease-in-out" 
                            placeholder="Cari judul kursus atau topik...">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="md:col-span-3">
                    <label for="category" class="sr-only">Kategori</label>
                    <select id="category" name="category" class="block w-full py-3 pl-3 pr-10 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Level Filter -->
                <div class="md:col-span-2">
                    <label for="level" class="sr-only">Level</label>
                    <select id="level" name="level" class="block w-full py-3 pl-3 pr-10 border border-gray-300 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <option value="">Semua Level</option>
                        <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Pemula</option>
                        <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Menengah</option>
                        <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Mahir</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-2">
                    <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Course Grid -->
        @if($courses->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($courses as $course)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 flex flex-col h-full overflow-hidden group">
                    <!-- Course Image Section -->
                    <div class="relative h-48 w-full bg-gray-200 overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        
                        <!-- Category Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/90 text-orange-800 backdrop-blur-sm shadow-sm">
                                {{ ucfirst($course->category) }}
                            </span>
                        </div>

                        <!-- Level Badge -->
                        <div class="absolute bottom-3 right-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->level == 'beginner' ? 'bg-green-100 text-green-800' : ($course->level == 'intermediate' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ ucfirst($course->level) }}
                            </span>
                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="p-6 flex-1 flex flex-col">
                        <!-- Title -->
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]">
                            <a href="{{ route('course.show.detail', $course->id) }}" class="hover:text-orange-600 transition-colors">
                                {{ $course->title }}
                            </a>
                        </h3>

                        <!-- Instructor -->
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-xs">
                                {{ $course->instructor->initial ?? 'I' }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $course->instructor->name ?? 'OtakAtik Instructor' }}</p>
                            </div>
                        </div>

                        <!-- Description (Fixed height with line clamp) -->
                        <div class="mb-4 flex-1">
                            <p class="text-sm text-gray-600 line-clamp-3">
                                {{ Str::limit(strip_tags($course->description), 150) }}
                            </p>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4 pt-4 border-t border-gray-50">
                            <div class="flex items-center">
                                <i class="far fa-clock mr-1.5 text-orange-500"></i>
                                {{ $course->duration ?? 'Flexible' }}
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users mr-1.5 text-blue-500"></i>
                                {{ $course->registrations_count ?? 0 }} Siswa
                            </div>
                        </div>

                        <!-- Price & Action -->
                        <div class="flex items-center justify-between mt-auto pt-2">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500">Harga</span>
                                <span class="text-lg font-bold text-gray-900">
                                    @if($course->price == 0)
                                        <span class="text-green-600">Gratis</span>
                                    @else
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            
                            <a href="{{ route('course.show.detail', $course->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all">
                                Detail
                                <i class="fas fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $courses->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="mx-auto h-24 w-24 text-gray-200 mb-4">
                    <i class="fas fa-book-open text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Tidak ada kursus ditemukan</h3>
                <p class="mt-2 text-gray-500">Coba ubah filter pencarian Anda atau cek kembali nanti.</p>
                <div class="mt-6">
                    <a href="{{ route('course.show') }}" class="text-orange-600 hover:text-orange-500 font-medium">
                        Reset Filter
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection