@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-2"><i class="fas fa-cog mr-2"></i>Settings</h1>
            <p class="text-purple-100">Manage your preferences and account settings</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800 font-semibold mb-2">Error:</p>
                <ul class="text-red-700 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tabs Navigation -->
        <div class="flex border-b border-gray-200 mb-8 gap-4 overflow-x-auto">
            <button onclick="switchTab('notifications')" id="tab-notifications" 
                    class="tab-button active px-4 py-3 font-medium text-purple-600 border-b-2 border-purple-600 whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-bell text-purple-600"></i> Notifications
            </button>
            <button onclick="switchTab('privacy')" id="tab-privacy"
                    class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent whitespace-nowrap hover:text-gray-800 flex items-center gap-2">
                <i class="fas fa-shield-alt text-gray-600"></i> Privacy & Visibility
            </button>
            <button onclick="switchTab('account')" id="tab-account"
                    class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent whitespace-nowrap hover:text-gray-800 flex items-center gap-2">
                <i class="fas fa-cog text-gray-600"></i> Account & Security
            </button>
        </div>

        <!-- Notifications Tab -->
        <div id="notifications-tab" class="tab-content">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Notification Preferences</h2>
                
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Assignment Posted -->
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">New Assignment Posted</p>
                                <p class="text-sm text-gray-600">Get notified when instructor posts new assignments</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_assignment_posted" class="sr-only peer" 
                                   {{ $user->notify_assignment_posted ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Deadline Reminder -->
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-hourglass-end text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Deadline Reminders</p>
                                <p class="text-sm text-gray-600">Remind me 1 day before assignment/quiz deadlines</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_deadline_reminder" class="sr-only peer"
                                   {{ $user->notify_deadline_reminder ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>

                    <!-- Quiz Posted -->
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-question-circle text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">New Quiz Posted</p>
                                <p class="text-sm text-gray-600">Get notified when instructor creates new quizzes</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_quiz_posted" class="sr-only peer"
                                   {{ $user->notify_quiz_posted ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <!-- Material Uploaded -->
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-book text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">New Material Uploaded</p>
                                <p class="text-sm text-gray-600">Get notified when instructor uploads course materials</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_material_posted" class="sr-only peer"
                                   {{ $user->notify_material_posted ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>

                    <!-- Forum Reply -->
                    <div class="flex items-center justify-between p-4 bg-cyan-50 rounded-lg border border-cyan-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-cyan-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-comments text-cyan-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Forum Replies</p>
                                <p class="text-sm text-gray-600">Get notified when someone replies to your forum post</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_forum_reply" class="sr-only peer"
                                   {{ $user->notify_forum_reply ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-600"></div>
                        </label>
                    </div>

                    <!-- Submission Graded -->
                    <div class="flex items-center justify-between p-4 bg-teal-50 rounded-lg border border-teal-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-teal-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Submission Graded</p>
                                <p class="text-sm text-gray-600">Get notified when your assignment/quiz is graded</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_submission_graded" class="sr-only peer"
                                   {{ $user->notify_submission_graded ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all mt-6">
                        💾 Save Notification Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Privacy Tab -->
        <div id="privacy-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Privacy & Visibility</h2>
                
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Profile Visibility -->
                    <div class="border-b border-gray-200 pb-6">
                        <p class="font-semibold text-gray-800 mb-4">Profile Visibility</p>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
                                   {{ $user->profile_visibility === 'private' ? 'class=border-purple-300 bg-purple-50' : '' }}>
                                <input type="radio" name="profile_visibility" value="private" class="w-4 h-4"
                                       {{ $user->profile_visibility === 'private' ? 'checked' : '' }}>
                                <div>
                                    <p class="font-medium text-gray-800"><i class="fas fa-lock text-red-600 mr-2"></i>Private Profile</p>
                                    <p class="text-sm text-gray-600">Your profile is only visible to you</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
                                   {{ $user->profile_visibility === 'public' ? 'class=border-purple-300 bg-purple-50' : '' }}>
                                <input type="radio" name="profile_visibility" value="public" class="w-4 h-4"
                                       {{ $user->profile_visibility === 'public' ? 'checked' : '' }}>
                                <div>
                                    <p class="font-medium text-gray-800"><i class="fas fa-globe text-blue-600 mr-2"></i>Public Profile</p>
                                    <p class="text-sm text-gray-600">Your profile is visible to other students</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Show Achievements -->
                    <div class="border-b border-gray-200 pb-6 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800"><i class="fas fa-trophy text-yellow-600 mr-2"></i>Show Achievements</p>
                            <p class="text-sm text-gray-600">Display your completed certificates and badges</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_achievements" class="sr-only peer"
                                   {{ $user->show_achievements ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <!-- Allow Direct Messages -->
                    <div class="pb-6 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800"><i class="fas fa-comments text-cyan-600 mr-2"></i>Allow Direct Messages</p>
                            <p class="text-sm text-gray-600">Let other students send you direct messages</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_direct_messages" class="sr-only peer"
                                   {{ $user->allow_direct_messages ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all">
                        💾 Save Privacy Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Account Tab -->
        <div id="account-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Account & Security</h2>
                
                <!-- Change Password Section -->
                <div class="border-b border-gray-200 pb-8 mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-key text-purple-600 mr-2"></i>Change Password</h3>
                    
                    <form action="{{ route('settings.password.update') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Current Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                                   placeholder="Enter your current password">
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                   placeholder="Enter a new password (min 8 characters)">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Confirm your new password">
                        </div>

                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-all">
                            🔐 Update Password
                        </button>
                    </form>
                </div>

                <!-- Account Information -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-envelope text-blue-600 mr-2"></i>Account Information</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium text-gray-800">{{ $user->email }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Account Created:</span>
                            <span class="font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-800">{{ $user->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            To change your email address, please contact support@otakatik.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('text-purple-600', 'border-purple-600');
            btn.classList.add('text-gray-600', 'border-transparent');
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        
        // Add active class to clicked button
        document.getElementById('tab-' + tabName).classList.remove('text-gray-600', 'border-transparent');
        document.getElementById('tab-' + tabName).classList.add('text-purple-600', 'border-purple-600');
    }
</script>
@endsection
