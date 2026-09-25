# Plan: CSV Bulk Import of Students with Supervisor/Faculty/Department/Programme Assignment

## Goal
Allow a supervisor (and later an admin) to upload a CSV to create many students in one operation. Each row maps **basic student details** plus **assignment to** a supervisor, faculty, department, and programme. Reuses the existing single-student creation logic; integrates with the already-implemented LASU structured-departments config (`config/lasu_departments.php`).

## Scope & Decisions
- **Primary actor:** supervisor (uses session context: `session('supervisor_id')`, `session('university_id')`). All created students belong to that supervisor's university.
- **Supervisor assignment:** a `supervisor_email` column is optional. If blank → assign to the session supervisor. If present → must resolve to a **supervisor user in the same university** (else the row errors).
- **Faculty / Department:** for LASU (preset `has_structured_departments => true`), validated against `config('lasu_departments')` via the existing `DepartmentController` allow-list (faculty must be a known faculty/school key; department must exist under that faculty/school). For other universities → free text (nullable).
- **Programme:** new concept, free-text, nullable (e.g. "BSc Computer Science", "MSc Economics"). No allow-list.
- **Schema:** students table currently has NO `faculty`, `department`, or `programme` columns. Add a single migration with three nullable string columns + an index on `(university_id, faculty, department)` for filtering. (Do **not** reuse `users.department`; keep academic affiliation on `students`.)
- **No new dependency:** use PHP native `fgetcsv` (Laravel ships no CSV lib; avoids `composer require league/csv`).
- **Reuse creation logic:** extract the body of `SupervisorController::createStudent` (User + Student + StageHistory + ArchiveSubmission) into `app/Services/StudentOnboardingService::create(array $data): Student`, then have both the single-create endpoint and the CSV import call it. Fix the existing shared-temp-password bug (`'student'.time()`) by generating a unique per-student random password (`Str::random(48)`), matching `UserInvitationService`.

## Affected Files
- `app/Services/StudentOnboardingService.php` (new) — extracted per-student creation.
- `app/Services/StudentBulkImportService.php` (new) — CSV parse + validate + create loop, per-row result collection, returns summary DTO.
- `app/Http/Controllers/SupervisorController.php` — refactor `createStudent` to delegate to `StudentOnboardingService`; add `importCsv()` + `downloadTemplate()`.
- `database/migrations/2026_09_24_000001_add_academic_fields_to_students_table.php` (new) — add `faculty`, `department`, `programme` (nullable string) + index.
- `routes/web.php` — add:
  - `GET /supervisor/students/import/template` → download sample CSV.
  - `POST /supervisor/students/import` → `SupervisorController@importCsv` (multipart).
- `resources/views/supervisor/students.blade.php` — add an "Import CSV" section (file picker + upload button + result toast/table).
- `app/Models/Student.php` — add `faculty`, `department`, `programme` to `$fillable`.
- `app/Http/Controllers/DepartmentController.php` — already serves `GET /api/departments?university_code=LASU`; CSV validation may call it directly or read `config('lasu_departments')` (prefer config to avoid an HTTP round-trip inside the job).
- Tests: `tests/Feature/SupervisorStudentBulkImportTest.php` (new); possibly `tests/Unit/StudentBulkImportServiceTest.php`.

## CSV Template (header row) + sample rows
```
full_name,lastname,matric_number,email,degree_level,phone,supervisor_email,faculty,department,programme,research_topic
```
- **Required:** `full_name`, `lastname`, `matric_number`, `email`, `degree_level` (`BSc`|`MSc`|`PhD`).
- **Assignment:** `supervisor_email` (blank → session supervisor), `faculty`, `department` (LASU: validated against `config/lasu_departments`), `programme` (free text).
- **Optional:** `phone`, `research_topic`.
- First row must be the header. Position-independent mapping by column name.

### Sample CSV (one annotated row per LASU faculty/school; supervisor_email left blank so each defaults to the uploading supervisor)
```
full_name,lastname,matric_number,email,degree_level,phone,supervisor_email,faculty,department,programme,research_topic
Adewale,Ogunsiji,CS/2020/001,adewale.ogunsiji@laspark.edu.NG,BSc,08030000001,,Arts,Theatre Arts,BSc Theatre Arts,Performative Traditions in Contemporary Nigerian Drama
Emea,Okonkwo,SC/2021/045,emea.okonkwo@laspark.edu.NG,MSc,08030000045,,Science,Computer Science,MSc Computer Science,Machine Learning Applications in Agriculture
Babatunde,Fashola,SS/2019/010,babatunde.fashola@laspark.edu.NG,BSc,08030000011,,Social Sciences,Economics,BSc Economics,Impact of Microfinance on SME Growth in Lagos
Chinedu,Onyejio,ES/2020/078,chinedu.onyejio@laspark.edu.NG,MSc,08030000099,,Environmental Sciences,Architecture,MSc Architecture,Sustainable Housing Design Using Local Materials
Samuel,Olu,BEN/2022/003,samuel.olu@laspark.edu.NG,BSc,,Engineering,Chemical Engineering,BSc Chemical Engineering,Process Optimisation for Palm Oil Refining
Aisha,Mustapha,LA/2021/055,aisha.mustapha@laspark.edu.NG,PhD,,Law,Public Law,LLM Legal Theory (PhD track),Judicial Review and Administrative Law
Kunle,Da-silver,ED/2020/088,kunle.dasilver@laspark.edu.NG,BSc,08030000077,,Education,Science and Technology Education,BSc Education (Chemistry),Integrating ICT in Senior Secondary Chemistry
Tayo,Sodimu,BMS/2019/022,tayo.sodimu@laspark.edu.NG,MSc,,Basic Medical Sciences,Physiology,MSc Physiology,Cardiovascular Responses to Altitude Training
Ngozi,Eze,CSC/2021/066,ngozi.eze@laspark.edu.NG,BSc,,Clinical Sciences,Nursing,BSc Nursing,Pain Management Practices Among Paediatric Patients
Tunji,Bakare,DENT/2022/014,tunji.bakare@laspark.edu.NG,BSc,,Dentistry,Prosthodontics and Oral Rehabilitation,BSc Dentistry,Implant-Supported Prostheses Outcomes
Ife,Kelvin,AHS/2020/033,ife.kelvin@laspark.edu.NG,BSc,,Allied Health Sciences,Physiotherapy,BSc Physiotherapy,Post-Stroke Rehabilitation Protocols
Zainab,Abdul,ICT/2021/091,zainab.abdul@laspark.edu.NG,BSc,08030000022,,Computing and Information Technology,Software Engineering,BSc Computer Science,Agile Requirements Elicitation in EdTech Startups
```

**Notes on the sample:**
- All rows use the LASU faculty/school keys exactly as they appear in `config/lasu_departments.php` (`Arts`, `Science`, `Social Sciences`, `Environmental Sciences`, `Engineering`, `Law`, `Education`, `Basic Medical Sciences`, `Clinical Sciences`, `Dentistry`, `Allied Health Sciences`, `Computing and Information Technology`). Schools (e.g. `School of Communications`) are likewise valid faculty values; the allow-list is the union of `faculties` + `schools` keys.
- `department` must be a verbatim member of the chosen faculty/school's department list (e.g. `Chemistry` is invalid under `Engineering` → row rejected with a per-row error).
- `programme` is free text and is **not** validated against the config; it is the student-facing degree programme name.
- `supervisor_email` left blank → assigned to the session supervisor. Provide an email to assign to a different active supervisor in the same university.
- Matric numbers and emails are unique; duplicates against existing records are skipped (not overwritten) and reported in the result summary.
- Real uploads omit the explanatory comment lines above; the file should be pure CSV (header + data rows), UTF-8.

## Data Flow
1. Supervisor opens `supervisor/students` → clicks "Import CSV" → selects a file → POSTs to `/supervisor/students/import` (multipart `csv_file`).
2. `SupervisorController::importCsv` resolves university from session, reads file (max ~5 MB, mime `text/csv`|`text/plain`), delegates to `StudentBulkImportService`.
3. `StudentBulkImportService`:
   - `fopen` the file; `fgetcsv` in a loop with `auto_detect_line_endings`.
   - First row = header → map columns by name (position-independent, so column order can vary).
   - For each data row: validate required fields, validate `degree_level`, validate/resolves `supervisor_email` (if provided), validate `faculty`/`department` against `config('lasu_departments')` when structured, check `matric_number`/`email` uniqueness within the tenant.
   - On success → `StudentOnboardingService::create([...])` (creates User + Student + StageHistory + ArchiveSubmission). On failure → record `(row_number, columns, reason)` in the errors list.
   - Skip duplicate `matric_number`/`email` rows (do not halt); collect them as `skipped`.
   - Run per-row creation inside a single DB transaction — **decide:** commit incrementally (return what succeeded) vs. all-or-nothing. Recommendation: **incremental with report** (bulk imports rarely fail mid-way; partial success + clear per-row errors is the better UX). Flag the all-or-nothing option in the plan.
   - Return `{total, created, skipped_duplicates, errors: [{row, matric, reason}]}`.
4. Controller returns `$this->success(...)` JSON; the view renders a result summary (counts + downloadable error list).

## Validation Rules (per row)
- `full_name`, `lastname`: required, string, max 255.
- `matric_number`: required, string, max 255, unique within `students` where `university_id = X`.
- `email`: required, email, unique within `users` where `university_id = X`.
- `degree_level`: required, in:`BSc`,`MSc`,`PhD`.
- `phone`: nullable, string max 20.
- `supervisor_email` (if provided): must exist on `users` with role `supervisor` in the same university.
- `faculty` (for structured universities): must be a key in `config('lasu_departments.faculties')` or `schools` (normalise school keys to their names).
- `department` (for structured universities): must appear under the chosen `faculty` in `config('lasu_departments')`.
- `programme`: nullable, string max 255.
- `research_topic`: nullable, text.

## Risks / Edge Cases
- **Header variance:** column names may differ (`First Name`, `Matric`, `Degree`). Map the template's canonical names; reject unknown/missing required headers with a clear row-0 error.
- **Encoding:** enforce UTF-8; handle BOM.
- **Large files:** 5 MB cap + per-row transaction (not loading whole file into memory). For very large cohorts (>500) recommend chunking or queue job — mark as future enhancement.
- **Duplicate matric/email** within the CSV itself or against existing rows → record as skipped/duplicate, not a hard stop.
- **Temp password:** `StudentOnboardingService` generates a unique random password per student; credentials are not emailed by this flow (reuses existing single-create gap). Recommend a follow-up to send invites via `UserInvitationService` — deferred, flagged.
- **`supervisor_email` resolution:** must be scoped to the session university to prevent cross-tenant assignment.
- **Non-LASU universities:** faculty/department/programme remain free text (nullable); structured validation skipped.
- **Matric number format:** no format validation currently; keep as string. (Could add per-university regex in future.)
- **Idempotency:** re-uploading the same CSV re-creates duplicates → dedupe by matric_number is skipped, not overwritten. Recommend a `--overwrite` flag in future.

## Validation / Test Plan
- `php artisan test --filter=SupervisorStudentBulkImportTest`
- Upload sample CSV with 3 valid rows + 1 duplicate matric + 1 invalid degree_level + 1 unknown supervisor_email → assert: created=3, skipped/duplicate=1, errors=2 (invalid degree, unknown supervisor), and the 3 students exist in `students` with correct `faculty`/`department`/`programme` and `supervisor_id`.
- `GET /supervisor/students/import/template` returns a 200 CSV with the header row.
- `php artisan config:cache` does not break `config('lasu_departments')` reads inside the service.

## Rollout / Migration Path
- New migration is additive (nullable columns) → zero-downtime; no existing data affected.
- `StudentOnboardingService` refactor is behaviour-preserving for `createStudent` (single create) + fixes the shared-password bug.
- Supervisor students view gets a new "Import CSV" subsection (non-destructive to the existing list).
- Optional follow-ups (out of this plan): admin/bulk scope, queue-based large imports, email invite delivery, programme allow-list, `--overwrite` idempotency.

## Open Questions
1. **Transaction mode:** incremental-with-report (recommended) vs. all-or-nothing? 
2. **`programme` semantics:** keep as free text, or derive from `degree_level` + `department` (e.g. "BSc Computer Science")?
3. **Credentials delivery:** should CSV-created students receive an invite email via `UserInvitationService` (instead of a silent temp password)?
4. **Actor scope:** supervisor-only for now, or also admin/super-admin in the same endpoint? (Plan designs it supervisor-scoped; admin reuse needs its own scope guard.)
