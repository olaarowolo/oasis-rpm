# Student Account Recovery via Knowledge-Based Authentication (KBA)

## Context

The student login gate (`resources/views/partials/auth/login-gate.blade.php`) currently requires a
university **email** as the first step: `handleStudentEmailSubmit` → `/api/auth/student/send-otp`
→ `/api/auth/student/verify-otp` → credentials step (`matric_number` + `lastname` +
`university_code`) → `AuthController::loginStudent`.

The gap: a student who is locked out and cannot receive or recall the email on file cannot start
the flow at all, even though `matric_number` + `lastname` are strong, university-issued identity
markers and the student record exposes several other confirmable fields.

`AuthController::loginStudent` (AuthController.php:36) already authenticates with
`university_code` + `matric_number` + `lastname`. The recovery flow reuses this identity proof and
adds a knowledge-based confirmation of an additional field, then sends a login OTP to the
on-file email (so "it can find their email").

## Goal

Add an account-recovery path for students that starts from `matric_number` + `lastname` (no email
required up front), confirms one of their other registered details, then issues a login OTP to the
email found on the record. Reuses the existing `verifyStudentOtp` final step.

## Decisions

1. **Minimum identity proof:** `university_code` + `matric_number` + `lastname` (matches
   `loginStudent`). Only `active` and `graduated` students qualify (mirrors
   `loginStudent` status scoping). Suspended/archived students cannot recover here.
2. **Final access grant = OTP to on-file email (recommended).** After KBA confirmation, dispatch a
   login OTP to the student's recorded email; the existing `verifyStudentOtp` finalises the login.
   This reuses the OTP infrastructure and adds email-channel verification. The alternative (direct
   session login with no OTP) is a one-line branch but is NOT recommended — mark as a config flag
   `RECOVERY_GRANT=otp|direct` for deployments that want lower friction.
3. **Challenge selection:** backend picks ONE random pop­ulated confirmable field per start
   request (less attacker control, simpler frontend). The selected field + masked hint are
   returned; the expected value is stored only server-side (cache), never returned.
4. **Confirmable fields** (only non-empty values are offered):
   `phone` (mask → last 4 digits), `full_name` (mask → first letter + last token), `degree_level`
   (enum: BSc/MSc/PhD — show label, ask to retype), `faculty`, `department`, `programme` (mask →
   first 2 chars + length). Comparison: case-insensitive, trimmed.
5. **No email enumeration:** a failed/unknown lookup returns the same generic message as a
   successful start. The email is never revealed until the OTP is dispatched to it.
6. **Rate limiting / lock:** per-matic number, 5 failed confirm attempts → 15-minute lockout;
   per-IP throttle on start requests.
7. **Channel:** new routes under `/api/auth/student/recovery/*`, wired in `web.php` (public, no
   auth middleware) and added to the `api/auth` API grouping for consistency.

## Data / Schema

- No schema change strictly required. Optional enhancement: add `purpose` enum/string column on
  `otp_tokens` (`login` vs `login_recovery`) for audit clarity. If added, migrate and set default
  `login`; recovery writes `login_recovery`. Marked **optional** — skip if minimizing diff.
- Confirmable source: `students` columns `full_name`, `email`, `phone`, `degree_level`,
  `faculty`, `department`, `programme`, plus relationship fields (`supervisor.user.name`,
  `supervisor.department`) if a supervisor is linked.

## API Endpoints

All in `AuthController` (new methods) or a thin `StudentRecoveryController` extending
`BaseController`. Recommendation: keep in `AuthController` next to `sendStudentOtp`/`verifyStudentOtp`
to share `simulateSlowOperation`, `generateSecureOtp`, `sendOtpMail`, `logOtpLookupMiss`.

### 1. `POST /api/auth/student/recovery/start`
Body: `university_code`, `matric_number`, `lastname`.
- Resolve `University` by code (upper-cased). Not found → `simulateSlowOperation()` + generic
  message, 200 OK (no enumeration).
- Resolve `Student` by (university_id, matric_number, lastname) with status in [active, graduated].
  Not found → generic message, 200 OK.
- Compute pop­ulated confirmable fields; pick one at random.
- Store challenge in cache: key `student_recovery:{hash(matric+ip+time)}`, payload
  `{user_id, student_id, email, field, expected_hash, attempts:0, ip}`, TTL 10 min, IP-bound.
- Return `challenge_id` + `field` + `hint` (masked) + generic message. Do NOT return email.

### 2. `POST /api/auth/student/recovery/confirm`
Body: `challenge_id`, `field`, `value`.
- Load challenge cache; verify IP matches; verify `field` matches stored field; verify not expired.
- `hash_equals` on `strtolower(trim(value))` vs `strtolower(expected)`. Increment `attempts`.
- On mismatch: if attempts ≥ 5 → lock key `student_recovery_lock:{matric}` TTL 15 min + clear
  challenge, return generic failure. Else update cache, return generic failure.
- On success: clear challenge; delete existing student OTPs; generate OTP; create `OtpToken`
  (role `student`, email = student email); `sendOtpMail`; return `email` (full, only to the
  verified identity) + `email_hint` (masked) so the UI can route into the existing OTP step.
  Reuse the send path from `sendStudentOtp` (AuthController.php:473-487).

### 3. (Existing, reused) `POST /api/auth/student/verify-otp`
No change. The recovery OTP is a normal `student` role token on the same `user_id`, so
`verifyStudentOtp` (AuthController.php:506) logs the student in identically to the email-OTP flow.
Frontend should prefill/keep the email context that `verifyStudentOtp` expects.

## Frontend Changes (`resources/views/partials/auth/login-gate.blade.php` + `inline-script.blade.php`)

- Add a **"Can't access your email?"** link on the student email step → `showStudentRecoveryStep()`.
- New student recovery step (`#student-recovery-step`, hidden by default) with:
  - university selector (reuse `university-selector` partial, type `student-recovery`),
  - matric number input, lastname input, submit.
  - On submit → `handleStudentRecoveryStart(event)` → `/api/auth/student/recovery/start` →
    render challenge: masked hint + single input for the confirmed value + submit.
- `handleStudentRecoveryConfirm(event)` → `/api/auth/student/recovery/confirm` → on success, jump
  to `#student-otp-step` (prefilled with the masked email context) and start the OTP flow.
- `resendOtpCode()` already works; point it at `sendStudentOtp` is NOT suitable here (requires email).
  Add a recovery-specific resend that calls `recovery/confirm` again? No — prefer "restart recovery"
  (back to matric+lastname). Keep OTP resend via the existing `verifyStudentOtp` email path: the
  OTP exists for `pendingStudentEmail`-equivalent. Simplest: store the resolved on-file email in a
  `data-` attr after confirm so `pendingStudentEmail` is set, enabling the existing resend OTB
  behaviour. (Frontend wiring detail; implementation agent to finalise.)

## Security

- Generic responses on start (no enumeration, no email leak).
- `simulateSlowOperation()` on miss paths (timing attack parity with `loginStudent`).
- Challenge cache IP-bound, single-use, 10-min TTL, `hash_equals` comparison.
- OTP: single-use, 5-min TTL, `hash_equals`, deletes prior student tokens (existing).
- Rate limit: 5 confirm failures → 15-min lockout keyed by matric; start throttle per IP.
- Only `active`/`graduated` students (no suspended/archived account takeover).
- Audit log: record `student_account_recovery` event (start, challenge field chosen, confirm
  success/fail) with student_id, ip, masked email — if `AuditLog` is used elsewhere, follow that
  pattern.

## Edge Cases & Failure Modes

- Student found but email empty → fail closed: return generic failure (cannot send OTP). Do not
  reveal this reason.
- Multiple populated fields → random pick; deterministic per request is fine.
- Challenge expired / IP changed mid-flow → clear and redirect to start.
- OTP email send failure → `500`-style error, no account state change, challenge consumed? Keep
  challenge alive on send failure so student can retry confirm without re-entering matric.
- Student has no confirmable populated field (rare: only lastname/full_name/email set) → fall
  back to `full_name` challenge (always present) with masking.

## Tests (mirror `tests/Feature/AuthTest.php` conventions — `RefreshDatabase`, `Mail::fake`)

- `test_student_recovery_start_does_not_leak_account_existence` — unknown matric returns generic
  message, no `data.email`, status 200.
- `test_student_recovery_start_returns_masked_challenge_for_known_student` — returns `challenge_id`,
  `field` (one of the known set), `hint` (masked), never the full email.
- `test_student_recovery_confirm_with_valid_detail_sends_otp_to_onfile_email` — correct detail
  → `Mail::assertSent(LoginOtpMail::class)` to student email; returns masked email hint, no full
  email in response body.
- `test_student_recovery_confirm_with_invalid_detail_fails_and_rate_limits` — wrong value → fail;
  5 failures → lockout.
- `test_student_recovery_full_flow_logs_in_via_existing_otp_verify` — end-to-end: start → confirm
  → use returned OTP with `/api/auth/student/verify-otp` → session `user_id`/`student_id`/`role`
  set, `dashboard_url` = `/student/dashboard`.
- `test_student_recovery_rejects_suspended_student` — suspended student cannot start recovery.

## Rollout / Migration

- New code is additive; no breaking changes to existing login flows.
- The "Can't access your email?" link is opt-in UI behind the existing student tab — no migration.
- Optional `purpose` column: add migration `add_purpose_to_otp_tokens_table`, default `login`,
  backfill not required. Gate on `Schema::hasColumn` if mixed versions.
- No data migration needed for existing students (fields already populated).

## Validation Checklist

- [ ] Start returns generic message + no email for unknown matric
- [ ] Start returns `challenge_id`, `field`, masked `hint` for known active/graduated student
- [ ] Confirm with correct detail sends OTP to on-file email (Mail fake asserts sent + recipient)
- [ ] Confirm with wrong detail fails; locks after 5 attempts (15-min lockout key exists)
- [ ] OTP dispatched is consumable by existing `/api/auth/student/verify-otp` (login session set)
- [ ] Suspended/archived students blocked at start
- [ ] Email never returned in plaintext by start or confirm (only masked hint)
- [ ] Frontend: recovery branch reachable from student login gate; back to email step works
- [ ] No changes to supervisor/admin login flows
- [ ] Tests pass: `php artisan test --filter=AuthTest`

## Next Steps

1. Add endpoints + routes (`AuthController` methods + `web.php` entries).
2. Add optional `purpose` column migration (or skip).
3. Wire frontend recovery step in `login-gate.blade.php` + `inline-script.bake.php`.
4. Add feature tests in `tests/Feature/AuthTest.php`.
5. Run `php artisan test --filter=AuthTest` + `php artisan test --filter='*Recovery*'`.
