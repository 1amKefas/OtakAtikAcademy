<!DOCTYPE html>
<html>
<head>
    <title>Course Management - Instructor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">🎉 SUCCESS! Instructor Dashboard</h1>
            
            <div class="mb-6">
                <h2 class="text-xl font-semibold">{{ $course->title }}</h2>
                <p class="text-gray-600">{{ $course->description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800">Students</h3>
                    <p class="text-2xl font-bold">{{ $students->count() }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-green-800">Materials</h3>
                    <p class="text-2xl font-bold">{{ $materials->count() }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-purple-800">Assignments</h3>
                    <p class="text-2xl font-bold">{{ $assignments->count() }}</p>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-green-800 font-semibold">✅ Berhasil mengakses instructor dashboard!</p>
                <p class="text-sm text-green-600 mt-2">Course: {{ $course->title }} (ID: {{ $course->id }})</p>
                <p class="text-sm text-green-600">Instructor: {{ Auth::user()->name }}</p>
            </div>

            <!-- Students List -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Students List:</h3>
                @foreach($students as $student)
                <div class="flex items-center gap-3 mb-2 p-2 bg-gray-50 rounded">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm">
                        {{ substr($student->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $student->user->name }}</p>
                        <p class="text-sm text-gray-500">Progress: {{ $student->progress }}%</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                <a href="/instructor/courses" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    ← Back to Courses
                </a>
                <a href="/instructor/dashboard" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded ml-2">
                    Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>