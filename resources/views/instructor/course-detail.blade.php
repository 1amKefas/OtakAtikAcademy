<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course - Instructor - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-button.active { background-color: #3b82f6; color: white; }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">O</span>
                </div>
                <span class="text-xl font-bold text-gray-800">OtakAtik Instructor</span>
            </div>
            
            <!-- Menu -->
            <div class="hidden md:flex items-center gap-8">
                <a href="/instructor/dashboard" class="text-gray-700 hover:text-orange-500 font-medium transition">Dashboard</a>
                <a href="/instructor/courses" class="text-orange-500 font-medium transition">My Teaching</a>
                <a href="/course" class="text-gray-700 hover:text-orange-500 font-medium transition">Browse Courses</a>
            </div>
            
            <!-- User Info -->
            <div class="flex items-center gap-4">
                <span class="text-gray-700 font-medium">Instructor: {{ Auth::user()->name }}</span>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded-lg transition-all">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Course Management Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-6xl mx-auto">
            <!-- Course Header -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                        <div class="text-center text-white">
                            <h1 class="text-4xl font-bold mb-4">{{ $course->title }}</h1>
                            <p class="text-xl opacity-90">Teaching Dashboard</p>
                            <p class="text-lg opacity-80 mt-2">Students: {{ $students->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-2xl shadow-lg mb-6">
                <div class="flex border-b overflow-x-auto">
                    <button class="tab-button active px-6 py-4 font-medium text-gray-600 hover:text-blue-600 transition-colors whitespace-nowrap" data-tab="students">
                        Students ({{ $students->count() }})
                    </button>
                    <button class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-blue-600 transition-colors whitespace-nowrap" data-tab="materials">
                        Materials ({{ $materials->count() }})
                    </button>
                    <button class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-blue-600 transition-colors whitespace-nowrap" data-tab="assignments">
                        Assignments ({{ $assignments->count() }})
                    </button>
                    <button class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-blue-600 transition-colors whitespace-nowrap" data-tab="quizzes">
                        Quizzes
                    </button>
                    <button class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-blue-600 transition-colors whitespace-nowrap" data-tab="forum">
                        Forum
                    </button>
                    <button class="tab-button px-6 py-4 font-medium text-gray-600 hover:text-blue-600 transition-colors whitespace-nowrap" data-tab="analytics">
                        Analytics
                    </button>
                </div>
            </div>

            <!-- Tab Contents -->
            <div class="space-y-6">
                <!-- Students Tab -->
                <div id="students" class="tab-content active">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Course Students</h3>
                            <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $students->count() }} Students
                            </span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $registration)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">{{ substr($registration->user->name, 0, 1) }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $registration->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $registration->progress }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-700">{{ $registration->progress }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($registration->enrolled_at)
                                                {{ $registration->enrolled_at->format('M d, Y') }}
                                            @else
                                                {{ $registration->created_at->format('M d, Y') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ route('instructor.students.progress', $registration->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="progress" value="{{ $registration->progress }}" min="0" max="100" 
                                                       class="w-16 px-2 py-1 border border-gray-300 rounded text-sm">
                                                <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                                    Update
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Materials Tab -->
                <div id="materials" class="tab-content">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Course Materials</h3>
                            <button onclick="showMaterialForm()" class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg transition-all">
                                Add Material
                            </button>
                        </div>

                        <!-- Add Material Form -->
                        <div id="materialForm" class="bg-gray-50 rounded-lg p-6 mb-6 hidden">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Add New Material</h4>
                            <form action="{{ route('instructor.materials.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                        <input type="text" name="title" required 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea name="description" rows="3"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                                        <input type="file" name="file" required 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               accept=".pdf,.doc,.docx,.ppt,.pptx,.txt">
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-all">
                                            Upload Material
                                        </button>
                                        <button type="button" onclick="hideMaterialForm()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Materials List -->
                        <div class="space-y-4">
                            @foreach($materials as $material)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                        PDF
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $material->title }}</h4>
                                        @if($material->description)
                                        <p class="text-sm text-gray-600">{{ $material->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">{{ $material->file_name }} • {{ number_format($material->file_size / 1024, 0) }} KB</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/storage/{{ $material->file_path }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all"
                                       target="_blank">
                                        Download
                                    </a>
                                    <form action="{{ route('instructor.materials.delete', $material->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-all"
                                                onclick="return confirm('Delete this material?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach

                            @if($materials->count() === 0)
                            <div class="text-center py-12 text-gray-500">
                                <p>No materials uploaded yet.</p>
                                <p class="text-sm mt-1">Add your first material using the button above.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Assignments Tab -->
                <div id="assignments" class="tab-content">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Course Assignments</h3>
                            <button onclick="showAssignmentForm()" class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg transition-all">
                                Add Assignment
                            </button>
                        </div>

                        <!-- Add Assignment Form -->
                        <div id="assignmentForm" class="bg-gray-50 rounded-lg p-6 mb-6 hidden">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Create New Assignment</h4>
                            <form action="{{ route('instructor.assignments.store', $course->id) }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                        <input type="text" name="title" required 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea name="description" rows="3"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                                        <textarea name="instructions" rows="4" required
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                            <input type="datetime-local" name="due_date" required 
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Points</label>
                                            <input type="number" name="max_points" required min="1" max="1000"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-all">
                                            Create Assignment
                                        </button>
                                        <button type="button" onclick="hideAssignmentForm()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Edit Assignment Modal -->
                        <div id="editAssignmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
                            <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                                <div class="bg-blue-500 text-white px-6 py-4 flex items-center justify-between sticky top-0 z-10">
                                    <h3 class="text-xl font-bold">Edit Assignment</h3>
                                        <button type="button" onclick="closeEditAssignmentModal()" class="text-white hover:bg-blue-600 rounded-lg p-1 transition">
                                        
                                    </button>
                                </div>
                                <form id="editAssignmentForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="px-6 py-6 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                            <input type="text" id="editTitle" name="title" required 
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                            <textarea id="editDescription" name="description" rows="2"
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                                            <textarea id="editInstructions" name="instructions" rows="3" required
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                                <input type="datetime-local" id="editDueDate" name="due_date" required 
                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <p class="text-xs text-gray-500 mt-2">
                                                    📢 Students will be notified of deadline changes
                                                </p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Published</label>
                                                <select id="editIsPublished" name="is_published" required 
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-gray-50 flex gap-3 justify-end border-t">
                                        <button type="button" onclick="closeEditAssignmentModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-all font-medium">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-all font-medium flex items-center gap-2">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Assignments List -->
                        <div class="space-y-6">
                            @foreach($assignments as $assignment)
                            <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 transition-colors">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-800">{{ $assignment->title }}</h4>
                                        @if($assignment->description)
                                        <p class="text-gray-600 mt-2">{{ $assignment->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Due Date</div>
                                        <div class="font-semibold text-gray-800">{{ $assignment->due_date->format('d M Y, H:i') }}</div>
                                        <div class="text-sm text-blue-600 font-medium">{{ $assignment->max_points }} Points</div>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <h5 class="font-semibold text-gray-800 mb-2">Instructions:</h5>
                                    <p class="text-gray-700">{{ $assignment->instructions }}</p>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('instructor.submissions', $assignment->id) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                        View Submissions
                                    </a>
                                    <button onclick="editAssignment({{ $assignment->id }})" 
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                        Edit
                                    </button>
                                    <form action="{{ route('instructor.assignments.delete', $assignment->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2"
                                                onclick="return confirm('Delete this assignment?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach

                            @if($assignments->count() === 0)
                            <div class="text-center py-12 text-gray-500">
                                <p>No assignments created yet.</p>
                                <p class="text-sm mt-1">Create your first assignment using the button above.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Analytics Tab -->
                <div id="analytics" class="tab-content">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Course Analytics</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-blue-50 rounded-lg p-6 text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $students->count() }}</div>
                                <p class="text-blue-800 font-medium">Total Students</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-6 text-center">
                                @php
                                    $avgProgress = $students->avg('progress');
                                @endphp
                                <div class="text-3xl font-bold text-green-600 mb-2">{{ number_format($avgProgress, 1) }}%</div>
                                <p class="text-green-800 font-medium">Average Progress</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-6 text-center">
                                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $assignments->count() }}</div>
                                <p class="text-purple-800 font-medium">Total Assignments</p>
                            </div>
                        </div>

                        <!-- Progress Distribution -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Student Progress Distribution</h4>
                            <div class="space-y-3">
                                @php
                                    $progressRanges = [
                                        '0-25%' => $students->where('progress', '<=', 25)->count(),
                                        '26-50%' => $students->whereBetween('progress', [26, 50])->count(),
                                        '51-75%' => $students->whereBetween('progress', [51, 75])->count(),
                                        '76-99%' => $students->whereBetween('progress', [76, 99])->count(),
                                        '100%' => $students->where('progress', 100)->count(),
                                    ];
                                @endphp
                                
                                @foreach($progressRanges as $range => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">{{ $range }}</span>
                                    <div class="flex items-center gap-3">
                                        <div class="w-48 bg-gray-200 rounded-full h-3">
                                            @if($students->count() > 0)
                                            <div class="bg-blue-500 h-3 rounded-full" style="width: {{ ($count / $students->count()) * 100 }}%"></div>
                                            @endif
                                        </div>
                                        <span class="text-sm font-bold text-gray-800 w-8 text-right">{{ $count }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quizzes Tab -->
                <div id="quizzes" class="tab-content">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Course Quizzes</h3>
                            <a href="{{ route('instructor.quiz.create', $course->id) }}" 
                               class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                Create Quiz
                            </a>
                        </div>

                        <!-- Quizzes List -->
                        <div class="space-y-6">
                            @if($quizzes && $quizzes->count() > 0)
                                @foreach($quizzes as $quiz)
                                <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 transition-colors">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-800">{{ $quiz->title }}</h4>
                                            @if($quiz->description)
                                            <p class="text-gray-600 mt-2">{{ $quiz->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">Questions</div>
                                            <div class="font-semibold text-gray-800">{{ $quiz->questions->count() ?? 0 }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                        <div class="grid grid-cols-3 gap-4 text-center">
                                            <div>
                                                <p class="text-sm text-gray-600">Duration</p>
                                                <p class="font-semibold text-gray-800">{{ $quiz->duration_minutes ?? 'N/A' }} min</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Total Points</p>
                                                <p class="font-semibold text-gray-800">{{ $quiz->total_points ?? 0 }} pts</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">Submissions</p>
                                                <p class="font-semibold text-gray-800">{{ $quiz->submissions->count() ?? 0 }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <a href="{{ route('instructor.quiz.edit', [$course->id, $quiz->id]) }}" 
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                            Edit
                                        </a>
                                        <a href="{{ route('instructor.quiz.submissions', [$course->id, $quiz->id]) }}" 
                                           class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                            View Submissions
                                        </a>
                                        <form action="{{ route('instructor.quiz.destroy', [$course->id, $quiz->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2"
                                                    onclick="return confirm('Delete this quiz?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            @else
                            <div class="text-center py-12 text-gray-500">
                                ❔
                                <p>No quizzes created yet.</p>
                                <p class="text-sm mt-1">Create your first quiz using the button above.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Forum Tab -->
                <div id="forum" class="tab-content">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Course Forum</h3>
                            <button onclick="showNewTopicForm()" 
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                New Topic
                            </button>
                        </div>

                        <!-- New Topic Form -->
                        <div id="newTopicForm" class="bg-gray-50 rounded-lg p-6 mb-6 hidden">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Create New Topic</h4>
                            <form action="{{ route('instructor.forum.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                        <input type="text" name="subject" required 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                        <textarea name="message" rows="4" required
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment (Optional)</label>
                                        <input type="file" name="attachment" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-all">
                                            Post Topic
                                        </button>
                                        <button type="button" onclick="hideNewTopicForm()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Forum Topics List -->
                        <div class="space-y-6">
                            @if($forums && $forums->count() > 0)
                                @foreach($forums as $forum)
                                <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 transition-colors">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-800">{{ $forum->subject }}</h4>
                                            <p class="text-sm text-gray-500">by {{ $forum->user->name }} • {{ $forum->created_at->diffForHumans() }}</p>
                                        </div>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                                            {{ $forum->replies->count() ?? 0 }} Replies
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-700 mb-4">{{ Str::limit($forum->message, 200) }}</p>
                                    
                                    <div class="flex gap-2">
                                        <a href="{{ route('instructor.forum.show', [$course->id, $forum->id]) }}" 
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm">
                                            View
                                        </a>
                                        <form action="{{ route('instructor.forum.destroy', [$course->id, $forum->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm"
                                                    onclick="return confirm('Delete this topic?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            @else
                            <div class="text-center py-12 text-gray-500">
                                📄
                                <p>No forum topics yet.</p>
                                <p class="text-sm mt-1">Start the discussion by creating a new topic.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">O</span>
                </div>
                <span class="text-2xl font-bold">OtakAtik Academy - Instructor Portal</span>
            </div>
            <p class="text-gray-400 mb-4">Empowering Educators, Inspiring Students</p>
            <p class="text-gray-500 text-sm">&copy; 2025 OtakAtik Academy. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to current button and content
                    this.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });
        });

        // Material form toggle
        function showMaterialForm() {
            document.getElementById('materialForm').classList.remove('hidden');
        }

        function hideMaterialForm() {
            document.getElementById('materialForm').classList.add('hidden');
        }

        // Assignment form toggle
        function showAssignmentForm() {
            document.getElementById('assignmentForm').classList.remove('hidden');
        }

        function hideAssignmentForm() {
            document.getElementById('assignmentForm').classList.add('hidden');
        }

        // Edit assignment modal
        function editAssignment(assignmentId) {
            // Fetch assignment data
            fetch(`/api/assignments/${assignmentId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Populate form fields
                    document.getElementById('editTitle').value = data.title;
                    document.getElementById('editDescription').value = data.description || '';
                    document.getElementById('editInstructions').value = data.instructions;
                    
                    // Convert due_date to datetime-local format (YYYY-MM-DDTHH:mm)
                    const dueDate = new Date(data.due_date);
                    const formattedDate = dueDate.toISOString().slice(0, 16);
                    document.getElementById('editDueDate').value = formattedDate;
                    
                    document.getElementById('editIsPublished').value = data.is_published ? '1' : '0';
                    
                    // Set form action
                    const form = document.getElementById('editAssignmentForm');
                    form.action = `/instructor/assignments/${assignmentId}`;
                    
                    // Show modal
                    document.getElementById('editAssignmentModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load assignment details: ' + error.message);
                });
        }

        function closeEditAssignmentModal() {
            document.getElementById('editAssignmentModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('editAssignmentModal');
            if (event.target === modal) {
                closeEditAssignmentModal();
            }
        });

        // Forum form toggle
        function showNewTopicForm() {
            document.getElementById('newTopicForm').classList.remove('hidden');
        }

        function hideNewTopicForm() {
            document.getElementById('newTopicForm').classList.add('hidden');
        }
    </script>

</body>
</html>