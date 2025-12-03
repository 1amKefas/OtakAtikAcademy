@extends('layouts.app')

@section('content')
@push('head')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
@endpush

<div class="bg-gray-50 min-h-screen pb-20">
    
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="max-w-3xl">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('course.index') }}" class="text-slate-300 hover:text-white text-sm flex items-center gap-1 transition">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <span class="px-3 py-1 bg-blue-600/20 border border-blue-500/30 text-blue-300 rounded-full text-xs font-semibold tracking-wide uppercase">
                        {{ $course->type }}
                    </span>
                </div>
                
                <h1 class="text-3xl md:text-5xl font-bold mb-6 leading-tight">{{ $course->title }}</h1>
                <p class="text-lg text-slate-300 mb-8 leading-relaxed max-w-2xl">
                    {{ Str::limit(strip_tags($course->description), 180) }}
                </p>
                
                <div class="flex flex-wrap items-center gap-6 text-sm border-t border-white/10 pt-6">
                    <div class="flex items-center gap-3">
                        @if($course->instructor && $course->instructor->profile_picture)
                            <img src="{{ Storage::url($course->instructor->profile_picture) }}" class="w-10 h-10 rounded-full border-2 border-blue-400">
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white shadow-lg">
                                {{ substr($course->instructor->name ?? 'O', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-slate-400 text-xs uppercase tracking-wider">Instructor</p>
                            <p class="font-medium text-white">{{ $course->instructor->name ?? 'OtakAtik Team' }}</p>
                        </div>
                    </div>
                    
                    <div class="hidden md:block h-8 w-px bg-white/10"></div>
                    
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-wider">Last Updated</p>
                        <p class="font-medium text-white">{{ $course->updated_at->format('M Y') }}</p>
                    </div>
                    
                    <div class="hidden md:block h-8 w-px bg-white/10"></div>
                    
                    <div class="flex items-center gap-2 bg-white/5 px-3 py-1.5 rounded-lg">
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <span class="font-bold text-white">{{ number_format($course->average_rating ?? 4.8, 1) }}</span>
                        <span class="text-slate-400 text-xs">({{ $course->rating_count ?? 12 }} reviews)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 -mt-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Tentang Kursus Ini</h3>
                    <div class="prose max-w-none text-gray-600 leading-relaxed">
                        {!! nl2br(e($course->description)) !!}
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-book-open text-blue-600"></i> Materi Pembelajaran
                        </h3>
                        <span class="text-sm text-gray-500">{{ $course->modules->count() }} Modul • {{ $course->materials_count ?? $course->modules->sum(fn($m) => $m->materials->count()) }} Konten</span>
                    </div>

                    <div class="space-y-3" x-data="{ activeModule: 0 }">
                        @forelse($course->modules as $index => $module)
                        <div class="border border-gray-200 rounded-xl overflow-hidden transition-all duration-200 hover:border-blue-300 bg-white">
                            <button 
                                @click="activeModule === {{ $index }} ? activeModule = null : activeModule = {{ $index }}"
                                class="w-full flex items-center justify-between p-4 bg-gray-50/50 hover:bg-blue-50/30 transition text-left focus:outline-none"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <h4 class="font-semibold text-gray-800 text-base">{{ $module->title }}</h4>
                                </div>
                                <div class="transform transition-transform duration-300 text-gray-400" 
                                     :class="{'rotate-180 text-blue-600': activeModule === {{ $index }}}">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>

                            <div x-show="activeModule === {{ $index }}" 
                                 x-collapse
                                 class="bg-white border-t border-gray-100">
                                <div class="p-2 space-y-1">
                                    @foreach($module->materials as $material)
                                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition ml-2 group">
                                        <div class="w-6 text-center">
                                            <i class="fas {{ $material->type == 'video' ? 'fa-play-circle text-red-500' : 'fa-file-alt text-blue-500' }} text-sm group-hover:scale-110 transition"></i>
                                        </div>
                                        <span class="text-sm text-gray-600 flex-1 group-hover:text-gray-900">{{ $material->title }}</span>
                                        
                                        @if($material->type == 'video')
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Video</span>
                                        @else
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Bacaan</span>
                                        @endif
                                    </div>
                                    @endforeach
                                    
                                    @foreach($module->quizzes as $quiz)
                                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition ml-2">
                                        <div class="w-6 text-center">
                                            <i class="fas fa-question-circle text-purple-500 text-sm"></i>
                                        </div>
                                        <span class="text-sm text-gray-600 flex-1">{{ $quiz->title }}</span>
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-0.5 rounded font-medium">Quiz</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12 bg-white border-2 border-dashed border-gray-200 rounded-xl">
                            <i class="fas fa-box-open text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Materi kursus sedang disusun.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mt-6 hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-start gap-5">
                            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-comments text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Forum Diskusi Kelas</h3>
                                <p class="text-gray-600 text-sm max-w-md">
                                    Punya pertanyaan atau kendala saat belajar? Diskusikan langsung dengan instruktur dan teman sekelas di sini.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('student.forum.index', $course->id) }}" class="w-full md:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                            <span>Buka Forum</span>
                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-28 z-10">
                    
                    <div class="rounded-xl overflow-hidden mb-6 relative group cursor-pointer bg-gray-900 shadow-lg aspect-video">
                        @if($course->image_url)
                            <img src="{{ $course->image_url }}" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-play-circle text-white/80 text-6xl group-hover:scale-110 transition"></i>
                            </div>
                        @endif
                        
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:bg-white/30 transition">
                                <i class="fas fa-play text-white text-2xl ml-1"></i>
                            </div>
                        </div>
                        <span class="absolute bottom-3 right-3 bg-black/70 text-white text-xs font-bold px-2 py-1 rounded">Preview</span>
                    </div>

                    <div class="mb-8">
                        @if($course->price == 0)
                            <h2 class="text-4xl font-bold text-gray-900 mb-2">GRATIS</h2>
                        @else
                            <div class="flex items-end gap-3 flex-wrap">
                                <h2 class="text-3xl font-bold text-gray-900">
                                    {{ 'Rp ' . number_format($course->price - ($course->price * ($course->discount_percent/100)), 0, ',', '.') }}
                                </h2>
                                @if($course->discount_percent > 0)
                                    <span class="text-gray-400 line-through mb-1.5 text-base">
                                        {{ 'Rp ' . number_format($course->price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded-full mb-2">
                                        {{ $course->discount_percent }}% OFF
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

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
                            <div class="flex items-center justify-center gap-2 text-green-700 font-bold mb-3">
                                <i class="fas fa-check-circle text-xl"></i>
                                <span>Anda Terdaftar</span>
                            </div>
                            
                            <div class="w-full bg-green-200 rounded-full h-2.5 mb-1">
                                <div class="bg-green-600 h-2.5 rounded-full transition-all duration-1000" style="width: {{ $userRegistration->progress }}%"></div>
                            </div>
                            <p class="text-xs text-green-600 text-right">{{ $userRegistration->progress }}% Selesai</p>
                        </div>

                        <a href="{{ route('student.learning.index', $course->id) }}" 
                           class="block w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-blue-200 transition transform hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                            <span>Lanjut Belajar</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>

                    @else
                        <form action="{{ route('checkout.show', $course->id) }}" method="GET">
                            <button type="submit" class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-orange-200 transition transform hover:-translate-y-0.5 mb-4 flex items-center justify-center gap-2">
                                <span>Enroll Now</span>
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </form>
                        
                        <p class="text-center text-xs text-gray-500 mb-6">
                            <i class="fas fa-shield-alt mr-1"></i> 30-Day Money-Back Guarantee
                        </p>
                    @endif

                    <div class="mt-8 space-y-4 pt-6 border-t border-gray-100">
                        <h4 class="font-bold text-gray-800 text-sm">Kursus ini mencakup:</h4>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="fas fa-video w-5 text-center text-gray-400"></i>
                                <span>{{ $course->duration_hours ?? '10+' }} jam video on-demand</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="fas fa-file-download w-5 text-center text-gray-400"></i>
                                <span>Bahan bacaan & download</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="fas fa-mobile-alt w-5 text-center text-gray-400"></i>
                                <span>Akses di Perangkat Mobile</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="fas fa-certificate w-5 text-center text-gray-400"></i>
                                <span>Sertifikat Kelulusan</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 flex justify-center gap-4">
                        <button class="text-gray-400 hover:text-blue-600 transition"><i class="fab fa-facebook text-xl"></i></button>
                        <button class="text-gray-400 hover:text-sky-500 transition"><i class="fab fa-twitter text-xl"></i></button>
                        <button class="text-gray-400 hover:text-green-600 transition"><i class="fab fa-whatsapp text-xl"></i></button>
                        <button class="text-gray-400 hover:text-gray-600 transition"><i class="fas fa-link text-xl"></i></button>
                    </div>
                    

                </div>
            </div>

        </div>
    </div>
</div>
@endsection