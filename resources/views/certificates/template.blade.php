<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Kelulusan</title>
    <style>
        @page { margin: 0px; }
        body { 
            margin: 0px; 
            font-family: 'Helvetica', sans-serif; 
            color: #333; 
        }
        
        /* Gambar Background Full Screen */
        .bg-image {
            position: absolute;
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            z-index: -1;
        }

        /* Container Utama - Posisi Absolute biar bisa ditumpuk di atas gambar */
        .container {
            width: 100%;
            height: 100%;
            text-align: center;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* PENTING: Atur jarak tulisan dari atas kertas (Margin Top) 
           Sesuaikan angka '280px' ini dengan desain gambar background kamu */
        .content-wrapper {
            margin-top: 280px; 
        }

        .subtitle {
            font-size: 20px;
            color: #555;
            margin-bottom: 10px;
        }

        .student-name {
            font-size: 48px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a202c;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .course-label {
            font-size: 20px;
            color: #555;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .course-title {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb; /* Warna Biru */
            margin-bottom: 40px;
        }

        .footer {
            position: absolute;
            bottom: 60px;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    {{-- Gambar Background (Wajib Absolute Path dari Controller) --}}
    <img src="{{ $background_image }}" class="bg-image">

    <div class="container">
        <div class="content-wrapper">
            <div class="subtitle">Diberikan kepada:</div>
            <div class="student-name">{{ $student_name }}</div>
            
            <div class="course-label">Atas kelulusannya menyelesaikan kursus:</div>
            <div class="course-title">{{ $course_title }}</div>
        </div>
    </div>

    <div class="footer">
        <p>Nomor Sertifikat: <strong>{{ $code }}</strong> &nbsp;|&nbsp; Tanggal Terbit: {{ $date }}</p>
    </div>
</body>
</html>