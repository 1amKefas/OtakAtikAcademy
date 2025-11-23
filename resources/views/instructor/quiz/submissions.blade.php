@extends('layouts.app')

@section('title', 'Submissions - ' . $quiz->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.quiz.edit', [$course->id, $quiz->id]) }}" class="hover:opacity-80 text-sm">
                    ← Kembali ke Quiz
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Submissions</h1>
            <p class="text-indigo-100">{{ $quiz->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Submissions -->
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg border border-indigo-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-600 text-sm font-medium">Total Submissions</p>
                        <p class="text-3xl font-bold text-indigo-700 mt-2">{{ $stats['total_submissions'] }}</p>
                    </div>
                    <i class="fas fa-chart-bar text-indigo-300 text-4xl"></i>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-medium">Rata-rata Nilai</p>
                        <p class="text-3xl font-bold text-green-700 mt-2">{{ number_format($stats['average_score'], 1) }}%</p>
                    </div>
                    <i class="fas fa-calculator text-green-300 text-4xl"></i>
                </div>
            </div>

            <!-- Pass Rate -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-600 text-sm font-medium">Pass Rate</p>
                        <p class="text-3xl font-bold text-purple-700 mt-2">{{ number_format($stats['pass_rate'], 1) }}%</p>
                    </div>
                    <i class="fas fa-check-circle text-purple-300 text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Daftar Submission</h3>
            </div>

            @if($submissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Siswa</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Skor</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Waktu Pengerjaan</th>
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
                                        @if($submission->status === 'completed')
                                            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                <i class="fas fa-check-circle mr-1"></i> Selesai
                                            </span>
                                        @elseif($submission->status === 'in_progress')
                                            <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                <i class="fas fa-hourglass-half mr-1"></i> Sedang Dikerjakan
                                            </span>
                                        @elseif($submission->status === 'submitted')
                                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                <i class="fas fa-paper-plane mr-1"></i> Submitted
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
                                            <span class="inline-block">
                                                @if($submission->score >= $quiz->passing_score)
                                                    <span class="text-green-600 font-bold text-lg">{{ $submission->score }}%</span>
                                                    <i class="fas fa-check-circle text-green-500 ml-1"></i>
                                                @else
                                                    <span class="text-red-600 font-bold text-lg">{{ $submission->score }}%</span>
                                                    <i class="fas fa-times-circle text-red-500 ml-1"></i>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-sm">-</span>
                                        @endif
                                    </td>

                                    <!-- Time Spent -->
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @if($submission->submitted_at)
                                            {{ $submission->submitted_at->format('d M Y H:i') }}
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-center text-sm">
                                        <a href="{{ route('instructor.quiz.submission.detail', [$course->id, $quiz->id, $submission->id]) }}" 
                                           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition font-medium">
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
                    <p class="text-gray-600">Siswa belum mengerjakan quiz ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
