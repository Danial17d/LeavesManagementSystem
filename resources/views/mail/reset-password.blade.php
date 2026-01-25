<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" style="padding:40px 0;">

            ```
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.05);">

                <!-- Header -->
                <tr>
                    <td style="background:#2563eb; padding:24px; text-align:center;">
                        <h1 style="margin:0; color:#ffffff; font-size:22px;">
                            {{ config('app.name') }}
                        </h1>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:32px; color:#111827;">
                        <h2 style="margin-top:0; font-size:20px;">
                            Reset your password
                        </h2>

                        <p style="font-size:15px; line-height:1.6; color:#374151;">
                            We received a request to reset your password. Click the button below to choose a new one.
                        </p>

                        <!-- Button -->
                        <p style="text-align:center; margin:32px 0;">
                            <a href="{{ $url }}"
                               style="
                           background:#2563eb;
                           color:#ffffff;
                           text-decoration:none;
                           padding:14px 28px;
                           border-radius:6px;
                           font-weight:bold;
                           display:inline-block;
                           ">
                                Reset Password
                            </a>
                        </p>

                        <p style="font-size:14px; color:#6b7280;">
                            This password reset link will expire in 10 minutes.
                        </p>

                        <p style="font-size:14px; color:#6b7280;">
                            If you did not request a password reset, you can safely ignore this email.
                        </p>

                        <hr style="border:none; border-top:1px solid #e5e7eb; margin:32px 0;">

                        <p style="font-size:13px; color:#9ca3af;">
                            If the button doesn’t work, copy and paste this link into your browser:
                        </p>

                        <p style="font-size:13px; word-break:break-all; color:#2563eb;">
                            {{ $url }}
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f9fafb; padding:20px; text-align:center; font-size:12px; color:#9ca3af;">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
    ```

</table>

</body>
</html>
