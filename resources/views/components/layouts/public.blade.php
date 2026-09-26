@props([
    'title' => 'AfriScribe Supervise',
    'description' => '',
])

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }}</title>
  @if($description)
    <meta name="description" content="{{ $description }}">
  @endif

  <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

  @vite(['resources/css/landing.css', 'resources/js/public-header.js'])

  {{ $head ?? '' }}
</head>
<body class="font-sans text-slate-800 bg-slate-50 min-h-screen flex flex-col">
  <!-- Public Header Component -->
  <x-public-header />

  <main id="main-content" class="flex-1">
    {{ $slot }}
  </main>

  <x-layouts.footer :variant="session('user_id') && session('role') ? 'authenticated' : 'mega-public'" :role="session('role')" :scope="session('university_id') ? 'University scope' : 'Authenticated workspace'" />
  {{ $scripts ?? '' }}
</body>
</html>