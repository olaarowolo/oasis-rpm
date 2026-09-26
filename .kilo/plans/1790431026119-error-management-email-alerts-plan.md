# Error Management: Friendly User Error UI + Email Every Error to tech@olaarowolo.com

## Context

Today the portal has **no proactive error visibility and no error experience for users**. `app/Exceptions/Handler.php:27` has an empty `reportable()` closure, so exceptions are written to `storage/logs/laravel.log` (`LOG_CHANNEL=stack` → `single`, a single unbounded file) and otherwise disappear. `APP_DEBUG=false`, so a user currently sees the framework's default 500 page: a bare "Server Error" with no guidance, no route back, and no way to tell us what they were doing.

Two further weaknesses make this worse:

- The `single` log channel never rotates. One file grows indefinitely and is hard to search.
- `QUEUE_CONNECTION=sync` and `app/Services/EmailService.php` is a **stub that only calls `Log::info`** — so no alerting layer can be assumed to exist.

This plan delivers two connected layers:

1. **Operations layer** — every reportable production exception is emailed to **tech@olaarowolo.com** with full diagnostic context, throttled, redacted and kill-switchable.
2. **User layer** — a branded, customer-focused error page with a countdown redirect back to where the user came from, plus a "report this to support" action that auto-composes the technical details and emails them to the same address.

### Current infrastructure this builds on

| Concern | Existing asset |
|---|---|
| Exception handling | `app/Exceptions/Handler.php` (legacy skeleton, bound in `bootstrap/app.php`) — `reportable()` empty, `render()` has a custom JSON 401 branch for `api/*` |
| Logging | `config/logging.php` — `stack` → `single` at `storage/logs/laravel.log`, `LOG_LEVEL=debug` |
| Mail transport | `config/mail.php` — SMTP `mail.afriscribe.org:465` implicit SSL, `admin@afriscribe.org`, relaxed peer verification for that cPanel Exim host |
| Mailable pattern | `App\Mail\PortalEmail(string $viewName, array $data)` → `resources/views/emails/portal/*.blade.php`, self-contained inline-styled HTML (`test.blade.php`) |
| Send pattern | `Mail::to($x)->send(new PortalEmail(...))` in `try/catch` + `Log::warning` (`SuperAdminWebController.php:1966`, `ResourceController.php:166-273`) |
| JSON envelope | `BaseController::success()` / `::error()` → `{success, message, data}` — the contract every `fetch` caller expects |
| Frontend | Vite + Tailwind 4 + FontAwesome + Inter font. **Vanilla JS only, no framework** (`resources/js/header.js`, `defense-readiness.js`) |
| App shell | `x-layouts.app` (`resources/views/components/layouts/app.blade.php`) — Vite loads `landing.css` + `header.js`, includes `<x-layouts.footer variant="authenticated">`. Requires session role, so **not** safe for a guest-facing error page |
| Brand tokens | `academic-900/800/700`, amber accent (`#f59e0b`), logo `asset('img/afriscribe-logo-white.png')`, `config('footer.brand')` |
| Queue | `sync` — `queue()` gives no asynchrony; the SMTP timeout is capped instead so alert delivery cannot stall a failing request |
| Sessions / tenancy | `session('user_id')`, `session('role')`, `session('university_id')`, `session('student_id')`, `session('supervisor_id')` |
| Throttling | Laravel `throttle:n,1` middleware already used on all OTP/login routes (`routes/web.php:86-106`) |
| Tests | PHPUnit 10, `tests/Feature/*Test.php` with `Mail::fake()` + `assertSentCount()` (`EmailViewsTest.php`) |

## Decisions

### Operations layer

1. **Recipient**: `tech@olaarowolo.com` from `ERROR_ALERT_TO` (comma-separated). Only hard-coded default.
2. **Sender**: existing `MAIL_FROM_ADDRESS` (`admin@afriscribe.org`), not a fabricated address, so alerts pass SPF/DKIM instead of landing in spam.
3. **Delivery is best-effort and never escalates**: a failed alert send is swallowed and logged only. A broken mail server must not turn a 500 into a timeout, and must not recursively alert.
4. **Delivery is synchronous but bounded**: alerts are sent inline from the exception handler, wrapped in a nested `try/catch` and a static re-entrancy guard, so a report can never be silently lost. Because the send happens while a request is already failing, the SMTP timeout is capped (`MAIL_TIMEOUT`, default 15s) so a hung mail server cannot stall the error page. No queue worker or new infrastructure is required. (`QUEUE_CONNECTION=sync`, so `queue()` would offer no asynchrony anyway; `dispatchAfterResponse` was considered and rejected because terminating callbacks do not run on every failure path, which risks dropping the very alerts this feature exists to deliver.)
5. **Reportable, not literally every Throwable**: Laravel already treats validation, auth and 404 as non-reportable. A deliberate ignore list is kept because 404/419 from crawlers is pure noise. Everything else — `Error`, `TypeError`, `QueryException` — is emailed.
6. **Storms are throttled by fingerprint**: dedupe per `exception class + origin file:line` for a window (default 10 min) with a global hourly ceiling (default 10). Both env-configurable, `0` disables.
7. **Redaction is mandatory**: sensitive request keys replaced with `[redacted]`; explicit header allowlist (never dump all headers — leaks cookies and session IDs).
8. **Environments**: alerts only where `app()->environment()` is in `ERROR_ALERT_ENVIRONMENTS` (default `production`).

### User layer

9. **One page, four statuses**: a single branded view renders 403, 404, 419 and 500. Copy is plain, human and reassuring — no jargon, no class names, no stack trace, no "whoops". `APP_DEBUG=true` (local only) keeps Ignition, so developers are not blocked.
10. **A reference code is the bridge**: the page shows a short deterministic code (`SUP-7K2M4Q`) derived from the exception fingerprint. The same code appears in the automatic alert email, so a user quoting it in a message maps directly to the log line. No database needed.
11. **Countdown redirect, default 10 seconds**, with a visible ring, a live region, and a **Cancel** button. Under `prefers-reduced-motion` the ring animation is dropped but the countdown text and Cancel remain.
12. **Return target is "where you were", resolved safely**:
    - `Referer` header if same-origin;
    - else `session('error_return_url')`, set only from safe `GET` page views;
    - else a **role-aware home** (`route('...')` per `session('role')`, landing page for guests).
    A **loop guard**: the target is rejected if it equals the failing URL, or if the session already shows the same URL errored within the last 60 seconds. Without this, a persistently failing page becomes an infinite redirect carousel.
13. **Support reporting is server-side, not `mailto:`**: a form posts to `POST /support/error-report` and the app emails tech@olaarowolo.com itself. A `mailto:` fallback link is offered alongside, because guests on locked-down machines may have no working client. The user's note is **supplementary** — the technical alert has already been sent automatically; the button says so.
14. **Guest-accessible and rate-limited**: the report route sits outside the auth middleware groups (errors happen on the login page too) and is throttled `throttle:3,1`, with a honeypot field and a reCAPTCHA-ready hook.
15. **JSON stays JSON**: `expectsJson()` / `api/*` requests keep returning the `{success, message}` envelope, so `header.js` and `defense-readiness.js` fetch callers are unaffected. Only HTML requests get the friendly page.
16. **No new dependencies** — Blade, existing Laravel primitives, vanilla JS, Tailwind classes already in the design system.

## Implementation

### Operations layer

#### 1. `config/error_reporting.php` (new)

| Key | Env | Default | Purpose |
|---|---|---|---|
| `enabled` | `ERROR_ALERT_ENABLED` | `true` | Master kill-switch |
| `to` | `ERROR_ALERT_TO` | `tech@olaarowolo.com` | Comma-separated recipients |
| `from_address` | `ERROR_ALERT_FROM` | `MAIL_FROM_ADDRESS` | Envelope sender |
| `environments` | `ERROR_ALERT_ENVIRONMENTS` | `production` | Environments that alert |
| `throttle_minutes` | `ERROR_ALERT_THROTTLE_MINUTES` | `10` | Per-fingerprint suppression; `0` disables |
| `max_per_hour` | `ERROR_ALERT_MAX_PER_HOUR` | `10` | Global hourly ceiling; `0` disables |
| `include_request` | `ERROR_ALERT_INCLUDE_REQUEST` | `true` | Attach request context |
| `max_trace_lines` | `ERROR_ALERT_TRACE_LINES` | `25` | Stack frames included |
| `max_value_length` | `ERROR_ALERT_MAX_VALUE_LENGTH` | `500` | Truncation per input value |
| `redact_keys` | `ERROR_ALERT_REDACT_KEYS` | password, password_confirmation, current_password, token, api_key, secret, otp, pin, authorization, _token, remember_token | Keys whose values are replaced |
| `ignore_exceptions` | — | Validation, Authentication, Authorization, `NotFoundHttpException`, `HttpResponseException`, generic `HttpException` | Fully suppressed classes |
| `include_headers` | — | `user-agent`, `referer`, `accept`, `x-requested-with` | Header allowlist |
| `cache_store` | `ERROR_ALERT_CACHE_STORE` | `null` (default) | Throttle counter store |

#### 2. `app/Services/ErrorAlertService.php` (new)

- `handle(Throwable $e, ?Request $request = null): void` — guarded entry point.
- `shouldAlert(Throwable $e): bool` — enabled → environment → not ignored → not throttled.
- `fingerprint(Throwable $e): string` — `md5(class|origin file:line|top frame)`.
- `referenceCode(Throwable $e): string` — `SUP-` + 6-char uppercase base36 of `crc32(fingerprint)`. **Deterministic and shared by the page and the email.**
- `isThrottled(string $fp): bool` — `Cache::add("error_alert:{$fp}", 1, now()->addMinutes(n))`; global ceiling via `Cache::increment('error_alert:hourly', 1, 60)`.
- `buildContext(Throwable $e, ?Request $r): array` — environment, class, message, origin, first N frames, method/URL/route name, session identity fields, IP, allowlisted headers, redacted+truncated input, `reference_code`, `source` (`automatic` | `user-report`), and `user_note` when present.
- `send(array $context): void` — `Mail::to($recipients)->send(new ErrorAlertMail($context))` in a nested `try/catch` that only `Log::warning`s.
- Static `private static bool $sending` re-entrancy guard: an exception raised while sending an alert is never itself alerted.
- `returnUrlFor(Request $r, ?string $fallback): string` and `isLoopRisk(string $target, Request $r): bool` — the safe-return-target and loop-guard logic for the user layer (kept here so the page, the JSON response and the email all agree).

#### 3. `app/Mail/ErrorAlertMail.php` (new)

Not `ShouldQueue` (queue is `sync`, so it would run inline). Takes pre-built `array $context`. Subject:
- automatic → `[supervise] {RefCode} {ERROR|CRITICAL} {ClassShort} — {first line of message}`
- user report → `[supervise] {RefCode} USER REPORT — {page the user was on}`

Exposes `renderHtml()` for tests, mirroring `PortalEmail::renderHtml()`.

#### 4. `resources/views/emails/portal/error-alert.blade.php` (new)

Self-contained inline-styled HTML following `test.blade.php` exactly (same brand constants, 560px card, `#002744` / `#035388` / `#f59e0b`). Sections: reference code + environment + timestamp + occurrence count, exception class + message, origin `file:line`, request summary, identity, redacted input table, monospace stack trace. When `source === 'user-report'`, a highlighted "What the user was doing" block sits directly under the header. Also added to the `$views` array in `tests/Feature/EmailViewsTest.php`.

### User layer

#### 5. `resources/views/errors/friendly.blade.php` (new)

Standalone document (does **not** extend `x-layouts.app`, which needs a session role and would break for guests). Loads the same Vite bundle (`landing.css`) plus `resources/js/error-page.js`, the Inter font, the AfriScribe favicon, and a `<x-layouts.footer variant="public">` in minimal form.

Structure, top to bottom:

- **Header** — logo lockup (`img/afriscribe-logo-white.png`) and portal name, matching `app-header.blade.php` proportions.
- **Hero card** — amber-accent icon (`fa-solid fa-compass` / `fa-circle-exclamation`), status-appropriate headline and one reassuring sentence:
  - 500 → "Something went wrong on our side" / "This is on us, not you. Your work is safe. We've already sent the technical details to our support team."
  - 403 → "You don't have access to this page" / "If you think you should, ask your supervisor or department admin to check your permissions."
  - 404 → "We couldn't find that page" / "It may have moved, or the link may be out of date."
  - 419 → "Your session expired" / "For your security we signed you out after a period of inactivity. Sign in again to pick up where you left off."
- **Reference code block** — `SUP-XXXXXX` in monospace with a "Copy" button (`navigator.clipboard`, `aria-live` confirmation, graceful fallback to `select()`).
- **Countdown panel** — "Taking you back in `<span data-error-countdown>10</span>s" with an SVG progress ring driven by a CSS custom property, plus a **Cancel** button (`data-error-cancel`) and a "Go back now" link. Copy is neutral, never alarming.
- **Two actions** — primary "Return to previous page" (`data-error-return`, `href` = resolved target) and secondary "Go to my dashboard" (role-aware).
- **Support card** — "Tell us what you were doing" (textarea, 1000 char max, counter), consent line, and two buttons: **"Email this to support"** (primary, POSTs to the report route via `fetch` with `Accept: application/json`, then swaps to a success state: "Sent. Our team will look into reference SUP-XXXXXX.") and a `mailto:` fallback link pre-composed with the reference, page, time and browser.
- **Reassurance footer** — "Your saved work has not been lost" + support email address as plain text.
- Accessibility: `role="status"` + `aria-live="polite"` on the countdown, visible focus rings, `prefers-reduced-motion` honoured, page fully usable with JS disabled (the return link and the support form are plain `<a>` / `<form>` elements that degrade to a normal submit).

#### 6. `resources/js/error-page.js` (new)

Vanilla, no dependencies, mirrors the defensive style of `header.js`:
- Reads `data-*` attributes for the target URL, countdown seconds and reference code; bails silently if absent.
- Drives the ring via `requestAnimationFrame` updating `--error-progress`; ticks the visible seconds; respects `prefers-reduced-motion` by jumping to a static state.
- On expiry, `window.location.assign(target)`. Cancel hides the countdown and reveals a calm static panel.
- Copy button with clipboard + `execCommand` fallback.
- Support form submit via `fetch` with `credentials: 'same-origin'`, `X-Requested-With: XMLHttpRequest`, `Accept: application/json` (the same convention as `header.js`); renders the server message inline; on network failure falls back to a normal form POST so the report is never lost.

#### 7. `app/Http/Controllers/ErrorReportController.php` (new)

`POST /support/error-report` (registered in `routes/web.php`, **outside** the auth groups, `->middleware('throttle:3,1')`):
- Validates `reference` (`required|string|max:24`, `regex:/^SUP-[A-Z0-9]{4,10}$/`), `page_url` (nullable, `url|max:500`), `user_note` (nullable, `string|max:1000`), `honeypot` (must be empty).
- Rejects a `page_url` that is not same-host as `config('app.url')` (prevents the endpoint being used as an open relay/spam vector).
- Re-reads the exception context from the **short-lived cache entry** written by `ErrorAlertService` keyed on the reference code (10-minute TTL), so the report attaches the real trace rather than trusting client input. Falls back to context from the submitted fields only if the cache entry has expired.
- Sends via `ErrorAlertService` with `source = 'user-report'` and `user_note`; returns `{success, message}` through the `BaseController` envelope. Never throws.
- Records `user_note` and the report in the existing `audit_logs` table if that model/table supports a generic action (verify before use; otherwise log-only).

#### 8. `app/Exceptions/Handler.php` (change)

- `register()`: replace the empty closure with

```php
$this->reportable(function (Throwable $e) {
    app(ErrorAlertService::class)->handle($e);
});
```

- `registerRenderable()` (or guarded branches inside `render()`) for the friendly page:
  - `Throwable` with `$e instanceof HttpExceptionInterface` → that status; everything else → 500.
  - `render()` first checks `$request->expectsJson() || $request->is('api/*')` → returns the JSON envelope (preserving the existing 401 branch) so fetch callers keep working.
  - Otherwise returns `response()->view('errors.friendly', [...], $status)`, passing: `status`, `reference` (from the service), `targetUrl` (safe return target), `homeUrl` (role-aware), `countdownSeconds`, `pageUrl`, `occurredAt`.
- `$dontFlash` extended with `token`, `otp`, `api_key` so they never enter the session flash bag.
- `context()` override supplies the console/queue context (artisan command name and argv, job class) so alerts fired outside HTTP are still complete.

#### 9. `app/Console/Commands/SendErrorAlertTest.php` (new)

`php artisan errors:test` builds a synthetic `RuntimeException` payload, pushes it through `ErrorAlertService`, and prints recipient, sender, reference code, throttle state and outcome. Also gains an `--user-report` flag to exercise the user-report template.

#### 10. Configuration changes

`.env`:

```
LOG_CHANNEL=daily
ERROR_ALERT_ENABLED=true
ERROR_ALERT_TO="tech@olaarowolo.com"
ERROR_ALERT_ENVIRONMENTS=production
ERROR_ALERT_THROTTLE_MINUTES=10
ERROR_ALERT_MAX_PER_HOUR=10
ERROR_ALERT_INCLUDE_REQUEST=true
ERROR_PAGE_COUNTDOWN=10
ERROR_PAGE_SUPPORT_EMAIL=tech@olaarowolo.com
```

`.env.example` sets `ERROR_ALERT_ENABLED=false` and `ERROR_PAGE_COUNTDOWN=10` so a fresh clone is silent.

### 11. Tests

**`tests/Feature/ErrorAlertTest.php`** (new, `Mail::fake()`, `config(['error_reporting.environments' => ['testing']])` in `setUp()`):
1. An `RuntimeException` produces exactly one `ErrorAlertMail` addressed to `tech@olaarowolo.com`.
2. `ValidationException` (422) and a 404 GET send **no** mail.
3. Redaction: a POST carrying `password` and `token` yields `[redacted]` in the rendered body and not the secret value.
4. Throttle: same class+origin twice in-window → one mail; after `travel()` past the window → a second.
5. `ERROR_ALERT_ENABLED=false` suppresses mail while still logging.
6. The `error-alert` view renders (also added to `EmailViewsTest`).

**`tests/Feature/ErrorPageTest.php`** (new):
7. A 500 on an HTML request renders `errors.friendly` with status 500, a `SUP-` reference and a non-empty target URL; the response **contains no exception class name and no stack trace**.
8. The same failure on a JSON request returns `{success: false, message: ...}` with a 500 status and no HTML.
9. 403, 404 and 419 each render the page with the right headline copy and status code.
10. The reference code shown on the page equals the reference code in the alert email.
11. Loop guard: with a `Referer` equal to the failing URL, the rendered target is **not** that URL.
12. No `Referer` and no session return URL → target is the role-aware home route; for a guest it is `route('landing')`.
13. `POST /support/error-report` with a valid reference sends one `ErrorAlertMail` whose subject contains `USER REPORT` and whose body contains the user's note.
14. The report endpoint rejects a `page_url` on a foreign host and rejects a filled honeypot, and returns `{success: false}` in both cases.
15. A 4th report request within a minute is throttled (429).
16. With `APP_DEBUG=true` the friendly page is bypassed in favour of Ignition's debug output.
17. `resources/js/error-page.js` — lint/parse check via the existing build (`npm run build` / `vite build`) to catch syntax errors.

## Rollout

1. Land config, service, mailable, views, JS, controller, routes, handler wiring, command, tests.
2. Local: `php artisan errors:test` with `MAIL_MAILER=log` to confirm payload shape and reference-code matching; run the test suite and the Vite build.
3. Deploy to production with `ERROR_ALERT_ENABLED=false`. Trigger a controlled failure (a temporary `abort(500)` on a test-only route) to confirm the friendly page, the countdown, the loop guard and the support button all behave, and that the report email arrives at **tech@olaarowolo.com**.
4. Remove the test route, flip `ERROR_ALERT_ENABLED=true`, `php artisan config:clear && php artisan view:clear`.
5. Watch 24 hours of volume; tune `ERROR_ALERT_THROTTLE_MINUTES` / `ERROR_ALERT_MAX_PER_HOUR` / `ERROR_PAGE_COUNTDOWN` to the observed rate.

## Operational Runbook

- **Alert received** → match the `SUP-` code and origin `file:line` in `storage/logs/laravel-*.log`, reproduce, fix forward. The email is a pointer, not the source of truth.
- **User report received** → same code links it to the original automatic alert; the user's note tells you what they were doing. Reproduce their steps first.
- **Alert storm** → `ERROR_ALERT_ENABLED=false`, `config:clear`, triage from logs, re-enable. The hourly ceiling normally prevents this without intervention.
- **SMTP down / mailbox full** → alerts are dropped by design (logged `warning` only) and user reports fall back to the `mailto:` link, which opens the user's own client. The daily log remains the record.
- **Countdown annoys users** → raise `ERROR_PAGE_COUNTDOWN` or set it to `0` to disable auto-redirect entirely, leaving manual actions.
- **Retention** → `daily` channel keeps 14 days.

## Risks and Mitigations

| Risk | Mitigation |
|---|---|
| Alert email fails and masks the real error | Nested try/catch, `Log::warning` only, never rethrown |
| Alert on alert (recursion) | Static `$sending` re-entrancy guard |
| SMTP latency added to a failing response | Bounded `MAIL_TIMEOUT` (15s) plus a nested `try/catch`; the alert never converts a 500 into a hang |
| Redirect carousel on a persistently failing page | Loop guard: target ≠ failing URL, plus a 60-second session cooldown per URL |
| User bounced back into a loop by the countdown | Cancel button, `ERROR_PAGE_COUNTDOWN=0` to disable, and a manual return link that always works without JS |
| Credential leakage via request input or page source | Redaction key list, per-value truncation, header allowlist, and no trace ever rendered to the user |
| Report endpoint used as a spam relay | Same-host validation on `page_url`, honeypot, `throttle:3,1`, `user_note` length cap, context re-read from cache rather than trusted from the client |
| Sensitive data in the user's free-text note | Note is passed through as text only, length-capped, never rendered as HTML |
| Crawler/scanner noise | 404/419 suppression, per-fingerprint throttle, hourly ceiling |
| Losing context when the failure is in console/queue | `Handler::context()` supplies command name, argv and job class |
