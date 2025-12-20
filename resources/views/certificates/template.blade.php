<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kelulusan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 0;
            size: A4 landscape;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 10px;
        }
        
        .certificate {
            width: 100%;
            height: 768px;
            margin: 0 auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            position: relative;
            page-break-after: always;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* White content area */
        .content-wrapper {
            position: relative;
            width: calc(100% - 100px);
            height: calc(100% - 80px);
            background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            padding: 40px;
            z-index: 2;
        }
        
        /* Subtle pattern overlay */
        .content-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(240, 147, 251, 0.03) 0%, transparent 50%);
            pointer-events: none;
        }
        
        /* Top accent bar */
        .accent-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            border-radius: 15px 15px 0 0;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .academy-name {
            font-size: 14px;
            color: #764ba2;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .certificate-title {
            font-size: 52px;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }
        
        .certificate-subtitle {
            font-size: 16px;
            color: #764ba2;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .divider {
            width: 150px;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 15px auto 0;
        }
        
        /* Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .awarded-to {
            font-size: 12px;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .recipient-name {
            font-size: 36px;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        
        .course-label {
            font-size: 11px;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .course-title {
            font-size: 20px;
            color: #764ba2;
            font-weight: 700;
            margin-bottom: 30px;
            font-style: italic;
        }
        
        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 30px;
        }
        
        .footer-item {
            flex: 1;
            text-align: center;
        }
        
        .footer-label {
            font-size: 10px;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .footer-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 700;
        }
        
        .signature-line {
            border-top: 2px solid #2c3e50;
            margin-bottom: 8px;
            height: 40px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            font-size: 20px;
            color: #667eea;
        }
        
        .sig-name {
            font-size: 11px;
            color: #2c3e50;
            font-weight: 700;
            margin-top: 3px;
        }
        
        .sig-title {
            font-size: 9px;
            color: #95a5a6;
            margin-top: 2px;
        }
        
        /* Seal */
        .seal {
            width: 90px;
            height: 90px;
            margin: 0 auto;
            border: 3px solid #f093fb;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 45px;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(245, 87, 108, 0.3);
        }
        
        /* Certificate ID */
        .cert-id {
            position: absolute;
            top: 55px;
            left: 65px;
            font-size: 9px;
            color: #95a5a6;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            z-index: 3;
        }
        
        /* Bottom left - Institutions */
        .institutions-logo {
            position: absolute;
            bottom: 85px;
            left: 65px;
            z-index: 3;
        }
        
        .logo-item {
            font-size: 8px;
            color: #667eea;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .logo-text {
            display: inline-block;
            padding: 4px 8px;
            border: 1px solid #667eea;
            border-radius: 3px;
            margin-right: 5px;
        }
        
        /* Bottom right - Verification */
        .verification {
            position: absolute;
            bottom: 85px;
            right: 65px;
            text-align: center;
            z-index: 3;
            background: rgba(102, 126, 234, 0.08);
            padding: 10px 12px;
            border-radius: 6px;
            border-left: 3px solid #667eea;
        }
        
        .verify-label {
            font-size: 8px;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .verify-url {
            font-size: 6px;
            color: #764ba2;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            max-width: 90px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="accent-bar"></div>
        
        <!-- Certificate ID -->
        <div class="cert-id">CERT: {{ substr($certificate->certificate_number, 0, 8) }}</div>
        
        <!-- Main Content -->
        <div class="content-wrapper">
            <!-- Header -->
            <div class="header">
                <div class="academy-name">OtakAtik Academy</div>
                <div class="certificate-title">SERTIFIKAT</div>
                <div class="certificate-subtitle">Kompetensi Kelulusan</div>
                <div class="divider"></div>
            </div>
            
            <!-- Content -->
            <div class="main-content">
                <div class="awarded-to">Diberikan Kepada</div>
                <div class="recipient-name">{{ strtoupper($user->name) }}</div>
                <div class="course-label">Atas Kelulusannya Pada Kelas</div>
                <div class="course-title">{{ $course->title }}</div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <div class="footer-item">
                    <div class="footer-label">Tanggal Kelulusan</div>
                    <div class="footer-value">{{ \Carbon\Carbon::parse($certificate->completion_date)->isoFormat('D MMMM YYYY') }}</div>
                </div>
                
                <div class="footer-item">
                    <div class="signature-line">✓</div>
                    <div class="sig-name">{{ $certificate->instructor_name }}</div>
                    <div class="sig-title">{{ $certificate->instructor_title }}</div>
                </div>
                
                <div class="footer-item">
                    <div class="seal">★</div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Left - Institutions -->
        <div class="institutions-logo">
            <div class="logo-item">
                <span class="logo-text">PNJ</span>
                <span class="logo-text">TIK</span>
            </div>
            <div style="font-size: 7px; color: #667eea; font-weight: 700; margin-top: 4px;">
                OtakAtik Academy
            </div>
        </div>
        
        <!-- Bottom Right - Verification -->
        <div class="verification">
            <div class="verify-label">Verifikasi</div>
            <div class="verify-url">{{ $verificationUrl }}</div>
        </div>
    </div>
</body>
</html>