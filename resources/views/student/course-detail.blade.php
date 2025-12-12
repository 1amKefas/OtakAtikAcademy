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
                        <span class="font-bold text-white">{{ number_format($course->average_rating, 1) }}</span>
                        <span class="text-slate-400 text-xs">({{ $course->rating_count }} reviews)</span>
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

                {{-- Taruh Disini (Setelah Forum Diskusi) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Apa Kata Teman Sekelas?</h3>
                    {{-- Paste kode foreach reviews disini... --}}
                     <div class="grid grid-cols-1 gap-6">
                        @forelse($course->reviews as $review)
                        <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                            <div class="flex items-start gap-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=random" class="w-10 h-10 rounded-full">
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-gray-900 text-sm">{{ $review->user->name }}</h4>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex text-yellow-400 text-xs my-1">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-gray-600 text-sm mt-2">"{{ $review->review }}"</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <p class="text-gray-400 italic">Belum ada ulasan.</p>
                        </div>
                        @endforelse
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

                        {{-- FITUR REVIEW & RATING --}}
            <div class="mt-8 pt-8 border-t border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wider">Rating Kursus</h4>
                
                @if($course->isReviewedBy(auth()->id()))
                    {{-- Tampilan Kalau Sudah Review --}}
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl text-center">
                        <div class="flex justify-center text-yellow-400 mb-2">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star {{ $i <= $course->reviews->where('user_id', auth()->id())->first()->rating ? '' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <p class="text-xs text-yellow-800 font-medium">Anda sudah memberi ulasan.</p>
                    </div>

                @elseif($registration->progress >= 100)
                    {{-- Form Review (Hanya muncul jika Progress 100%) --}}
                    <form action="{{ route('student.course.review', $course->id) }}" method="POST" class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm">
                        @csrf
                        <div class="mb-3 text-center" x-data="{ rating: 0, hover: 0 }">
                            <label class="block text-xs font-bold text-gray-500 mb-2">Beri Bintang</label>
                            <div class="flex justify-center gap-1 cursor-pointer">
                                <template x-for="star in 5">
                                    <i class="fas fa-star text-lg transition-colors"
                                       :class="(hover || rating) >= star ? 'text-yellow-400' : 'text-gray-300'"
                                       @mouseenter="hover = star"
                                       @mouseleave="hover = 0"
                                       @click="rating = star"></i>
                                </template>
                            </div>
                            <input type="hidden" name="rating" :value="rating" required>
                        </div>
                        
                        <div class="mb-3">
                            <textarea name="review" rows="3" placeholder="Tulis pengalaman belajarmu..." class="w-full text-xs p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 rounded-lg transition">
                            Kirim Ulasan
                        </button>
                    </form>
                @else
                    {{-- Pesan Kalau Belum Selesai --}}
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded-xl text-center">
                        <i class="fas fa-lock text-gray-300 text-xl mb-2"></i>
                        <p class="text-xs text-gray-500">Selesaikan kursus 100% untuk membuka fitur ulasan.</p>
                    </div>
                @endif
            </div>

                        @if($userRegistration->course_class_id && in_array(strtolower($course->type), ['hybrid', 'offline', 'tatap muka']))
                            <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-4 flex items-start gap-3 animate-fade-in">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold text-purple-600 uppercase tracking-wider mb-0.5">Kelas Terdaftar</p>
                                    <h4 class="font-bold text-gray-900 text-base truncate">{{ $userRegistration->courseClass->name ?? 'Nama Kelas' }}</h4>
                                    @if($userRegistration->courseClass->instructor)
                                        <p class="text-xs text-gray-500 mt-1 truncate">
                                            <span class="font-medium text-gray-400">PJ:</span> {{ $userRegistration->courseClass->instructor->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('student.learning.index', $course->id) }}" 
                           class="block w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-blue-200 transition transform hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                            <span>Lanjut Belajar</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>

                        @if($userRegistration->progress == 100)
                        <div class="mt-6 mb-6 animate-fade-in-up">
                            <a href="{{ route('student.certificate.download', $course->id) }}" 
                               class="flex items-center justify-center gap-3 w-full py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-yellow-500/30 transition transform hover:-translate-y-1 group">
                                <i class="fas fa-certificate text-2xl group-hover:rotate-12 transition-transform"></i>
                                <span>Download Sertifikat</span>
                            </a>
                            <p class="text-xs text-gray-500 text-center mt-3 flex items-center justify-center gap-1">
                                <i class="fas fa-medal text-yellow-500"></i> 
                                Selamat! Anda telah menyelesaikan kursus ini.
                            </p>
                        </div>
                        @endif

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

                    @if(in_array(strtolower($course->type), ['hybrid', 'offline', 'tatap muka']))
                        <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                            @php
                                // Cek apakah sudah pernah mengajukan refund
                                $existingRefund = \App\Models\Refund::where('course_registration_id', $registration->id)->first();
                            @endphp

                            @if(!$existingRefund)
                                <p class="text-xs text-gray-500 mb-2">Tidak puas dengan kursus ini?</p>
                                <a href="{{ route('refund.create', $registration->id) }}" class="text-sm font-medium text-red-500 hover:text-red-700 hover:underline transition">
                                    Ajukan Pengembalian Dana (Refund)
                                </a>
                            @else
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 inline-block w-full">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Status Refund</p>
                                    
                                    @if($existingRefund->status == 'pending')
                                        <span class="text-sm font-bold text-yellow-600"><i class="fas fa-clock mr-1"></i> Menunggu Konfirmasi</span>
                                    @elseif($existingRefund->status == 'approved')
                                        <span class="text-sm font-bold text-green-600"><i class="fas fa-check-circle mr-1"></i> Disetujui</span>
                                    @elseif($existingRefund->status == 'rejected')
                                        <span class="text-sm font-bold text-red-600"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                    
                </div>
            </div>

        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $course->name }}</h1>
            <p class="text-gray-500 mt-1">Masa Aktif Course: {{ \Carbon\Carbon::parse($course->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($course->end_date)->format('d M Y') }}</p>
        </div>
        
        <div class="text-right">
            @if($registration->has_active_access)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Akses Aktif
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    Akses Kadaluarsa
                </span>
                <a href="{{ route('student.course.renew', $course->id) }}" class="block mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-bold underline">
                    Perpanjang Sekarang
                </a>
            @endif
        </div>
    </div>

    <hr class="my-4 border-gray-100">

    @if($registration->has_active_access)
        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <h3 class="text-sm font-semibold text-indigo-900 uppercase tracking-wider">Sisa Waktu Akses Anda</h3>
                <p class="text-xs text-indigo-600">
                    @if($registration->access_expires_at)
                        Berakhir pada: {{ $registration->access_expires_at->format('d M Y, H:i:s') }}
                    @else
                        <span class="font-bold text-green-600">Akses Seumur Hidup</span>
                    @endif
                </p>
            </div>
            
            <div class="flex gap-4 text-center">
                <div class="bg-white p-2 rounded shadow-sm min-w-[70px]">
                    <span id="countdown-days" class="block text-2xl font-bold text-indigo-700">00</span>
                    <span class="text-xs text-gray-500">Hari</span>
                </div>
                <div class="bg-white p-2 rounded shadow-sm min-w-[70px]">
                    <span id="countdown-hours" class="block text-2xl font-bold text-indigo-700">00</span>
                    <span class="text-xs text-gray-500">Jam</span>
                </div>
                <div class="bg-white p-2 rounded shadow-sm min-w-[70px]">
                    <span id="countdown-minutes" class="block text-2xl font-bold text-indigo-700">00</span>
                    <span class="text-xs text-gray-500">Menit</span>
                </div>
                <div class="bg-white p-2 rounded shadow-sm min-w-[70px]">
                    <span id="countdown-seconds" class="block text-2xl font-bold text-red-600">00</span>
                    <span class="text-xs text-gray-500">Detik</span>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil sisa detik dari backend
        let remainingSeconds = {{ $registration->access_expires_at ? $registration->remaining_seconds : 0 }};
        
        const daysEl = document.getElementById('countdown-days');
        const hoursEl = document.getElementById('countdown-hours');
        const minutesEl = document.getElementById('countdown-minutes');
        const secondsEl = document.getElementById('countdown-seconds');

        function updateCountdown() {
            if (remainingSeconds <= 0) {
                // Jika waktu habis, reload halaman untuk update status jadi expired
                window.location.reload();
                return;
            }

            const days = Math.floor(remainingSeconds / (3600 * 24));
            const hours = Math.floor((remainingSeconds % (3600 * 24)) / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = Math.floor(remainingSeconds % 60);

            if(daysEl) daysEl.innerText = String(days).padStart(2, '0');
            if(hoursEl) hoursEl.innerText = String(hours).padStart(2, '0');
            if(minutesEl) minutesEl.innerText = String(minutes).padStart(2, '0');
            if(secondsEl) secondsEl.innerText = String(seconds).padStart(2, '0');

            remainingSeconds--;
        }

        // Update setiap 1 detik
        setInterval(updateCountdown, 1000);
        updateCountdown(); // Run immediately
    });
</script>
@endsection