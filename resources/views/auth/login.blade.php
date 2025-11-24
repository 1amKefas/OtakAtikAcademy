<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white">

    <div class="min-h-screen flex">
        
        <!-- Left Side: Full Image (Hidden on Mobile) -->
        <div class="hidden lg:block w-1/2 relative">
            <img src="/images/koala_login.png" alt="Login Illustration" class="absolute inset-0 w-full h-full object-cover">
            <!-- Overlay Gradient (Optional, biar teks lebih kebaca kalau ada) -->
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
            <div class="w-full max-w-md">
                
                <!-- Logo -->
                <div class="text-center mb-8">
                    <a href="/" class="inline-block">
                        <img src="/images/logo_OtakAtik.png" alt="OtakAtik Logo" class="h-12 mx-auto object-contain">
                    </a>
                </div>

                <!-- Form Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Sign in</h1>
                    <p class="text-sm text-gray-500">Start your learning journey today</p>
                </div>

                <!-- Alert Error -->
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

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500 sm:text-sm transition-all"
                            placeholder="your@email.com">
                    </div>

                    <!-- Password Field -->
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

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" 
                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900 cursor-pointer">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transform transition-all duration-200 hover:shadow-lg">
                        Sign In
                    </button>

                    <!-- Register Link -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-medium text-orange-600 hover:text-orange-500 transition-colors">
                                Sign up
                            </a>
                        </p>
                    </div>
                </form>
                
                <!-- Footer Copyright -->
                <div class="mt-10 text-center">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} OtakAtik Academy. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>