@extends('layouts.app')

@section('title', __('settings.settings'))

@section('content')
<script src="{{ asset('js/student-settings.js') }}"></script>

<div class="bg-white">
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-2"><i class="fas fa-cog mr-2"></i>{{ __('settings.settings') }}</h1>
            <p class="text-purple-100">{{ __('messages.manage_preferences') }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 py-8">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800 font-semibold mb-2">{{ __('messages.error') }}:</p>
                <ul class="text-red-700 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex border-b border-gray-200 mb-8 gap-4 overflow-x-auto">
            <button onclick="switchTab('notifications')" id="tab-notifications" 
                    class="tab-button active px-4 py-3 font-medium text-purple-600 border-b-2 border-purple-600 whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-bell text-purple-600"></i> {{ __('settings.notifications') }}
            </button>
            <button onclick="switchTab('language')" id="tab-language"
                    class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent whitespace-nowrap hover:text-gray-800 flex items-center gap-2">
                <i class="fas fa-globe text-gray-600"></i> {{ __('settings.language_preferences') }}
            </button>
            <button onclick="switchTab('privacy')" id="tab-privacy"
                    class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent whitespace-nowrap hover:text-gray-800 flex items-center gap-2">
                <i class="fas fa-shield-alt text-gray-600"></i> {{ __('settings.privacy') }}
            </button>
            <button onclick="switchTab('account')" id="tab-account"
                    class="tab-button px-4 py-3 font-medium text-gray-600 border-b-2 border-transparent whitespace-nowrap hover:text-gray-800 flex items-center gap-2">
                <i class="fas fa-cog text-gray-600"></i> {{ __('settings.account_security') }}
            </button>
        </div>

        <div id="notifications-tab" class="tab-content">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('settings.email_notifications') }}</h2>
                
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ __('settings.assignment_posted') }}</p>
                                <p class="text-sm text-gray-600">{{ __('settings.assignment_posted_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_assignment_posted" class="sr-only peer" 
                                   {{ $user->notify_assignment_posted ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-hourglass-end text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ __('settings.deadline_reminder') }}</p>
                                <p class="text-sm text-gray-600">{{ __('settings.deadline_reminder_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_deadline_reminder" class="sr-only peer"
                                   {{ $user->notify_deadline_reminder ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-question-circle text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ __('settings.quiz_posted') }}</p>
                                <p class="text-sm text-gray-600">{{ __('settings.quiz_posted_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_quiz_posted" class="sr-only peer"
                                   {{ $user->notify_quiz_posted ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-book text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ __('settings.material_posted') }}</p>
                                <p class="text-sm text-gray-600">{{ __('settings.material_posted_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_material_posted" class="sr-only peer"
                                   {{ $user->notify_material_posted ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-cyan-50 rounded-lg border border-cyan-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-cyan-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-comments text-cyan-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ __('settings.forum_reply') }}</p>
                                <p class="text-sm text-gray-600">{{ __('settings.forum_reply_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_forum_reply" class="sr-only peer"
                                   {{ $user->notify_forum_reply ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-teal-50 rounded-lg border border-teal-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-teal-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ __('settings.submission_graded') }}</p>
                                <p class="text-sm text-gray-600">{{ __('settings.submission_graded_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_submission_graded" class="sr-only peer"
                                   {{ $user->notify_submission_graded ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all mt-6">
                        {{ __('settings.save_notification_settings') }}
                    </button>
                </form>
            </div>
        </div>

        <div id="language-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('settings.language_preferences') }}</h2>
                <p class="text-gray-600 mb-6">{{ __('settings.select_language') }}</p>
                
                <form action="{{ route('settings.locale.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition-all" 
                           id="locale-en" style="border-color: {{ $user->locale === 'en' ? '#9333EA' : '#E5E7EB' }}; background-color: {{ $user->locale === 'en' ? '#F3E8FF' : 'white' }};">
                        <input type="radio" name="locale" value="en" class="w-5 h-5 text-purple-600 cursor-pointer" 
                               onchange="updateLocalePreview()" {{ $user->locale === 'en' ? 'checked' : '' }}>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-language"></i> {{ __('settings.english') }}
                            </p>
                            <p class="text-sm text-gray-600">English</p>
                        </div>
                        <i class="fas fa-check ml-auto text-purple-600" style="display: {{ $user->locale === 'en' ? 'block' : 'none' }};"></i>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition-all" 
                           id="locale-id" style="border-color: {{ $user->locale === 'id' ? '#9333EA' : '#E5E7EB' }}; background-color: {{ $user->locale === 'id' ? '#F3E8FF' : 'white' }};">
                        <input type="radio" name="locale" value="id" class="w-5 h-5 text-purple-600 cursor-pointer" 
                               onchange="updateLocalePreview()" {{ $user->locale === 'id' ? 'checked' : '' }}>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-language"></i> {{ __('settings.indonesian') }}
                            </p>
                            <p class="text-sm text-gray-600">Bahasa Indonesia</p>
                        </div>
                        <i class="fas fa-check ml-auto text-purple-600" style="display: {{ $user->locale === 'id' ? 'block' : 'none' }};"></i>
                    </label>

                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ __('settings.language_will_refresh') }}
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all">
                        <i class="fas fa-save mr-2"></i>{{ __('messages.save') }}
                    </button>
                </form>
            </div>
        </div>

        <div id="privacy-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('settings.privacy') }}</h2>
                
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="border-b border-gray-200 pb-6">
                        <p class="font-semibold text-gray-800 mb-4">{{ __('settings.profile_visibility') }}</p>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
                                   {{ $user->profile_visibility === 'private' ? 'class=border-purple-300 bg-purple-50' : '' }}>
                                <input type="radio" name="profile_visibility" value="private" class="w-4 h-4"
                                       {{ $user->profile_visibility === 'private' ? 'checked' : '' }}>
                                <div>
                                    <p class="font-medium text-gray-800"><i class="fas fa-lock text-red-600 mr-2"></i>{{ __('settings.private_profile') }}</p>
                                    <p class="text-sm text-gray-600">{{ __('settings.private_profile_desc') }}</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
                                   {{ $user->profile_visibility === 'public' ? 'class=border-purple-300 bg-purple-50' : '' }}>
                                <input type="radio" name="profile_visibility" value="public" class="w-4 h-4"
                                       {{ $user->profile_visibility === 'public' ? 'checked' : '' }}>
                                <div>
                                    <p class="font-medium text-gray-800"><i class="fas fa-globe text-blue-600 mr-2"></i>{{ __('settings.public_profile') }}</p>
                                    <p class="text-sm text-gray-600">{{ __('settings.public_profile_desc') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 pb-6 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800"><i class="fas fa-trophy text-yellow-600 mr-2"></i>{{ __('settings.show_achievements') }}</p>
                            <p class="text-sm text-gray-600">{{ __('settings.show_achievements_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_achievements" class="sr-only peer"
                                   {{ $user->show_achievements ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <div class="pb-6 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800"><i class="fas fa-comments text-cyan-600 mr-2"></i>{{ __('settings.allow_direct_messages') }}</p>
                            <p class="text-sm text-gray-600">{{ __('settings.allow_direct_messages_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_direct_messages" class="sr-only peer"
                                   {{ $user->allow_direct_messages ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all">
                        {{ __('settings.save_privacy_settings') }}
                    </button>
                </form>
            </div>
        </div>

        <div id="account-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('settings.account_security') }}</h2>
                
                <div class="border-b border-gray-200 pb-8 mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-key text-purple-600 mr-2"></i>{{ __('settings.change_password') }}</h3>
                    
                    <form action="{{ route('settings.password.update') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('settings.current_password') }}</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                                   placeholder="{{ __('settings.enter_current_password') }}">
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('settings.new_password') }}</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                   placeholder="{{ __('settings.enter_new_password') }}">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('settings.confirm_new_password') }}</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="{{ __('settings.confirm_new_password_text') }}">
                        </div>

                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-all">
                            {{ __('settings.update_password') }}
                        </button>
                    </form>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-envelope text-blue-600 mr-2"></i>{{ __('settings.account_information') }}</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">{{ __('settings.email') }}:</span>
                            <span class="font-medium text-gray-800">{{ $user->email }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">{{ __('settings.account_created') }}:</span>
                            <span class="font-medium text-gray-800">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">{{ __('settings.last_updated') }}:</span>
                            <span class="font-medium text-gray-800">{{ $user->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ __('settings.change_email_contact') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection