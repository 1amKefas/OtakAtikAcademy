<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Helvetica', sans-serif; width: 100%; height: 100%; }
        
        .container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .bg-image {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
        }

        /* Kelas Utility untuk Posisi Absolut */
        .element {
            position: absolute;
            transform: translate(-50%, -50%); /* Biar titik koordinatnya di tengah element */
            text-align: center;
            white-space: nowrap;
        }

        .student-name { font-size: 32px; font-weight: bold; text-transform: uppercase; color: #1a202c; }
        .course-title { font-size: 24px; font-weight: bold; color: #2563eb; }
        .message { font-size: 16px; color: #555; }
        .meta { font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Background --}}
        <img src="{{ $background_image }}" class="bg-image">

        {{-- Dynamic Elements --}}
        
        {{-- 1. Pesan Pengantar --}}
        <div class="element message" style="left: {{ $settings['message']['x'] }}%; top: {{ $settings['message']['y'] }}%;">
            {{ $settings['message']['text'] ?? 'Diberikan kepada:' }}
        </div>

        {{-- 2. Nama Siswa --}}
        <div class="element student-name" style="left: {{ $settings['student_name']['x'] }}%; top: {{ $settings['student_name']['y'] }}%;">
            {{ $student_name }}
        </div>

        {{-- 3. Judul Kursus --}}
        <div class="element course-title" style="left: {{ $settings['course_name']['x'] }}%; top: {{ $settings['course_name']['y'] }}%;">
            {{ $course_title }}
        </div>

        {{-- 4. Tanggal --}}
        <div class="element meta" style="left: {{ $settings['date']['x'] }}%; top: {{ $settings['date']['y'] }}%;">
            {{ $date }}
        </div>

        {{-- 5. Kode --}}
        <div class="element meta" style="left: {{ $settings['code']['x'] }}%; top: {{ $settings['code']['y'] }}%;">
            {{ $code }}
        </div>

        {{-- 6. Tanda Tangan (Jika ada) --}}
        @if($signature_image)
            <div class="element" style="left: {{ $settings['signature']['x'] ?? 50 }}%; top: {{ $settings['signature']['y'] ?? 80 }}%;">
                <img src="{{ $signature_image }}" style="width: 150px;">
            </div>
        @endif
    </div>
</body>
</html>