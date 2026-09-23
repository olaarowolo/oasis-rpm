<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - TheOAsis Research Portal</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(to right, #002744, #0b69a3); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">TheOAsis Research Portal</h1>
        <p style="color: #a0c0e0; margin: 5px 0 0;">Password Reset Request</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2 style="color: #002744; margin-top: 0;">Hello, <?php echo e($userName); ?>!</h2>

        <p>We received a request to reset your password for your TheOAsis Research Portal account. If you didn't make this request, you can safely ignore this email.</p>

        <p>To reset your password, click the button below:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="<?php echo e($resetUrl); ?>" style="background: #0b69a3; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Reset Password</a>
        </div>

        <p style="font-size: 14px; color: #666;">Or copy and paste this link into your browser:</p>
        <p style="font-size: 12px; color: #999; word-break: break-all;"><?php echo e($resetUrl); ?></p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <h3 style="color: #002744; margin-bottom: 10px;">Important Security Information:</h3>
        <ul style="color: #666; font-size: 14px; padding-left: 20px;">
            <li>This link will expire in <strong><?php echo e($expiresIn); ?></strong></li>
            <li>This link can only be used from the IP address that requested it</li>
            <li>Your new password must meet our security requirements</li>
        </ul>

        <p style="color: #666; font-size: 14px;">
            If you didn't request a password reset, please ignore this email or contact support if you have concerns.
        </p>

        <p style="color: #999; font-size: 12px; margin-top: 30px;">
            TheOAsis Research Portal<br>
            by Afriscribe
        </p>
    </div>

    <div style="background: #002744; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;">
        <p style="color: #a0c0e0; font-size: 12px; margin: 0;">
            This is an automated message. Please do not reply directly to this email.
        </p>
    </div>
</body>
</html>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/emails/password-reset.blade.php ENDPATH**/ ?>