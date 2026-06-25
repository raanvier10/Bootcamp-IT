<!DOCTYPE html>
<html>
<head>
    <title>Reset Password OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 20px;">
    <div style="max-w-md: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h2 style="color: #111827;">Permintaan Reset Password</h2>
        <p style="color: #374151;">Halo,</p>
        <p style="color: #374151;">Kami menerima permintaan untuk mereset password akun TrashReport Anda. Berikut adalah kode OTP Anda:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #0D530E; background-color: #f3f4f6; padding: 10px 20px; border-radius: 8px;">
                {{ $otp }}
            </span>
        </div>
        
        <p style="color: #374151;">Masukkan kode 6-digit di atas pada halaman aplikasi untuk membuat password baru.</p>
        <p style="color: #dc2626; font-size: 12px;">PENTING: Kode ini hanya berlaku selama 15 menit. Jangan berikan kode ini kepada siapapun.</p>
        
        <br>
        <p style="color: #6b7280; font-size: 12px;">Jika Anda tidak merasa meminta reset password, abaikan email ini.</p>
    </div>
</body>
</html>
