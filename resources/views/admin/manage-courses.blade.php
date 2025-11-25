<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Course - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
        
        /* Badges */
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .status-active { background: #d1fae5; color: #065f46; }
        .status-inactive { background: #fee2e2; color: #dc2626; }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active { background: #d1fae5; color: #065f46; }
        .status-inactive { background: #fee2e2; color: #dc2626; }
        
        .type-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-badge { padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .type-full-online { background: #dbeafe; color: #1e40af; }
        .type-hybrid { background: #fef3c7; color: #d97706; }
        .type-tatap-muka { background: #d1fae5; color: #065f46; }
        
        /* Fixed Card Styling */
        .course-card {
            height: 480px; /* Fixed Height Total */
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .course-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .course-image-container { height: 180px; overflow: hidden; position: relative; }
        .course-image { width: 100%; height: 100%; object-fit: cover; }
        .course-content { flex: 1; display: flex; flex-direction: column; padding: 1.25rem; }
        .course-description {
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Batasi 3 baris */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 4.5em; /* Fallback height */
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Admin</span></h1>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-users w-5"></i>
                            <span>Participants / Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/courses" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-book w-5"></i>
                            <span>Course Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/courses/manage" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span>Tambah Course</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/financial" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Financial Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/refund" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-exchange-alt w-5"></i>
                            <span>Refund Management</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- User Section -->
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Administrator</p>
                    </div>
                </div>
                <form action="/logout" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Tambah & Kelola Course</h1>
                        <p class="text-gray-600">Buat dan kelola course yang tersedia di platform</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total: {{ $courses->count() }} courses</p>
                        <p class="text-sm font-medium text-gray-800">{{ date('M j, Y') }}</p>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Add Course Form -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Tambah Course Baru</h3>
                        
                        <form action="{{ route('admin.courses.create') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <!-- Course Thumbnail -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Thumbnail Course</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors relative">
                                        <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" onchange="previewImage(this)">
                                        <div id="imagePreviewPlaceholder">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-xs text-gray-500">Klik atau drag gambar ke sini</p>
                                            <p class="text-[10px] text-gray-400 mt-1">Max: 2MB (JPG/PNG)</p>
                                        </div>
                                        <img id="imagePreview" class="hidden w-full h-32 object-cover rounded-md mx-auto" />
                                    </div>
                                </div>
                                <!-- Course Title -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Judul Course</label>
                                    <input type="text" name="title" required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Contoh: Frontend Development Full Online">
                                    @error('title')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Course Description -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Deskripsi Course</label>
                                    <textarea name="description" rows="3" required
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Deskripsi lengkap tentang course..."></textarea>
                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Course Type -->
<div>
    <label class="text-sm font-medium text-gray-700 mb-2 block">Tipe Course</label>
    <select name="type" required 
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            onchange="toggleInstructorField(this.value)">
        <option value="">Pilih Tipe Course</option>
        <option value="Full Online">Full Online</option>
        <option value="Hybrid">Hybrid</option>
        <option value="Tatap Muka">Tatap Muka</option>
    </select>
</div>
                                
                                <!-- Instructor Selection (Conditional) -->
                                <div id="instructorField" style="display: none;">
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Assign Instruktur</label>
                                    <select name="instructor_id" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Pilih Instruktur</option>
                                        @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->name }} ({{ $instructor->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Pricing -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Harga (Rp)</label>
                                        <input type="number" name="price" required min="0" step="1000"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="299000">
                                        @error('price')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Persentase Diskon (%)</label>
                                        <input type="number" name="discount_percent" min="0" max="100"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="10">
                                        @error('discount_percent')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Discount Code -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Kode Diskon (Opsional)</label>
                                    <input type="text" name="discount_code"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="PROMOPNJ">
                                    @error('discount_code')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Quota -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Kuota Minimal</label>
                                        <input type="number" name="min_quota" required min="1"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="5">
                                        @error('min_quota')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Kuota Maksimal</label>
                                        <input type="number" name="max_quota" required min="1"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="30">
                                        @error('max_quota')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Duration & Schedule -->
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Durasi (Hari)</label>
                                        <input type="number" name="duration_days" min="1"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="30">
                                        @error('duration_days')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Tanggal Mulai</label>
                                        <input type="date" name="start_date"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('start_date')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Tanggal Selesai</label>
                                        <input type="date" name="end_date"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('end_date')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Reschedule Info -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Reschedule (Jika ada perubahan jadwal)</label>
                                    <textarea name="reschedule_reason" rows="2"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Alasan perubahan jadwal (optional)"></textarea>
                                    @error('reschedule_reason')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Active Toggle -->
                                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="is_active" class="text-sm font-medium text-gray-700">Aktifkan Course (muncul di user)</label>
                                </div>
                                
                                <!-- Submit Button -->
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-all mt-6 flex items-center justify-center gap-2">
                                    <i class="fas fa-plus-circle"></i>
                                    Tambah Course
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Existing Courses -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Course yang Tersedia</h3>
                            <div class="text-sm text-gray-600">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">{{ $courses->where('is_active', true)->count() }} Active</span>
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium ml-2">{{ $courses->where('is_active', false)->count() }} Inactive</span>
                            </div>
                        </div>
                        
                        <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                            @foreach($courses as $course)
                            <div class="course-card border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 text-lg mb-1">{{ $course->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="type-badge type-{{ strtolower(str_replace(' ', '-', $course->type)) }}">
                                                {{ $course->type }}
                                            </span>
                                            <span class="status-badge status-{{ $course->is_active ? 'active' : 'inactive' }}">
                                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        
                                        <div class="text-sm text-gray-600 space-y-1">
                                            @if($course->instructor)
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-user-tie text-blue-500 text-xs"></i>
                                                <span>{{ $course->instructor->name }}</span>
                                            </div>
                                            @endif
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-users text-green-500 text-xs"></i>
                                                <span>{{ $course->current_enrollment }}/{{ $course->max_quota }} peserta</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-800 text-sm">
                                            @if($course->discount_percent > 0)
                                                <span class="text-green-600">{{ 'Rp' . number_format($course->price * (1 - $course->discount_percent/100), 0, ',', '.') }}</span>
                                                <span class="text-xs text-gray-500 line-through ml-1">{{ 'Rp' . number_format($course->price, 0, ',', '.') }}</span>
                                            @else
                                                {{ 'Rp' . number_format($course->price, 0, ',', '.') }}
                                            @endif
                                        </span>
                                        @if($course->discount_percent > 0)
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                            -{{ $course->discount_percent }}%
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.courses.edit', $course->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                            <i class="fas fa-edit text-xs"></i>
                                            Edit
                                        </a>
                                        
                                        <form action="{{ route('admin.courses.toggle', $course->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="text-{{ $course->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $course->is_active ? 'yellow' : 'green' }}-800 text-sm flex items-center gap-1">
                                                <i class="fas fa-{{ $course->is_active ? 'eye-slash' : 'eye' }} text-xs"></i>
                                                {{ $course->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.courses.delete', $course->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 text-sm flex items-center gap-1"
                                                    onclick="return confirm('Yakin ingin menghapus course \"{{ $course->title }}\"?')">
                                                <i class="fas fa-trash text-xs"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($courses->count() === 0)
                            <div class="text-center py-12 text-gray-500">
                                <i class="fas fa-book-open text-5xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium mb-2">Belum ada course yang dibuat</p>
                                <p class="text-sm mb-4">Buat course pertama Anda menggunakan form di samping</p>
                                <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto"></div>
                            </div>
                            @endif
                        </div>
                        
                        @if($courses->count() > 0)
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>Total: {{ $courses->count() }} courses</span>
                                <span>{{ $courses->where('is_active', true)->count() }} active • {{ $courses->where('is_active', false)->count() }} inactive</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Total Courses</p>
                                <p class="text-3xl font-bold mt-2">{{ $courses->count() }}</p>
                            </div>
                            <i class="fas fa-book-open text-2xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Active Courses</p>
                                <p class="text-3xl font-bold mt-2">{{ $courses->where('is_active', true)->count() }}</p>
                            </div>
                            <i class="fas fa-eye text-2xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Inactive Courses</p>
                                <p class="text-3xl font-bold mt-2">{{ $courses->where('is_active', false)->count() }}</p>
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
            </main>
        </div>
    </div>

    <!-- Success Message Handler -->
    @if(session('success'))
    <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.fixed.top-6');
            if(alert) alert.remove();
        }, 5000);
    </script>
    @endif

    @if(session('error'))
    <div class="fixed top-6 right-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.fixed.top-6');
            if(alert) alert.remove();
        }, 5000);
    </script>
    @endif

    <script>
        function toggleInstructorField(type) {
    const instructorField = document.getElementById('instructorField');
    if (type === 'Hybrid' || type === 'Tatap Muka') {
        instructorField.style.display = 'block';
        instructorField.querySelector('select').required = true;
    } else {
        instructorField.style.display = 'none';
        instructorField.querySelector('select').required = false;
        instructorField.querySelector('select').value = '';
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('imagePreviewPlaceholder').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
}

        // Initialize form based on current selection
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.querySelector('select[name="type"]');
            if (typeSelect) {
                toggleInstructorField(typeSelect.value);
            }
        });
    </script>

   
</body>
</html>