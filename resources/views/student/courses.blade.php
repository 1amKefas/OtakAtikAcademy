@extends('layouts.app')

@section('title', 'Kursus Saya')

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold mb-2">Kursus Saya</h1>
            <p class="text-blue-100">Kelola dan lihat semua kursus yang Anda daftar</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-8">
        @if ($courses->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Anda belum mendaftar kursus</h3>
                <p class="mt-1 text-gray-500">Jelajahi dan daftar kursus yang tersedia sekarang</p>
                <div class="mt-6">
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Jelajahi Kursus
                    </a>
                </div>
            </div>
        @else
            <!-- Courses Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courses as $registration)
                    @if ($registration->course)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <!-- Course Image -->
                            <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                @if ($registration->course->image_url)
                                    <img src="{{ $registration->course->image_url }}" alt="{{ $registration->course->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-white text-center">
                                        <svg class="mx-auto h-16 w-16 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747 0-6.002-4.5-10.747-10-10.747z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Course Info -->
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 flex-1">{{ $registration->course->title }}</h3>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $registration->status === 'paid' ? 'bg-green-100 text-green-800' : ($registration->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $registration->course->description }}</p>

                                <!-- Course Meta -->
                                <div class="space-y-2 mb-4 text-sm">
                                    <div class="flex items-center text-gray-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $registration->course->duration_days }} hari
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $registration->course->currentEnrollment ?? 0 }} peserta
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        {{ $registration->course->type }}
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700">Progress</span>
                                        <span class="text-xs font-semibold text-gray-700">{{ $registration->progress ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $registration->progress ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <a href="{{ route('student.course.detail', $registration->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($courses->hasPages())
                <div class="mt-8">
                    {{ $courses->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
