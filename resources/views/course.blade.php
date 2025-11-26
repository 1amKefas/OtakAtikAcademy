@extends('layouts.app')

@section('title', 'Katalog Course - OtakAtik Academy')

@section('content')
<section class="pt-32 pb-20 px-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Eksplorasi Skill Baru</h1>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">Tingkatkan karirmu dengan course terbaik dari para ahli di industri.</p>
        </div>

        @if($courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($courses as $course)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full border border-gray-100 group overflow-hidden">
                    
                    <div class="relative h-48 overflow-hidden bg-gray-200">
                        @if($course->image_url)
                            <img src="{{ $course->image_url }}" alt="{{ $course->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-600 to-indigo-800 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-5xl opacity-20 transform -rotate-12"></i>
                            </div>
                        @endif

                        <div class="absolute top-3 left-3 flex gap-2">
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-white/95 text-gray-800 shadow-sm backdrop-blur-sm">
                                {{ $course->type }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-1">
                        
                        <div class="flex items-center gap-2 mb-3">
                            @if($course->instructor)
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&background=random&color=fff" class="w-6 h-6 rounded-full border border-white shadow-sm">
                                <span class="text-xs font-semibold text-gray-500 truncate">{{ $course->instructor->name }}</span>
                            @else
                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-[10px] text-blue-600 font-bold">OA</div>
                                <span class="text-xs font-semibold text-gray-500">OtakAtik Team</span>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 leading-snug mb-2 line-clamp-2 min-h-[3.5rem]" title="{{ $course->title }}">
                            {{ $course->title }}
                        </h3>
                        
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ $course->description }}
                        </p>
                        
                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-4 mt-auto">
                            <div class="flex items-center text-yellow-500">
                                <i class="fas fa-star text-[10px] mr-1"></i>
                                <span class="font-bold text-gray-700">4.8</span>
                                <span class="text-gray-400 ml-1">(120)</span>
                            </div>
                            <span class="text-gray-300">|</span>
                            <span class="flex items-center">
                                <i class="far fa-clock mr-1"></i> {{ $course->duration_days }} Hari
                            </span>
                        </div>

                        <hr class="border-gray-100 mb-4">

                        <div class="flex items-center justify-between">
                            <div>
                                @if($course->discount_percent > 0)
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-400 line-through">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                        <span class="text-base font-bold text-gray-900">Rp {{ number_format($course->price * (1 - $course->discount_percent/100), 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <span class="text-base font-bold text-gray-900">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            
                            @if($course->is_active && $course->has_available_slots)
                                <a href="{{ route('course.show.detail', $course->id) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm hover:shadow-md">
                                    Detail <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            @else
                                <button disabled class="bg-gray-100 text-gray-400 text-sm font-semibold py-2 px-4 rounded-lg cursor-not-allowed">
                                    Penuh
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-24">
                <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-3xl text-blue-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada course ditemukan</h3>
                <p class="text-gray-500">Coba cari dengan kata kunci lain atau kembali lagi nanti.</p>
            </div>
        @endif
    </div>
</section>
@endsection