@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kursus Saya</h1>
                <p class="mt-1 text-sm text-gray-500">Lanjutkan pembelajaran Anda dan tingkatkan skill.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('course.show') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Jelajahi Kursus Baru
                </a>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="#" class="border-orange-500 text-orange-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Semua Kursus ({{ $registrations->count() }})
                </a>
                <!-- Future: Add Active/Completed tabs -->
            </nav>
        </div>

        @if($registrations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($registrations as $registration)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col h-full">
                        <!-- Course Image -->
                        <div class="relative h-48 w-full bg-gray-200">
                            <img src="{{ $registration->course->thumbnail_url }}" alt="{{ $registration->course->title }}" class="w-full h-full object-cover">
                            <div class="absolute top-0 right-0 mt-2 mr-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/90 text-gray-800 shadow backdrop-blur-sm">
                                    {{ $registration->progress ?? 0 }}% Selesai
                                </span>
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-md">
                                        {{ ucfirst($registration->course->category) }}
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]">
                                    <a href="{{ route('student.course-detail', $registration->id) }}" class="hover:text-orange-600 transition-colors">
                                        {{ $registration->course->title }}
                                    </a>
                                </h3>
                                
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs">
                                        {{ $registration->course->instructor->initial ?? 'I' }}
                                    </div>
                                    <span class="ml-2 text-xs text-gray-500">{{ $registration->course->instructor->name ?? 'Instructor' }}</span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progres Belajar</span>
                                    <span class="font-medium text-gray-900">{{ $registration->progress ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-orange-500 h-2 rounded-full transition-all duration-500 ease-out" style="width: {{ $registration->progress ?? 0 }}%"></div>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-gray-50">
                                <a href="{{ route('student.course-detail', $registration->id) }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-orange-700 bg-orange-100 hover:bg-orange-200 transition-colors">
                                    Lanjutkan Belajar
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
                <div class="mx-auto h-24 w-24 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-graduation-cap text-3xl text-orange-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Belum ada kursus yang diikuti</h3>
                <p class="mt-2 text-gray-500 max-w-sm mx-auto">Anda belum mendaftar di kursus manapun. Mulai perjalanan belajar Anda sekarang!</p>
                <div class="mt-6">
                    <a href="{{ route('course.show') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                        Cari Kursus
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection