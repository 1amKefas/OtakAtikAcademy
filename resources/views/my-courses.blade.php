@extends('layouts.app')

@section('title', 'My Courses - OtakAtik Academy')

@section('content')
<section class="pt-32 pb-20 px-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800">Course Saya</h1>
                    <p class="text-gray-600 mt-2">Kelola dan lanjutkan course yang sedang Anda jalani</p>
                </div>
                <a href="{{ route('course.show') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg transition-all font-medium inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Course
                </a>
            </div>
        </div>

        @if($enrolledCourses->count() > 0)
            <!-- Filter and Sort -->
            <div class="mb-8 flex gap-4 flex-wrap">
                <button class="px-4 py-2 bg-blue-500 text-white rounded-lg font-medium transition-all hover:bg-blue-600">
                    <i class="fas fa-list mr-2"></i> Semua
                </button>
                <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg font-medium transition-all hover:bg-gray-50">
                    <i class="fas fa-fire mr-2"></i> Sedang Berjalan
                </button>
                <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg font-medium transition-all hover:bg-gray-50">
                    <i class="fas fa-check-circle mr-2"></i> Selesai
                </button>
            </div>

            <!-- Courses List - Simple Full Width -->
            <div class="space-y-4">
                @foreach($enrolledCourses as $enrollment)
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border-l-4 border-blue-500">
                        <div class="flex items-center gap-6 p-6">
                            <!-- Course Visual -->
                            <div class="flex-shrink-0 w-32 h-32">
                                <div class="h-full bg-gradient-to-br from-blue-600 via-purple-500 to-pink-500 rounded-lg flex items-center justify-center text-center p-4 shadow-lg">
                                    <h3 class="text-white font-bold text-sm break-words">{{ $enrollment->course->title }}</h3>
                                </div>
                            </div>

                            <!-- Course Info - Middle Section -->
                            <div class="flex-1">
                                <!-- Title and Instructor -->
                                <div class="mb-3">
                                    <h2 class="text-xl font-bold text-gray-800">{{ $enrollment->course->title }}</h2>
                                    @if($enrollment->course->instructor)
                                        <p class="text-gray-600 text-sm">
                                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                                            {{ $enrollment->course->instructor->name }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Description -->
                                <p class="text-gray-700 text-sm mb-3 line-clamp-2">{{ $enrollment->course->description }}</p>

                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-600">Progress</span>
                                        <span class="text-sm font-bold text-blue-600">{{ $enrollment->progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all" 
                                             style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="flex gap-4 text-xs text-gray-600 flex-wrap">
                                    @if($enrollment->enrolled_at)
                                        <span><i class="fas fa-calendar text-orange-500 mr-1"></i>{{ $enrollment->enrolled_at->format('d M Y') }}</span>
                                    @endif
                                    <span><i class="fas fa-book text-green-500 mr-1"></i>Materi</span>
                                    <span><i class="fas fa-tasks text-purple-500 mr-1"></i>Tugas</span>
                                </div>
                            </div>

                            <!-- Status and Action - Right Section -->
                            <div class="flex-shrink-0 text-right">
                                <!-- Status Badge -->
                                <div class="mb-4">
                                    @if($enrollment->progress >= 100)
                                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                            ✓ Selesai
                                        </span>
                                    @elseif($enrollment->progress >= 50)
                                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                            Berjalan
                                        </span>
                                    @else
                                        <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                            Baru
                                        </span>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                <a href="{{ route('course.show.detail', $enrollment->course->id) }}" 
                                   class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg transition-all font-medium text-sm">
                                    <i class="fas fa-arrow-right mr-2"></i> Lanjutkan
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-32 bg-white rounded-2xl shadow-lg">
                <div class="max-w-md mx-auto">
                    <!-- Icon -->
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-6xl">🎓</span>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">Anda Belum Mendaftar Course</h3>
                    <p class="text-gray-600 text-base mb-8">Mulai belajar dengan mendaftar ke salah satu course kami yang menarik dan berkembang bersama.</p>
                    
                    <!-- CTA Button -->
                    <a href="{{ route('course.show') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg transition-all font-medium">
                        <i class="fas fa-search mr-2"></i> Jelajahi Course
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
