@php
    $context = $context ?? [];
    $isUserReport = ($context['source'] ?? 'automatic') === 'user-report';
    $request = $context['request'] ?? [];
    $identity = $context['identity'] ?? [];
    $input = $request['input'] ?? [];
    $headers = $request['headers'] ?? [];
    $trace = $context['trace'] ?? [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {{ $context['reference_code'] ?? '' }}</title>
</head>
<body style="margin:0;padding:24px 0;background:#f0f4f8;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#002744,#035388);padding:20px 24px;color:#fff;">
            <div style="font-size:16px;font-weight:800;">
                AfriScribe Supervise
                <span style="font-size:10px;background:#f59e0b;color:#1a1a1a;padding:2px 6px;border-radius:6px;vertical-align:middle;">{{ strtoupper($context['environment'] ?? 'production') }}</span>
            </div>
            <div style="font-size:11px;opacity:.85;margin-top:2px;">
                {{ $isUserReport ? 'User submitted error report' : 'Automated error alert' }} &bull; {{ $context['occurred_at'] ?? '' }}
            </div>
        </div>

        <div style="padding:24px;">
            <div style="display:inline-block;background:#0f172a;color:#fff;font-family:Menlo,Consolas,monospace;font-size:14px;font-weight:700;letter-spacing:1px;padding:6px 12px;border-radius:8px;">
                {{ $context['reference_code'] ?? 'SUP-UNKNOWN' }}
            </div>

            <h2 style="margin:16px 0 6px;color:#002744;font-size:18px;">
                {{ ($context['level'] ?? 'ERROR') }}: {{ class_basename($context['exception'] ?? 'Error') }}
            </h2>
            <p style="margin:0 0 16px;color:#1f2937;font-size:14px;line-height:1.6;word-break:break-word;">
                {{ $context['message'] ?? 'No message available.' }}
            </p>

            @if($isUserReport)
                <div style="background:#fffbeb;border:1px solid #fcd34d;border-left:4px solid #f59e0b;border-radius:10px;padding:14px 16px;margin-bottom:18px;">
                    <p style="margin:0 0 6px;color:#92400e;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">What the user was doing</p>
                    <p style="margin:0;color:#1f2937;font-size:14px;line-height:1.6;white-space:pre-wrap;">{{ $context['user_note'] ?? 'The user did not add a description.' }}</p>
                </div>
            @endif

            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:13px;color:#1f2937;">
                <tr>
                    <td style="padding:6px 12px 6px 0;color:#6b7280;width:34%;vertical-align:top;">Origin</td>
                    <td style="padding:6px 0;font-family:Menlo,Consolas,monospace;font-size:12px;word-break:break-all;">{{ $context['origin'] ?? 'unknown' }}</td>
                </tr>
                @if(!empty($request))
                    <tr>
                        <td style="padding:6px 12px 6px 0;color:#6b7280;vertical-align:top;">Request</td>
                        <td style="padding:6px 0;word-break:break-all;">{{ $request['method'] ?? '' }} {{ $request['url'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 12px 6px 0;color:#6b7280;vertical-align:top;">Route</td>
                        <td style="padding:6px 0;">{{ $request['route'] ?? 'n/a' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 12px 6px 0;color:#6b7280;vertical-align:top;">IP address</td>
                        <td style="padding:6px 0;">{{ $request['ip'] ?? 'n/a' }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:6px 12px 6px 0;color:#6b7280;vertical-align:top;">Signed in as</td>
                    <td style="padding:6px 0;">
                        @if(!empty($identity))
                            {{ collect($identity)->map(fn($value, $key) => $key.'='.$value)->implode(' &bull; ') }}
                        @else
                            Guest / not signed in
                        @endif
                    </td>
                </tr>
            </table>

            @if(!empty($input))
                <h3 style="margin:18px 0 8px;color:#002744;font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Request input (redacted)</h3>
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:12px;">
                    @foreach($input as $key => $value)
                        <tr>
                            <td style="padding:4px 10px 4px 0;color:#6b7280;width:34%;vertical-align:top;word-break:break-all;">{{ $key }}</td>
                            <td style="padding:4px 0;font-family:Menlo,Consolas,monospace;word-break:break-all;">{{ is_array($value) ? json_encode($value) : $value }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(!empty($headers))
                <h3 style="margin:18px 0 8px;color:#002744;font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Headers</h3>
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:12px;">
                    @foreach($headers as $key => $value)
                        <tr>
                            <td style="padding:4px 10px 4px 0;color:#6b7280;width:34%;vertical-align:top;">{{ $key }}</td>
                            <td style="padding:4px 0;word-break:break-all;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(!empty($trace))
                <h3 style="margin:18px 0 8px;color:#002744;font-size:13px;text-transform:uppercase;letter-spacing:.5px;">Stack trace</h3>
                <div style="background:#0f172a;color:#e2e8f0;border-radius:10px;padding:14px 16px;font-family:Menlo,Consolas,monospace;font-size:11px;line-height:1.7;overflow-x:auto;">
                    @foreach($trace as $index => $frame)
                        <div style="white-space:pre-wrap;">#{{ $index }} {{ $frame['file'] ?? '' }}:{{ $frame['line'] ?? 0 }} {{ $frame['call'] ?? '' }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        <div style="padding:14px 24px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:11px;">
            Automated message from the AfriScribe Supervise error reporting service. The user was shown reference
            <strong>{{ $context['reference_code'] ?? 'SUP-UNKNOWN' }}</strong> and nothing else from this report.
        </div>
    </div>
</body>
</html>
