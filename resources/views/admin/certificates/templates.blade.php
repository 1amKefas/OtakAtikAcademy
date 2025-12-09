<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Kelulusan</title>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; font-family: 'Helvetica', sans-serif; text-align: center; }
        
        /* Gambar Background Full Screen */
        .bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
        }

        /* Container Teks */
        .content {
            position: absolute;
            top: 35%; /* Geser naik turun sesuai desain gambar backgroundmu */
            left: 0; 
            width: 100%;
        }

        .name {
            font-size: 50px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            color: #1a202c;
        }

        .course {
            font-size: 30px;
            color: #4a5568;
            font-weight: bold;
        }

        .footer {
            position: absolute;
            bottom: 50px;
            width: 100%;
            font-size: 14px;
            color: #718096;
        }
    </style>
</head>
<body>
    {{-- Background Image --}}
    <img src="{{ $background_image }}" class="bg">

    <div class="content">
        <p style="font-size: 24px; color: #718096; margin-bottom: 10px;">Diberikan Kepada:</p>
        <div class="name">{{ $student_name }}</div>
        
        <p style="font-size: 20px; color: #718096; margin-top: 30px; margin-bottom: 10px;">Telah Menyelesaikan Kursus:</p>
        <div class="course">{{ $course_title }}</div>
    </div>

    <div class="footer">
        ID Sertifikat: {{ $code }} &bull; Tanggal: {{ $date }}
    </div>
</body>
</html>