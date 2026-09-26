@php
    $brandDark = '#002744';
    $brandBlue = '#035388';
    $brandGold = '#f59e0b';
    $ink = '#1f2937';
    $muted = '#6b7280';
    $line = '#e5e7eb';
    $bg = '#f0f4f8';
@endphp

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{{ $title }}</title></head>
<body style="margin:0;padding:24px 0;background:{{ $bg }};font-family:Arial,Helvetica,sans-serif;">
<div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid {{ $line }};border-radius:16px;overflow:hidden;">
  <div style="background:linear-gradient(135deg,{{ $brandDark }},{{ $brandBlue }});padding:20px 24px;color:#fff;">
    <div style="font-size:16px;font-weight:800;">{{ $portalName ?? 'Research Supervision Portal' }} <span style="font-size:10px;background:{{ $brandGold }};color:#1a1a1a;padding:2px 6px;border-radius:6px;vertical-align:middle;">{{ $universityCode ?? 'PORTAL' }}</span></div>
    <div style="font-size:11px;opacity:.85;margin-top:2px;">{{ $universityName ?? 'Your university' }} • Supervision relationship update</div>
  </div>
  <div style="padding:24px;">
    <h2 style="margin:0 0 12px;color:{{ $brandDark }};font-size:18px;">{{ $title }}</h2>
    <p style="margin:0 0 14px;color:{{ $ink }};font-size:14px;line-height:1.6;">Hello {{ $recipientName }},</p>
    <p style="margin:0 0 14px;color:{{ $ink }};font-size:14px;line-height:1.6;">{{ $introText }}</p>
    <div style="margin:0 0 14px;padding:14px 16px;background:{{ $bg }};border:1px solid {{ $line }};border-radius:12px;">
      <p style="margin:0;color:{{ $muted }};font-size:11px;text-transform:uppercase;letter-spacing:.12em;">{{ $counterpartRole }}</p>
      <p style="margin:8px 0 0;color:{{ $ink }};font-size:16px;font-weight:700;">{{ $counterpartName }}</p>
      @if(!empty($counterpartMeta))
        <p style="margin:6px 0 0;color:{{ $muted }};font-size:13px;line-height:1.5;">{{ $counterpartMeta }}</p>
      @endif
    </div>
    @if(!empty($relationshipNote))
      <p style="margin:0 0 14px;color:{{ $ink }};font-size:14px;line-height:1.6;">{{ $relationshipNote }}</p>
    @endif
    @if(!empty($url))
      <p style="margin:6px 0 14px;"><a href="{{ $url }}" style="display:inline-block;padding:10px 18px;background:{{ $brandDark }};color:#fff;text-decoration:none;border-radius:10px;font-size:13px;font-weight:700;">{{ $ctaLabel ?? 'Open the portal' }}</a></p>
    @endif
  </div>
  <div style="padding:14px 24px;border-top:1px solid {{ $line }};color:{{ $muted }};font-size:11px;">This is an automated message from the {{ $portalBrand ?? 'AfriScribe Supervise' }} {{ $portalName ?? 'Research Supervision Portal' }} for {{ $universityName ?? 'your university' }}.</div>
</div>
</body>
</html>