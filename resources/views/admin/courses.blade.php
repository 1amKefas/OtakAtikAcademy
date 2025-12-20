<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Course Analytics - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <div class="sidebar w-64 text-white flex flex-col">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Admin</span></h1>
            </div>
            
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
                        <a href="/admin/courses" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white">
                            <i class="fas fa-book w-5"></i>
                            <span>List Courses</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white' : '' }}">
                            <i class="fas fa-tags w-5"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                     <li>
                        <a href="/admin/courses/manage" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span>Course Management</span>
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

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Course Overview</h1>
                        <p class="text-gray-600">Analisis performa, jumlah siswa, dan ulasan per course</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total: {{ $courses->total() }} Kursus</p>
                            <p class="text-sm font-medium text-gray-800">{{ date('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                
                {{-- STATS SUMMARY (Diubah agar relevan dengan Course, bukan Payment) --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Kursus</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\Course::count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Siswa Aktif</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\CourseRegistration::where('status', 'paid')->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Ulasan</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\CourseReview::count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Instruktur</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\User::where('is_instructor', true)->count() }}</p>
                            </div>
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Course Detail</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Instruktur</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Siswa</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Rating & Ulasan</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($courses as $course)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Kolom 1: Course Detail --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-16 bg-gray-200 rounded-lg overflow-hidden border border-gray-200">
                                                @if($course->image_url)
                                                    <img src="{{ $course->image_url }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 line-clamp-1" title="{{ $course->title }}">
                                                    {{ $course->title }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    {{ $course->type }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 2: Instruktur --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($course->instructor)
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                                    {{ substr($course->instructor->name, 0, 1) }}
                                                </div>
                                                <span class="text-sm text-gray-700">{{ $course->instructor->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada instruktur</span>
                                        @endif
                                    </td>

                                    {{-- Kolom 3: Jumlah Siswa --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-user-graduate mr-1.5"></i>
                                            {{ $course->registrations_count }} Siswa
                                        </span>
                                    </td>

                                    {{-- Kolom 4: Rating --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center">
                                            <div class="flex items-center text-yellow-400 text-sm">
                                                @php $rating = round($course->reviews_avg_rating ?? 0, 1); @endphp
                                                <span class="font-bold text-gray-800 mr-1">{{ $rating }}</span>
                                                @for($i=1; $i<=5; $i++)
                                                    @if($i <= floor($rating))
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.5)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star text-gray-300"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-500 mt-1">({{ $course->reviews_count }} Ulasan)</span>
                                        </div>
                                    </td>

                                    {{-- Kolom 5: Status --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($course->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom 6: Actions --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="editCourse({{ $course->id }}, '{{ addslashes($course->title) }}')" class="px-3 py-1 bg-blue-50 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>
                                            <button onclick="deleteCourse({{ $course->id }}, '{{ addslashes($course->title) }}')" class="px-3 py-1 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-book-open text-3xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900">Belum ada course</h3>
                                            <p class="text-sm text-gray-500 mt-1">Silakan tambah course baru di menu "Tambah Course"</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($courses->hasPages())
                <div class="mt-6">
                    {{ $courses->links() }}
                </div>
                @endif
            </main>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 animate-bounce-in">
        <div class="bg-white/20 p-2 rounded-full">
            <i class="fas fa-check"></i>
        </div>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.fixed.top-6');
            if(alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }
        }, 3000);
    </script>
    @endif

    <!-- Edit Course Modal -->
    <div id="editCourseModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold">Edit Course</h2>
                <button onclick="closeEditCourseModal()" class="text-white/80 hover:text-white text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editCourseForm" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course Title</label>
                        <input type="text" id="editTitle" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select id="editType" name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Select Type --</option>
                            <option value="Full Online">Full Online</option>
                            <option value="Hybrid">Hybrid</option>
                            <option value="Tatap Muka">Tatap Muka</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="editDescription" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price (Rp)</label>
                        <input type="number" id="editPrice" name="price" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount (%)</label>
                        <input type="number" id="editDiscount" name="discount_percent" min="0" max="100" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Code</label>
                    <input type="text" id="editDiscountCode" name="discount_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., PROMO2024">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Quota</label>
                        <input type="number" id="editMinQuota" name="min_quota" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Quota</label>
                        <input type="number" id="editMaxQuota" name="max_quota" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" id="editIsActive" name="is_active" class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Active Course</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeEditCourseModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteCourseModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="bg-red-50 p-6 border-b border-red-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-red-900">Delete Course</h3>
                        <p class="text-sm text-red-700 mt-1">This action cannot be undone</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <p class="text-gray-700 mb-4">Are you sure you want to delete <span id="deleteCourseTitle" class="font-bold"></span>?</p>
                <p class="text-sm text-gray-500">All associated data will be removed from the system.</p>
            </div>

            <div class="flex gap-3 p-6 border-t border-gray-200 bg-gray-50">
                <button type="button" onclick="closeDeleteCourseModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-white transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="confirmDeleteCourse()" class="flex-1 px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        let currentCourseId = null;

        function editCourse(id, title) {
            currentCourseId = id;
            
            // Fetch course data
            fetch(`/admin/courses/${id}/edit`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => {
                        throw new Error(`HTTP ${res.status}: ${text.substring(0, 200)}`);
                    });
                }
                return res.json();
            })
            .then(data => {
                document.getElementById('editTitle').value = data.title || '';
                document.getElementById('editType').value = data.type || '';
                document.getElementById('editDescription').value = data.description || '';
                document.getElementById('editPrice').value = data.price || '';
                document.getElementById('editDiscount').value = data.discount_percent || 0;
                document.getElementById('editDiscountCode').value = data.discount_code || '';
                document.getElementById('editMinQuota').value = data.min_quota || '';
                document.getElementById('editMaxQuota').value = data.max_quota || '';
                document.getElementById('editIsActive').checked = data.is_active;

                document.getElementById('editCourseForm').action = `/admin/courses/${id}`;
                document.getElementById('editCourseModal').classList.remove('hidden');
            })
            .catch(err => {
                alert('Error loading course data: ' + err.message);
                console.error(err);
            });
        }

        function closeEditCourseModal() {
            document.getElementById('editCourseModal').classList.add('hidden');
            currentCourseId = null;
        }
        // Form submit handler for edit course
        document.getElementById('editCourseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!currentCourseId) return;

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            // Convert checkbox to boolean
            data.is_active = formData.has('is_active') ? 1 : 0;

            fetch(`/admin/courses/${currentCourseId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => {
                        throw new Error(`HTTP ${res.status}: ${text.substring(0, 200)}`);
                    });
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Course updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update course'));
                }
            })
            .catch(err => {
                alert('Error updating course: ' + err.message);
                console.error(err);
            });
        });
        function deleteCourse(id, title) {
            currentCourseId = id;
            document.getElementById('deleteCourseTitle').textContent = title;
            document.getElementById('deleteCourseModal').classList.remove('hidden');
        }

        function closeDeleteCourseModal() {
            document.getElementById('deleteCourseModal').classList.add('hidden');
            currentCourseId = null;
        }

        function confirmDeleteCourse() {
            if (!currentCourseId) return;

            fetch(`/admin/courses/${currentCourseId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => {
                        throw new Error(`HTTP ${res.status}: ${text.substring(0, 200)}`);
                    });
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete course'));
                }
            })
            .catch(err => {
                alert('Error deleting course: ' + err.message);
                console.error(err);
            });
        }

        // Close modals when clicking outside
        document.getElementById('editCourseModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditCourseModal();
        });

        document.getElementById('deleteCourseModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteCourseModal();
        });
    </script>

</body>
</html>