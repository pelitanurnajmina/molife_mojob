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
                    <h1 style="margin:0 0 12px;font-size:20px;color:#111827;">
                        @if($daysLeft === 0)
                            Langganan kamu berakhir hari ini
                        @elseif($daysLeft === 1)
                            Langganan kamu berakhir besok
                        @else
                            Langganan kamu berakhir {{ $daysLeft }} hari lagi
                        @endif
                    </h1>
                    <p style="margin:0 0 8px;font-size:14px;line-height:1.6;color:#4b5563;">
                        Halo <strong>{{ $name }}</strong>, masa aktif langganan molife kamu berakhir pada <strong>{{ $endsAt }}</strong>.
                    </p>
                    <p style="margin:0 0 24px;font-size:14px;line-height:1.6;color:#4b5563;">
                        Perpanjang sekarang supaya akses, streak sholat, dan semua data tracking kamu tetap berjalan tanpa jeda. Datamu aman dan tidak akan hilang, tapi akses aplikasi terkunci sampai diperpanjang.
                    </p>
                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
                        <tr><td style="border-radius:12px;background:#111827;">
                            <a href="{{ $renewUrl }}" style="display:inline-block;padding:14px 28px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">Perpanjang Sekarang</a>
                        </td></tr>
                    </table>
                    <p style="margin:0;font-size:12px;line-height:1.6;color:#9ca3af;">
                        Pembayaran mudah lewat QRIS dari aplikasi apa pun. Kalau kamu sudah memperpanjang, abaikan email ini.
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
