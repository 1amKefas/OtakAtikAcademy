@extends('layouts.app')

@section('title', 'Katalog Course - OtakAtik Academy')

@section('content')

<section class="pt-32 pb-20 px-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Eksplorasi Skill Baru</h1>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">Tingkatkan karirmu dengan course terbaik dari para ahli di industri.</p>
        </div>

        <div class="max-w-xl mx-auto mb-12 relative" 
             x-data="courseSearch('{{ request('search') }}')"
             @click.outside="showSuggestions = false">
            
            <form action="{{ route('course.index') }}" method="GET" class="relative z-20">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                
                <input type="text" 
                       name="search" 
                       x-model="query"
                       @input.debounce.300ms="search()"
                       @keydown.escape="showSuggestions = false"
                       @keydown.arrow-down.prevent="active < suggestions.length - 1 ? active++ : active = 0"
                       @keydown.arrow-up.prevent="active > 0 ? active-- : active = suggestions.length - 1"
                       @keydown.enter="if(active >= 0) { $event.preventDefault(); select(suggestions[active].id); }"
                       placeholder="Cari kursus yang kamu inginkan..." 
                       autocomplete="off"
                       class="w-full px-6 py-4 rounded-full border border-gray-200 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none pl-14 text-gray-700 transition-all hover:shadow-md">
                
                <div class="absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <i class="fas fa-search text-xl"></i>
                </div>

                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center">
                    <button type="button" x-show="query.length > 0" 
                            @click="clear(); $el.form.submit()" 
                            class="text-gray-400 hover:text-red-500 mr-3 transition" 
                            style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                    <button type="submit" class="bg-blue-600 text-white p-2 rounded-full w-10 h-10 flex items-center justify-center hover:bg-blue-700 transition shadow-md transform hover:scale-105">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>

            <div x-show="showSuggestions && suggestions.length > 0" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50"
                 style="display: none;">
                
                <ul class="divide-y divide-gray-50">
                    <template x-for="(course, index) in suggestions" :key="course.id">
                        <li>
                            <a :href="`/course/${course.id}`" 
                               class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition cursor-pointer"
                               :class="{ 'bg-blue-50': index === active }"
                               @mouseenter="active = index">
                                
                                <div class="w-10 h-10 rounded-lg bg-gray-200 flex-shrink-0 overflow-hidden relative">
                                    <template x-if="course.image_url">
                                        <img :src="course.image_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!course.image_url">
                                        <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-500">
                                            <i class="fas fa-graduation-cap text-xs"></i>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex-1 min-w-0 text-left">
                                    <p class="text-sm font-bold text-gray-800 truncate" x-text="course.title"></p>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wider" x-text="course.type"></p>
                                </div>

                                <i class="fas fa-chevron-right text-xs text-gray-300"></i>
                            </a>
                        </li>
                    </template>
                </ul>
                
                <div class="bg-gray-50 px-4 py-2 text-center border-t border-gray-100">
                    <a :href="`{{ route('course.index') }}?search=${query}`" class="text-xs text-blue-600 font-bold hover:underline">
                        Lihat semua hasil untuk "<span x-text="query"></span>"
                    </a>
                </div>
            </div>
        </div>
        <div class="mb-12 flex justify-center gap-3 flex-wrap">
            <a href="{{ route('course.index', request()->except('category')) }}" class="px-6 py-2 rounded-full font-semibold {{ !request('category') ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition">
                Semua Kategori
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('course.index', array_merge(request()->except('category'), ['category' => $cat->slug])) }}" 
                   class="px-6 py-2 rounded-full font-semibold {{ request('category') === $cat->slug ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if($courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($courses as $course)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full border border-gray-100 group overflow-hidden">
                    
                    <div class="relative h-48 overflow-hidden bg-gray-200">
                        @if($course->image_url)
                            <img src="{{ $course->image_url }}" alt="{{ $course->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                 loading="lazy"
                                 decoding="async">
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
                            @php
                                $instructorName = 'OtakAtik Team';
                                $instructor = $course->instructor;
                                
                                if ($instructor && $instructor->name !== 'Tidak tersedia' && $instructor->name !== 'N/A') {
                                    $instructorName = $instructor->name;
                                } 
                                elseif ($course->assistants && $course->assistants->count() > 0) {
                                    $instructorName = $course->assistants->first()->name;
                                }
                            @endphp

                            <img src="https://ui-avatars.com/api/?name={{ urlencode($instructorName) }}&background=random&color=fff" 
                                class="w-6 h-6 rounded-full border border-white shadow-sm"
                                loading="lazy"
                                decoding="async"
                                width="24" height="24"
                                alt="{{ $instructorName }}">
                            <span class="text-xs font-semibold text-gray-500 truncate">{{ $instructorName }}</span>
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
                                {{-- [FIX] Gunakan data asli dari database, fallback ke 0 jika null --}}
                                <span class="font-bold text-gray-700">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                                <span class="text-gray-400 ml-1">({{ $course->rating_count ?? 0 }})</span>
                            </div>
                            <span class="text-gray-300">|</span>
                            <span class="flex items-center">
                                <i class="far fa-clock mr-1"></i> {{ $course->duration_days ?? '-' }} Hari
                            </span>
                        </div>

                        <hr class="border-gray-100 mb-4">

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-base font-bold text-gray-900">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
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

            {{-- [TAMBAHAN] Pagination Links --}}
            <div class="mt-12 px-4 flex justify-center">
                {{ $courses->links() }}
            </div>

        @else
            <div class="text-center py-24">
                <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-3xl text-blue-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada course ditemukan</h3>
                <p class="text-gray-500">
                    @if(request('search'))
                        Tidak ada hasil untuk pencarian "{{ request('search') }}".
                    @else
                        Coba cari dengan kata kunci lain atau kembali lagi nanti.
                    @endif
                </p>
                @if(request('search') || request('category'))
                    <a href="{{ route('course.index') }}" class="mt-4 inline-block px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-full text-gray-700 font-semibold transition">
                        Reset Filter
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
    <script src="{{ asset('js/course-search.js') }}" defer></script>
@endpush