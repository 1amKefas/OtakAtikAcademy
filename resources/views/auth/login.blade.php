<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OtakAtik Academy</title>
    
    {{-- HAPUS CDN TAILWIND & STYLE INLINE --}}
    {{-- GANTI DENGAN VITE --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- FontAwesome (Pastikan pakai crossorigin) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
</head>
<body class="bg-white font-sans"> {{-- Tambahkan font-sans --}}

    <div class="min-h-screen flex">
        
        <div class="hidden lg:block w-1/2 relative">
            <img src="{{ asset('images/koala_login.png') }}" alt="Login Illustration" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
            <div class="w-full max-w-md">
                
                <div class="text-center mb-8">
                    <a href="/" class="inline-block">
                        <img src="{{ asset('images/logo_OtakAtik.png') }}" alt="OtakAtik Logo" class="h-12 mx-auto object-contain">
                    </a>
                </div>

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Sign in</h1>
                    <p class="text-sm text-gray-500">Start your learning journey today</p>
                </div>

                @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium">Login Failed</p>
                            <ul class="list-disc list-inside text-xs text-red-600 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500 sm:text-sm transition-all"
                            placeholder="your@email.com">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <a href="#" class="text-sm font-medium text-orange-600 hover:text-orange-500 opacity-50 cursor-not-allowed" title="Coming soon">
                                Forgot password?
                            </a>
                        </div>
                        <input id="password" type="password" name="password" required
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500 sm:text-sm transition-all"
                            placeholder="••••••••">
                    </div>

                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" 
                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900 cursor-pointer">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transform transition-all duration-200 hover:shadow-lg">
                        Sign In
                    </button>

                    <div class="relative flex py-5 items-center">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">Or continue with</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>

                    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="h-5 w-5 mr-2" alt="Google">
                        Sign in with Google
                    </a>

                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-medium text-orange-600 hover:text-orange-500 transition-colors">
                                Sign up
                            </a>
                        </p>
                    </div>
                </form>
                
                <div class="mt-10 text-center">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} Politeknik Negeri Jakarta. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>