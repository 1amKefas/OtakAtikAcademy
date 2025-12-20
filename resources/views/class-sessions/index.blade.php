@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header -->
        <div class="mb-6 border-b pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Class Sessions</h1>
                    <p class="text-gray-600 mt-2">{{ $course->title }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="/instructor/courses/{{ $course->id }}/manage" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="/instructor/courses/{{ $course->id }}/class-sessions/create" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus"></i> New Session
                    </a>
                </div>
            </div>
        </div>

        <!-- Sessions List -->
        <div class="space-y-4">
            @forelse($sessions as $session)
                <div class="border border-gray-200 rounded-lg hover:shadow-md transition-shadow overflow-hidden">
                    <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800">{{ $session->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $session->description }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="/instructor/courses/{{ $course->id }}/class-sessions/{{ $session->id }}/edit" class="px-3 py-2 border border-blue-300 rounded text-blue-600 hover:bg-blue-50 text-sm font-medium transition-colors">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="/instructor/courses/{{ $course->id }}/class-sessions/{{ $session->id }}" method="POST" class="inline" onclick="return confirm('Delete this session?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 border border-red-300 rounded text-red-600 hover:bg-red-50 text-sm font-medium transition-colors">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Session Details -->
                    <div class="p-4 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 font-medium">Date & Time</p>
                            <p class="text-gray-800 mt-1">
                                {{ $session->session_date->format('d M Y') }}
                                @if($session->start_time)
                                    <br><span class="text-xs text-gray-500">{{ date('H:i', strtotime($session->start_time)) }} - {{ date('H:i', strtotime($session->end_time)) }}</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-600 font-medium">Type</p>
                            <p class="mt-1">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $session->session_type === 'online' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $session->session_type === 'offline' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $session->session_type === 'hybrid' ? 'bg-green-100 text-green-800' : '' }}
                                ">
                                    {{ ucfirst($session->session_type) }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-600 font-medium">Meeting Type</p>
                            <p class="mt-1">
                                @if($session->meeting_type === 'zoom')
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-video"></i> Zoom
                                    </span>
                                @elseif($session->meeting_type === 'tatap_muka')
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                        <i class="fas fa-users"></i> Tatap Muka
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Other
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-600 font-medium">Attendance</p>
                            <p class="text-gray-800 mt-1">
                                <a href="/instructor/courses/{{ $course->id }}/class-sessions/{{ $session->id }}/attendance" class="text-blue-600 hover:text-blue-800 font-semibold">
                                    Mark Attendance →
                                </a>
                            </p>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="px-4 pb-4 text-sm text-gray-600">
                        @if($session->meeting_type === 'zoom' && $session->zoom_link)
                            <p class="flex items-center gap-2">
                                <i class="fas fa-link text-blue-600"></i>
                                <a href="{{ $session->zoom_link }}" target="_blank" class="text-blue-600 hover:underline">Zoom Link</a>
                            </p>
                        @endif

                        @if($session->session_type === 'offline' && $session->location)
                            <p class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                <span>{{ $session->location }}</span>
                            </p>
                        @endif

                        @if($session->session_type === 'offline' && $session->room_number)
                            <p class="flex items-center gap-2">
                                <i class="fas fa-door-open text-yellow-600"></i>
                                <span>Room {{ $session->room_number }}</span>
                            </p>
                        @endif

                        @if($session->agenda)
                            <p class="flex gap-2">
                                <i class="fas fa-list text-green-600 flex-shrink-0 mt-0.5"></i>
                                <span>{{ Str::limit($session->agenda, 80) }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600">No class sessions yet.</p>
                    <a href="/instructor/courses/{{ $course->id }}/class-sessions/create" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus"></i> Create First Session
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f9fafb;
    }
</style>
@endsection
