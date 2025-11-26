@extends('layouts.app')

@section('title', 'OtakAtik Academy')

@section('content')
<div class="w-full bg-white">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20 px-6">
        <div class="max-w-6xl mx-auto text-center">
            <h1 class="text-5xl font-bold mb-6">{{ __('messages.welcome_otakatik') }}</h1>
            <p class="text-2xl text-blue-100 mb-8">{{ __('messages.platform_desc') }}</p>
            <div class="flex gap-4 justify-center">
                <a href="/course" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg inline-block transition duration-300">
                    {{ __('messages.explore_courses') }}
                </a>
                <a href="#features" class="bg-white hover:bg-gray-100 text-blue-600 font-bold py-3 px-8 rounded-lg inline-block transition duration-300">
                    {{ __('messages.learn_more') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-6 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-16 text-gray-800">{{ __('messages.why_choose_us') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition duration-300">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">{{ __('messages.flexible_learning') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.flexible_learning_desc') }}</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition duration-300">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">{{ __('messages.interactive_materials') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.interactive_materials_desc') }}</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition duration-300">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">{{ __('messages.experienced_instructors') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.experienced_instructors_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-20 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">{{ __('messages.about_otakatik') }}</h2>
                    <p class="text-lg text-gray-700 mb-4 leading-relaxed">
                        {{ __('messages.about_desc_1') }}
                    </p>
                    <p class="text-lg text-gray-700 mb-4 leading-relaxed">
                        {{ __('messages.about_desc_2') }}
                    </p>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        {{ __('messages.about_desc_3') }}
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-orange-500 rounded-2xl h-96 shadow-xl flex items-center justify-center">
                    <div class="text-center text-white">
                        <svg class="w-32 h-32 mx-auto mb-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        <p class="text-2xl font-bold">{{ __('messages.smart_learning') }}</p>
                        <p class="text-blue-100 mt-2">{{ __('messages.best_investment') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-20 px-6 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-12">{{ __('messages.otakatik_statistics') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-5xl font-bold mb-2">1000+</div>
                    <p class="text-xl text-blue-100">{{ __('messages.active_students') }}</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold mb-2">50+</div>
                    <p class="text-xl text-blue-100">{{ __('messages.courses_available') }}</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold mb-2">95%</div>
                    <p class="text-xl text-blue-100">{{ __('messages.student_satisfaction') }}</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold mb-2">24/7</div>
                    <p class="text-xl text-blue-100">{{ __('messages.support_available') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Preview Section -->
    <section class="py-20 px-6 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-800">{{ __('messages.our_course_categories') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Course Category 1 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-32 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ __('messages.mathematics') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('messages.mathematics_desc') }}</p>
                        <a href="/course" class="text-blue-600 font-bold hover:text-blue-800">{{ __('messages.view_course') }}</a>
                    </div>
                </div>

                <!-- Course Category 2 -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-32 flex items-center justify-center">
                        <div class="text-5xl">🔬</div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ __('messages.science') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('messages.science_desc') }}</p>
                        <a href="/course" class="text-blue-600 font-bold hover:text-blue-800">{{ __('messages.view_course') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6 bg-white">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-6">{{ __('messages.ready_to_start') }}</h2>
            <p class="text-xl text-gray-600 mb-8">{{ __('messages.ready_to_start_desc') }}</p>
            <a href="/course" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-12 rounded-lg inline-block text-lg transition duration-300 shadow-lg">
                {{ __('messages.start_learning_now') }}
            </a>
        </div>
    </section>

   
</div>
@endsection
