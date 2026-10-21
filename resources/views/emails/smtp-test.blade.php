<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>{{ $appName }} - Email settings check</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:24px 12px;">
    <tr>
        <td align="center">

            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                   style="width:100%;max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;">

                {{-- Header --}}
                <tr>
                    <td align="center" style="background-color:#4f46e5;padding:36px 24px;">
                        <div style="font-size:34px;font-weight:700;color:#ffffff;line-height:1.2;">{{ $appName }}</div>
                        <div style="font-size:16px;color:#e0e7ff;margin-top:8px;">Email settings check</div>
                    </td>
                </tr>

                {{-- Message --}}
                <tr>
                    <td style="padding:32px 32px 8px 32px;">
                        <div style="font-size:26px;font-weight:700;color:#0f172a;">Hello {{ $name }} 👋</div>
                        <p style="font-size:16px;line-height:1.7;color:#334155;margin:16px 0 0 0;">
                            Great news! Your <strong style="color:#4f46e5;">SMTP settings are working</strong>.
                            This is a test email sent from <strong style="color:#0f172a;">{{ $appName }}</strong>.
                        </p>
                    </td>
                </tr>

                {{-- SMTP details --}}
                <tr>
                    <td style="padding:20px 32px 8px 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#f1f5f9;border-radius:12px;">
                            <tr>
                                <td style="padding:16px 20px 8px 20px;font-size:14px;color:#64748b;">SMTP host</td>
                                <td align="right" style="padding:16px 20px 8px 20px;font-size:14px;font-weight:600;color:#4f46e5;">{{ $host }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px 20px;font-size:14px;color:#64748b;">Port</td>
                                <td align="right" style="padding:8px 20px;font-size:14px;font-weight:600;color:#0f172a;">{{ $port }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px 20px;font-size:14px;color:#64748b;">Encryption</td>
                                <td align="right" style="padding:8px 20px;font-size:14px;font-weight:600;color:#0f172a;">{{ $encryption }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px 20px 16px 20px;font-size:14px;color:#64748b;">Sent at</td>
                                <td align="right" style="padding:8px 20px 16px 20px;font-size:14px;font-weight:600;color:#0f172a;">{{ $sentAt }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Button --}}
                <tr>
                    <td align="center" style="padding:24px 32px 40px 32px;">
                        <a href="{{ $url }}"
                           style="display:inline-block;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:16px;font-weight:600;padding:14px 32px;border-radius:10px;">
                            Open {{ $appName }}
                        </a>
                    </td>
                </tr>
            </table>

            <p style="font-size:12px;color:#94a3b8;margin:16px 0 0 0;">© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>

        </td>
    </tr>
</table>

</body>
</html>