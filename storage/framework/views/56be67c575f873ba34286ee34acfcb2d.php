<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in verification</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif; color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e5e7eb;">
                    <tr style="background:#0f172a;">
                        <td style="padding:24px; color:#ffffff; font-size:28px; font-weight:bold; text-align:center;">
                            TheOAsis
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px;">
                            <p style="margin:0 0 12px; font-size:20px; font-weight:bold; color:#111827;">Hello <?php echo e($name); ?>,</p>
                            <p style="margin:0 0 20px; font-size:16px; line-height:1.6; color:#374151;">
                                Use the verification code below to sign in to your <?php echo e($role); ?> account.
                            </p>
                            <div style="text-align:center; padding:22px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin:20px 0;">
                                <div style="font-size:12px; letter-spacing:1px; text-transform:uppercase; color:#64748b; margin-bottom:10px;">One-time code</div>
                                <div style="font-size:36px; font-weight:bold; letter-spacing:8px; color:#0f172a;"><?php echo e($otpCode); ?></div>
                            </div>
                            <p style="margin:0; font-size:14px; color:#6b7280; line-height:1.6;">
                                This code expires in 5 minutes. Never share it with anyone.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px 28px; font-size:12px; color:#6b7280; text-align:center; border-top:1px solid #e5e7eb;">
                            Research Supervision Portal
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/emails/login-otp.blade.php ENDPATH**/ ?>