@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header -->
        <div class="mb-6 border-b pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">My Attendance</h1>
                    <p class="text-gray-600 mt-2">{{ $course->title }}</p>
                </div>
                <a href="/student/courses" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Back to Courses
                </a>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $stats = $attendance->getAttendanceSummary(auth()->id(), $course->id);
                $percentage = $attendance->getAttendancePercentage(auth()->id(), $course->id);
                $total = $stats['present'] + $stats['absent'] + $stats['late'] + $stats['excused'];
            @endphp
            
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Present</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['present'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total sessions</p>
            </div>

            <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                <p class="text-sm text-gray-600">Absent</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total sessions</p>
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                <p class="text-sm text-gray-600">Late</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['late'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total sessions</p>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Attendance Rate</p>
                <p class="text-2xl font-bold text-blue-600">{{ $percentage }}%</p>
                <p class="text-xs text-gray-500 mt-1">{{ $total }} total sessions</p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6 bg-gray-100 p-4 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="font-medium text-gray-800">Attendance Progress</p>
                <p class="text-sm font-semibold text-gray-600">{{ $percentage }}%</p>
            </div>
            <div class="w-full bg-gray-300 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                @if($percentage >= 80)
                    <span class="text-green-600"><i class="fas fa-check-circle"></i> Excellent attendance!</span>
                @elseif($percentage >= 70)
                    <span class="text-blue-600"><i class="fas fa-info-circle"></i> Good attendance</span>
                @elseif($percentage >= 60)
                    <span class="text-yellow-600"><i class="fas fa-exclamation-circle"></i> Attendance acceptable</span>
                @else
                    <span class="text-red-600"><i class="fas fa-times-circle"></i> Low attendance - at risk!</span>
                @endif
            </p>
        </div>

        <!-- Attendance Details Table -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Session History</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-gray-300">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Session</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Date & Time</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Type</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessionAttendances as $sa)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $sa->classSession->title }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $sa->classSession->session_date->format('d M Y') }}
                                    @if($sa->classSession->start_time)
                                        • {{ date('H:i', strtotime($sa->classSession->start_time)) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
                                        {{ $sa->classSession->session_type === 'online' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $sa->classSession->session_type === 'offline' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $sa->classSession->session_type === 'hybrid' ? 'bg-green-100 text-green-800' : '' }}
                                    ">
                                        {{ ucfirst($sa->classSession->session_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($sa->status === 'present')
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check"></i> Present
                                        </span>
                                    @elseif($sa->status === 'absent')
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                            <i class="fas fa-times"></i> Absent
                                        </span>
                                    @elseif($sa->status === 'late')
                                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                            <i class="fas fa-clock"></i> Late
                                        </span>
                                    @elseif($sa->status === 'excused')
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                            <i class="fas fa-calendar-check"></i> Excused
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $sa->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b border-gray-200">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-2xl mb-2 block opacity-50"></i>
                                    No attendance records yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Certificate Eligibility Info -->
        @if($registration)
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                <h3 class="font-semibold text-blue-900 mb-2">Certificate Eligibility</h3>
                <p class="text-sm text-blue-800 mb-2">
                    Your certificate eligibility is calculated as:
                </p>
                <ul class="text-sm text-blue-800 space-y-1 mb-3">
                    <li>• Progress: {{ $registration->progress ?? 0 }}% (60% weight)</li>
                    <li>• Attendance: {{ $percentage }}% (40% weight)</li>
                    <li>• Combined Score: <strong>{{ round($registration->progress * 0.6 + $percentage * 0.4) }}%</strong></li>
                    <li>• Required: 70% or 100% progress</li>
                </ul>
                @if($registration->isEligibleForCertificate())
                    <p class="text-green-700 font-semibold">
                        <i class="fas fa-check-circle"></i> You are eligible for a certificate!
                    </p>
                @else
                    <p class="text-orange-700 font-semibold">
                        <i class="fas fa-exclamation-circle"></i> Keep improving your attendance and progress to become eligible.
                    </p>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
    body {
        background-color: #f9fafb;
    }
</style>
@endsection
