<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kelas - {{ $course->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        {{-- SIDEBAR (Copy dari layout instructor sebelumnya biar konsisten) --}}
        <div class="sidebar w-64 bg-slate-900 text-white flex flex-col hidden md:flex flex-shrink-0 transition-all duration-300">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white tracking-tight">OtakAtik<span class="text-blue-400">Instructor</span></h1>
            </div>
            
            <nav class="flex-1 p-4 overflow-y-auto custom-scrollbar">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-xl transition-all duration-200">
                            <i class="fas fa-chart-line w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('instructor.courses') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-600 text-white shadow-lg shadow-blue-900/20 rounded-xl transition-all duration-200">
                            <i class="fas fa-chalkboard-teacher w-5 text-center"></i>
                            <span>My Courses</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-gray-700 bg-slate-900">
                <div class="flex items-center gap-3 mb-4 px-2">
                    <div class="w-10 h-10 bg-gradient-to-tr from-blue-500 to-purple-500 rounded-full flex items-center justify-center border-2 border-slate-700 shadow-md">
                        <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Instructor Access</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col h-full overflow-hidden relative">
            
            {{-- Header --}}
            <header class="bg-white border-b border-gray-200 px-8 py-5 shadow-sm flex justify-between items-center z-10">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-users-cog text-blue-600"></i> Manajemen Kelas
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Mengatur pembagian kelas untuk: <span class="font-semibold text-gray-800">{{ $course->title }}</span></p>
                </div>
                <a href="{{ route('instructor.courses') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </header>

            {{-- Scrollable Content --}}
            <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                
                {{-- Alert Messages --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
                        <i class="fas fa-check-circle text-xl"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    
                    {{-- KOLOM KIRI: DAFTAR KELAS (Lebar 2/3) --}}
                    <div class="xl:col-span-2 space-y-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-layer-group text-blue-500"></i> Daftar Kelas Aktif
                            </h2>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">{{ $classes->count() }} Kelas</span>
                        </div>

                        @forelse($classes as $class)
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow group" x-data="{ open: false }">
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-200">
                                            {{ substr($class->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">{{ $class->name }}</h3>
                                            <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                                                <i class="fas fa-chalkboard-user text-gray-400"></i>
                                                <span>PJ: {{ $class->instructor->name ?? 'Belum ada PJ' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <button @click="open = !open" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition flex items-center gap-2">
                                            <i class="fas fa-users"></i>
                                            <span x-text="open ? 'Tutup Daftar' : 'Lihat Siswa'"></span>
                                            <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': open}"></i>
                                        </button>
                                        <form action="{{ route('instructor.courses.classes.destroy', [$course->id, $class->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kelas ini? Siswa akan menjadi unassigned.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition" title="Hapus Kelas">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Progress Bar Kuota --}}
                                <div class="mt-4">
                                    <div class="flex justify-between text-xs font-semibold mb-1">
                                        <span class="{{ $class->students->count() >= $class->quota ? 'text-red-600' : 'text-gray-600' }}">
                                            Terisi: {{ $class->students->count() }} / {{ $class->quota }} Siswa
                                        </span>
                                        <span class="text-blue-600">{{ round(($class->students->count() / $class->quota) * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full {{ $class->students->count() >= $class->quota ? 'bg-red-500' : 'bg-blue-600' }}" 
                                             style="width: {{ ($class->students->count() / $class->quota) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Accordion Daftar Siswa --}}
                            <div x-show="open" x-collapse class="border-t border-gray-100 bg-gray-50/50">
                                <div class="p-4">
                                    @if($class->students->count() > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($class->students as $studentReg)
                                            <div class="bg-white p-3 rounded-lg border border-gray-200 flex justify-between items-center hover:border-blue-300 transition">
                                                <div class="flex items-center gap-3">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($studentReg->user->name) }}&background=random" class="w-8 h-8 rounded-full">
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800">{{ $studentReg->user->name }}</p>
                                                        <p class="text-[10px] text-gray-500">{{ $studentReg->user->email }}</p>
                                                    </div>
                                                </div>
                                                <form action="{{ route('instructor.courses.classes.remove-student', [$course->id, $studentReg->id]) }}" method="POST" onsubmit="return confirm('Keluarkan siswa dari kelas?');">
                                                    @csrf
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 p-1 transition" title="Keluarkan Siswa">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-6 text-gray-400 italic text-sm">
                                            Belum ada siswa di kelas ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-school text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Belum Ada Kelas</h3>
                            <p class="text-gray-500 text-sm mt-1">Silakan buat kelas baru melalui form di sebelah kanan.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- KOLOM KANAN: FORM BUAT KELAS & UNASSIGNED (Lebar 1/3) --}}
                    <div class="space-y-8">
                        
                        {{-- Card: Buat Kelas Baru --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sticky top-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-plus-circle text-green-500"></i> Buat Kelas Baru
                            </h3>
                            <form action="{{ route('instructor.courses.classes.store', $course->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Kelas</label>
                                    <input type="text" name="name" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition text-sm" placeholder="Contoh: Batch 1 - Weekend" required>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kuota Siswa</label>
                                    <input type="number" name="quota" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition text-sm" placeholder="Jumlah maksimal siswa" min="1" value="30" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Penanggung Jawab (Instructor)</label>
                                    <select name="instructor_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition text-sm">
                                        <option value="{{ Auth::id() }}">{{ Auth::user()->name }} (Saya)</option>
                                        @foreach($availableInstructors as $inst)
                                            @if($inst->id !== Auth::id())
                                                <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="w-full py-2.5 bg-gray-900 hover:bg-black text-white font-bold rounded-lg shadow hover:shadow-lg transition transform hover:-translate-y-0.5 text-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i> Simpan Kelas
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- Card: Siswa Belum Dapat Kelas (Full Width di Bawah) --}}
                <div class="mt-8 bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-user-clock text-orange-500"></i> Siswa Belum Dapat Kelas
                            </h3>
                            <p class="text-sm text-gray-500">Siswa yang sudah membayar tapi belum masuk kelas manapun.</p>
                        </div>
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">{{ $unassignedStudents->count() }} Siswa</span>
                    </div>

                    @if($unassignedStudents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-xs font-bold text-gray-500 border-b border-gray-100">
                                        <th class="py-3 pl-2">Nama Siswa</th>
                                        <th class="py-3">Tanggal Daftar</th>
                                        <th class="py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($unassignedStudents as $registration)
                                    <tr class="group hover:bg-gray-50 transition">
                                        <td class="py-3 pl-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                                    {{ substr($registration->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-800 text-sm">{{ $registration->user->name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $registration->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-sm text-gray-500">
                                            {{ $registration->created_at->format('d M Y') }}
                                        </td>
                                        <td class="py-3">
                                            <form action="" method="POST" class="flex items-center gap-2 class-assignment-form">
                                                @csrf
                                                <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                                                
                                                <select name="class_id_dummy" class="class-selector text-xs rounded-lg border-gray-300 focus:ring-blue-500 py-1.5 pr-8">
                                                    <option value="" disabled selected>Pilih Kelas...</option>
                                                    @foreach($classes as $c)
                                                        <option value="{{ $c->id }}" {{ $c->students->count() >= $c->quota ? 'disabled' : '' }}>
                                                            {{ $c->name }} (Sisa: {{ $c->quota - $c->students->count() }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                
                                                <button type="button" onclick="submitAssignment(this, '{{ $course->id }}')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                                                    Assign
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50/50 rounded-xl border border-gray-100">
                            <i class="fas fa-check-circle text-green-400 text-3xl mb-2"></i>
                            <p class="text-gray-500 text-sm">Semua siswa sudah mendapatkan kelas.</p>
                        </div>
                    @endif
                </div>

            </main>
        </div>
    </div>

    <script>
        // Script untuk menangani dinamis URL form assignment
        // Karena route-nya butuh ID Kelas di URL: instructor/course/{course}/class/{class}/assign
        function submitAssignment(btn, courseId) {
            const form = btn.closest('.class-assignment-form');
            const select = form.querySelector('.class-selector');
            const classId = select.value;

            if (!classId) {
                alert('Silakan pilih kelas terlebih dahulu!');
                return;
            }

            // Construct URL manual sesuai route Laravel
            // Route: Route::post('/instructor/course/{courseId}/class/{classId}/assign', ...)
            const url = `/instructor/course/${courseId}/class/${classId}/assign`;
            
            form.action = url;
            form.submit();
        }
    </script>

</body>
</html>