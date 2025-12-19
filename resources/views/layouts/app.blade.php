<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OtakAtik')</title>

    <link rel="icon" href="{{ asset('images/brain.png') }}" type="image/png">
    <link rel="preload" href="{{ asset('build/assets/font-file-name.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <script src="https://cdn.tiny.cloud/1/40wmpfbvzkycl0abvcvdpedgmg1a5pa6mu5yyv37jgk0thqo/tinymce/7/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    

    {{-- SEMUA LIBRARY (Alpine, FontAwesome, Tailwind) SUDAH DISINI --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('head')
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
    
    {{-- TinyMCE Tetap Pakai CDN (Aman karena sudah ada atribut security) --}}
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous" defer></script>

    @stack('scripts')
</body>
</html>