<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Research Supervision Portal</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: {{ $bg }}; margin: 0; padding: 0; color: {{ $ink }};">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);">
                    <tr>
                        <td style="padding: 36px 40px; background-color: {{ $brandDark }};">
                            <div style="font-size: 12px; letter-spacing: 0.24em; text-transform: uppercase; color: {{ $brandGold }}; font-weight: 700;">Welcome</div>
                            <h1 style="margin: 18px 0 0 0; color: #ffffff; font-size: 28px; line-height: 1.2;">Welcome to the {{ $universityName ?? $universityCode }} portal</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px 40px;">
                            <p style="margin: 0; font-size: 16px; line-height: 1.7; color: {{ $ink }};">Hello {{ $name }},</p>
                            <p style="margin: 16px 0 0 0; font-size: 16px; line-height: 1.7; color: {{ $ink }};">
                                You have been added as a {{ $roleLabel }} on {{ $portalName }}{{ !empty($universityName) ? ' for ' . $universityName : '' }}. Your supervisor is
                                {{ $supervisorName ?? 'an assigned member of staff' }}.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 24px 0; border-collapse: collapse;">
                                <tr>
                                    <td colspan="2" style="font-size: 14px; font-weight: 700; color: {{ $ink }};; padding-bottom: 8px;">Your login details</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: {{ $muted }}; padding: 6px 0; min-width: 140px;">University code</td>
                                    <td style="font-size: 14px; color: {{ $ink }}; font-family: 'SFMono-Regular', Consolas, monospace; padding: 6px 0;">{{ $universityCode }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: {{ $muted }}; padding: 6px 0; border-top: 1px solid {{ $line }};">Matric number</td>
                                    <td style="font-size: 14px; color: {{ $ink }}; font-family: 'SFMono-Regular', Consolas, monospace; padding: 6px 0; border-top: 1px solid {{ $line }};">{{ $matric }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 14px; color: {{ $muted }}; padding: 6px 0; border-top: 1px solid {{ $line }};">Surname</td>
                                    <td style="font-size: 14px; color: {{ $ink }}; font-family: 'SFMono-Regular', Consolas, monospace; padding: 6px 0; border-top: 1px solid {{ $line }};">the surname on your registration</td>
                                </tr>
                            </table>

                            @if (!empty($loginHint))
                                <p style="margin: 0 0 16px 0; font-size: 14px; line-height: 1.7; color: {{ $muted }};">{{ $loginHint }}</p>
                            @endif

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 28px 0;">
                                <tr>
                                    <td>
                                        <a href="{{ $loginUrl }}" style="display: inline-block; padding: 14px 24px; border-radius: 999px; background-color: {{ $brandBlue }}; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700;">Go to the portal</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 18px 0 0 0; font-size: 14px; line-height: 1.7; color: {{ $muted }};">
                                After signing in, you can upload your research work, track your progress and stay in touch with {{ $supervisorName ?? 'your supervisor' }}.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 40px; background-color: {{ $bg }}; color: {{ $muted }}; font-size: 12px; line-height: 1.6;">
                            This is an automated message from {{ $portalName }}. Do not reply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
