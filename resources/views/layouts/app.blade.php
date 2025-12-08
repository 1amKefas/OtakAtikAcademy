<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OtakAtik')</title>

    <link rel="icon" href="{{ asset('images/brain.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    
    @stack('head')

    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-50 font-sans leading-normal tracking-normal">

    @auth
        @include('components.navbar')
        <div class="pt-20"> @yield('content')
        </div>
    @else
        @includeWhen(View::exists('components.navbar'), 'components.navbar')
        <div class="pt-20">
            @yield('content')
        </div>
    @endauth

    @include('components.footer')

    @stack('scripts')
</body>
</html>