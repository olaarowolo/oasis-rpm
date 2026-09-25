@props([
    'title' => 'TheOAsis Research Portal',
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

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            display: ['Space Grotesk', 'Inter', 'sans-serif']
          },
          colors: {
            academic: {
              50: '#f0f4f8',
              100: '#d9e2ec',
              500: '#102a43',
              600: '#0b69a3',
              700: '#035388',
              800: '#003e6b',
              900: '#002744'
            },
            lasu: {
              gold: '#f59e0b',
              blue: '#002744'
            }
          },
          boxShadow: {
            glow: '0 0 0 1px rgba(245,158,11,.15), 0 20px 50px rgba(2,39,68,.20)'
          }
        }
      }
    }
  </script>

  <style>
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.03); }
    ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.4); border-radius: 4px; }
  </style>

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