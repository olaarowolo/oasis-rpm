@php
    $status = (int) ($status ?? 500);
    $reference = $reference ?? 'SUP-UNKNOWN';
    $targetUrl = $targetUrl ?? url('/');
    $homeUrl = $homeUrl ?? url('/');
    $countdownSeconds = (int) ($countdownSeconds ?? 10);
    $pageUrl = $pageUrl ?? url()->current();
    $supportEmail = $supportEmail ?? 'tech@olaarowolo.com';
    $copy = $copy ?? [];

    $fallbackCopy = match ($status) {
        403 => [
            'icon' => 'fa-solid fa-lock',
            'eyebrow' => 'Permission needed',
            'title' => 'You do not have access to this page',
            'body' => 'Your account is signed in, but it is not cleared for this part of the portal. If you think it should be, ask your supervisor or department administrator to check your permissions.',
            'action' => 'Back to your dashboard',
        ],
        404 => [
            'icon' => 'fa-solid fa-compass',
            'eyebrow' => 'Page not found',
            'title' => 'We could not find that page',
            'body' => 'The page may have moved, or the link you followed may be out of date. Your work is untouched.',
            'action' => 'Back to your dashboard',
        ],
        419 => [
            'icon' => 'fa-solid fa-clock-rotate-left',
            'eyebrow' => 'Session expired',
            'title' => 'Your session timed out',
            'body' => 'For your security we sign you out after a period of inactivity. Sign in again and you will pick up exactly where you left off.',
            'action' => 'Sign in again',
        ],
        default => [
            'icon' => 'fa-solid fa-compass',
            'eyebrow' => 'Something went wrong',
            'title' => 'Something went wrong on our side',
            'body' => 'This one is on us, not you. Nothing you saved has been lost, and the technical details are already with our support team.',
            'action' => 'Back to your dashboard',
        ],
    };

    $copy = array_merge($fallbackCopy, $copy);

    $mailtoSubject = rawurlencode('[supervise] Error report '.$reference);
    $mailtoBody = rawurlencode(
        "Reference: {$reference}\n"
        ."Page: {$pageUrl}\n"
        .'Time: '.now()->toDayDateTimeString()."\n"
        .'Browser: '.request()->userAgent()
    );
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>{{ $copy['title'] }} &bull; AfriScribe Supervise</title>

  <link rel="icon" type="image/svg+xml" href="https://afriscribe.org/favicon.svg">
  <link rel="alternate icon" href="https://afriscribe.org/favicon.ico">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/landing.css', 'resources/js/error-page.js'])
  @endif
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-800 antialiased dark:bg-slate-900 dark:text-slate-100">
  <main class="mx-auto flex min-h-screen w-full max-w-3xl flex-col justify-center px-4 py-10 sm:px-6">
    <div class="flex items-center gap-3">
      <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-tr from-academic-900 via-academic-800 to-amber-600 text-white shadow-sm">
        <img src="{{ asset('img/afriscribe-logo-white.png') }}" alt="AfriScribe" class="h-6 w-auto object-contain">
      </div>
      <div>
        <p class="text-sm font-bold text-slate-900 dark:text-white">AfriScribe Supervise</p>
        <p class="text-[11px] text-slate-500 dark:text-slate-400">Research Supervision Portal</p>
      </div>
    </div>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 dark:border-slate-700 dark:bg-slate-800/60" aria-labelledby="error-title">
      <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
        <i class="{{ $copy['icon'] }} text-lg" aria-hidden="true"></i>
      </div>

      <p class="mt-5 text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-300">{{ $copy['eyebrow'] }}</p>
      <h1 id="error-title" class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl dark:text-white">{{ $copy['title'] }}</h1>
      <p class="mt-3 max-w-xl text-[15px] leading-7 text-slate-600 dark:text-slate-300">{{ $copy['body'] }}</p>

      <div class="mt-6 flex flex-wrap items-center gap-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 dark:border-slate-600 dark:bg-slate-900/60">
        <div class="min-w-0">
          <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Your reference</p>
          <p class="font-mono text-sm font-bold text-slate-900 dark:text-white" data-error-reference>{{ $reference }}</p>
        </div>
        <button type="button" data-error-copy="{{ $reference }}" class="ml-auto inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-academic-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:text-white">
          <i class="fa-solid fa-copy text-[11px]" aria-hidden="true"></i>
          <span data-error-copy-label>Copy reference</span>
        </button>
        <p class="sr-only" role="status" aria-live="polite" data-error-copy-status></p>
      </div>

      @if ($countdownSeconds > 0)
        <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/60" data-error-countdown-root data-error-seconds="{{ $countdownSeconds }}">
          <div class="flex items-center gap-4">
            <div class="relative h-12 w-12 shrink-0" aria-hidden="true">
              <svg viewBox="0 0 44 44" class="h-12 w-12 -rotate-90">
                <circle cx="22" cy="22" r="20" fill="none" stroke="currentColor" stroke-width="3" class="text-slate-200 dark:text-slate-700"></circle>
                <circle cx="22" cy="22" r="20" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="text-academic-700 dark:text-academic-300" data-error-ring stroke-dasharray="125.6" stroke-dashoffset="0"></circle>
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Taking you back in <span class="font-mono font-bold" data-error-countdown>{{ $countdownSeconds }}</span>s
              </p>
              <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">{{ $targetUrl }}</p>
            </div>
            <button type="button" data-error-cancel class="shrink-0 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-academic-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:text-white">
              Cancel
            </button>
          </div>
        </div>
      @endif

      <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ $targetUrl }}" data-error-return class="inline-flex items-center gap-2 rounded-xl bg-academic-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-academic-800 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-academic-600">
          <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>
          <span>{{ $copy['action'] }}</span>
        </a>
        <a href="{{ $homeUrl }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-300 transition hover:ring-slate-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-academic-600 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600">
          <i class="fa-solid fa-house text-xs" aria-hidden="true"></i>
          <span>Go to my dashboard</span>
        </a>
      </div>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 dark:border-slate-700 dark:bg-slate-800/60" aria-labelledby="support-title">
      <h2 id="support-title" class="text-lg font-bold text-slate-900 dark:text-white">Tell us what you were doing</h2>
      <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">
        The technical details are already with our team. A short note about what you clicked just before this happened helps us fix it faster.
      </p>

      <form method="POST" action="{{ route('support.error-report') }}" class="mt-4 space-y-3" data-error-report-form>
        @csrf
        <input type="hidden" name="reference" value="{{ $reference }}">
        <input type="hidden" name="page_url" value="{{ $pageUrl }}">
        <div class="absolute -left-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
          <label for="error-report-website">Website</label>
          <input type="text" id="error-report-website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <label for="error-report-note" class="sr-only">What were you doing?</label>
        <textarea id="error-report-note" name="user_note" rows="3" maxlength="1000" data-error-report-note placeholder="I was saving my proposal when the page stopped responding…" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-academic-600 focus:outline-none focus:ring-2 focus:ring-academic-600/30 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"></textarea>

        <div class="flex flex-wrap items-center gap-3">
          <button type="submit" data-error-report-submit class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow-sm transition hover:bg-amber-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500 disabled:cursor-not-allowed disabled:opacity-60">
            <i class="fa-solid fa-paper-plane text-xs" aria-hidden="true"></i>
            <span data-error-report-label>Email this to support</span>
          </button>
          <a href="mailto:{{ $supportEmail }}?subject={{ $mailtoSubject }}&amp;body={{ $mailtoBody }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-300 transition hover:ring-slate-400 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600">
            <i class="fa-solid fa-envelope text-xs" aria-hidden="true"></i>
            <span>Email from my own app</span>
          </a>
          <p class="text-xs text-slate-500 dark:text-slate-400" role="status" aria-live="polite" data-error-report-status></p>
        </div>
      </form>
    </section>

    <p class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
      Need a hand? Email <a href="mailto:{{ $supportEmail }}" class="font-semibold text-academic-700 underline dark:text-academic-300">{{ $supportEmail }}</a> and quote reference <span class="font-mono">{{ $reference }}</span>.
    </p>
  </main>
</body>
</html>
