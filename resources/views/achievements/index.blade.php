@extends('layouts.app')

@section('title', __('achievements.achievements'))

@section('content')
<div class="bg-white min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-12">
        <div class="max-w-6xl mx-auto text-center">
            <h1 class="text-4xl font-bold mb-3">{{ __('achievements.your_journey') }}</h1>
            <p class="text-purple-100 text-lg">{{ __('achievements.my_achievements') }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-6 py-12">
        <!-- Stats Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-semibold">{{ __('achievements.courses_completed') }}</p>
                        <p class="text-3xl font-bold text-blue-700 mt-2">{{ $coursesCompleted }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17.25m20-11.002c0 5.277-4.5 9.999-10 9.999"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-semibold">{{ __('achievements.total_hours') }}</p>
                        <p class="text-3xl font-bold text-green-700 mt-2">{{ $totalHours }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-600 text-sm font-semibold">{{ __('achievements.average_score') }}</p>
                        <p class="text-3xl font-bold text-yellow-700 mt-2">{{ $averageScore }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-600 text-sm font-semibold">{{ __('achievements.current_streak') }}</p>
                        <p class="text-3xl font-bold text-orange-700 mt-2">0 {{ __('achievements.days') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657L13.414 22.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Badges Section -->
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">{{ __('achievements.earned_badges') }}</h2>
            
            @if(count($userAchievements) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($achievements as $achievement)
                        @php
                            $earned = in_array($achievement->id, $userAchievements);
                            [$bgColor, $textColor] = explode(' ', $achievement->color);
                        @endphp
                        <div class="rounded-lg p-6 border-2 transition-all {{ $earned ? 'border-' . explode('-', $bgColor)[1] . '-500 ' . $bgColor . ' shadow-lg' : 'border-gray-200 bg-gray-50 opacity-60' }}">
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-16 flex items-center justify-center rounded-full flex-shrink-0 {{ $bgColor }}">
                                    <svg class="w-8 h-8 {{ $textColor }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="{{ $achievement->icon }}"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">{{ $achievement->name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $achievement->description }}</p>
                                    @if($earned)
                                        <span class="inline-block mt-3 px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                            ✓ {{ __('messages.unlocked') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">{{ __('achievements.no_certificates_yet') }}</p>
                    <a href="/course" class="text-orange-600 hover:text-orange-700 font-semibold mt-4 inline-block">
                        {{ __('messages.start_learning_now') }} →
                    </a>
                </div>
            @endif
        </div>

        <!-- Certificates Section -->
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-8">{{ __('achievements.certificates') }}</h2>
            
            @if($certificates->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($certificates as $cert)
                        <div class="border-2 border-orange-200 rounded-lg p-8 bg-gradient-to-br from-orange-50 to-orange-100 hover:shadow-xl transition-all">
                            <div class="text-center">
                                <svg class="w-12 h-12 text-orange-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20H5a2 2 0 01-2-2V9.828a2 2 0 01.586-1.414l5-5H15a2 2 0 012 2v10a2 2 0 01-2 2h-5.414a2 2 0 01-1.414-.586l-2-2H7v4z"></path>
                                </svg>
                                <h3 class="text-2xl font-bold text-gray-800 mt-2">{{ $cert->course->title }}</h3>
                                <p class="text-sm text-gray-600 mt-2">{{ __('achievements.certificate_of_completion') }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ __('achievements.earned_on') }}: {{ $cert->issued_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400 mt-2">{{ $cert->certificate_number }}</p>
                                
                                <div class="flex gap-3 justify-center mt-6">
                                    <a href="{{ route('student.certificates.download', $cert) }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-semibold transition">
                                        {{ __('achievements.download_certificate') }}
                                    </a>
                                    <button class="px-4 py-2 border border-orange-600 text-orange-600 hover:bg-orange-50 rounded-lg text-sm font-semibold transition">
                                        {{ __('achievements.share_achievement') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4M7 20H5a2 2 0 01-2-2V9.828a2 2 0 01.586-1.414l5-5H15a2 2 0 012 2v10a2 2 0 01-2 2h-5.414a2 2 0 01-1.414-.586l-2-2H7v4z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">{{ __('achievements.no_certificates_yet') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
