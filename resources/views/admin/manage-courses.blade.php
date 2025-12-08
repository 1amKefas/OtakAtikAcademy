<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Course - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" crossorigin="anonymous">
    
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" crossorigin="anonymous"></script>
    
    {{-- Load External Custom Logic --}}
    <script src="{{ asset('js/admin-course-manage.js') }}" defer></script>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    
    <div class="flex h-full">
        <div class="sidebar w-64 text-white flex flex-col flex-shrink-0 hidden lg:flex">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Admin</span></h1>
            </div>
            <nav class="flex-1 p-4 overflow-y-auto custom-scrollbar">
                <ul class="space-y-2">
                    <li><a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors"><i class="fas fa-chart-line w-5"></i><span>Dashboard</span></a></li>
                    <li><a href="/admin/users" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors"><i class="fas fa-users w-5"></i><span>Participants / Users</span></a></li>
                    <li><a href="/admin/courses" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors"><i class="fas fa-book w-5"></i><span>Course Management</span></a></li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white' : '' }}">
                            <i class="fas fa-tags w-5"></i>
                            <span>Kategori</span>
                        </a>
                    </li>
                    <li><a href="/admin/courses/manage" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white"><i class="fas fa-plus-circle w-5"></i><span>Tambah Course</span></a></li>
                    <li><a href="/admin/financial" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors"><i class="fas fa-chart-bar w-5"></i><span>Financial Analytics</span></a></li>
                    <li><a href="/admin/refund" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors"><i class="fas fa-exchange-alt w-5"></i><span>Refund Management</span></a></li>
                    </li>  <li class="pt-4 mt-4 border-t border-gray-700"></li>

                    <li>
                        <a href="/" class="flex items-center gap-3 px-4 py-3 text-emerald-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                            <i class="fas fa-home w-5"></i>
                            <span>Back to Home</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center"><span class="text-white font-bold text-sm">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span></div>
                    <div class="flex-1 min-w-0"><p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</p><p class="text-xs text-gray-400 truncate">Administrator</p></div>
                </div>
                <form action="/logout" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors"><i class="fas fa-sign-out-alt w-4"></i><span>Logout</span></button>
                </form>
            </div>
        </div>

        <div class="flex-1 flex flex-col h-full overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Tambah & Kelola Course</h1>
                        <p class="text-gray-600">Buat dan kelola course yang tersedia di platform</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-800">{{ date('d M Y') }}</p>
                    </div>
                </div>
            </header>

            <main class="flex-1 flex overflow-hidden bg-gray-50">
                
                <div class="w-full lg:w-5/12 h-full overflow-y-auto p-6 border-r border-gray-200 bg-white custom-scrollbar">
                    <div class="max-w-xl mx-auto">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 sticky top-0 bg-white z-10 py-2 border-b border-transparent">
                            <i class="fas fa-plus-circle text-blue-600 mr-2"></i> Tambah Course Baru
                        </h3>
                        
                        <form action="{{ route('admin.courses.create') }}" method="POST" enctype="multipart/form-data" id="createCourseForm">
                            @csrf
                            <div class="space-y-5">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Thumbnail Course</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-blue-50 transition-colors relative h-48 flex flex-col items-center justify-center overflow-hidden group cursor-pointer">
                                        
                                        <input type="file" name="image" id="imageInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" accept="image/*">
                                        
                                        <div id="imagePlaceholder" class="text-center p-4">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-xs text-gray-500">Klik / Drag gambar ke sini</p>
                                            <p class="text-[10px] text-gray-400 mt-1">Auto Crop 16:9</p>
                                        </div>

                                        <img id="imagePreview" class="hidden absolute inset-0 w-full h-full object-cover z-10" />
                                        
                                        <div id="imageOverlay" class="hidden absolute inset-0 bg-black/50 z-20 flex items-center justify-center text-white font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fas fa-pen mr-2"></i> Ganti Gambar
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-1 block">Judul Course</label>
                                    <input type="text" name="title" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Frontend Master">
                                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-1 block">Deskripsi Course</label>
                                    <textarea name="description" rows="3" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi lengkap..."></textarea>
                                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Struktur Modul</label>
                                    <p class="text-xs text-gray-500 mb-3">Tambahkan modul/bab untuk kursus ini. Drag icon untuk urutkan.</p>
                                    
                                    <div id="modules-container" class="space-y-3">
                                    </div>

                                    <button type="button" onclick="addModuleInput()" class="mt-3 text-sm flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-plus-circle mr-2"></i> Tambah Modul
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-1 block">Tipe Course</label>
                                        <select name="type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="toggleInstructorField(this.value)">
                                            <option value="Full Online">Full Online</option>
                                            <option value="Hybrid">Hybrid</option>
                                            <option value="Tatap Muka">Tatap Muka</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 mb-1 block">Kategori</label>
                                            <select name="category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih Kategori</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 mb-1 block">Template Sertifikat</label>
                                            <select name="certificate_template_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Default (Otomatis)</option>
                                                @foreach($certificates as $cert)
                                                    <option value="{{ $cert->id }}">{{ $cert->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="p-4 border border-gray-200 rounded-xl bg-gray-50/50">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 flex items-center gap-1">
                                            <i class="fas fa-chalkboard-teacher"></i> Tim Pengajar
                                        </h4>
                                        
                                        <div class="mb-3">
                                            <label class="text-sm font-medium text-gray-700 mb-1 block">
                                                Instruktur Utama <span id="instructor-required" class="text-red-500" style="display:none">*</span>
                                            </label>
                                            <select name="instructor_id" id="instructor_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih Instruktur</option>
                                                @foreach($instructors as $instructor)
                                                <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-[10px] text-blue-500 mt-1" id="instructor-note">Opsional untuk Full Online (Bisa dipilih jika ada)</p>
                                        </div>

                                        <div>
                                            <label class="text-sm font-medium text-gray-700 mb-1 block">Instruktur Tambahan</label>
                                            <select name="assistants[]" multiple class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24 text-sm custom-scrollbar">
                                                @foreach($instructors as $instructor)
                                                <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-[10px] text-gray-400 mt-1">Tahan CTRL/CMD untuk pilih banyak</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-1 block">Harga (Rp)</label>
                                        <input type="number" name="price" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-1 block">Diskon (%)</label>
                                        <input type="number" name="discount_percent" min="0" max="100" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-1 block">Kode Promo (Opsional)</label>
                                    <input type="text" name="discount_code" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="KODE123">
                                </div>
                                
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Min Kuota</label>
                                        <input type="number" name="min_quota" required min="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Max Kuota</label>
                                        <input type="number" name="max_quota" required min="1" value="30" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Durasi (Hari)</label>
                                        <input type="number" name="duration_days" value="30" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-1 block">Mulai</label>
                                        <input type="date" name="start_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-1 block">Selesai</label>
                                        <input type="date" name="end_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-1 block">Reschedule Info</label>
                                    <textarea name="reschedule_reason" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Opsional..."></textarea>
                                </div>
                                
                                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                    <label for="is_active" class="text-sm font-medium text-gray-700">Aktifkan Course</label>
                                </div>
                                
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i> Simpan Course
                                </button>
                            </div>
                        </form>
                        <div class="h-10"></div> </div>
                </div>

                <div class="w-full lg:w-7/12 h-full bg-gray-50 flex flex-col">
                    <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-5 border-b border-gray-200 bg-white sticky top-0 z-10 flex justify-between items-center">
                                <h3 class="font-bold text-gray-800">Daftar Course ({{ $courses->total() }})</h3>
                                <div class="text-xs text-gray-500">Menampilkan 20 item per halaman</div>
                            </div>

                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 sticky top-[60px] z-10 shadow-sm">
                                    <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                        <th class="p-4">Info Course</th>
                                        <th class="p-4">Harga</th>
                                        <th class="p-4 text-center">Status</th>
                                        <th class="p-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($courses as $course)
                                    <tr class="hover:bg-blue-50/50 transition-colors group">
                                        <td class="p-4">
                                            <div class="flex gap-3 items-center">
                                                <div class="w-14 h-10 rounded-md bg-gray-200 flex-shrink-0 overflow-hidden border border-gray-200">
                                                    @if($course->image_url)
                                                        <img src="{{ $course->image_url }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="font-semibold text-gray-900 text-sm truncate max-w-[200px]" title="{{ $course->title }}">{{ $course->title }}</h4>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 border border-gray-200">{{ $course->type }}</span>
                                                        <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                                            <i class="fas fa-user-friends"></i> {{ $course->current_enrollment }}/{{ $course->max_quota }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="text-sm font-medium text-gray-900">Rp{{ number_format($course->price, 0, ',', '.') }}</div>
                                            @if($course->discount_percent > 0)
                                                <span class="text-[10px] text-green-600 font-medium">Disc {{ $course->discount_percent }}%</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="inline-flex w-2.5 h-2.5 rounded-full {{ $course->is_active ? 'bg-green-500' : 'bg-red-500' }}" title="{{ $course->is_active ? 'Aktif' : 'Nonaktif' }}"></span>
                                        </td>
                                        <td class="p-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-100 rounded transition-colors">
                                                    <i class="fas fa-pen text-xs"></i>
                                                </a>
                                                <form action="{{ route('admin.courses.toggle', $course->id) }}" method="POST" class="inline">
                                                    @csrf @method('PUT')
                                                    <button class="p-1.5 text-gray-500 hover:bg-gray-100 rounded transition-colors" title="Toggle">
                                                        <i class="fas fa-{{ $course->is_active ? 'eye-slash' : 'eye' }} text-xs"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.courses.delete', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus course ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="p-1.5 text-red-600 hover:bg-red-100 rounded transition-colors">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-gray-500">
                                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-sm">Belum ada data course.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 px-2">
                            {{ $courses->links() }}
                        </div>

                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-6 pb-10">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm opacity-90">Total Courses</p>
                                        <p class="text-3xl font-bold mt-2">{{ \App\Models\Course::count() }}</p>
                                    </div>
                                    <i class="fas fa-book-open text-2xl opacity-80"></i>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm opacity-90">Active Courses</p>
                                        <p class="text-3xl font-bold mt-2">{{ \App\Models\Course::where('is_active', true)->count() }}</p>
                                    </div>
                                    <i class="fas fa-eye text-2xl opacity-80"></i>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-2xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm opacity-90">Inactive Courses</p>
                                        <p class="text-3xl font-bold mt-2">{{ \App\Models\Course::where('is_active', false)->count() }}</p>
                                    </div>
                                    <i class="fas fa-eye-slash text-2xl opacity-80"></i>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm opacity-90">Total Instructors</p>
                                        <p class="text-3xl font-bold mt-2">{{ $instructors->count() }}</p>
                                    </div>
                                    <i class="fas fa-chalkboard-teacher text-2xl opacity-80"></i>
                                </div>
                            </div>
                        </div>
                        </div>
                </div>
            </main>
        </div>
    </div>

    <div id="cropModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl w-full max-w-2xl overflow-hidden shadow-2xl transform transition-all">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Potong Gambar (16:9)</h3>
                <button type="button" onclick="closeCropper()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="bg-gray-900 h-[400px] flex justify-center items-center relative">
                <img id="imageToCrop" class="max-w-full max-h-full block">
            </div>
            <div class="p-4 border-t border-gray-200 flex justify-end gap-3 bg-white">
                <button type="button" onclick="closeCropper()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Batal
                </button>
                <button type="button" onclick="cropImage()" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-check"></i> Simpan Gambar
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div id="alert-success" class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 animate-fade-in-down">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div id="alert-error" class="fixed top-6 right-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 animate-fade-in-down">
        <i class="fas fa-exclamation-circle"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

</body>
</html>