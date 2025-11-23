@extends('layouts.app')

@section('title', 'Submissions - ' . $assignment->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.courses.show', $assignment->course_id) }}" class="hover:opacity-80 text-sm">
                    ← Kembali ke Course
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Submissions</h1>
            <p class="text-indigo-100">{{ $assignment->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <!-- Filters -->
        <div class="mb-6 flex gap-4 flex-wrap">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium transition hover:bg-indigo-700">
                Semua
            </button>
            <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg font-medium transition hover:bg-gray-50">
                Belum Dinilai
            </button>
            <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg font-medium transition hover:bg-gray-50">
                Sudah Dinilai
            </button>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            @if($submissions && $submissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Siswa</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Nilai</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tanggal Submit</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($submissions as $submission)
                                <tr class="hover:bg-gray-50 transition">
                                    <!-- Student Name -->
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        <div class="flex items-center gap-3">
                                            @if($submission->user && $submission->user->profile_picture && Storage::disk('public')->exists($submission->user->profile_picture))
                                                <img src="{{ Storage::disk('public')->url($submission->user->profile_picture) }}" alt="{{ $submission->user->name }}" class="w-8 h-8 rounded-full">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold">
                                                    {{ substr($submission->user->name ?? 'U', 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="font-medium">{{ $submission->user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 text-sm">
                                        @if($submission->status === 'submitted')
                                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                <i class="fas fa-paper-plane mr-1"></i> Submitted
                                            </span>
                                        @elseif($submission->status === 'graded')
                                            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                <i class="fas fa-check-circle mr-1"></i> Dinilai
                                            </span>
                                        @else
                                            <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Score -->
                                    <td class="px-6 py-4 text-center text-sm">
                                        @if($submission->score !== null)
                                            <span class="font-bold text-indigo-600">{{ $submission->score }}</span>
                                        @else
                                            <span class="text-gray-500 text-sm">-</span>
                                        @endif
                                    </td>

                                    <!-- Submitted Date -->
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @if($submission->submitted_at)
                                            {{ $submission->submitted_at->format('d M Y H:i') }}
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-center text-sm space-x-2">
                                        <a href="{{ route('instructor.submissions.detail', [$assignment->id, $submission->id]) }}" 
                                           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition font-medium text-sm">
                                            <i class="fas fa-eye mr-1"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20 px-6">
                    <i class="fas fa-inbox text-gray-300 text-5xl mb-4 block"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Submission</h3>
                    <p class="text-gray-600">Siswa belum mengumpulkan tugas ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
