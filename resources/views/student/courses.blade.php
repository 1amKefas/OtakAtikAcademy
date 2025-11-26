@extends('layouts.app')

@section('title', 'Kursus Saya - OtakAtik Academy')

@section('content')
<div class="bg-gray-50 min-h-screen pt-24 pb-12"> <div class="max-w-7xl mx-auto px-6 mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Pembelajaran Saya</h1>
        <p class="text-gray-500 mt-2">Lanjutkan progress belajar Anda hari ini.</p>
    </div>

    <div class="max-w-7xl mx-auto px-6">
        @if ($courses->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book-reader text-3xl text-blue-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada kursus aktif</h3>
                <p class="text-gray-500 mb-6">Anda belum mendaftar di kursus manapun saat ini.</p>
                <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-compass"></i> Jelajahi Katalog
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courses as $registration)
                    @if ($registration->course)
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 flex flex-col overflow-hidden h-full">
                            
                            <div class="relative h-40 bg-gray-200 group">
                                @if ($registration->course->image_url)
                                    <img src="{{ $registration->course->image_url }}" alt="{{ $registration->course->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full bg-gradient-to-r from-gray-800 to-gray-900 flex items-center justify-center">
                                        <i class="fas fa-laptop-code text-white text-4xl opacity-50"></i>
                                    </div>
                                @endif
                                
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider shadow-sm
                                        {{ $registration->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                          ($registration->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $registration->status === 'paid' ? 'Aktif' : ucfirst($registration->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="text-lg font-bold text-gray-900 leading-tight mb-2 line-clamp-2 h-12" title="{{ $registration->course->title }}">
                                    {{ $registration->course->title }}
                                </h3>

                                <p class="text-xs text-gray-500 mb-4 flex items-center gap-1">
                                    <i class="fas fa-chalkboard-teacher text-gray-400"></i>
                                    {{ $registration->course->instructor ? $registration->course->instructor->name : 'OtakAtik Academy' }}
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="text-xs font-semibold text-gray-700">Progress Belajar</span>
                                        <span class="text-xs font-bold text-blue-600">{{ $registration->progress ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4 overflow-hidden">
                                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $registration->progress ?? 0 }}%"></div>
                                    </div>

                                    <a href="{{ route('student.course.detail', $registration->id) }}" 
                                       class="block w-full text-center py-2.5 rounded-lg font-semibold text-sm transition-colors border border-blue-600 text-blue-600 hover:bg-blue-50">
                                        @if($registration->progress > 0)
                                            Lanjutkan Belajar
                                        @else
                                            Mulai Belajar
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-8">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection