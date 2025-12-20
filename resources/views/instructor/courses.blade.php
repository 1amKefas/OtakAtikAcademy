<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - OtakAtik Instructor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    {{-- Load External Script --}}
    <script src="{{ asset('js/instructor-courses.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/instructor.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <div class="sidebar w-64 text-white flex flex-col hidden md:flex">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Instructor</span></h1>
            </div>
            
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="/instructor/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/instructor/courses" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white shadow-lg">
                            <i class="fas fa-chalkboard-teacher w-5"></i>
                            <span>Course Management</span>
                        </a>
                    </li>
                    
                    <li class="pt-4 mt-4 border-t border-gray-700"></li>
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Other</p>

                    <li>
                        <a href="/course" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors">
                            <i class="fas fa-shopping-cart w-5"></i>
                            <span>Browse Courses</span>
                        </a>
                    </li>
                    <li>
                        <a href="/my-courses" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors">
                            <i class="fas fa-user-graduate w-5"></i>
                            <span>As Student</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center border border-blue-400">
                        <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Instructor</p>
                    </div>
                </div>
                <form action="/logout" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-gray-300 hover:bg-red-900/50 hover:text-red-300 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">My Assigned Courses</h1>
                        <p class="text-gray-600">Kelola konten dan materi untuk kursus Anda</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total: {{ $courses->total() }} courses</p>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($courses as $course)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300 group">
                        <div class="h-40 bg-gray-100 relative overflow-hidden">
                            @if($course->image_url)
                                <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center">
                                    <i class="fas fa-book-open text-white/20 text-5xl"></i>
                                </div>
                            @endif
                            
                            <div class="absolute top-4 right-4 flex gap-2">
                                <span class="px-2 py-1 bg-white/90 backdrop-blur text-xs font-bold rounded-md shadow-sm text-gray-700">
                                    {{ ucfirst($course->type) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900 line-clamp-1 mb-1" title="{{ $course->title }}">{{ $course->title }}</h3>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1"><i class="fas fa-users"></i> {{ $course->registrations_count }} Siswa</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="flex items-center gap-1 {{ $course->is_active ? 'text-green-600' : 'text-red-500' }}">
                                        <i class="fas fa-circle text-[8px]"></i> {{ $course->is_active ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-6 py-3 border-t border-b border-gray-100 bg-gray-50/50 rounded-lg">
                                <div class="text-center">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Modules</p>
                                    {{-- [FIX] Data ini sekarang sudah tersedia dari Controller --}}
                                    <p class="font-bold text-gray-700 text-lg">{{ $course->modules_count ?? 0 }}</p>
                                </div>
                                <div class="text-center border-l border-gray-200">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Contents</p>
                                    {{-- [FIX] Penjumlahan total konten (Materi + Tugas + Quiz) --}}
                                    <p class="font-bold text-gray-700 text-lg">
                                        {{ ($course->materials_count ?? 0) + ($course->assignments_count ?? 0) + ($course->quizzes_count ?? 0) }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex gap-3 mb-4">
                                <a href="{{ route('instructor.courses.manage', $course->id) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg transition-all text-sm font-medium shadow-sm hover:shadow">
                                    <i class="fas fa-edit mr-2"></i> Edit Konten
                                </a>
                                
                                <a href="{{ route('instructor.courses.students', $course->id) }}" 
                                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 py-2.5 px-4 rounded-lg transition-all border border-gray-200" title="Lihat Siswa">
                                    <i class="fas fa-users"></i>
                                </a>

                                <!-- Zoom Pertemuan Button -->
                                <button onclick="openZoomModuleModal({{ $course->id }}, '{{ $course->title }}')" 
                                   class="bg-purple-100 hover:bg-purple-200 text-purple-700 hover:text-purple-800 py-2.5 px-4 rounded-lg transition-all border border-purple-200" title="Add Zoom Pertemuan">
                                    <i class="fas fa-video"></i>
                                </button>
                            </div>

                            <!-- Expandable Modules -->
                            <details class="group cursor-pointer">
                                <summary class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <span class="font-medium text-gray-700 text-sm">
                                        <i class="fas fa-list mr-2"></i> Modules ({{ $course->modules_count ?? 0 }})
                                    </span>
                                    <i class="fas fa-chevron-down text-gray-500 group-open:rotate-180 transition-transform"></i>
                                </summary>
                                
                                <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-100 space-y-2">
                                    @forelse($course->modules as $module)
                                        <div class="flex items-center justify-between p-2 bg-white rounded border border-gray-200">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                @if($module->module_type === 'zoom_pertemuan')
                                                    <i class="fas fa-video text-purple-600 flex-shrink-0"></i>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $module->title }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $module->session_date ? $module->session_date->format('d M Y') : '' }}
                                                            @if($module->start_time)
                                                                • {{ date('H:i', strtotime($module->start_time)) }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                @else
                                                    <i class="fas fa-book text-blue-600 flex-shrink-0"></i>
                                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $module->title }}</p>
                                                @endif
                                            </div>
                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full flex-shrink-0 ml-2">
                                                {{ $module->module_type === 'zoom_pertemuan' ? 'Zoom' : 'Regular' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-600 text-center py-2">No modules yet</p>
                                    @endforelse
                                </div>
                            </details>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="fas fa-folder-open text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Belum Ada Kursus</h3>
                        <p class="text-gray-500 mt-2">Anda belum ditugaskan ke kursus manapun oleh Admin.</p>
                    </div>
                    @endforelse
                </div>

                {{-- TAMBAHKAN KODE INI DI SINI --}}
                <div class="mt-6">
                    {{ $courses->links() }}
                </div>
                {{-- SAMPAI SINI --}}

            </main>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center gap-3 animate-bounce">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Zoom Pertemuan Modal -->
    <div id="zoomModuleModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Add Zoom Pertemuan</h2>
                    <p id="courseTitle" class="text-sm text-purple-100 mt-1"></p>
                </div>
                <button onclick="closeZoomModuleModal()" class="text-white hover:bg-purple-800 p-2 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="zoomModuleForm" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="courseIdInput" name="course_id">

                <!-- Title -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Session Title *</label>
                    <input type="text" name="title" placeholder="e.g., Introduction to Laravel" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="2" placeholder="What will be covered?"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <!-- Session Date & Time -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date *</label>
                        <input type="date" name="session_date" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Start Time *</label>
                        <input type="time" name="start_time" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">End Time *</label>
                        <input type="time" name="end_time" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <!-- Meeting Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Meeting Type *</label>
                    <select name="meeting_type" onchange="updateZoomFields()" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">-- Select Type --</option>
                        <option value="zoom">Zoom Meeting</option>
                        <option value="tatap_muka">Tatap Muka (In-Person)</option>
                        <option value="hybrid">Hybrid (Online + In-Person)</option>
                    </select>
                </div>

                <!-- Zoom Link (Conditional) -->
                <div id="zoomLinkField" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Zoom Link *</label>
                    <input type="url" name="zoom_link" placeholder="https://zoom.us/j/..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Location (Conditional) -->
                <div id="locationField" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Location *</label>
                    <input type="text" name="location" placeholder="e.g., Room 101, Building A"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Room Number (Conditional) -->
                <div id="roomNumberField" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room Number</label>
                    <input type="text" name="room_number" placeholder="e.g., 101"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Agenda -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Agenda</label>
                    <textarea name="agenda" rows="2" placeholder="What topics will be discussed?"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 justify-end pt-4 border-t">
                    <button type="button" onclick="closeZoomModuleModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Create Zoom Module
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openZoomModuleModal(courseId, courseTitle) {
            document.getElementById('zoomModuleModal').classList.remove('hidden');
            document.getElementById('courseIdInput').value = courseId;
            document.getElementById('courseTitle').textContent = courseTitle;
            document.getElementById('zoomModuleForm').action = `/instructor/courses/${courseId}/zoom-module`;
        }

        function closeZoomModuleModal() {
            document.getElementById('zoomModuleModal').classList.add('hidden');
        }

        function updateZoomFields() {
            const meetingType = document.querySelector('select[name="meeting_type"]').value;
            document.getElementById('zoomLinkField').classList.add('hidden');
            document.getElementById('locationField').classList.add('hidden');
            document.getElementById('roomNumberField').classList.add('hidden');

            if (meetingType === 'zoom') {
                document.getElementById('zoomLinkField').classList.remove('hidden');
            } else if (meetingType === 'tatap_muka' || meetingType === 'hybrid') {
                document.getElementById('locationField').classList.remove('hidden');
                document.getElementById('roomNumberField').classList.remove('hidden');
            }
        }

        // Close modal when clicking outside
        document.getElementById('zoomModuleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeZoomModuleModal();
            }
        });
    </script>

</body>
</html>