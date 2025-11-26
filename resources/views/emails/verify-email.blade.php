<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #1f2937;
            margin: 0;
            font-size: 28px;
        }
        .logo p {
            color: #6b7280;
            font-size: 14px;
            margin: 5px 0 0 0;
        }
        .content {
            margin: 30px 0;
            color: #374151;
            line-height: 1.6;
        }
        .content h2 {
            color: #1f2937;
            margin-top: 0;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 500;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            margin-top: 30px;
            padding-top: 20px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
        .copy-link {
            background-color: #f3f4f6;
            padding: 12px;
            border-radius: 6px;
            word-break: break-all;
            font-size: 12px;
            color: #374151;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>OtakAtik Academy</h1>
            <p>Platform Pembelajaran Online Terpercaya</p>
        </div>

        <div class="content">
            <h2>Verifikasi Email Anda</h2>
            
            <p>Halo {{ $name }},</p>
            
            <p>Terima kasih telah mendaftar di OtakAtik Academy! Untuk melanjutkan, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah:</p>
            
            <p style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email Saya</a>
            </p>

            <p>Atau salin dan buka link ini di browser Anda:</p>
            <div class="copy-link">{{ $verificationUrl }}</div>

            <p>Link verifikasi ini akan berlaku selama 24 jam.</p>
            
            <p>Jika Anda tidak membuat akun ini, silakan abaikan email ini.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} OtakAtik Academy. Semua hak dilindungi.</p>
            <p>Jika Anda memiliki pertanyaan, hubungi kami di support@otakatik-academy.com</p>
        </div>
    </div>
</body>
</html>
