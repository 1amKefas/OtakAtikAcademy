<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Kelulusan</title>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; font-family: 'Helvetica', sans-serif; color: #1f2937; }
        
        /* Gambar Background Full Screen */
        .bg-image {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            z-index: -1;
        }

        /* Container Utama */
        .container {
            width: 100%;
            height: 100%;
            text-align: center;
            position: absolute;
            top: 0;
        }

        /* Pengaturan Posisi Teks (Sesuaikan 'top' dengan desain gambarmu) */
        .content-wrapper {
            margin-top: 280px; /* Jarak dari atas kertas */
        }

        .subtitle {
            font-size: 20px;
            color: #4b5563;
            margin-bottom: 10px;
        }

        .student-name {
            font-size: 48px;
            font-weight: bold;
            text-transform: uppercase;
            color: #111827;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .course-title {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb; /* Warna Biru */
            margin-top: 10px;
            margin-bottom: 40px;
        }

        .footer {
            position: absolute;
            bottom: 60px; /* Jarak dari bawah */
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    {{-- Background Image --}}
    <img src="{{ $background_image }}" class="bg-image">

    <div class="container">
        <div class="content-wrapper">
            <div class="subtitle">Diberikan kepada:</div>
            <div class="student-name">{{ $student_name }}</div>
            
            <div class="subtitle">Atas kelulusannya dalam kursus:</div>
            <div class="course-title">{{ $course_title }}</div>
        </div>
    </div>

    <div class="footer">
        <p>Nomor Sertifikat: <strong>{{ $code }}</strong> &bull; Tanggal Terbit: {{ $date }}</p>
    </div>
</body>
</html>