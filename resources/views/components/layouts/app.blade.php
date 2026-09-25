@props([
    'title' => 'Research Supervision Portal',
])

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  @vite(['resources/css/landing.css', 'resources/js/header.js'])

  {{ $head ?? '' }}
</head>
<body class="h-full flex flex-col font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
  {{ $slot }}

  <div id="nav-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 opacity-0" aria-hidden="true"></div>

  <x-layouts.footer variant="authenticated" :role="session('role')" :user="auth()->user()" :scope="session('university_id') ? 'University scope' : 'Authenticated workspace'" />
</body>
</html>
