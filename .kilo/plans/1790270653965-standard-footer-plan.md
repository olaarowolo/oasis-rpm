# Standard Footer Implementation Plan

## Context

The OAsis Research Portal has four layout types, each with inconsistent footer handling:

| Layout | Current Footer | Location |
|--------|----------------|----------|
| Public (`components/layouts/public.blade.php`) | Inline (lines 73-86) | Brand + tagline + AfriScribe attribution |
| App (`components/layouts/app.blade.php`) | **None** | Used by Student, Supervisor, Admin dashboards |
| Super Admin (`components/layouts/super-admin.blade.php`) | Component (`x-super-admin.footer`) | Detailed: brand, quick links, system status, GitHub, copyright, legal |
| Auth (`components/layouts/auth.blade.php`) | **None** | Login, registration, invitation pages |

## Goal

Create a **single reusable footer component** with variant-based configuration that serves all four layouts consistently, while allowing per-layout customisation via props and slots.

---

## Decisions

### 1. Component Location
- New component: `resources/views/components/layouts/footer.blade.php`
- Replaces inline public footer and super-admin footer component

### 2. Variant Props
The footer accepts a `variant` prop with five presets, each with sensible defaults:

| Variant | Brand | Version | Legal Links | External Links | System Status | Quick Links |
|---------|-------|---------|-------------|----------------|---------------|-------------|
| `public` | ✓ | ✗ | ✓ | ✗ | ✗ | ✗ |
| `app` | ✓ | ✓ | ✓ | ✓ (website) | ✗ | ✗ |
| `super-admin` | ✓ | ✓ | ✓ | ✓ (website) | ✓ | ✓ (dashboard routes) |
| `auth` | ✓ (minimal) | ✗ | ✗ | ✗ | ✗ | ✗ |
| `minimal` | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |

All boolean props can be overridden individually.

### 3. Configuration Source
- Version: `config('app.version')` — add to `config/app.php`
- Brand name: `config('app.name')` (already exists)
- Website URL: config or env

### 4. Slots for Customisation
- `brand` — custom brand markup (default: icon + app name)
- `links` — custom link groups
- `legal` — custom legal links
- `status` — custom system status badge
- `bottom` — content below the divider (copyright line by default)

---

## Implementation Steps

### Step 1: Add Version to Config
**File:** `config/app.php`
- Add `'version' => env('APP_VERSION', '1.0.0'),` after `name`

### Step 2: Create Reusable Footer Component
**File:** `resources/views/components/layouts/footer.blade.php`

Props:
```php
@props([
    'variant' => 'minimal',           // public | app | super-admin | auth | minimal
    'showBrand' => null,              // override variant default
    'showVersion' => null,
    'showLegalLinks' => null,
    'showExternalLinks' => null,
    'showSystemStatus' => null,
    'showQuickLinks' => null,
    'quickLinks' => [],               // [['label' => '', 'route' => ''], ...]
    'legalLinks' => [],               // [['label' => '', 'url' => ''], ...]
    'externalLinks' => [],            // [['label' => '', 'url' => '', 'icon' => ''], ...] (e.g., website only)
    'systemStatus' => 'All Systems Operational',
    'systemStatusColor' => 'emerald', // emerald | amber | rose
])
```

Structure:
1. **Top row**: Brand | Quick Links (desktop) | External Links + System Status
2. **Divider**
3. **Bottom row**: Copyright | Legal Links

Use Tailwind classes consistent with existing layouts (dark mode, responsive).

### Step 3: Update Public Layout
**File:** `resources/views/components/layouts/public.blade.php`
- Remove inline footer (lines 73-86)
- Add `<x-layouts.footer variant="public" />` before closing `</body>`
- Pass AfriScribe attribution via `brand` slot or `showBrand` with custom content

### Step 4: Update App Layout
**File:** `resources/views/components/layouts/app.blade.php`
- Add `<x-layouts.footer variant="app" />` before closing `</body>`
- Ensure main content wrapper has `flex-1` / `min-h-[calc(100vh-...)]` so footer sticks to bottom

### Step 5: Update Super Admin Layout
**File:** `resources/views/components/layouts/super-admin.blade.php`
- Replace `<x-super-admin.footer />` with `<x-layouts.footer variant="super-admin" :quickLinks="$quickLinks ?? []" />`
- Pass breadcrumbs-aware quick links if needed
- Can deprecate `components/super-admin/footer.blade.php` after verification
- **Note:** Super-admin footer currently has GitHub link; new component will only include website link by default

### Step 6: Update Auth Layout
**File:** `resources/views/components/layouts/auth.blade.php`
- Add `<x-layouts.footer variant="auth" />` before closing `</body>`

### Step 7: Add APP_VERSION to Environment
**Files:** `.env.example`, `.env` (local)
- `APP_VERSION=1.0.0`
- `WEBSITE_URL=https://olaarowolo.com` (optional, for website link)

### Step 8: Testing & Verification
- Load each layout type in browser (public landing, student dashboard, supervisor dashboard, admin dashboard, super admin, login)
- Verify footer renders correctly in light/dark mode
- Verify responsive behaviour (mobile < 768px)
- Verify version displays from config
- Verify all links work

---

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Existing super-admin footer tests/page tests break | Medium | Medium | Verify all super-admin pages after swap |
| Public layout AfriScribe attribution lost | Low | High | Preserve via `brand` slot in public variant |
| Sticky footer overlaps content on short pages | Low | Medium | Ensure layout wrappers use `flex-1` / `min-h-screen` |
| Dark mode colours inconsistent | Low | Low | Use existing `dark:` classes from super-admin footer |

---

## Validation Checklist

- [ ] Footer appears on all 4 layout types
- [ ] Variant defaults match table above
- [ ] Overrides work per layout
- [ ] Version reads from `config('app.version')`
- [ ] Dark mode renders correctly
- [ ] Mobile responsive (< 768px stacks correctly)
- [ ] All links functional (routes, external)
- [ ] Copyright year dynamic (`date('Y')`)
- [ ] No duplicate footers
- [ ] Super-admin footer component can be removed

---

## Next Steps

1. Implement Step 1–2 (config + component)
2. Implement Step 3–6 (layout integrations)
3. Run validation checklist
4. Remove deprecated `components/super-admin/footer.blade.php`