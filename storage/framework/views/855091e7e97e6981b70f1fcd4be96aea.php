<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'title' => 'Research Supervision Portal',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'title' => 'Research Supervision Portal',
]); ?>
<?php foreach (array_filter(([
    'title' => 'Research Supervision Portal',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($title); ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome/css/all.min.css')); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          screens: {
            xs: '400px',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          colors: {
            academic: {
              50: '#f0f4f8',
              100: '#d9e2ec',
              500: '#102a43',
              600: '#0b69a3',
              700: '#035388',
              800: '#003e6b',
              900: '#002744',
            },
            lasu: {
              gold: '#f59e0b',
              blue: '#002744',
            }
          }
        }
      }
    }
  </script>

  <style>
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.03); }
    ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.4); border-radius: 4px; }

    .mobile-nav-toggle {
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 4px;
    }
    .mobile-nav-line {
      display: block;
      width: 20px;
      height: 2px;
      border-radius: 2px;
      background: currentColor;
      transition: transform 0.24s ease, opacity 0.24s ease;
    }
    .mobile-nav-toggle[aria-expanded="true"] .mobile-nav-line:nth-child(1) {
      transform: translateY(6px) rotate(45deg);
    }
    .mobile-nav-toggle[aria-expanded="true"] .mobile-nav-line:nth-child(2) {
      opacity: 0;
    }
    .mobile-nav-toggle[aria-expanded="true"] .mobile-nav-line:nth-child(3) {
      transform: translateY(-6px) rotate(-45deg);
    }
    .mobile-nav-toggle:focus-visible,
    [data-mobile-nav-close]:focus-visible,
    .nav-btn:focus-visible {
      outline: 2px solid #0b69a3;
      outline-offset: 2px;
    }

    @media (max-width: 767.98px) {
      #app-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 50;
        width: min(86vw, 20rem);
        max-width: 20rem;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 1rem;
        transform: translateX(-105%);
        visibility: hidden;
        transition: transform 0.28s ease, visibility 0.28s ease;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
      }
      #app-sidebar.drawer-open {
        transform: translateX(0);
        visibility: visible;
      }
      body.nav-drawer-locked {
        overflow: hidden;
      }
    }
    @media (min-width: 768px) {
      #app-sidebar {
        transform: none !important;
        visibility: visible !important;
      }
    }

    #nav-backdrop {
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.25s ease;
    }
    #nav-backdrop.nav-backdrop-visible {
      pointer-events: auto;
      opacity: 1;
    }
  </style>

  <?php echo app('Illuminate\Foundation\Vite')(['resources/js/header.js']); ?>

  <?php echo e($head ?? ''); ?>

</head>
<body class="h-full flex flex-col font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
  <?php echo e($slot); ?>


  <div id="nav-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 opacity-0" aria-hidden="true"></div>

  <?php if (isset($component)) { $__componentOriginal2851f1e47c9108aacbab05e6d2ec4a68 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2851f1e47c9108aacbab05e6d2ec4a68 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.footer','data' => ['variant' => 'authenticated','role' => session('role'),'user' => auth()->user(),'scope' => session('university_id') ? 'University scope' : 'Authenticated workspace']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('layouts.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'authenticated','role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('role')),'user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()),'scope' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('university_id') ? 'University scope' : 'Authenticated workspace')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2851f1e47c9108aacbab05e6d2ec4a68)): ?>
<?php $attributes = $__attributesOriginal2851f1e47c9108aacbab05e6d2ec4a68; ?>
<?php unset($__attributesOriginal2851f1e47c9108aacbab05e6d2ec4a68); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2851f1e47c9108aacbab05e6d2ec4a68)): ?>
<?php $component = $__componentOriginal2851f1e47c9108aacbab05e6d2ec4a68; ?>
<?php unset($__componentOriginal2851f1e47c9108aacbab05e6d2ec4a68); ?>
<?php endif; ?>
</body>
</html>
<?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/layouts/app.blade.php ENDPATH**/ ?>