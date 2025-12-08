@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold">Manajemen Kelas: {{ $course->title }}</h1>
            <p class="text-gray-600">Atur pembagian kelas untuk mata kuliah ini.</p>
        </div>
        <a href="{{ route('instructor.courses.manage', $course->id) }}" class="text-blue-600 hover:underline">
            &larr; Kembali ke Course
        </a>
    </div>

    {{-- Form Buat Kelas Baru --}}
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h3 class="font-bold text-lg mb-4">Buat Kelas Baru</h3>
        <form action="{{ route('instructor.classes.store', $course->id) }}" method="POST" class="flex gap-4 items-end">
            @csrf
            <div class="w-1/3">
                <label class="block text-sm text-gray-600 mb-1">Nama Kelas</label>
                <input type="text" name="name" class="border p-2 rounded w-full" placeholder="Contoh: TI-4A" required>
            </div>
            <div class="w-1/4">
                <label class="block text-sm text-gray-600 mb-1">Kuota</label>
                <input type="number" name="quota" class="border p-2 rounded w-full" value="30" required>
            </div>
            <div class="w-1/3">
                <label class="block text-sm text-gray-600 mb-1">Asisten/PJ (Opsional)</label>
                <select name="instructor_id" class="border p-2 rounded w-full">
                    <option value="{{ Auth::id() }}">Saya Sendiri ({{ Auth::user()->name }})</option>
                    @foreach($availableInstructors as $inst)
                        @if($inst->id !== Auth::id())
                            <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                + Tambah
            </button>
        </form>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- KOLOM KIRI: Siswa Belum Dapat Kelas --}}
        <div class="w-full lg:w-1/3">
            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg sticky top-24">
                <h3 class="font-bold text-yellow-800 mb-2">
                    ⚠️ Belum Dapat Kelas ({{ $unassignedStudents->count() }})
                </h3>
                <div class="space-y-2 max-h-[600px] overflow-y-auto">
                    @forelse($unassignedStudents as $reg)
                        <div class="bg-white p-3 rounded shadow-sm border border-gray-100 flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-sm">{{ $reg->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $reg->user->email }}</p>
                            </div>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded hover:bg-blue-200">
                                    Assign &rarr;
                                </button>
                                {{-- Dropdown Pilih Kelas --}}
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 w-40 bg-white border shadow-lg rounded z-10">
                                    @foreach($classes as $class)
                                        <form action="{{ route('instructor.classes.assign', [$course->id, $class->id]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="registration_id" value="{{ $reg->id }}">
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                                Ke: {{ $class->name }}
                                                @if($class->students->count() >= $class->quota) (Penuh) @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic">Semua siswa sudah masuk kelas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Daftar Kelas --}}
        <div class="w-full lg:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($classes as $class)
                <div class="bg-white border rounded-lg shadow-sm h-fit">
                    <div class="bg-gray-100 p-4 border-b flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg">{{ $class->name }}</h3>
                            <p class="text-xs text-gray-600">
                                PJ: {{ $class->instructor->name }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold px-2 py-1 rounded {{ $class->students->count() >= $class->quota ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                {{ $class->students->count() }} / {{ $class->quota }}
                            </span>
                            <form action="{{ route('instructor.classes.destroy', [$course->id, $class->id]) }}" method="POST" class="mt-2" onsubmit="return confirm('Hapus kelas ini? Siswa akan kembali ke status unassigned.');">
                                @csrf @method('DELETE')
                                <button class="text-red-500 text-xs hover:underline">Hapus Kelas</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-2 max-h-[300px] overflow-y-auto">
                        @forelse($class->students as $student)
                            <div class="flex justify-between items-center text-sm border-b pb-1 last:border-0">
                                <span>{{ $student->user->name }}</span>
                                <form action="{{ route('instructor.classes.remove-student', [$course->id, $student->id]) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-xs" title="Keluarkan">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-sm py-4">Belum ada siswa.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection