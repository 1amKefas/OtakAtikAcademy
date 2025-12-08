<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Kelulusan</title>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; font-family: 'Helvetica', sans-serif; color: #333; }
        
        /* Background Full Screen */
        .certificate-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* Kontainer Teks (Sesuaikan top/left dengan desain gambar kamu) */
        .content {
            position: absolute;
            top: 38%; /* Geser ini biar pas di tengah gambar */
            left: 0;
            width: 100%;
            text-align: center;
        }

        .student-name {
            font-size: 42px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
            color: #1a202c;
            letter-spacing: 1px;
        }

        .description {
            font-size: 18px;
            color: #555;
            margin-bottom: 10px;
        }

        .course-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 30px;
        }

        .footer {
            position: absolute;
            bottom: 60px; /* Geser ini untuk posisi tanggal/kode */
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #718096;
        }
        
        .code {
            font-family: 'Courier', monospace;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    {{-- Gambar Background yang diupload Admin --}}
    <img src="{{ $background_image }}" class="certificate-bg">

    <div class="content">
        <div class="description">Diberikan kepada:</div>
        <div class="student-name">{{ $student_name }}</div>
        
        <div class="description">Atas kelulusannya dalam kursus:</div>
        <div class="course-title">{{ $course_title }}</div>
    </div>

    <div class="footer">
        <div>Diterbitkan pada: {{ $date }}</div>
        <div class="code">No. Seri: {{ $code }}</div>
    </div>
</body>
</html>