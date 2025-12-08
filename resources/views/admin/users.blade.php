<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
                        <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white">
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
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white' : '' }}">
                            <i class="fas fa-tags w-5"></i>
                            <span>Kategori</span>
                        </a>
                    </li>
                     <li>
                        <a href="/admin/courses/manage" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
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
                        <h1 class="text-2xl font-bold text-gray-800">Participants / Users</h1>
                        <p class="text-gray-600">Manage all registered users with detailed analytics</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total Users: {{ $userStats['total_users'] }}</p>
                            <p class="text-sm font-medium text-gray-800">{{ date('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <div class="stats-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Total Users</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['total_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">All registered users</p>
                            </div>
                            <i class="fas fa-users text-3xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="admin-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Admin Users</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['admin_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Administrators</p>
                            </div>
                            <i class="fas fa-user-shield text-3xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="instructor-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Instructors</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['instructor_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Teachers</p>
                            </div>
                            <i class="fas fa-chalkboard-teacher text-3xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="user-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Regular Users</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['regular_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Students</p>
                            </div>
                            <i class="fas fa-user text-3xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="active-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Active This Month</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['active_this_month'] }}</p>
                                <p class="text-xs opacity-80 mt-2">New registrations</p>
                            </div>
                            <i class="fas fa-chart-line text-3xl opacity-80"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Age Distribution</h3>
                        <div class="h-64">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Education Level</h3>
                        <div class="h-64">
                            <canvas id="educationChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Location Distribution</h3>
                        <div class="h-64">
                            <canvas id="locationChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800">All Users</h3>
                        <div class="flex gap-2">
                            <button onclick="addUser()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                <i class="fas fa-plus"></i> Add User
                            </button>
                            <button class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Profile</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demographics</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Stats</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-lg">{{ $user->initial }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                                <div class="text-xs text-gray-400">Joined {{ $user->joinedDate }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                                <span>{{ $user->email }}</span>
                                            </div>
                                            @if($user->phone)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-phone text-gray-400 text-xs"></i>
                                                <span>{{ $user->phone }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 space-y-1">
                                            @if($user->age_range)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-birthday-cake text-blue-500 text-xs"></i>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $user->age_range }}
                                                </span>
                                            </div>
                                            @endif
                                            @if($user->education_level)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-graduation-cap text-green-500 text-xs"></i>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $user->education_level }}
                                                </span>
                                            </div>
                                            @endif
                                            @if($user->location)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-map-marker-alt text-red-500 text-xs"></i>
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $user->location }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <span class="role-badge 
                                                @if($user->is_admin) role-admin 
                                                @elseif($user->is_instructor) role-instructor 
                                                @else role-user @endif">
                                                @if($user->is_admin) Admin
                                                @elseif($user->is_instructor) Instructor
                                                @else User
                                                @endif
                                            </span>
                                            <div class="text-sm text-gray-600">
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-book text-purple-500 text-xs"></i>
                                                    <span>{{ $user->courseCount }} courses</span>
                                                </div>
                                                @if($user->is_instructor && $user->taughtCourses)
                                                <div class="flex items-center gap-1 mt-1">
                                                    <i class="fas fa-chalkboard-teacher text-blue-500 text-xs"></i>
                                                    <span>{{ $user->taughtCourses->count() }} courses taught</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            @if($user->id !== Auth::id())
                                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <select name="role" onchange="this.form.submit()" 
                                                        class="text-xs border rounded p-1 
                                                               @if($user->is_admin) bg-yellow-100 
                                                               @elseif($user->is_instructor) bg-blue-100 
                                                               @else bg-green-100 @endif">
                                                    <option value="user" {{ !$user->is_admin && !$user->is_instructor ? 'selected' : '' }}>User</option>
                                                    <option value="admin" {{ $user->is_admin ? 'selected' : '' }}>Admin</option>
                                                    <option value="instructor" {{ $user->is_instructor ? 'selected' : '' }}>Instructor</option>
                                                </select>
                                            </form>
                                            <button onclick="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Delete user {{ $user->name }}?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-gray-400 text-xs">Current User</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                                        <p>No users found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($users->hasPages())
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">
                    {{ $users->links() }}
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">User Management</h3>
                        <div class="space-y-3">
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-user-plus"></i>
                                <span class="font-medium">Add New User</span>
                            </a>
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                                <i class="fas fa-file-export"></i>
                                <span class="font-medium">Export User Data</span>
                            </a>
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-chart-bar"></i>
                                <span class="font-medium">User Analytics</span>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Demographic Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-2">
                                <span class="text-sm text-gray-600">Average Age:</span>
                                <span class="font-bold text-gray-800">28.5 years</span>
                            </div>
                            <div class="flex justify-between items-center p-2">
                                <span class="text-sm text-gray-600">Top Location:</span>
                                <span class="font-bold text-gray-800">Jakarta</span>
                            </div>
                            <div class="flex justify-between items-center p-2">
                                <span class="text-sm text-gray-600">Most Common Education:</span>
                                <span class="font-bold text-gray-800">Bachelor's Degree</span>
                            </div>
                            <div class="flex justify-between items-center p-2">
                                <span class="text-sm text-gray-600">Active Rate:</span>
                                <span class="font-bold text-green-600">85%</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">User Growth</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Monthly Growth</p>
                                        <p class="text-2xl font-bold text-green-600">+{{ $userStats['active_this_month'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-sm text-gray-600">User registration has increased by 15% compared to last month.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

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

    <script>
        window.chartData = {
            age: {
                labels: {!! json_encode(array_column($ageDistribution, 'range')) !!},
                data: {!! json_encode(array_column($ageDistribution, 'count')) !!},
                colors: {!! json_encode(array_column($ageDistribution, 'color')) !!}
            },
            education: {
                labels: {!! json_encode(array_column($educationDistribution, 'level')) !!},
                data: {!! json_encode(array_column($educationDistribution, 'count')) !!},
                colors: {!! json_encode(array_column($educationDistribution, 'color')) !!}
            },
            location: {
                labels: {!! json_encode(array_column($locationDistribution, 'location')) !!},
                data: {!! json_encode(array_column($locationDistribution, 'count')) !!},
                colors: {!! json_encode(array_column($locationDistribution, 'color')) !!}
            }
        };
    </script>
    
    <script src="{{ asset('js/admin-users.js') }}"></script>

</body>
</html>