<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Request - {{ $universityCode }} Research Supervision Portal</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: {{ $bg }}; margin: 0; padding: 0; color: {{ $ink }};">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 40px 20px 40px; background-color: {{ $brandDark }}; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: {{ $brandGold }}; font-size: 28px; font-weight: bold;">{{ $portalBrand }}</h1>
                            <p style="margin: 10px 0 0 0; color: {{ $muted }}; font-size: 16px;">{{ $portalName }}</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px 40px;">
                            <h2 style="margin: 0 0 20px 0; color: {{ $ink }}; font-size: 22px; font-weight: 600;">Demo Request Received</h2>
                            
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 20px;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid {{ $line }};">
                                        <strong style="color: {{ $muted }}; font-size: 14px;">Requester Name</strong><br>
                                        <span style="color: {{ $ink }}; font-size: 16px; font-weight: 500;">{{ $name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid {{ $line }};">
                                        <strong style="color: {{ $muted }}; font-size: 14px;">Email</strong><br>
                                        <span style="color: {{ $ink }}; font-size: 16px; font-weight: 500;">{{ $email }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid {{ $line }};">
                                        <strong style="color: {{ $muted }}; font-size: 14px;">University</strong><br>
                                        <span style="color: {{ $ink }}; font-size: 16px; font-weight: 500;">{{ $universityName }} ({{ $universityCode }})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid {{ $line }};">
                                        <strong style="color: {{ $muted }}; font-size: 14px;">Department</strong><br>
                                        <span style="color: {{ $ink }}; font-size: 16px; font-weight: 500;">{{ $department }}</span>
                                    </td>
                                </tr>
                            </table>
                            
                            @if(!empty($notes))
                            <div style="margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 6px; border-left: 4px solid {{ $brandBlue }};">
                                <p style="margin: 0; color: {{ $ink }}; font-size: 14px; line-height: 1.6;">
                                    <strong>Notes / What they want to evaluate:</strong><br>
                                    {{ $notes }}
                                </p>
                            </div>
                            @endif
                            
                            <p style="margin: 30px 0 20px 0; color: {{ $ink }}; font-size: 14px; line-height: 1.6;">
                                Thank you for your interest in the {{ $portalName }}. A member of our team will review your request and contact you shortly to schedule your personalized demo.
                            </p>
                            
                            <p style="margin: 0; color: {{ $muted }}; font-size: 12px; line-height: 1.5;">
                                If you have any questions in the meantime, please reply to this email or contact us at <a href="mailto:support@afriscribe.org" style="color: {{ $brandBlue }}; text-decoration: none;">support@afriscribe.org</a>.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 40px; background-color: {{ $bg }}; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; color: {{ $muted }}; font-size: 12px; text-align: center;">
                                &copy; {{ date('Y') }} {{ $portalBrand }}. All rights reserved.
                            </p>
                            <p style="margin: 10px 0 0 0; color: {{ $muted }}; font-size: 12px; text-align: center;">
                                This is an automated message from the {{ $portalName }}.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
