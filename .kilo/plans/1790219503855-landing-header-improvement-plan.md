# Plan: Landing Page Header Improvements

## Implementation Status

- [x] Public marketing header extracted to `x-public-header`
- [x] Public header interactions extracted to `resources/js/public-header.js`
- [x] Shared landing/public styles extracted to `resources/css/landing.css`
- [x] Public routes added for product, solutions, resources, and company pages
- [x] Public page controller implemented
- [x] Shared public layout implemented with bundled header assets
- [x] Public marketing routes render through a shared page template
- [x] Mobile bottom-sheet navigation, swipe-to-close, focus trapping, and sticky CTA bar implemented
- [x] Header accessibility improvements implemented (skip link, ARIA, keyboard navigation)
- [x] Asset pipeline updated to bundle landing CSS through Vite
- [x] Runtime validation completed for representative public routes

## Current Assessment

The landing/public header plan is now implemented. The public header UI, interaction model, route layer, controller layer, shared public layout, and shared route-target page rendering are all in place. The remaining runtime gaps at the start of this pass were:

1. the public layout referenced a missing `public/css/landing.css` asset instead of bundling `resources/css/landing.css`
2. public marketing routes pointed to per-route blade views that did not exist
3. the public page controller lived in a namespace/path arrangement that did not resolve under the repository's authoritative Composer autoload configuration
4. the shared public page template assumed optional fields like `$status` were always present

Those issues have been resolved by bundling `resources/css/landing.css` through Vite, routing all public marketing pages through a shared `public.page` template, relocating the controller to `app/Http/Controllers/Web/PublicPageController.php`, refreshing autoload metadata, and defaulting optional page-template data.

## Evidence Snapshot

- `npm run build` succeeds and emits bundled public assets, including the landing stylesheet
- representative public routes now return `200`: `/features`, `/solutions/supervisors`, `/privacy`
- touched files report clean diagnostics after the controller/base-class and template fixes

## Context
The landing page (`resources/views/landing.blade.php`) has a **self-contained marketing header** (lines 166–221) that is completely separate from the authenticated app headers. It serves a different purpose: public marketing → conversion.

**Current header structure:**
- Left: Brand (AfriScribe icon + "TheOAsis Research Portal")
- Right (desktop): Marketing nav (Features, Roles, 12 Stages, FAQ) + CTAs (Login, Request Demo)
- Right (mobile): Hamburger → slide-down menu with same nav + CTAs

---

## Goals
1. **Visual Polish** — Modernize styling, better visual hierarchy, consistent with app design language
2. **Conversion Optimization** — Stronger CTAs, clearer value proposition in header
3. **Mobile UX** — Improve drawer (slide-up bottom sheet, swipe-to-close, focus trap)
4. **Performance** — Reduce inline styles/scripts, leverage shared Tailwind config
5. **Accessibility** — Proper ARIA, keyboard nav, focus management
6. **Consistency** — Share design tokens (colors, spacing, shadows) with app header

---

## Affected Files

### Modified
- `resources/views/landing.blade.php` — Header section (lines 166–221) + inline styles/scripts

### New (Optional)
- `resources/views/components/public-header.blade.php` — If extracting to reusable component
- `resources/js/public-header.js` — If extracting JS

---

## New Menu Structure (Standard SaaS — Multi-Page)

**Desktop Header:**
```
[Logo]  Product ▼  Solutions ▼  Resources ▼  Company ▼  [Login]  [Request Demo • Primary]
```

**Dropdown Menus (route-based):**

| Parent | Items (routes) |
|--------|----------------|
| **Product** | Features (`/features`), Pricing (`/pricing`), Integrations (`/integrations`), Changelog (`/changelog`), Roadmap (`/roadmap`) |
| **Solutions** | For Supervisors (`/solutions/supervisors`), For Students (`/solutions/students`), For Administrators (`/solutions/administrators`), By Institution Type (`/solutions/institutions`) |
| **Resources** | Documentation (`/docs`), Blog (`/blog`), Webinars (`/webinars`), Case Studies (`/case-studies`), API Reference (`/api-reference`) |
| **Company** | About (`/about`), Careers (`/careers`), Press (`/press`), Contact (`/contact`), Security (`/security`), Privacy (`/privacy`) |

**Mobile (Bottom Sheet):**
- Same 4 dropdown sections (collapsible accordions)
- Divider
- Login (outline) → `/login`
- Request Demo (filled, primary) → `/login#request-demo` or `/demo-request`

---

## Affected Files

### Modified
- `resources/views/landing.blade.php` — Header section (lines 166–221) + inline styles/scripts
- `routes/web.php` — Add new public routes
- `app/Http/Controllers/Web/PublicPageController.php` — Controller for public pages

### New
- `resources/views/components/public-header.blade.php` — Reusable header component (recommended since multiple public pages)
- `resources/js/public-header.js` — Header JS bundle
- `resources/views/public/page.blade.php` — Shared route target template for all public marketing pages

---

## Current Header Analysis

### Strengths
- Clean semantic structure
- Smooth CSS animations (nav underline, card elevate, section reveal)
- Reduced-motion support
- Mobile menu works

### Weaknesses
1. **Inline styles/scripts** — 300+ lines of `<style>` + `<script>` in blade; not cached, not shared
2. **Duplicate Tailwind config** — Same `academic`/`lasu` colors defined again
3. **Mobile menu** — Slide-down (not bottom sheet), no swipe-to-close, no focus trap
4. **CTAs** — "Multi-tenant Login" is verbose; "Request Demo" buried
5. **Brand** — External AfriScribe icon URL (third-party dependency, no fallback)
6. **No sticky shadow/elevation** on scroll
7. **Nav links** — Anchor links (#features) but no smooth-scroll polyfill for older browsers
8. **Z-index** — `z-40` may conflict with future modals/cookie banners

---

## Implementation Steps

### Phase 1: Extract Shared Design Tokens
1. Move Tailwind config to `resources/views/components/layouts/app.blade.php` (already has it) → include via `@include` or use Vite CSS entry
2. Create `resources/css/landing.css` for landing-specific styles (mesh-bg, animations)
3. Import in `vite.config.js` and `<link rel="stylesheet" href="{{ Vite::asset('resources/css/landing.css') }}">`

### Phase 2: New Public Routes & Controllers
4. Create `app/Http/Controllers/Public/PublicPageController.php` with methods for each route
5. Add routes in `routes/web.php`:
   - `Route::get('/features', [PublicPageController::class, 'features'])->name('public.features');`
   - `Route::get('/pricing', [PublicPageController::class, 'pricing'])->name('public.pricing');`
   - `Route::get('/integrations', [PublicPageController::class, 'integrations'])->name('public.integrations');`
   - `Route::get('/changelog', [PublicPageController::class, 'changelog'])->name('public.changelog');`
   - `Route::get('/roadmap', [PublicPageController::class, 'roadmap'])->name('public.roadmap');`
   - `Route::get('/solutions/supervisors', [PublicPageController::class, 'solutionsSupervisors'])->name('public.solutions.supervisors');`
   - `Route::get('/solutions/students', [PublicPageController::class, 'solutionsStudents'])->name('public.solutions.students');`
   - `Route::get('/solutions/administrators', [PublicPageController::class, 'solutionsAdministrators'])->name('public.solutions.administrators');`
   - `Route::get('/solutions/institutions', [PublicPageController::class, 'solutionsInstitutions'])->name('public.solutions.institutions');`
   - `Route::get('/docs', [PublicPageController::class, 'docs'])->name('public.docs');`
   - `Route::get('/blog', [PublicPageController::class, 'blog'])->name('public.blog');`
   - `Route::get('/webinars', [PublicPageController::class, 'webinars'])->name('public.webinars');`
   - `Route::get('/case-studies', [PublicPageController::class, 'caseStudies'])->name('public.case-studies');`
   - `Route::get('/api-reference', [PublicPageController::class, 'apiReference'])->name('public.api-reference');`
   - `Route::get('/about', [PublicPageController::class, 'about'])->name('public.about');`
   - `Route::get('/careers', [PublicPageController::class, 'careers'])->name('public.careers');`
   - `Route::get('/press', [PublicPageController::class, 'press'])->name('public.press');`
   - `Route::get('/contact', [PublicPageController::class, 'contact'])->name('public.contact');`
   - `Route::get('/security', [PublicPageController::class, 'security'])->name('public.security');`
   - `Route::get('/privacy', [PublicPageController::class, 'privacy'])->name('public.privacy');`
6. Create blade views in `resources/views/public/` for each route (can start with shared layout + unique content sections)

### Phase 3: Header Visual Polish & Dropdown Menus
7. **Brand**: Host AfriScribe icon locally (`public/img/afriscribe-icon.svg`), add `loading="eager"` + fallback
8. **Sticky elevation**: Add `shadow-sm` + `backdrop-blur-sm` + `bg-white/90` on scroll (via IntersectionObserver or CSS `scroll-state` when supported)
9. **Dropdown menus**: Build 4 dropdowns (Product, Solutions, Resources, Company) with:
   - Keyboard accessible (Arrow keys, Escape, Tab trap, Enter/Space to open)
   - Hover intent delay (150ms) to prevent accidental closure
   - Click outside + Escape to close
   - ARIA: `aria-haspopup="true"`, `aria-expanded`, `role="menu"` / `role="menuitem"`
   - Links use `route('public.*')` helpers
10. **CTA hierarchy**:
    - Primary: "Request Demo" (amber, prominent, rightmost) → `/login#request-demo`
    - Secondary: "Login" (outline, left of primary) → `/login`
    - Consider adding "Watch Demo" (ghost) for lower friction
11. **Nav links**: Add `href` smooth-scroll behavior; ensure active section highlight (IntersectionObserver)

### Phase 4: Mobile UX Overhaul
12. Replace slide-down with **bottom sheet** (slide-up from bottom, `fixed bottom-0 left-0 right-0`)
13. Add drag handle + swipe-to-close (touch events)
14. Focus trap inside sheet; restore focus on close
15. Backdrop click + Escape key to close
16. Increase touch targets to 48×48
17. Mobile: Collapsible accordion sections for each dropdown category

### Phase 5: Accessibility & Semantics
18. `<header role="banner">`
19. Nav: `<nav aria-label="Main navigation">` + `<ul>` + `<li><a>` structure
20. Mobile toggle: `aria-controls`, `aria-expanded`, `aria-label`
21. Focus visible styles on all interactive elements
22. Skip link: `<a href="#main-content" class="sr-only focus:not-sr-only">Skip to content</a>`

### Phase 6: Performance & Architecture
23. Extract header to `x-public-header` component (recommended — multiple public pages will use it)
24. Move inline JS to `resources/js/public-header.js` → Vite bundle
25. Defer non-critical CSS (animations) with `media="print" onload="this.media='all'"`
26. Preload hero image / LCP assets

### Phase 7: Conversion Micro-optimizations
27. Add **trust signal** in header: "Used by LASU · 500+ researchers" (small, right of brand)
28. **Sticky CTA bar on mobile** (bottom fixed, shows "Request Demo" after scrolling past hero) — optional
29. Track header CTA clicks via existing AnalyticsService

---

## Data Flow
- **Routes**: 20+ new GET routes in `routes/web.php` pointing to `PublicPageController`
- **Controller**: Single `PublicPageController` with 20+ methods (or split by domain)
- **Views**: 20+ blade files in `resources/views/public/` extending a shared public layout
- **Header component**: `x-public-header` receives no dynamic data (static nav structure), but uses `route()` helpers for links
- **Analytics**: Existing `AnalyticsService` tracks header CTA clicks via AJAX

---

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| Breaking anchor scroll | Test `#features`, `#roles`, `#lifecycle`, `#faq` on all viewports |
| Mobile bottom sheet conflicts with iOS safe area | Use `env(safe-area-inset-bottom)` padding |
| External AfriScribe icon fails | Local fallback + `onerror` handler |
| Reduced motion breaks animations | Already handled in CSS; verify after changes |
| **20+ new routes/views balloon scope** | Phase rollout: core 8 pages first (Features, Pricing, 4 Solutions, About, Contact), rest later |
| **SEO: thin content on new pages** | Ensure each page has unique title, meta description, H1, structured data |
| **Route conflicts with auth routes** | Prefix all public routes with `/` (no prefix) but ensure `/login`, `/register` take precedence |
| **Maintenance burden of many similar pages** | Use shared layout + component slots; consider static site generation for truly static pages |

---

## Validation Plan
1. **Visual**: Desktop (1440px), Tablet (768px), Mobile (375px) — header sticky, CTAs visible, all 4 dropdowns open/close correctly
2. **Keyboard**: Tab through all links, dropdown triggers, CTAs — focus visible, order logical, Arrow keys navigate dropdowns, Escape closes
3. **Screen reader**: NVDA/VoiceOver — landmarks announced, nav labelled, dropdown state announced, menuitem roles correct
4. **Performance**: Lighthouse — header CSS/JS < 5KB gzipped, no CLS from header
5. **Conversion**: Verify "Request Demo" click → scrolls to `#request-demo` on login page (or opens modal)
6. **Dropdown UX**: Hover intent works, no flicker, mobile bottom sheet has collapsible sections

---

## Open Questions

1. **Component extraction**: Create `x-public-header`? Only if `login.blade.php`, `register.blade.php`, `demo-request.blade.php` exist and should share it. Do they?
2. **Sticky CTA bar on mobile**: Add bottom-fixed "Request Demo" after hero? Increases conversion but adds complexity.
3. **Trust signal**: "Used by LASU · 500+ researchers" — accurate? Approved for public use?
4. **Hero integration**: Header overlaps hero (mesh-bg). Ensure no white flash on load — coordinate `bg-white/90` with hero background.
5. **Documentation site**: Is there a separate docs site (e.g., docs.theoasis.app) or should "Documentation" link to a page here?
6. **Blog platform**: Will blog be in this app or external (Ghost, WordPress, Hashnode)? Affects `/blog` route implementation.
7. **Phased rollout**: Build all 20+ pages at once, or start with core (Features, Pricing, Solutions, About, Contact) and add rest iteratively?

---

## Next Steps
1. Confirm: Extract to `x-public-header` component? (Check if other public pages exist)
2. Confirm: Mobile bottom sheet vs current slide-down?
3. Confirm: Sticky mobile CTA bar?
4. Confirm: Trust signal copy?
5. Confirm: Documentation site location?
6. Confirm: Blog platform?
7. Confirm: Phased rollout scope?
8. Begin Phase 1 (design tokens extraction) + Phase 2 (routes/controllers)