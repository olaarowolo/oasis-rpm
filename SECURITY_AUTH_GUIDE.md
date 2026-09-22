# Security Authentication Guide

## Overview

The Research Supervision Portal has been redesigned with enterprise-grade security features including MFA, rate limiting, session hardening, and comprehensive password policies.

## Security Features Implemented

### 1. Multi-Factor Authentication (MFA)

- Required for admin and super_admin roles
- Token-based MFA with 5-minute expiry
- IP validation to prevent token reuse from different locations
- Secure MFA token generation using Laravel's Str::random()

### 2. Rate Limiting & Brute Force Protection

- Maximum 5 failed login attempts per 60 seconds
- Automatic 15-minute lockout after 5 failed attempts
- IP-based and email/matric-based tracking
- Redis/cache fallback for distributed deployments

### 3. Secure Session Management

- Session regeneration on every request
- Session fixation prevention
- 30-minute session timeout with automatic logout
- User agent validation to detect hijacking
- Security headers (HSTS, CSP, X-Frame-Options, Referrer-Policy)

### 4. Password Security Policies

- Minimum 12 characters
- Must contain uppercase, lowercase, number, and special character
- Checks against common password database
- Argon2id hashing with high memory cost (64MB) and time cost (4)
- Password complexity validation on reset and change

### 5. XSS/CSRF Protection

- Automatic CSRF token generation and validation
- Content Security Policy (CSP) headers
- X-Frame-Options: DENY to prevent clickjacking
- X-Content-Type-Options: nosniff
- Input sanitization via Laravel's validation

### 6. Secure Password Reset

- Time-limited tokens (1 hour expiry)
- IP-based token binding (cannot use from different IP)
- Rate limiting (3 reset requests per day)
- Automatic session invalidation after password change
- Secure email templates with expiration info

## Security Headers Sent

```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=()
```

## Password Requirements

When creating or resetting passwords, users must:

1. Be at least 12 characters long
2. Contain at least one uppercase letter (A-Z)
3. Contain at least one lowercase letter (a-z)
4. Contain at least one number (0-9)
5. Contain at least one special character (!@#$%^&*(),.?":{}|<>)
6. Not be in the common passwords list

## Protected Routes

The following routes require MFA for admin roles:

- `/api/admin/*` - All admin API routes
- `/admin/dashboard` - Admin dashboard
- `/admin/users/*` - User management
- `/admin/config/*` - Configuration
- `/admin/audit-logs/*` - Audit logs

## Security Middleware

### AuthGuard
- Session validation
- MFA verification
- Rate limiting
- Login attempt tracking

### SecureSession
- Session regeneration
- Session fixation prevention
- Security headers
- Session hijacking detection

## Usage Examples

### Login Flow

1. User submits credentials
2. System checks rate limits
3. If locked out, returns 429 with lockout time
4. If valid credentials, generates session
5. For admin roles, sets MFA requirement flag
6. User receives MFA challenge if required

### Password Reset Flow

1. User requests reset
2. Rate limit check (max 3/day)
3. Reset token generated and email sent
4. User clicks link (IP validation)
5. New password validated for complexity
6. All sessions invalidated
7. Success message returned

### MFA Flow

1. Admin logs in successfully
2. System sets MFA required flag in session
3. Subsequent requests check MFA verification
4. User must complete MFA challenge
5. Token validated and session marked verified

## Security Best Practices

1. **Always use HTTPS** - Session cookies require secure flag
2. **Regular password rotation** - Enforce password changes every 90 days
3. **Monitor logs** - Watch for repeated failed attempts
4. **IP whitelisting** - Consider restricting admin access to specific IPs
5. **Session cleanup** - Periodically clean expired sessions

## Security Audit Checklist

- [ ] Enable HTTPS in production
- [ ] Configure Redis for rate limiting (better than cache)
- [ ] Set up log monitoring for failed login attempts
- [ ] Implement IP-based restrictions for admin routes
- [ ] Enable two-factor authentication for all admin accounts
- [ ] Review password policy periodically
- [ ] Audit session storage mechanism
- [ ] Test MFA flow thoroughly

## Troubleshooting

### "Too many attempts" Error
- Wait for lockout period (15 minutes) or clear cache
- Check Redis/cache for lockout keys

### "Session expired" Error
- Session timeout reached (30 minutes)
- User needs to login again

### "MFA required" Error
- Admin/super_admin role requires MFA
- Complete MFA challenge to proceed

### "Invalid reset token" Error
- Token expired (1 hour) or used from different IP
- Request new password reset link

## Compliance

This authentication system helps meet:
- OWASP Top 10 security requirements
- SOC 2 Type II controls
- GDPR security requirements
- NIST Cybersecurity Framework
