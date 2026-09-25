@props([
    'title' => 'Super Admin Portal',
    'breadcrumbs' => [],
])

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }}</title>

  <link rel="icon" type="image/svg+xml" href="https://afriscribe.org/favicon.svg">
  <link rel="alternate icon" href="https://afriscribe.org/favicon.ico">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  @vite(['resources/css/landing.css', 'resources/js/header.js'])

  {{ $head ?? '' }}
</head>
<body class="h-full font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
  @include('partials.auth.inline-script')
  <!-- Navigation Backdrop -->
  <div id="nav-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 opacity-0 hidden" aria-hidden="true"></div>

  <!-- Main Wrapper -->
  <div class="min-h-screen lg:flex lg:flex-row lg:items-start">
    <!-- Super Admin Sidebar -->
    <x-super-admin.sidebar :currentUser="$currentUser ?? null" />

    <!-- Header + Content + Footer Column -->
    <div class="flex min-h-screen min-w-0 flex-1 flex-col">
      <!-- Header -->
      <x-app-header role="super-admin" :page-title="$title" :current-user="$currentUser ?? null" :breadcrumbs="$breadcrumbs" />

      <!-- Main Content -->
      <main id="super-admin-main" class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-28 sm:py-8 sm:pb-32">
        {{ $slot }}
      </main>

      <x-layouts.footer variant="authenticated" role="super_admin" :user="$currentUser ?? null" scope="Platform-wide" />
    </div>
  </div>
</body>
</html>