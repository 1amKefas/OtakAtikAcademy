@extends('layouts.app')

@section('title', 'Forum Diskusi - ' . $course->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.course-detail', $registration->id) }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">💬 Forum Diskusi</h1>
            <p class="text-green-100">{{ $course->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="grid grid-cols-3 gap-8">
            <!-- Forum List -->
            <div class="col-span-2">
                <!-- Create Thread Button -->
                <div class="mb-6">
                    <a href="{{ route('student.forum.create', $course->id) }}" 
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        + Buat Topik Baru
                    </a>
                </div>

                <!-- Forum Threads -->
                <div class="space-y-4">
                    @forelse($threads as $thread)
                    <a href="{{ route('student.forum.detail', $thread->id) }}" 
                       class="block bg-white rounded-lg border border-gray-200 hover:border-green-400 hover:shadow-lg transition p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 hover:text-green-600">{{ $thread->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    by <span class="font-semibold">{{ $thread->user->name }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $thread->replies()->count() }} Balasan
                                </span>
                            </div>
                        </div>

                        <p class="text-gray-700 mb-3 line-clamp-2">{{ $thread->content }}</p>

                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <div class="flex gap-4">
                                <span>👁️ {{ $thread->views ?? 0 }} Views</span>
                                <span>💬 {{ $thread->replies()->count() }} Replies</span>
                            </div>
                            <span>{{ $thread->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                    @empty
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-12 text-center">
                        <p class="text-3xl mb-4"></p>
                        <p class="text-gray-600 text-lg">Belum ada diskusi</p>
                        <p class="text-gray-500 text-sm mt-2">Mulai diskusi dengan membuat topik baru</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-1">
                <div class="sticky top-6">
                    <!-- Forum Info -->
                    <div class="bg-green-50 rounded-lg border border-green-200 p-4 mb-4">
                        <h4 class="font-semibold text-green-900 mb-3">Statistik Forum</h4>
                        <div class="text-sm text-green-800 space-y-2">
                            <div class="flex justify-between">
                                <span>Total Topik:</span>
                                <strong>{{ $course->forums()->count() }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Total Balasan:</span>
                                <strong>{{ $course->forums()->sum(fn($f) => $f->replies()->count()) }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Anggota Aktif:</span>
                                <strong>{{ $course->registrations()->count() }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Rules -->
                    <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                        <h4 class="font-semibold text-blue-900 mb-3">Peraturan Forum</h4>
                        <ul class="text-xs text-blue-800 space-y-2">
                            <li>Hormati semua anggota</li>
                            <li>Tulis pertanyaan dengan jelas</li>
                            <li>Gunakan bahasa yang sopan</li>
                            <li>Hindari spam & iklan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
