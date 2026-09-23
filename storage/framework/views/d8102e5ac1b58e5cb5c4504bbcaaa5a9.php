<?php
    $brandDark = '#002744';
    $brandBlue = '#035388';
    $brandGold = '#f59e0b';
    $ink = '#1f2937';
    $muted = '#6b7280';
    $line = '#e5e7eb';
    $bg = '#f0f4f8';
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
    <p style="margin:0 0 14px;color:<?php echo e($ink); ?>;font-size:14px;line-height:1.6;">Dear <?php echo e($studentName); ?>,</p>
    <p style="margin:0 0 14px;"><span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#d97706;color:#fff;font-size:11px;font-weight:700;letter-spacing:.3px;">CONDITIONALLY APPROVED</span></p>
    <p style="margin:0 0 14px;color:<?php echo e($ink); ?>;font-size:14px;line-height:1.6;">Your research topic has been <strong>conditionally approved</strong>. You may proceed with the next stage, but you <strong>must satisfy the conditions below</strong> before your work can be fully accepted.</p>
    <blockquote style="margin:0 0 14px;padding:12px 16px;background:<?php echo e($bg); ?>;border-left:4px solid <?php echo e($brandGold); ?>;border-radius:8px;color:<?php echo e($ink); ?>;font-size:14px;">“<?php echo e($topic); ?>”</blockquote>
    <?php if(!empty($conditions)): ?>
      <div style="margin:0 0 14px;padding:12px;background:#fffbeb;border-left:4px solid #f59e0b;border-radius:8px;">
        <strong style="color:#92400e;">Conditions:</strong>
        <ul style="margin:6px 0 0 18px;padding:0;color:#92400e;font-size:13px;line-height:1.6;">
          <?php $__currentLoopData = explode("\n", $conditions); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lineItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(trim($lineItem) !== ''): ?>
              <li><?php echo e(trim($lineItem)); ?></li>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>
    <?php if(!empty($comment)): ?>
      <blockquote style="margin:0 0 14px;padding:12px 16px;background:<?php echo e($bg); ?>;border-left:4px solid <?php echo e($brandGold); ?>;border-radius:8px;color:<?php echo e($ink); ?>;font-size:14px;">Supervisor comment: <?php echo e($comment); ?></blockquote>
    <?php endif; ?>
    <table style="width:100%;border-collapse:collapse;margin:0 0 14px;">
      <tr><td style="padding:6px 12px 6px 0;color:<?php echo e($muted); ?>;font-size:12px;white-space:nowrap;vertical-align:top;">Approved on</td><td style="padding:6px 0;color:<?php echo e($ink); ?>;font-size:13px;"><?php echo e($approvedDate ?? ''); ?></td></tr>
    </table>
    <p style="margin:0 0 14px;color:<?php echo e($ink); ?>;font-size:14px;line-height:1.6;">Please ensure you meet all listed conditions. If you have questions, contact your supervisor before proceeding.</p>
    <?php if(!empty($url)): ?>
      <p style="margin:6px 0 14px;"><a href="<?php echo e($url); ?>" style="display:inline-block;padding:10px 18px;background:<?php echo e($brandDark); ?>;color:#fff;text-decoration:none;border-radius:10px;font-size:13px;font-weight:700;">View your portal</a></p>
    <?php endif; ?>
  </div>
  <div style="padding:14px 24px;border-top:1px solid <?php echo e($line); ?>;color:<?php echo e($muted); ?>;font-size:11px;">This is an automated message from the <?php echo e($portalBrand ?? 'TheOAsis'); ?> <?php echo e($portalName ?? 'Research Supervision Portal'); ?> for <?php echo e($universityName ?? 'your university'); ?>. Please do not reply.</div>
</div>
</body>
</html>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/emails/portal/topic-conditionally-approved.blade.php ENDPATH**/ ?>