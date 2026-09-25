# Plan: LASU Structured Academic Units as Selectable Departments

## Goal
Replace free-text `department` inputs with a structured, data-driven, cascading dropdown (University → Faculty/School → Department) sourced from a deep search of the official LASU site (`lasu.edu.ng`). Initial scope: Lagos State University (LASU only; UI preset kept free-text until its data is sourced).

## Decision: Schema Approach — Option A (config-backed string)
- **Keep** the existing `department` text columns on `users`, `supervisors`, `universities` (no migration to add foreign keys).
- The dropdown submits the **department name** (verbatim LASU spelling) into the existing `department` column — zero data-model churn, matches the `university_code` precedent (canonical string into a text field).
- Add a **config-driven source of truth** (not a DB table): `config/lasu_departments.php`, returning `faculties => [ name => [departments...] ]`, plus `schools`/`colleges`/`directorate` blocks. This avoids migrations, stays deployable on shared hosting, and mirrors `config/universities.php`.
- Gate the structured select behind a new `has_structured_departments` flag on a university preset (true for LASU, false/absent for others → fall back to the existing text input).

## Compiled LASU Data (Source of Truth)
Source: official `lasu.edu.ng` faculty pages (verified thumbnail department lists) + all-departments index. Encoding into `config/lasu_departments.php`:

Faculties:
- Arts: English Language; Foreign Languages; History & International Studies; Linguistics & African Languages / Literatures & Communication Arts; Music; Philosophy & Logic; Religions & Peace Studies; Theatre Arts.
- Science: Biochemistry; Botany; Chemistry; Computer Science; Fisheries; Mathematics & Statistics; Microbiology; Physics; Science Laboratory Technology; Zoology & Environmental Biology.
- Social Sciences: Economics; Geography & Planning; Political Science; Psychology; Sociology.
- Environmental Sciences: Architecture; Building; Estate Management; Urban & Regional Planning; Quantity Surveying; Construction Management.
- Engineering: Aeronautical & Astronautical Engineering; Chemical Engineering; Electronics & Computer Engineering; Mechanical Engineering.
- Law: Commercial & Industrial Law; Public Law; Islamic Law; Private & Property Law; Legal Studies; Public & International Affairs.
- Education: Arts & Social Science Education; Science & Technology Education; Educational Foundations; Educational Management & Policy; Vocational & Technical Education.
- Basic Medical Sciences: Physiology; Biochemistry; Anatomy; Medical Biochemistry; Anatomy/Histology; Physiology; Biochemistry.
- Clinical Sciences: Human Anatomy & Physiology; Medicine & Surgery; Nursing; Medical Laboratory Science; Pharmacology & Therapeutics.
- Dentistry: Dental & Oral Surgery; Oral & Maxillofacial Surgery; Prosthodontics & Oral Rehabilitation; Periodontics & Oral Medicine; Orthodontics; Pediatric Dentistry.
- Allied Health Sciences: Medical Laboratory Science; Nursing; Physiotherapy; Radiography; Occupational Therapy; Nutrition & Dietetics.
- Computing & Information Technology: Computer Science; Information Technology; Software Engineering; Cybersecurity & Information Security; Information Systems.

Schools:
- School of Communications: Broadcasting; Journalism; Public Relations & Advertising.
- School of Agriculture: Agricultural Economics; Agronomy; Animal Science & Fisheries; Food Science & Technology; Water Resources & Environmental Management.
- School of Transport: Transport Management; Transport Planning & Policy; Transport Technology & Logistics.
- School of Library & Information Science: Library & Information Science; Collection Development & Management; Reader Services; Technical Services.
- School of Continuing Education (CESSED): English Language Programmes; Science & ICT Programmes; Professional Development Programmes.

Actions / Notes:
- **Re-verify** Faculty of Education and Faculty of Environmental Sciences against the official faculty pages before finalising the config (their department lists were navigation-heavy in the initial fetch). Capture verbatim names.
- The `config/universities.php` LASU preset currently has a single `'department' =>` key — remove it (or keep as a fallback default) once the structured selector is wired.
- Medical faculties use "and"/"&" inconsistently in source pages; standardise to "and" for consistency.

## Affected Boundaries
- `config/universities.php` (LASU preset): add `'has_structured_departments' => true`; remove legacy scalar `department` or mark deprecated.
- New file: `config/lasu_departments.php` — structured source-of-truth array.
- Views (replace free-text `<input name="department">` with a structured `<select>`/cascader):
  - `resources/views/auth/complete-invitation.blade.php` (lines ~89 & ~108) — supervisor + admin role completion.
  - `resources/views/super-admin/user-form.blade.php` (line ~141) — user creation/edit.
  - `resources/views/super-admin/university-form.blade.php` (line ~88) — university record `department` (seed value).
  - `resources/views/admin/users.blade.php` (line ~295) — department filter field: convert to structured filter or leave as text search. (Mark as optional/defer if it is a free-text search box, not a form save.)
- Service layer:
  - `app/Services/UserInvitationService.php`: `completionRules()` keeps `department => required|string|max:255`; no rule change needed (it already validates string). `createInvitedUser` + `completeInvitation` persist `department` name unchanged. Optionally add allow-list validation against config when `has_structured_departments` is true.
- Auth views JS: extend `resources/views/partials/auth/inline-script.blade.php` with `loadDepartments(code)`, `renderDepartmentList(type)`, `toggleDepartmentDropdown`, `filterDepartments`, `selectDepartment` mirroring the existing university selector helpers.
- New controller: `app/Http/Controllers/DepartmentController.php` with `GET /@api/departments?university_code=LASU` returning `{data:{faculties:{...}}}`. Register route in `routes/web.php` (or an existing API route group).
- Seeders: no DB migration needed; config is the source of truth. Optionally a doc comment in `UniversitySeeder.php` noting LASU now uses structured departments.

## Data Flow
1. Page with a `department` field renders a "Select university" (existing) → once LASU chosen, a "Select faculty/school" dropdown appears → choosing a faculty reveals a "Select department" dropdown.
2. On change, the **department name** is written into a hidden/real `<input name="department">` so the existing Laravel validation + model fill (`'department' => $attributes['department']`) continues to work unchanged.
3. Backend (`UserInvitationService`, `SupervisorController`, `SuperAdminWebController`) validates `department` as a non-empty string from the allow-list when `has_structured_departments` is true; otherwise falls back to free-text (UI preset behaviour).

## Rollout / Migration
- No DB migration; config is the source of truth. Backwards-compatible: existing `department` text values persist as-is.
- For LASU, the `UniversitySeeder` should be updated to **not** seed a single scalar department on the university record (or set it to null) since departments are now per-user/supervisor. Check `UniversitySeeder` for that seeding call.
- UI shows the structured cascade **only** when the selected university preset has `has_structured_departments => true`; all other presets render the existing text input.

## Validation
- `php artisan config:clear` then `php artisan tinker` → `config('lasu_departments.faculties.Arts')` returns the expected department array.
- `php artisan test` (or `php artisan test --filter=<relevant>`) — ensure no regression in `AuthTest`/`AdminRelationshipTest`/`UserInvitationServiceTest` which currently hardcode `department` strings.
- Cypress/feature: select LASU → select "Faculty of Science" → select "Computer Science" → assert the form submits `department=Computer Science`.

## Risks / Edge Cases
- Faculty pages that returned navigation noise (Education, Environmental Sciences): resolve by re-fetching before config freeze.
- Duplicate department names across faculties (e.g. "Biochemistry" appears in Science, Basic Medical Sciences, Clinical Sciences): the cascade scopes department selection by selected faculty, so the submitted `department` name is unambiguous in practice but not unique in the column. Acceptable for Option A; if uniqueness matters later, switch to Option B (FK to `university_departments`).
- Non-LASU tenants (UI, others): keep free-text path; do not block them.
- The `super-admin/university-form.blade.php` "department" likely seeds a placeholder/default for the university itself — confirm via `UniversitySeeder` whether this is per-institution default department or a leftover. If leftover, the field can be dropped from that form for structured universities.

## Open Questions
1. (Deferred in plan) Should `admin/users.blade.php` department field be a structured filter or remain a free-text search? — Treat as a search box (leave as text) unless it's a save form; mark as optional defer.
2. Confirm whether `UniversitySeeder` seeds the scalar `department` on the `universities` row — if so, plan to null it for LASU.

## Ordered Task List
1. Fetch & re-verify Faculty of Education & Environmental Sciences pages from `lasu.edu.ng`; finalise verbatim department names.
2. Author `config/lasu_departments.php` (faculties/schools/directorates → departments).
3. Edit `config/universities.php` LASU preset: add `'has_structured_departments' => true`; remove/neutralise scalar `'department'`.
4. Add `GET /api/departments` route + `DepartmentController` returning the config array for a university code.
5. Extend `inline-script.blade.php` with department cascade helpers (toggle/load/filter/render/select) mirroring university selector.
6. Replace `complete-invitation.blade.php` free-text `department` inputs with the cascader component (gated on `has_structured_departments`).
7. Replace `super-admin/user-form.blade.php` `department` input with cascader.
8. Update `super-admin/university-form.blade.php` department field handling (neutralise scalar seed field for LASU).
9. Add server-side allow-list validation in `UserInvitationService` for structured universities (optional hard validation).
10. Update `UniversitySeeder` so LASU row does not carry a scalar department default (or set to null).
11. Run `php artisan config:clear && php artisan test`; assert selects render and submissions persist the correct department name.
