<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Approval - TheOAsis Portal</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #102a43 0%, #0b69a3 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">Research Supervision Portal</h1>
        <p style="color: #e0e0e0; margin: 5px 0 0;">Lagos State University (LASU)</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 10px 10px;">
        <h2 style="color: #102a43; margin-top: 0;">
            {{ $status === 'approved' ? '🎉 Resource Approved' : '⚠️ Resource Rejected' }}
        </h2>

        <p>Hello,</p>

        <p>Your resource submission has been {{ $status === 'approved' ? 'approved' : 'rejected' }} by your supervisor.</p>

        <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin: 20px 0;">
            <h3 style="color: #102a43; margin-top: 0;">{{ $resourceTitle }}</h3>
            
            @if($status === 'approved')
                <p style="color: #28a745; font-weight: bold;">
                    ✅ Your resource has been APPROVED!
                </p>
                <p><strong>Points Earned:</strong> {{ $pointsEarned }}</p>
            @else
                <p style="color: #dc3545; font-weight: bold;">
                    ❌ Your resource has been REJECTED
                </p>
            @endif

            @if($comment)
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #102a43;">
                    <strong style="color: #102a43;">Supervisor's Comment:</strong>
                    <p style="margin: 5px 0 0; color: #666;">{{ $comment }}</p>
                </div>
            @endif
        </div>

        <p>Thank you for your continued efforts in your research journey.</p>

        <p>Best regards,<br>TheOAsis Research Supervision Portal Team</p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="font-size: 12px; color: #666;">
            This is an automated message from the Research Supervision Portal.<br>
            Please do not reply directly to this email.
        </p>
    </div>

    <div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">
        <p>Lagos State University - Department of Journalism & Media Studies</p>
        <p>&copy; {{ date('Y') }} TheOAsis Research Supervision Portal. All rights reserved.</p>
    </div>
</body>
</html>