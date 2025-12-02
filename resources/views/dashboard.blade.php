@extends('layouts.app')

@section('title', 'OtakAtik Academy')

@section('content')
<div class="w-full bg-white">
    
    <section class="relative py-24 px-6 overflow-hidden">
        
        <div class="absolute inset-0 z-0">
            {{-- 
                TIPS: Simpan gambar background lo di: public/images/hero-bg.jpg
                Kalau nama filenya beda, ganti 'hero-bg.jpg' di bawah ini.
            --}}
            <img src="{{ asset('images/home_background.png') }}" 
                 alt="Hero Background" 
                 class="w-full h-full object-cover"
                 onerror="this.style.display='none'"> {{-- Kalau gambar gak ada, sembunyiin (pake warna backup) --}}
        </div>

        {{-- Ubah opacity (misal: /90 jadi /80) kalau mau lebih terang --}}
        <div class="absolute inset-0 z-0 bg-gradient-to-r from-blue-900/95 via-blue-800/90 to-blue-900/80 mix-blend-multiply"></div>
        
        <div class="absolute inset-0 -z-10 bg-blue-800"></div>

        <div class="max-w-6xl mx-auto text-center relative z-10 text-white">
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight tracking-tight drop-shadow-md">
                {{ __('messages.welcome_otakatik') }}
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-10 max-w-3xl mx-auto font-light leading-relaxed">
                {{ __('messages.platform_desc') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('course.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-10 rounded-full inline-block transition duration-300 shadow-lg hover:shadow-orange-500/30 transform hover:-translate-y-1">
                    {{ __('messages.explore_courses') }}
                </a>
                <a href="#features" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm border-2 border-white/30 text-white font-bold py-4 px-10 rounded-full inline-block transition duration-300">
                    {{ __('messages.learn_more') }}
                </a>
            </div>
        </div>
    </section>

    <section id="features" class="py-20 px-6 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-16 text-gray-800">
                {{ __('messages.why_choose_us') }}
                <div class="w-24 h-1.5 bg-orange-500 mx-auto mt-4 rounded-full"></div>
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition duration-300 border border-gray-100 group">
                    <div class="w-20 h-20 bg-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-orange-500 transition-colors duration-300">
                        <svg class="w-10 h-10 text-orange-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">{{ __('messages.flexible_learning') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.flexible_learning_desc') }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition duration-300 border border-gray-100 group">
                    <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 transition-colors duration-300">
                        <svg class="w-10 h-10 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">{{ __('messages.interactive_materials') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.interactive_materials_desc') }}</p>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition duration-300 border border-gray-100 group">
                    <div class="w-20 h-20 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-10 h-10 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">{{ __('messages.experienced_instructors') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.experienced_instructors_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 px-6 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-end mb-10 px-2">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">{{ __('messages.our_course_categories') }}</h2>
                    <p class="text-gray-500 mt-2 text-lg">Temukan topik menarik dan mulai perjalanan belajarmu</p>
                </div>
                <a href="{{ route('course.index') }}" class="group text-blue-600 font-bold hover:text-blue-800 flex items-center gap-2 transition bg-blue-50 px-4 py-2 rounded-lg hover:bg-blue-100">
                    Lihat Semua <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="relative group/slider">
                <button onclick="document.getElementById('cat-scroll').scrollBy({left: -320, behavior: 'smooth'})" 
                        class="absolute -left-5 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white shadow-xl rounded-full flex items-center justify-center text-gray-700 hover:text-blue-600 hover:scale-110 transition-all opacity-0 group-hover/slider:opacity-100 duration-300 hidden md:flex border border-gray-100">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <button onclick="document.getElementById('cat-scroll').scrollBy({left: 320, behavior: 'smooth'})" 
                        class="absolute -right-5 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white shadow-xl rounded-full flex items-center justify-center text-gray-700 hover:text-blue-600 hover:scale-110 transition-all opacity-0 group-hover/slider:opacity-100 duration-300 hidden md:flex border border-gray-100">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div id="cat-scroll" class="flex gap-6 overflow-x-auto pb-12 pt-2 px-2 hide-scrollbar snap-x scroll-smooth">
                    @forelse($categories as $category)
                        <a href="{{ route('course.index', ['category' => $category->slug]) }}" 
                           class="flex-shrink-0 w-72 md:w-80 snap-start group/card block relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 h-[400px]">
                            
                            <img src="{{ $category->thumbnail_url }}" 
                                 alt="{{ $category->name }}" 
                                 class="absolute inset-0 w-full h-full object-cover transform group-hover/card:scale-110 transition-transform duration-700"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($category->name) }}&background=random&color=fff&size=600'">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-80 group-hover/card:opacity-90 transition-opacity"></div>
                            
                            <div class="absolute inset-0 p-6 flex flex-col justify-end text-white z-10">
                                <div class="transform translate-y-2 group-hover/card:translate-y-0 transition-transform duration-300">
                                    <div class="bg-orange-500 w-10 h-1 mb-4 rounded-full"></div>
                                    <h3 class="text-2xl font-bold mb-2 shadow-black drop-shadow-md leading-tight">
                                        {{ $category->name }}
                                    </h3>
                                    <p class="text-gray-200 text-sm line-clamp-2 mb-4 opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 delay-75">
                                        {{ $category->description ?? 'Pelajari berbagai materi menarik di kategori ' . $category->name . ' dan tingkatkan keahlianmu.' }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between pt-4 border-t border-white/20">
                                        <span class="text-xs font-semibold bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                                            {{ $category->courses_count ?? 0 }} Kursus
                                        </span>
                                        <span class="w-8 h-8 rounded-full bg-white text-blue-600 flex items-center justify-center transform scale-0 group-hover/card:scale-100 transition-transform duration-300">
                                            <i class="fas fa-arrow-right"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="w-full text-center py-16 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 mx-auto">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Belum ada kategori</h3>
                            <p class="text-gray-500 mt-1">Kategori kursus akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-6 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <h2 class="text-4xl font-bold text-gray-800 mb-6 relative inline-block">
                        {{ __('messages.about_otakatik') }}
                        <div class="absolute bottom-1 left-0 w-full h-3 bg-blue-200 -z-10 transform -rotate-1"></div>
                    </h2>
                    <div class="space-y-4 text-lg text-gray-600">
                        <p class="leading-relaxed">{{ __('messages.about_desc_1') }}</p>
                        <p class="leading-relaxed">{{ __('messages.about_desc_2') }}</p>
                        <p class="leading-relaxed font-medium text-blue-800">{{ __('messages.about_desc_3') }}</p>
                    </div>
                    <div class="mt-8">
                        <a href="#" class="text-blue-600 font-bold hover:text-blue-800 flex items-center gap-2">
                            Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="order-1 md:order-2 bg-white rounded-3xl p-4 shadow-xl transform rotate-2 hover:rotate-0 transition duration-500">
                    <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 rounded-2xl h-80 sm:h-96 flex items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
                        <div class="text-center text-white relative z-10 p-6">
                            <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <p class="text-3xl font-bold mb-2">{{ __('messages.smart_learning') }}</p>
                            <p class="text-blue-100">{{ __('messages.best_investment') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-6 bg-blue-900 text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-blue-500 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full bg-indigo-500 blur-3xl"></div>
        </div>
        
        <div class="max-w-6xl mx-auto relative z-10">
            <h2 class="text-4xl font-bold text-center mb-16">{{ __('messages.otakatik_statistics') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-blue-800/50">
                <div class="p-4">
                    <div class="text-5xl font-bold mb-2 text-orange-400">1000+</div>
                    <p class="text-lg text-blue-200">{{ __('messages.active_students') }}</p>
                </div>
                <div class="p-4">
                    <div class="text-5xl font-bold mb-2 text-blue-300">50+</div>
                    <p class="text-lg text-blue-200">{{ __('messages.courses_available') }}</p>
                </div>
                <div class="p-4">
                    <div class="text-5xl font-bold mb-2 text-green-400">95%</div>
                    <p class="text-lg text-blue-200">{{ __('messages.student_satisfaction') }}</p>
                </div>
                <div class="p-4">
                    <div class="text-5xl font-bold mb-2 text-purple-400">24/7</div>
                    <p class="text-lg text-blue-200">{{ __('messages.support_available') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 px-6 bg-white relative">
        <div class="max-w-5xl mx-auto text-center bg-gray-50 rounded-[3rem] p-12 shadow-sm border border-gray-100">
            <h2 class="text-4xl font-bold text-gray-800 mb-6">{{ __('messages.ready_to_start') }}</h2>
            <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto">{{ __('messages.ready_to_start_desc') }}</p>
            <a href="{{ route('course.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-12 rounded-xl inline-block text-lg transition duration-300 shadow-xl shadow-orange-500/20 hover:-translate-y-1">
                {{ __('messages.start_learning_now') }}
            </a>
        </div>
    </section>
</div>

{{-- CSS KHUSUS HALAMAN INI --}}
<style>
    /* Sembunyikan scrollbar tapi tetep bisa scroll */
    .hide-scrollbar::-webkit-scrollbar {
        height: 8px;
    }
    .hide-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .hide-scrollbar::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
    }
    .hide-scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
    }
    
    /* Efek Smooth Scroll */
    html {
        scroll-behavior: smooth;
    }
</style>
@endsection