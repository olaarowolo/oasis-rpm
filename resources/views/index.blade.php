<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Research Supervision Portal | Multi-Tenant Access</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome 6 Icons -->
  <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
  <!-- Google Fonts: Inter -->
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
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.03);
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(156, 163, 175, 0.4);
      border-radius: 4px;
    }
    .fade-in {
      animation: fadeIn 0.25s ease-out forwards;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(6px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ===== Mobile off-canvas navigation drawer ===== */
    /* On small screens the sidebar becomes a fixed slide-in panel. On md+ it
       behaves as a normal inline sidebar (transforms/fixed positioning reset). */
    @media (max-width: 767px) {
      #app-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 50;
        width: 84%;
        max-width: 20rem;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 1rem;
        transform: translateX(-100%);
        transition: transform 0.28s ease;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
      }
      #app-sidebar.drawer-open {
        transform: translateX(0);
      }
      /* Prevent the page scrolling behind an open drawer. */
      body.nav-drawer-locked {
        overflow: hidden;
      }
    }

     /* Backdrop behind the mobile drawer. */
     #nav-backdrop {
       transition: opacity 0.25s ease;
     }

     /* Supervision log cards: collapsible details start hidden. */
     .meeting-log-details-collapsible {
       max-height: 0;
       overflow: hidden;
     }
   </style>

</head>

<body class="h-full flex flex-col font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 transition-colors duration-200">

  @include('partials.auth.login-gate')
  @include('partials.auth.admin-mfa-modal')
  @include('partials.auth.demo-request-modal')
  @include('partials.auth.toast-container')
  @include('partials.auth.inline-script')
</body>
</html>