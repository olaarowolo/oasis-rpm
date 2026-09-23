<?php
    $brandDark = '#002744';
    $brandBlue = '#035388';
    $brandGold = '#f59e0b';
    $ink = '#1f2937';
    $muted = '#6b7280';
    $line = '#e5e7eb';
    $bg = '#f0f4f8';
    $statusMap = [
        'SUBMITTED' => ['label' => 'SUBMITTED', 'color' => '#035388', 'line' => 'A new meeting log was submitted and is pending review.'],
        'PENDING' => ['label' => 'PENDING', 'color' => '#f59e0b', 'line' => 'Your meeting log is pending supervisor review.'],
        'UNDER_REVIEW' => ['label' => 'UNDER REVIEW', 'color' => '#2563eb', 'line' => 'Your meeting log has been selected for review.'],
        'APPROVED' => ['label' => 'APPROVED', 'color' => '#059669', 'line' => 'Your meeting log has been approved.'],
        'REJECTED' => ['label' => 'REWRITE REQUESTED', 'color' => '#e11d48', 'line' => 'A rewrite of your meeting log has been requested. Please revise and resubmit.']
    ];
    $status = $statusMap[$status ?? 'PENDING'] ?? $statusMap['PENDING'];
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?php echo e($title); ?></title></head>
<body style="margin:0;padding:24px 0;background:<?php echo e($bg); ?>;font-family:Arial,Helvetica,sans-serif;">
<div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid <?php echo e($line); ?>;border-radius:16px;overflow:hidden;">
  <div style="background:linear-gradient(135deg,<?php echo e($brandDark); ?>,<?php echo e($brandBlue); ?>);padding:20px 24px;color:#fff;">
    <div style="font-size:16px;font-weight:800;"><?php echo e($portalName ?? 'Research Supervision Portal'); ?> <span style="font-size:10px;background:<?php echo e($brandGold); ?>;color:#1a1a1a;padding:2px 6px;border-radius:6px;vertical-align:middle;"><?php echo e($universityCode ?? 'PORTAL'); ?></span></div>
    <div style="font-size:11px;opacity:.85;margin-top:2px;"><?php echo e($supervisorName ?? 'Supervisor'); ?> • <?php echo e($department ?? 'Research Affairs'); ?></div>
  </div>
  <div style="padding:24px;">
    <h2 style="margin:0 0 12px;color:<?php echo e($brandDark); ?>;font-size:18px;"><?php echo e($title); ?></h2>
    <p style="margin:0 0 14px;color:<?php echo e($ink); ?>;font-size:14px;line-height:1.6;">Dear <?php echo e($isSupervisorNote ? 'Dr. Arowolo' : ($studentName ?? 'Student')); ?>,</p>
    <p style="margin:0 0 14px;"><span style="display:inline-block;padding:4px 10px;border-radius:999px;background:<?php echo e($status['color']); ?>;color:#fff;font-size:11px;font-weight:700;letter-spacing:.3px;"><?php echo e($status['label']); ?></span></p>
    <p style="margin:0 0 14px;color:<?php echo e($ink); ?>;font-size:14px;line-height:1.6;"><?php echo e($status['line']); ?></p>
    <table style="width:100%;border-collapse:collapse;margin:0 0 14px;">
      <tr><td style="padding:6px 12px 6px 0;color:<?php echo e($muted); ?>;font-size:12px;white-space:nowrap;vertical-align:top;">Meeting</td><td style="padding:6px 0;color:<?php echo e($ink); ?>;font-size:13px;"><?php echo e($meetingNumber ?? ''); ?></td></tr>
      <tr><td style="padding:6px 12px 6px 0;color:<?php echo e($muted); ?>;font-size:12px;white-space:nowrap;vertical-align:top;">Log ID</td><td style="padding:6px 0;color:<?php echo e($ink); ?>;font-size:13px;"><?php echo e($logId ?? ''); ?></td></tr>
      <tr><td style="padding:6px 12px 6px 0;color:<?php echo e($muted); ?>;font-size:12px;white-space:nowrap;vertical-align:top;">Student</td><td style="padding:6px 0;color:<?php echo e($ink); ?>;font-size:13px;"><?php echo e($studentName ?? ''); ?></td></tr>
    </table>
    <?php if(!empty($feedback)): ?>
      <blockquote style="margin:0 0 14px;padding:12px 16px;background:<?php echo e($bg); ?>;border-left:4px solid <?php echo e($brandGold); ?>;border-radius:8px;color:<?php echo e($ink); ?>;font-size:14px;">Supervisor note: <?php echo e($feedback); ?></blockquote>
    <?php endif; ?>
    <?php if(!empty($url)): ?>
      <p style="margin:6px 0 14px;"><a href="<?php echo e($url); ?>" style="display:inline-block;padding:10px 18px;background:<?php echo e($brandDark); ?>;color:#fff;text-decoration:none;border-radius:10px;font-size:13px;font-weight:700;">Open the portal</a></p>
    <?php endif; ?>
  </div>
  <div style="padding:14px 24px;border-top:1px solid <?php echo e($line); ?>;color:<?php echo e($muted); ?>;font-size:11px;">This is an automated message from the <?php echo e($portalBrand ?? 'TheOAsis'); ?> <?php echo e($portalName ?? 'Research Supervision Portal'); ?> for <?php echo e($universityName ?? 'your university'); ?>. Please do not reply.</div>
</div>
</body>
</html>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/emails/portal/meeting-status.blade.php ENDPATH**/ ?>