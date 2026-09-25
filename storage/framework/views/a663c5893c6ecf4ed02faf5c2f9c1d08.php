<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'currentRoute' => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'currentRoute' => null,
]); ?>
<?php foreach (array_filter(([
    'currentRoute' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<header id="public-header" class="public-header" role="banner">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    
    <!-- Brand -->
    <a href="/" class="flex items-center gap-3 min-w-0" aria-label="TheOAsis Research Portal - Home">
      <span class="h-11 px-3 flex items-center justify-center shrink-0 rounded-xl bg-academic-900 shadow-sm">
        <img 
          src="<?php echo e(asset('img/afriscribe-logo-white.png')); ?>" 
          alt="AfriScribe" 
          class="h-5 sm:h-6 w-auto object-contain" 
          loading="eager" 
          decoding="async"
        >
      </span>
      <div class="min-w-0">
        <span class="font-display font-bold text-sm sm:text-base text-academic-900 truncate block">TheOAsis Research Portal</span>
      </div>
    </a>

    <!-- Trust Signal -->
    <div class="public-header__trust hidden lg:inline-flex" aria-hidden="true">
      <i class="fa-solid fa-circle-check public-header__trust-icon"></i>
      <span>Built for UG and PG research</span>
    </div>

    <!-- Desktop Navigation -->
    <nav class="public-header__nav hidden lg:flex items-center gap-2" aria-label="Main navigation">
      <ul class="public-header__nav-list" role="menubar">
        
        <!-- Product Dropdown -->
        <li class="public-header__nav-item" role="none">
          <button 
            type="button" 
            class="public-header__nav-trigger" 
            role="menuitem" 
            aria-haspopup="true" 
            aria-expanded="false"
            aria-controls="dropdown-product"
          >
            Product
            <i class="fa-solid fa-chevron-down public-header__nav-chevron" aria-hidden="true"></i>
          </button>
          <div 
            id="dropdown-product" 
            class="public-header__dropdown" 
            role="menu" 
            aria-orientation="vertical"
            aria-label="Product"
          >
            <div class="public-header__dropdown-section">
              <button 
                type="button" 
                class="public-header__dropdown-label" 
                aria-haspopup="true" 
                aria-expanded="true"
                aria-controls="dropdown-product-features"
              >
                Features
                <i class="fa-solid fa-chevron-down public-header__dropdown-chevron" aria-hidden="true"></i>
              </button>
              <ul id="dropdown-product-features" class="public-header__dropdown-list public-header__dropdown-list--open" role="menu">
                <li role="none"><a href="<?php echo e(route('public.features')); ?>" class="public-header__dropdown-item" role="menuitem">Features</a></li>
                <li role="none"><a href="<?php echo e(route('public.pricing')); ?>" class="public-header__dropdown-item" role="menuitem">Pricing</a></li>
                <li role="none"><a href="<?php echo e(route('public.integrations')); ?>" class="public-header__dropdown-item" role="menuitem">Integrations</a></li>
                <li role="none"><a href="<?php echo e(route('public.changelog')); ?>" class="public-header__dropdown-item" role="menuitem">Changelog</a></li>
                <li role="none"><a href="<?php echo e(route('public.roadmap')); ?>" class="public-header__dropdown-item" role="menuitem">Roadmap</a></li>
              </ul>
            </div>
          </div>
        </li>

        <!-- Solutions Dropdown -->
        <li class="public-header__nav-item" role="none">
          <button 
            type="button" 
            class="public-header__nav-trigger" 
            role="menuitem" 
            aria-haspopup="true" 
            aria-expanded="false"
            aria-controls="dropdown-solutions"
          >
            Solutions
            <i class="fa-solid fa-chevron-down public-header__nav-chevron" aria-hidden="true"></i>
          </button>
          <div 
            id="dropdown-solutions" 
            class="public-header__dropdown" 
            role="menu" 
            aria-orientation="vertical"
            aria-label="Solutions"
          >
            <div class="public-header__dropdown-section">
              <button 
                type="button" 
                class="public-header__dropdown-label" 
                aria-haspopup="true" 
                aria-expanded="true"
                aria-controls="dropdown-solutions-roles"
              >
                By Role
                <i class="fa-solid fa-chevron-down public-header__dropdown-chevron" aria-hidden="true"></i>
              </button>
              <ul id="dropdown-solutions-roles" class="public-header__dropdown-list public-header__dropdown-list--open" role="menu">
                <li role="none"><a href="<?php echo e(route('public.solutions.supervisors')); ?>" class="public-header__dropdown-item" role="menuitem">For Supervisors</a></li>
                <li role="none"><a href="<?php echo e(route('public.solutions.students')); ?>" class="public-header__dropdown-item" role="menuitem">For Students</a></li>
                <li role="none"><a href="<?php echo e(route('public.solutions.administrators')); ?>" class="public-header__dropdown-item" role="menuitem">For Administrators</a></li>
              </ul>
            </div>
            <div class="public-header__dropdown-section">
              <button 
                type="button" 
                class="public-header__dropdown-label" 
                aria-haspopup="true" 
                aria-expanded="true"
                aria-controls="dropdown-solutions-institutions"
              >
                By Institution
                <i class="fa-solid fa-chevron-down public-header__dropdown-chevron" aria-hidden="true"></i>
              </button>
              <ul id="dropdown-solutions-institutions" class="public-header__dropdown-list public-header__dropdown-list--open" role="menu">
                <li role="none"><a href="<?php echo e(route('public.solutions.institutions')); ?>" class="public-header__dropdown-item" role="menuitem">By Institution Type</a></li>
              </ul>
            </div>
          </div>
        </li>

        <!-- Resources Dropdown -->
        <li class="public-header__nav-item" role="none">
          <button 
            type="button" 
            class="public-header__nav-trigger" 
            role="menuitem" 
            aria-haspopup="true" 
            aria-expanded="false"
            aria-controls="dropdown-resources"
          >
            Resources
            <i class="fa-solid fa-chevron-down public-header__nav-chevron" aria-hidden="true"></i>
          </button>
          <div 
            id="dropdown-resources" 
            class="public-header__dropdown" 
            role="menu" 
            aria-orientation="vertical"
            aria-label="Resources"
          >
            <div class="public-header__dropdown-section">
              <button 
                type="button" 
                class="public-header__dropdown-label" 
                aria-haspopup="true" 
                aria-expanded="true"
                aria-controls="dropdown-resources-learn"
              >
                Learn
                <i class="fa-solid fa-chevron-down public-header__dropdown-chevron" aria-hidden="true"></i>
              </button>
              <ul id="dropdown-resources-learn" class="public-header__dropdown-list public-header__dropdown-list--open" role="menu">
                <li role="none"><a href="<?php echo e(route('public.docs')); ?>" class="public-header__dropdown-item" role="menuitem">Documentation</a></li>
                <li role="none"><a href="<?php echo e(route('public.blog')); ?>" class="public-header__dropdown-item" role="menuitem">Blog</a></li>
                <li role="none"><a href="<?php echo e(route('public.webinars')); ?>" class="public-header__dropdown-item" role="menuitem">Webinars</a></li>
                <li role="none"><a href="<?php echo e(route('public.case-studies')); ?>" class="public-header__dropdown-item" role="menuitem">Case Studies</a></li>
              </ul>
            </div>
            <div class="public-header__dropdown-section">
              <button 
                type="button" 
                class="public-header__dropdown-label" 
                aria-haspopup="true" 
                aria-expanded="true"
                aria-controls="dropdown-resources-dev"
              >
                Developers
                <i class="fa-solid fa-chevron-down public-header__dropdown-chevron" aria-hidden="true"></i>
              </button>
              <ul id="dropdown-resources-dev" class="public-header__dropdown-list public-header__dropdown-list--open" role="menu">
                <li role="none"><a href="<?php echo e(route('public.api-reference')); ?>" class="public-header__dropdown-item" role="menuitem">API Reference</a></li>
              </ul>
            </div>
          </div>
        </li>

        <!-- Company Dropdown -->
        <li class="public-header__nav-item" role="none">
          <button 
            type="button" 
            class="public-header__nav-trigger" 
            role="menuitem" 
            aria-haspopup="true" 
            aria-expanded="false"
            aria-controls="dropdown-company"
          >
            Company
            <i class="fa-solid fa-chevron-down public-header__nav-chevron" aria-hidden="true"></i>
          </button>
          <div 
            id="dropdown-company" 
            class="public-header__dropdown" 
            role="menu" 
            aria-orientation="vertical"
            aria-label="Company"
          >
            <div class="public-header__dropdown-section">
              <button 
                type="button" 
                class="public-header__dropdown-label" 
                aria-haspopup="true" 
                aria-expanded="true"
                aria-controls="dropdown-company-about"
              >
                About
                <i class="fa-solid fa-chevron-down public-header__dropdown-chevron" aria-hidden="true"></i>
              </button>
              <ul id="dropdown-company-about" class="public-header__dropdown-list public-header__dropdown-list--open" role="menu">
                <li role="none"><a href="<?php echo e(route('public.about')); ?>" class="public-header__dropdown-item" role="menuitem">About Us</a></li>
                <li role="none"><a href="<?php echo e(route('public.careers')); ?>" class="public-header__dropdown-item" role="menuitem">Careers</a></li>
                <li role="none"><a href="<?php echo e(route('public.press')); ?>" class="public-header__dropdown-item" role="menuitem">Press</a></li>
                <li role="none"><a href="<?php echo e(route('public.contact')); ?>" class="public-header__dropdown-item" role="menuitem">Contact</a></li>
              </ul>
            </div>
            <div class="public-header__dropdown-section">
              <button 
                type="button" 
                class="public-header__dropdown-label" 
                aria-haspopup="true" 
                aria-expanded="true"
                aria-controls="dropdown-company-legal"
              >
                Legal & Security
                <i class="fa-solid fa-chevron-down public-header__dropdown-chevron" aria-hidden="true"></i>
              </button>
              <ul id="dropdown-company-legal" class="public-header__dropdown-list public-header__dropdown-list--open" role="menu">
                <li role="none"><a href="<?php echo e(route('public.security')); ?>" class="public-header__dropdown-item" role="menuitem">Security</a></li>
                <li role="none"><a href="<?php echo e(route('public.privacy')); ?>" class="public-header__dropdown-item" role="menuitem">Privacy</a></li>
              </ul>
            </div>
          </div>
        </li>

      </ul>
    </nav>

    <!-- Desktop CTAs -->
    <div class="public-header__cta-group hidden lg:flex items-center gap-2">
      <a href="<?php echo e(route('login')); ?>" class="public-header__cta public-header__cta--secondary">
        Login
      </a>
      <a href="<?php echo e(route('login')); ?>#request-demo" class="public-header__cta public-header__cta--primary">
        <i class="fa-solid fa-calendar-check"></i>
        Request Demo
      </a>
    </div>

    <!-- Mobile Toggle -->
    <button 
      type="button" 
      id="public-header__mobile-toggle" 
      class="public-header__mobile-toggle" 
      aria-label="Open navigation menu" 
      aria-expanded="false" 
      aria-controls="public-header__sheet"
    >
      <i class="fa-solid fa-bars" aria-hidden="true"></i>
    </button>

  </div>
</header>

<!-- Mobile Backdrop -->
<div 
  id="public-header__sheet-backdrop" 
  class="public-header__sheet-backdrop" 
  aria-hidden="true"
></div>

<!-- Mobile Bottom Sheet -->
<div 
  id="public-header__sheet" 
  class="public-header__sheet" 
  role="dialog" 
  aria-modal="true" 
  aria-label="Navigation menu"
  aria-hidden="true"
>
  <div class="public-header__sheet-handle">
    <div class="public-header__sheet-handle-bar" aria-hidden="true"></div>
  </div>

  <div class="public-header__sheet-content">
    
    <!-- Product Section -->
    <div class="public-header__sheet-section">
      <button 
        type="button" 
        class="public-header__sheet-trigger" 
        aria-haspopup="true" 
        aria-expanded="false"
        aria-controls="sheet-product"
      >
        Product
        <i class="fa-solid fa-chevron-down public-header__sheet-chevron" aria-hidden="true"></i>
      </button>
      <ul id="sheet-product" class="public-header__sheet-list" role="menu">
        <li role="none"><a href="<?php echo e(route('public.features')); ?>" class="public-header__sheet-item" role="menuitem">Features</a></li>
        <li role="none"><a href="<?php echo e(route('public.pricing')); ?>" class="public-header__sheet-item" role="menuitem">Pricing</a></li>
        <li role="none"><a href="<?php echo e(route('public.integrations')); ?>" class="public-header__sheet-item" role="menuitem">Integrations</a></li>
        <li role="none"><a href="<?php echo e(route('public.changelog')); ?>" class="public-header__sheet-item" role="menuitem">Changelog</a></li>
        <li role="none"><a href="<?php echo e(route('public.roadmap')); ?>" class="public-header__sheet-item" role="menuitem">Roadmap</a></li>
      </ul>
    </div>

    <!-- Solutions Section -->
    <div class="public-header__sheet-section">
      <button 
        type="button" 
        class="public-header__sheet-trigger" 
        aria-haspopup="true" 
        aria-expanded="false"
        aria-controls="sheet-solutions"
      >
        Solutions
        <i class="fa-solid fa-chevron-down public-header__sheet-chevron" aria-hidden="true"></i>
      </button>
      <ul id="sheet-solutions" class="public-header__sheet-list" role="menu">
        <li role="none"><a href="<?php echo e(route('public.solutions.supervisors')); ?>" class="public-header__sheet-item" role="menuitem">For Supervisors</a></li>
        <li role="none"><a href="<?php echo e(route('public.solutions.students')); ?>" class="public-header__sheet-item" role="menuitem">For Students</a></li>
        <li role="none"><a href="<?php echo e(route('public.solutions.administrators')); ?>" class="public-header__sheet-item" role="menuitem">For Administrators</a></li>
        <li role="none"><a href="<?php echo e(route('public.solutions.institutions')); ?>" class="public-header__sheet-item" role="menuitem">By Institution Type</a></li>
      </ul>
    </div>

    <!-- Resources Section -->
    <div class="public-header__sheet-section">
      <button 
        type="button" 
        class="public-header__sheet-trigger" 
        aria-haspopup="true" 
        aria-expanded="false"
        aria-controls="sheet-resources"
      >
        Resources
        <i class="fa-solid fa-chevron-down public-header__sheet-chevron" aria-hidden="true"></i>
      </button>
      <ul id="sheet-resources" class="public-header__sheet-list" role="menu">
        <li role="none"><a href="<?php echo e(route('public.docs')); ?>" class="public-header__sheet-item" role="menuitem">Documentation</a></li>
        <li role="none"><a href="<?php echo e(route('public.blog')); ?>" class="public-header__sheet-item" role="menuitem">Blog</a></li>
        <li role="none"><a href="<?php echo e(route('public.webinars')); ?>" class="public-header__sheet-item" role="menuitem">Webinars</a></li>
        <li role="none"><a href="<?php echo e(route('public.case-studies')); ?>" class="public-header__sheet-item" role="menuitem">Case Studies</a></li>
        <li role="none"><a href="<?php echo e(route('public.api-reference')); ?>" class="public-header__sheet-item" role="menuitem">API Reference</a></li>
      </ul>
    </div>

    <!-- Company Section -->
    <div class="public-header__sheet-section">
      <button 
        type="button" 
        class="public-header__sheet-trigger" 
        aria-haspopup="true" 
        aria-expanded="false"
        aria-controls="sheet-company"
      >
        Company
        <i class="fa-solid fa-chevron-down public-header__sheet-chevron" aria-hidden="true"></i>
      </button>
      <ul id="sheet-company" class="public-header__sheet-list" role="menu">
        <li role="none"><a href="<?php echo e(route('public.about')); ?>" class="public-header__sheet-item" role="menuitem">About Us</a></li>
        <li role="none"><a href="<?php echo e(route('public.careers')); ?>" class="public-header__sheet-item" role="menuitem">Careers</a></li>
        <li role="none"><a href="<?php echo e(route('public.press')); ?>" class="public-header__sheet-item" role="menuitem">Press</a></li>
        <li role="none"><a href="<?php echo e(route('public.contact')); ?>" class="public-header__sheet-item" role="menuitem">Contact</a></li>
        <li role="none"><a href="<?php echo e(route('public.security')); ?>" class="public-header__sheet-item" role="menuitem">Security</a></li>
        <li role="none"><a href="<?php echo e(route('public.privacy')); ?>" class="public-header__sheet-item" role="menuitem">Privacy</a></li>
      </ul>
    </div>

    <!-- Divider -->
    <div class="public-header__sheet-divider" aria-hidden="true"></div>

    <!-- Mobile CTAs -->
    <div class="public-header__sheet-ctas">
      <a href="<?php echo e(route('login')); ?>" class="public-header__sheet-cta public-header__sheet-cta--secondary">
        <i class="fa-solid fa-sign-in-alt"></i>
        Login
      </a>
      <a href="<?php echo e(route('login')); ?>#request-demo" class="public-header__sheet-cta public-header__sheet-cta--primary">
        <i class="fa-solid fa-calendar-check"></i>
        Request Demo
      </a>
    </div>

  </div>
</div>

<!-- Mobile Sticky CTA Bar -->
<div id="public-header__mobile-cta-bar" class="public-header__mobile-cta-bar" aria-hidden="true">
  <a href="<?php echo e(route('login')); ?>#request-demo" class="public-header__sheet-cta public-header__sheet-cta--primary" style="width: 100%;">
    <i class="fa-solid fa-calendar-check"></i>
    Request Demo
  </a>
</div>

<!-- Skip Link -->
<a href="#main-content" class="skip-link">Skip to main content</a><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/components/public-header.blade.php ENDPATH**/ ?>