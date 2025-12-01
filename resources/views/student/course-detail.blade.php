@extends('layouts.app')

@section('title', $registration->course->title . ' - My Course')

@section('content')
<div class="bg-gray-50 min-h-screen pt-20">
    <!-- Course Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-12">
        <div class="max-w-6xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('student.courses') }}" class="hover:opacity-80 text-white font-medium">
                    ← Kembali ke Kursus Saya
                </a>
            </div>
            <h1 class="text-4xl font-bold mb-3">{{ $registration->course->title }}</h1>
            <p class="text-lg opacity-90">{{ $registration->course->description }}</p>
            <div class="mt-6 flex items-center gap-6">
                <div>
                    <p class="text-sm opacity-75">Progres</p>
                    <div class="w-64 bg-white bg-opacity-20 rounded-full h-3 mt-2">
                        <div class="bg-white h-3 rounded-full" style="width: {{ $registration->progress ?? 0 }}%"></div>
                    </div>
                    <p class="text-sm mt-1">{{ $registration->progress ?? 0 }}% Selesai</p>
                </div>
                <div>
                    <p class="text-sm opacity-75">Terdaftar</p>
                    <p class="font-semibold">{{ $registration->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="max-w-6xl mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Tentang Kursus -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Kursus Ini</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $registration->course->description }}</p>
                </div>

                <!-- Materi Kursus -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">📚 Materi Kursus</h2>
                    @if($registration->course->publishedMaterials->count() > 0)
                        <div class="space-y-3">
                            @foreach($registration->course->publishedMaterials as $material)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-400 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800">{{ $material->title }}</h4>
                                            @if($material->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $material->description }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                                <span>📦 {{ number_format($material->file_size / 1024, 0) }} KB</span>
                                                <span>📅 {{ $material->created_at->format('d M Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="/storage/{{ $material->file_path }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap ml-4">
                                        ⬇️ Unduh
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Belum ada materi tersedia</p>
                    @endif
                </div>

                <!-- Tugas -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">✏️ Tugas</h2>
                    @if($registration->course->publishedAssignments->count() > 0)
                        <div class="space-y-4">
                            @foreach($registration->course->publishedAssignments as $assignment)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $assignment->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">Deadline: <strong>{{ $assignment->due_date->format('d M Y H:i') }}</strong></p>
                                    </div>
                                    @php
                                        $submission = $assignment->submissions()->where('user_id', auth()->id())->first();
                                    @endphp
                                    @if($submission)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                                            ✓ Sudah Dikumpulkan
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-3 py-1 rounded-full">
                                            ⚠️ Belum Dikerjakan
                                        </span>
                                    @endif
                                </div>
                                @if($assignment->description)
                                <p class="text-gray-700 text-sm mb-3">{{ $assignment->description }}</p>
                                @endif
                                <div class="flex gap-2">
                                    @if(!$submission)
                                        <a href="{{ route('student.assignment.submit.form', $assignment->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Kumpulkan Tugas →
                                        </a>
                                    @else
                                        <a href="{{ route('student.assignment.view', $assignment->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Lihat Submission →
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Belum ada tugas</p>
                    @endif
                </div>

                <!-- Quizzes -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">📝 Quizzes</h2>
                        @php
                            $totalQuizzes = $registration->course->quizzes()->where('is_published', true)->count();
                            $completedQuizzes = $registration->course->quizzes()
                                ->where('is_published', true)
                                ->whereHas('submissions', function($q) {
                                    $q->where('user_id', auth()->id());
                                })
                                ->count();
                            $pendingQuizzes = $totalQuizzes - $completedQuizzes;
                        @endphp
                        @if($pendingQuizzes > 0)
                        <span class="bg-red-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">
                            {{ $pendingQuizzes }}
                        </span>
                        @endif
                    </div>
                    @if($totalQuizzes > 0)
                        <div class="space-y-3">
                            @foreach($registration->course->quizzes()->where('is_published', true)->get() as $quiz)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $quiz->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">📊 {{ $quiz->questions()->count() }} Soal • ⏱️ {{ $quiz->duration_minutes ?? 0 }} menit</p>
                                    </div>
                                    @php
                                        $quizSubmission = $quiz->submissions()->where('user_id', auth()->id())->first();
                                    @endphp
                                    @if($quizSubmission)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                                            ✓ Selesai
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-3 py-1 rounded-full">
                                            ⚠️ Belum Dikerjain
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('student.quiz.index', $registration->course->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        Mulai Kuis →
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Belum ada kuis</p>
                    @endif
                </div>

                <!-- Discussion Forum -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">💬 Discussion Forum</h2>
                    <a href="{{ route('student.forum.index', $registration->course->id) }}" 
                       class="inline-block text-blue-600 hover:text-blue-800 font-medium">
                        Join the Discussion →
                    </a>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Info Instruktur -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-800 mb-4">👨‍🏫 Instruktur</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($registration->course->instructor->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $registration->course->instructor->name }}</p>
                            <p class="text-sm text-gray-600">{{ $registration->course->instructor->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Kursus -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-800 mb-4">ℹ️ Info Kursus</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Tipe</p>
                            <p class="font-semibold text-gray-800">{{ $registration->course->display_type }}</p>
                        </div>
                        @if($registration->course->duration_days)
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Durasi</p>
                            <p class="font-semibold text-gray-800">{{ $registration->course->duration_days }} hari</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-600 uppercase">Siswa Terdaftar</p>
                            <p class="font-semibold text-gray-800">{{ $registration->course->registrations()->where('status', 'paid')->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Links - Request Refund (HANYA UNTUK HYBRID DAN TATAP MUKA) -->
                @if($registration->status === 'paid' && !$refund && in_array($registration->course->display_type, ['Hybrid', 'Tatap Muka']))
                <div class="bg-red-50 border-2 border-red-200 rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-red-800 mb-4">💸 Request Refund</h3>
                    <p class="text-sm text-red-700 mb-4">Not satisfied with this course? You can request a refund within 30 days.</p>
                    <a href="{{ route('refund.create', $registration->id) }}" 
                       class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                        Request Refund
                    </a>
                </div>
                @endif

                <!-- Info untuk Kursus Online (Tidak Bisa Refund) -->
                @if($registration->course->display_type === 'Online' && !$refund)
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-yellow-800 mb-3">ℹ️ Refund Policy</h3>
                    <p class="text-sm text-yellow-700">
                        Kursus <strong>Online</strong> tidak dapat di-refund. Refund hanya tersedia untuk kursus <strong>Hybrid</strong> dan <strong>Tatap Muka</strong>.
                    </p>
                </div>
                @endif

                <!-- Status Pengembalian Dana -->
                @if($refund)
                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-blue-800 mb-3">📋 Status Refund</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-blue-600 uppercase">Status</p>
                            <p class="font-semibold text-gray-800">
                                @if($refund->status === 'pending')
                                    <span class="text-yellow-600">⏳ Pending</span>
                                @elseif($refund->status === 'approved')
                                    <span class="text-green-600">✓ Approved</span>
                                @elseif($refund->status === 'rejected')
                                    <span class="text-red-600">✗ Rejected</span>
                                @endif
                            </p>
                        </div>
                        @if($refund->status === 'rejected' && $refund->admin_notes)
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded">
                            <p class="text-xs text-red-600 uppercase mb-1">Alasan Ditolak</p>
                            <p class="text-sm text-red-800">{{ $refund->admin_notes }}</p>
                        </div>
                        @endif
                        @if($refund->status === 'approved' && $refund->admin_notes)
                        <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded">
                            <p class="text-xs text-green-600 uppercase mb-1">Catatan Admin</p>
                            <p class="text-sm text-green-800">{{ $refund->admin_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection