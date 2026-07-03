<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:32px 16px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:16px;overflow:hidden;">
                <tr><td style="background:#111827;padding:24px 32px;">
                    <span style="color:#ffffff;font-size:18px;font-weight:bold;letter-spacing:-0.5px;">molife</span>
                </td></tr>
                <tr><td style="padding:32px;">
                    <h1 style="margin:0 0 12px;font-size:20px;color:#111827;">Undangan Kolaborasi</h1>
                    <p style="margin:0 0 8px;font-size:14px;line-height:1.6;color:#4b5563;">
                        <strong>{{ $inviterName }}</strong> mengundang kamu untuk berkolaborasi mengerjakan produk
                        <strong>{{ $productName }}</strong> di molife.
                    </p>
                    <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#4b5563;">
                        Sebagai kolaborator kamu bisa ikut mengelola proposal, template pesan, dan melihat statistik produk ini.
                    </p>
                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                        <tr><td style="border-radius:12px;background:#111827;">
                            <a href="{{ $acceptUrl }}" style="display:inline-block;padding:14px 28px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">Terima Undangan</a>
                        </td></tr>
                    </table>
                    <p style="margin:0 0 8px;font-size:12px;line-height:1.6;color:#9ca3af;">
                        Belum punya akun molife? Daftar dulu di <a href="{{ route('register') }}" style="color:#111827;">{{ route('register') }}</a> memakai email ini, lalu klik lagi tombol di atas.
                    </p>
                    <p style="margin:0;font-size:12px;line-height:1.6;color:#9ca3af;">
                        Kalau kamu tidak merasa diundang, abaikan saja email ini.
                    </p>
                </td></tr>
                <tr><td style="padding:16px 32px;border-top:1px solid #f3f4f6;">
                    <p style="margin:0;font-size:11px;color:#9ca3af;">© {{ date('Y') }} molife</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
