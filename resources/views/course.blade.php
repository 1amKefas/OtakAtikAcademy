@extends('layouts.app')

@section('title', 'Our Courses - OtakAtik Academy')

@section('content')
<section class="pt-32 pb-20 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Our Courses</h1>
            <p class="text-gray-600 text-lg">Pilih course yang sesuai dengan kebutuhan belajar Anda</p>
        </div>

        @if($courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($courses as $course)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all">
                    <!-- Course Header -->
                    <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative flex items-center justify-center">
                        <h3 class="text-xl font-bold text-white text-center px-4">{{ $course->title }}</h3>
                    </div>
                    
                    <!-- Course Content -->
                    <div class="p-6">
                        <!-- Type and Status -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-4 py-1 rounded-full text-xs font-semibold"
                                @class([
                                    'bg-blue-100 text-blue-800' => $course->type === 'Full Online',
                                    'bg-yellow-100 text-yellow-800' => $course->type === 'Hybrid',
                                    'bg-green-100 text-green-800' => $course->type === 'Tatap Muka',
                                ])>
                                {{ $course->type }}
                            </span>
                            @if($course->is_active)
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                    Available
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">
                                    Not Available
                                </span>
                            @endif
                        </div>
                        
                        <!-- Description -->
                        <p class="text-gray-700 mb-4">{{ $course->description }}</p>
                        
                        <!-- Availability Status -->
                        @if($course->has_available_slots)
                            <div class="flex items-center gap-2 text-green-600 mb-4">
                                <i class="fas fa-check-circle"></i>
                                <span class="font-medium text-sm">Masih tersedia</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-red-600 mb-4">
                                <i class="fas fa-times-circle"></i>
                                <span class="font-medium text-sm">Kuota penuh</span>
                            </div>
                        @endif
                        
                        <!-- Price -->
                        <div class="mb-6">
                            <span class="text-2xl font-bold text-gray-800">
                                {{ $course->formatted_price ?? 'Hubungi Admin' }}
                            </span>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="{{ route('course.show.detail', $course->id) }}" 
                               class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-3 px-4 rounded-lg transition-all font-medium">
                                <i class="fas fa-eye mr-2"></i> Lihat Detail
                            </a>
                            @if($course->is_active && $course->has_available_slots)
                                <a href="{{ route('checkout.show', $course->id) }}" 
                                   class="bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg transition-all font-medium">
                                    <i class="fas fa-shopping-cart mr-2"></i> Daftar
                                </a>
                            @else
                                <button disabled 
                                        class="bg-gray-400 text-white py-3 px-4 rounded-lg font-medium cursor-not-allowed">
                                    <i class="fas fa-clock mr-2"></i> Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-32 bg-white rounded-2xl shadow-lg">
                <div class="max-w-md mx-auto">
                    <!-- Icon with gradient background -->
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-6xl">📚</span>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-3xl font-bold text-gray-800 mb-3">Belum Ada Course Tersedia</h3>
                    <p class="text-gray-600 text-base mb-8">Saat ini belum ada course yang aktif. Silakan cek kembali nanti atau hubungi admin kami.</p>
                    
                    <!-- CTA Buttons -->
                    <a href="/" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-all font-medium mb-4">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
