@extends('layouts.app')

@section('content')
@push('head')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
@endpush

<div class="bg-gray-50 min-h-screen pb-20">
    
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="max-w-3xl">
                <span class="px-3 py-1 bg-blue-700 text-blue-100 rounded-full text-xs font-semibold tracking-wide uppercase">{{ $course->type }} Course</span>
                <h1 class="text-4xl md:text-5xl font-bold mt-4 mb-4 leading-tight">{{ $course->title }}</h1>
                <p class="text-xl text-blue-100 mb-6 leading-relaxed">{{ Str::limit(strip_tags($course->description), 150) }}</p>
                
                <div class="flex items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        @if($course->instructor && $course->instructor->profile_picture)
                            <img src="{{ Storage::url($course->instructor->profile_picture) }}" class="w-10 h-10 rounded-full border-2 border-blue-400">
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center font-bold">
                                {{ substr($course->instructor->name ?? 'I', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-blue-300 text-xs">Instructor</p>
                            <p class="font-semibold">{{ $course->instructor->name ?? 'OtakAtik Team' }}</p>
                        </div>
                    </div>
                    <div class="h-8 w-px bg-blue-700"></div>
                    <div>
                        <p class="text-blue-300 text-xs">Last Updated</p>
                        <p class="font-semibold">{{ $course->updated_at->format('M Y') }}</p>
                    </div>
                    <div class="h-8 w-px bg-blue-700"></div>
                    <div class="flex items-center gap-1">
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="font-bold">4.8</span>
                        <span class="text-blue-300">(120 reviews)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 -mt-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Tentang Kursus Ini</h3>
                    <div class="prose max-w-none text-gray-600">
                        {!! nl2br(e($course->description)) !!}
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-list-ul text-blue-600"></i> Materi Pembelajaran
                    </h3>
                    
                    <div class="flex gap-4 text-sm text-gray-500 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <span><i class="fas fa-layer-group text-gray-400 mr-1"></i> {{ $course->modules->count() }} Modul</span>
                        <span><i class="fas fa-play-circle text-gray-400 mr-1"></i> {{ $course->materials_count ?? $course->modules->sum(fn($m) => $m->materials->count()) }} Konten</span>
                        <span><i class="fas fa-clock text-gray-400 mr-1"></i> {{ $course->duration_minutes ?? 'Start anytime' }}</span>
                    </div>

                    <div class="space-y-4" x-data="{ activeModule: 0 }">
                        @forelse($course->modules as $index => $module)
                        <div class="border border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:border-blue-300">
                            <button 
                                @click="activeModule === {{ $index }} ? activeModule = null : activeModule = {{ $index }}"
                                class="w-full flex items-center justify-between p-5 bg-white hover:bg-gray-50 transition text-left focus:outline-none"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-lg">{{ $module->title }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $module->materials->count() }} Materi • {{ $module->quizzes->count() }} Quiz
                                        </p>
                                    </div>
                                </div>
                                <div class="transform transition-transform duration-300 text-gray-400" 
                                     :class="{'rotate-180 text-blue-600': activeModule === {{ $index }}}">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>

                            <div x-show="activeModule === {{ $index }}" 
                                 x-collapse
                                 class="bg-gray-50 border-t border-gray-100">
                                <div class="p-2 space-y-1">
                                    @foreach($module->materials as $material)
                                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white hover:shadow-sm transition ml-2">
                                        <i class="fas {{ $material->type == 'video' ? 'fa-play-circle text-red-500' : 'fa-file-alt text-blue-500' }} w-5 text-center"></i>
                                        <span class="text-sm text-gray-700 flex-1">{{ $material->title }}</span>
                                        <i class="fas fa-lock text-gray-300 text-xs"></i>
                                    </div>
                                    @endforeach
                                    
                                    @foreach($module->quizzes as $quiz)
                                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white hover:shadow-sm transition ml-2">
                                        <i class="fas fa-question-circle text-purple-500 w-5 text-center"></i>
                                        <span class="text-sm text-gray-700 flex-1">{{ $quiz->title }}</span>
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-0.5 rounded">Quiz</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-200 rounded-xl">
                            Belum ada kurikulum yang dipublikasikan.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-24">
                    
                    <div class="rounded-xl overflow-hidden mb-6 relative group cursor-pointer shadow-sm">
                        @if($course->image_url)
                            <img src="{{ $course->image_url }}" class="w-full h-48 object-cover transform group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                                <i class="fas fa-play-circle text-white text-5xl opacity-80 group-hover:scale-110 transition"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-10 transition"></div>
                    </div>

                    @if($course->price == 0)
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">GRATIS</h2>
                    @else
                        <div class="flex items-end gap-3 mb-6">
                            <h2 class="text-3xl font-bold text-gray-900">
                                {{ 'Rp ' . number_format($course->price - ($course->price * ($course->discount_percent/100)), 0, ',', '.') }}
                            </h2>
                            @if($course->discount_percent > 0)
                                <span class="text-gray-400 line-through mb-1 text-sm">
                                    {{ 'Rp ' . number_format($course->price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded-full mb-1">
                                    {{ $course->discount_percent }}% OFF
                                </span>
                            @endif
                        </div>
                    @endif

                    @php
                        $userRegistration = null;
                        if(Auth::check()) {
                            $userRegistration = \App\Models\CourseRegistration::where('user_id', Auth::id())
                                ->where('course_id', $course->id)
                                ->where('status', 'paid')
                                ->first();
                        }
                    @endphp

                    @if($userRegistration)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-6 text-center">
                            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-check text-xl"></i>
                            </div>
                            <h4 class="font-bold text-green-800 text-sm">Anda Terdaftar!</h4>
                            
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-green-700 mb-1">
                                    <span>Progress</span>
                                    <span class="font-bold">{{ $userRegistration->progress }}%</span>
                                </div>
                                <div class="w-full bg-green-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $userRegistration->progress }}%"></div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.learning.index', $course->id) }}" 
                           class="block w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                            <i class="fas fa-play"></i> Lanjut Belajar
                        </a>
                    @else
                        <form action="{{ route('checkout.show', $course->id) }}" method="GET">
                            <button type="submit" class="w-full py-3.5 bg-orange-600 hover:bg-orange-700 text-white font-bold text-lg rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 mb-3">
                                Enroll Now
                            </button>
                        </form>
                        <p class="text-center text-xs text-gray-500">Akses selamanya • Sertifikat Selesai</p>
                    @endif

                    <div class="mt-8 space-y-4 pt-6 border-t border-gray-100">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i class="fas fa-infinity w-5 text-center text-gray-400"></i>
                            <span>Akses seumur hidup</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i class="fas fa-mobile-alt w-5 text-center text-gray-400"></i>
                            <span>Akses di HP dan TV</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i class="fas fa-certificate w-5 text-center text-gray-400"></i>
                            <span>Sertifikat kelulusan</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <i class="fas fa-file-download w-5 text-center text-gray-400"></i>
                            <span>Download materi sumber</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection