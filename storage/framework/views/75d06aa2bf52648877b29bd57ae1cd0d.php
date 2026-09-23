<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete your account setup</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: <?php echo e($bg); ?>; margin: 0; padding: 0; color: <?php echo e($ink); ?>;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);">
                    <tr>
                        <td style="padding: 36px 40px; background-color: <?php echo e($brandDark); ?>;">
                            <div style="font-size: 12px; letter-spacing: 0.24em; text-transform: uppercase; color: <?php echo e($brandGold); ?>; font-weight: 700;">Account Invitation</div>
                            <h1 style="margin: 18px 0 0 0; color: #ffffff; font-size: 28px; line-height: 1.2;">Complete your <?php echo e($roleLabel); ?> account setup</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px 40px;">
                            <p style="margin: 0; font-size: 16px; line-height: 1.7; color: <?php echo e($ink); ?>;">Hello <?php echo e($name); ?>,</p>
                            <p style="margin: 16px 0 0 0; font-size: 16px; line-height: 1.7; color: <?php echo e($ink); ?>;">
                                An account has been created for you on <?php echo e($portalName); ?><?php echo e(!empty($universityName) ? ' for ' . $universityName : ''); ?>. Your email and role have already been registered. Use the button below to complete the remaining details and activate your access.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 28px 0;">
                                <tr>
                                    <td>
                                        <a href="<?php echo e($completionUrl); ?>" style="display: inline-block; padding: 14px 24px; border-radius: 999px; background-color: <?php echo e($brandBlue); ?>; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700;">Complete account setup</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 0; font-size: 14px; line-height: 1.7; color: <?php echo e($muted); ?>;">This invitation expires on <?php echo e($expiresAt); ?>.</p>
                            <p style="margin: 18px 0 0 0; font-size: 14px; line-height: 1.7; color: <?php echo e($muted); ?>;">If the button does not work, copy and paste this link into your browser:</p>
                            <p style="margin: 8px 0 0 0; word-break: break-all; font-size: 13px; line-height: 1.7; color: <?php echo e($brandBlue); ?>;"><?php echo e($completionUrl); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 40px; background-color: <?php echo e($bg); ?>; color: <?php echo e($muted); ?>; font-size: 12px; line-height: 1.6;">
                            This is an automated message from <?php echo e($portalName); ?>.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/emails/portal/account-invite.blade.php ENDPATH**/ ?>