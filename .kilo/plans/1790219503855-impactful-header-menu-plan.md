# Plan: More Impactful Header Menu

## Implementation Status Checklist

Legend: `[x]` done, `[-]` partial, `[ ]` missing.

### Core Goal Status
- [x] Unify and consolidate into a single reusable `x-app-header`
- [x] Add navigation improvements
- [x] Add notifications center
- [x] Improve mobile UX
- [x] Visual polish and branding
- [x] Context-aware primary actions in header

### File-Level Status
- [x] `resources/views/components/app-header.blade.php` created
- [x] `resources/views/components/app-header-navigation.blade.php` created
- [x] `resources/views/components/app-header-notifications.blade.php` created
- [x] `resources/views/components/app-header-user-menu.blade.php` created
- [x] `resources/js/header.js` created and used
- [x] `vite.config.js` updated to bundle header script
- [x] `resources/views/layouts/supervisor.blade.php` migrated to unified header
- [x] `resources/views/layouts/student.blade.php` migrated to unified header
- [x] `resources/views/layouts/super-admin.blade.php` or equivalent migrated to unified header
- [x] `resources/views/components/layouts/app.blade.php` updated to load shared header assets
- [x] Legacy header files removed after migration

### Phase 1: Foundation
- [x] Create `AppHeaderComponent` class (`app/View/Components/AppHeader.php`)
- [x] Create `resources/views/components/app-header.blade.php`
- [x] Extract dropdown partials for reusability
- [x] Add shared header styling foundation

### Phase 2: Navigation System
- [x] Build unified role-to-nav mapping for the header
- [x] Implement keyboard-accessible authenticated header dropdown navigation
- [x] Add active state highlighting in unified header navigation

### Phase 3: Notifications Center
- [x] Create notification API shape required for header consumption
- [x] Build reusable notifications dropdown
- [x] Add polling on header mount
- [x] Add unread badge for all authenticated roles

### Phase 4: User Menu and Context Actions
- [x] Add user menu dropdown for all authenticated roles
- [x] Render primary action CTA from header props or route auto-detection
- [x] Add `x-app-header-primary-action` slot override

### Phase 5: Mobile UX Enhancements
- [x] Improve drawer with swipe-to-close and focus trap
- [x] Ensure 44x44 minimum touch targets across authenticated header controls
- [x] Add bottom-sheet style mobile nav alternative
- [x] Ensure backdrop click and Escape key close drawer
- [x] Preserve scroll position on mobile

### Phase 6: Visual Polish and Branding
- [x] Refine color tokens for consistent academic and LASU branding
- [x] Add consistent elevation treatment
- [x] Add micro-animations across authenticated header interactions
- [x] Validate dark mode contrast across unified header surfaces
- [x] Support logo variants for responsive branding

### Phase 7: Integration and Cleanup
- [x] Update all target layouts to use `<x-app-header>`
- [x] Remove old header partials and views
- [x] Add shared header JS to the build without duplicate listeners
- [x] Test across roles, pages, and viewport sizes

## Current Assessment

The unified authenticated header plan is implemented across student, supervisor, and super-admin flows. The repository now uses a shared class-based `x-app-header` component with extracted navigation, notifications, and user-menu partials, a bundled `resources/js/header.js` entry, and shared notification endpoints designed for header consumption.

Primary action CTAs are now header-driven, role-aware navigation is centralized in the component class, and authenticated mobile behavior includes drawer open/close, focus management, and swipe-to-close support. Legacy authenticated header partials were removed after migration.

## Evidence Snapshot

- Shared header component class exists at `app/View/Components/AppHeader.php`
- Shared header blade and partials exist under `resources/views/components/`
- Shared authenticated header logic is bundled from `resources/js/header.js`
- Student, supervisor, and super-admin layouts all render `x-app-header`
- Notifications API now exposes header-friendly index and mark-all-read endpoints
- Resource notifications include `action_url` metadata for direct navigation

## Context
The application currently has **four separate header implementations**:
- `components/header.blade.php` — Generic portal header (brand + role switcher + user pill + logout)
- `components/super-admin/header.blade.php` — Super-admin header (breadcrumbs + notifications + user menu)
- `partials/dashboards/supervisor-header.blade.php` — Supervisor dashboard header (role badge + user avatar + logout)
- `partials/dashboards/student-header.blade.php` — Student dashboard header (role badge + user avatar + logout)

**Problems**: Duplication, inconsistent features (notifications only in super-admin), no navigation links, limited mobile UX, no search, no context-aware actions.

---

## Goals
1. **Unify & Consolidate** → Single reusable `x-app-header` component, role-aware via props
2. **Add Navigation Links** → Role-specific nav items in a dropdown/menu
3. **Add Notifications Center** → Bell icon + dropdown for all authenticated roles
4. **Improve Mobile UX** → Better drawer, touch targets, swipe-to-close, focus management
5. **Visual Polish & Branding** → Consistent spacing, elevation, dark-mode refinement, LASU/AfriScribe branding
6. **Context-Aware Actions** → Primary CTA per page/role (e.g., "New Proposal" on proposals page)

---

## Affected Files

### New / Primary
- `resources/views/components/app-header.blade.php` — **New unified header component**
- `resources/views/components/app-header-navigation.blade.php` — Navigation dropdown partial
- `resources/views/components/app-header-notifications.blade.php` — Notifications dropdown partial
- `resources/views/components/app-header-user-menu.blade.php` — User menu dropdown partial
- `resources/js/header.js` — **New** header-specific JS (dropdowns, search, mobile)
- `vite.config.js` — Ensure header.js is bundled

### Modified (Replace includes)
- `resources/views/layouts/supervisor.blade.php` → Use `<x-app-header :role="supervisor" ... />`
- `resources/views/layouts/student.blade.php` → Use `<x-app-header :role="student" ... />`
- `resources/views/layouts/super-admin.blade.php` (or component) → Use `<x-app-header :role="super-admin" ... />`
- `resources/views/components/layouts/app.blade.php` — Add `@vite('resources/js/header.js')` and global event handlers

### Deleted (after migration)
- `resources/views/components/header.blade.php`
- `resources/views/components/super-admin/header.blade.php`
- `resources/views/partials/dashboards/supervisor-header.blade.php`
- `resources/views/partials/dashboards/student-header.blade.php`

---

## Data Flow & Props

```php
// AppHeaderComponent.php (new class-based component recommended)
public function __construct(
    public string $role,                    // 'super-admin' | 'supervisor' | 'student'
    public ?array $navigation = null,       // Override default nav items
    public ?string $primaryAction = null,   // Context CTA: { label, href, icon }
    public ?array $breadcrumbs = null,      // For super-admin
    public ?string $pageTitle = null,       // Fallback title
) {}
```

**Navigation config per role** (defined in component or Service class):

| Role | Nav Items |
|------|-----------|
| super-admin | Dashboard, Universities, Users, Resources, Audit Logs, System Status, Config |
| supervisor | Dashboard, My Students, Proposals, Meetings, Resources, Analytics |
| student | Dashboard, My Proposals, Meetings, Resources, Defense Readiness, Profile |

**Notifications**: Fetch via AJAX `/api/notifications` (paginated, unread count). Poll every 60s or use Laravel Echo/Pusher if available.

**Context-Aware Primary Action**: Determined by current route:
- `supervisor.proposals.index` → "New Proposal" → `route('supervisor.proposals.create')`
- `supervisor.meetings.index` → "Schedule Meeting" → `route('supervisor.meetings.create')`
- `student.proposals.index` → "Submit Proposal" → `route('student.proposals.create')`
- etc.

---

## Implementation Steps

### Phase 1: Foundation (Component + Styles)
1. Create `AppHeaderComponent` class (`app/View/Components/AppHeaderComponent.php`)
2. Create `resources/views/components/app-header.blade.php` with:
   - Semantic `<header>` with sticky positioning
   - Left: Mobile toggle + Brand/Logo + Page Title (truncated)
   - Center (desktop): Primary navigation dropdown (role-based)
   - Right: Search (optional, future) | Notifications bell | Primary Action CTA | User Menu
3. Extract dropdown partials for reusability
4. Add Tailwind config extensions for header-specific utilities (z-index scale, animation curves)

### Phase 2: Navigation System
5. Build `NavigationService` or config array mapping roles → nav items (label, route, icon, active patterns)
6. Implement keyboard-accessible dropdown (Arrow keys, Escape, Tab trap)
7. Add active state highlighting via `request()->routeIs()` or `Route::currentRouteName()`

### Phase 3: Notifications Center
8. Create `NotificationController@index` API endpoint returning `{ unread_count, items: [{id, type, title, message, time, read, action_url}] }`
9. Build notifications dropdown with:
   - Header: "Notifications" + "Mark all read" + count badge
   - List: Icon by type, title, relative time, click → navigate + mark read
   - Footer: "View all" link
10. Add polling (60s) + immediate fetch on header mount
11. Add badge on bell icon with unread count

### Phase 4: User Menu & Context Actions
12. User menu dropdown: Profile, Settings, (role-specific), Divider, Sign Out
13. Primary Action CTA: Render when `$primaryAction` prop set or auto-detect from route
14. Add `x-app-header-primary-action` slot for blade-level override

### Phase 5: Mobile UX Enhancements
15. Improve drawer: Swipe-to-close (touchstart/move/end), overscroll behavior, focus trap
16. Increase touch targets to 44×44 minimum
17. Add bottom-sheet style nav on mobile (slide up from bottom) as alternative to side drawer
18. Ensure backdrop click & Escape key close drawer
19. Preserve scroll position on mobile

### Phase 6: Visual Polish & Branding
20. Refine color tokens: Use `academic` palette consistently, LASU gold accents
21. Add subtle elevation: `shadow-sm` base, `shadow-lg` on hover/focus for interactive elements
22. Micro-animations: Dropdown fade+scale (150ms), bell pulse on new notification, CTA hover lift
23. Dark mode: Ensure all surfaces have proper contrast, test with `prefers-color-scheme`
24. Logo: Ensure AfriScribe logo works at all sizes, add LASU badge variant

### Phase 7: Integration & Cleanup
25. Update all layout files to use `<x-app-header>` with correct props
26. Remove old header partials/views
27. Add header.js to Vite entry, ensure no duplicate event listeners
28. Test across all roles, pages, viewports (320px–1920px)

---

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| Breaking existing pages during migration | Migrate one layout at a time; keep old headers as fallback behind feature flag |
| Notification API not existing | Create minimal endpoint first; enhance later |
| Mobile drawer conflicts with existing sidebar | Use unique IDs (`#app-header-drawer`), coordinate z-index (50 for sidebar, 55 for header drawer) |
| Performance: too many dropdowns | Lazy-render dropdown content (render on first open) |
| Role detection inconsistency | Centralize `auth()->user()->getHeaderRole()` method on User model |

---

## Validation Plan

1. **Visual Regression**: Screenshot test key pages per role (desktop + mobile)
2. **Accessibility Audit**: axe-core / WAVE — focus order, ARIA labels, contrast, keyboard nav
3. **Functional Tests**:
   - Each role sees correct nav items
   - Active route highlighted
   - Notifications fetch + badge updates
   - Primary CTA appears on correct pages
   - Mobile drawer opens/closes via toggle, backdrop, Escape, swipe
   - User menu sign-out works
4. **Performance**: Lighthouse — ensure header JS < 10KB gzipped, no layout shift
5. **Cross-browser**: Chrome, Firefox, Safari (mobile + desktop)

---

## Open Questions

1. **Search**: Deferred to future phase? If yes, confirm no search icon in header for now.
2. **Real-time notifications**: Laravel Echo/Pusher available? If not, polling is fine for MVP.
3. **Super-admin breadcrumbs**: Keep in header or move to page content area? (Current super-admin header has breadcrumbs; unified header may not have space — recommend moving to page-level `<x-super-admin.breadcrumbs />` slot)
4. **Role switcher** (in current generic header): Keep for dev/testing? Remove for production?
5. **Avatar source**: Currently hardcoded initials. Integrate with `User::getAvatarAttribute()` or Gravatar later?

---

## Next Steps

1. Confirm plan scope (especially Open Questions)
2. Create `AppHeaderComponent` class + blade view
3. Build navigation config + dropdown JS
4. Implement notifications API + dropdown
5. Migrate layouts one by one
6. Delete old headers