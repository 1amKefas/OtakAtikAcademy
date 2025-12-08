<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OtakAtik')</title>

    <link rel="icon" href="{{ asset('images/brain.png') }}" type="image/png">

    {{-- SEMUA LIBRARY (Alpine, FontAwesome, Tailwind) SUDAH DISINI --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('head')

    {{-- TinyMCE Tetap Pakai CDN (Aman karena sudah ada atribut security) --}}
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