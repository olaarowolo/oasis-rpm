# Defense Readiness: Structured Manuscript Form with Per-Section Supervisor Review

## Context

Today `student.defense-readiness` (`routes/web.php:402`) renders a **static, non-persistent** 5-item checklist plus a weighted score. Nothing is stored, there is no editor, and a supervisor has no way to review it.

This plan replaces that page with a **structured manuscript readiness form**: an ordered set of sections (matching the faculty word-count guidance), each with a rich text editor, autosave, and its own supervisor review cycle. A section must be approved before the next one can be opened. Every supervisor action fires an in-app notification and an email.

The existing checklist and score are **kept** at the top of the same page.

### Current infrastructure this builds on

| Concern | Existing asset |
|---|---|
| Auth | Session-based. `session('user_id')`, `session('role')`, `session('university_id')`, `session('student_id')`, `session('supervisor_id')` |
| Middleware | `app.auth`, `role:student`, `role:supervisor`, `EnsureStudentLogin`, `EnsureSupervisorLogin`, `CheckUniversity` |
| Tenancy guard | `Proposal::find($id)` then check `university_id == session('university_id')` and `student->supervisor_id == session('supervisor_id')` (see `ResourceController.php:174`) |
| API envelope | `BaseController::success()` / `::error()` → `{success, message, data}` |
| Client HTTP | Vanilla `fetch` with `credentials: 'same-origin'`, `X-Requested-With: XMLHttpRequest`, `Accept: application/json` (`resources/js/header.js`) |
| Frontend build | Vite, Tailwind 4, FontAwesome only. **No Alpine, no JS framework, no editor, no autosave pattern exists** |
| Notifications | Custom `notifications` table + `App\Models\Notification`, created inline via `Notification::create([...])` with `metadata.action_url`. Drawer polls `GET /api/notifications` every 60s (`resources/js/header.js:98-233`), `iconForType()` at line 106 |
| Email | `App\Mail\PortalEmail(string $viewName, array $data)` → `resources/views/emails/portal/{viewName}.blade.php`, dispatched via `Mail::to(...)->send(...)` in `try/catch` + `Log::warning`. **Synchronous, not queued** |
| Review pattern | `ProposalController` (email only) and `ResourceController.php:166-273` (email **and** notification) — mirror the latter |

## Decisions Confirmed

1. Existing checklist + score stay at the top of the page; the 8-section form goes below.
2. Rich text = self-contained `contenteditable` component in vanilla JS. No new npm dependency, no CDN.
3. Supervisor actions = **Accept**, **Conditional approval**, **Request revision**, **Reject** (each requires a comment) **plus a standalone Add comment** that does not change status.
4. Draft + explicit **Submit for review** + immutable version snapshots. Read-only while a review is pending.
5. Sections are collapsible cards with auto-growing editor boxes; students can add custom sections; sections are conditional on study type.
6. **Section gating**: a section unlocks only when the previous one is `accepted` or `conditional`. `revision_requested` does not unlock. **`rejected` halts the entire document** (all editing stops, statuses and content preserved).
7. Conditional approval unlocks the next section immediately; conditions stay pinned in the header until the student ticks "conditions addressed" (recorded, no re-submission).
8. When the final section is accepted, the document auto-completes and the existing "Submit for Defense" button enables (the 70% score gate is retained as an additional condition).
9. Methodology's prescribed subheadings are **separate tracked sub-fields, each with its own status and review cycle**, nested under Methodology.
10. Every event fires **both** an in-app notification and an email. Autosave fires neither.
11. Word counts are an **advisory badge only**, never block submission. Count is computed server-side and stored on the version snapshot.
12. Supervisor gets a **dedicated Manuscripts queue + reader**, mirroring `supervisor.resources.pending`.
13. Conditional sections are driven by a **study approach** selector (quantitative / qualitative / mixed) and a **primary data collection** toggle that the student sets on the form.

## Data Model

Four new tables. Filenames follow the `2026_09_26_*` convention already in `database/migrations/`.

### `defense_readiness_documents`
One manuscript per student.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `university_id` | FK → universities, cascade | |
| `student_id` | FK → students, cascade, **unique** | one manuscript per student |
| `study_approach` | enum(`quantitative`,`qualitative`,`mixed`) nullable | |
| `primary_data_collection` | boolean, default false | drives the ethics sub-section |
| `status` | enum(`draft`,`in_review`,`in_revision`,`halted`,`completed`), default `draft` | |
| `halted_at` | timestamp nullable | |
| `halted_section_id` | FK → sections nullable | which section triggered the halt |
| `halted_reason` | text nullable | the rejecting supervisor's comment |
| `completed_at` | timestamp nullable | |
| timestamps | | |
| indexes | `['university_id','status']`, `['student_id']` (unique) | |

### `defense_readiness_sections`
Methodology subheadings live here too, via self-referencing `parent_id`.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `document_id` | FK → documents, cascade | |
| `parent_id` | FK → sections nullable | null = top-level section |
| `key` | string | e.g. `title_abstract`, `literature_review`, `methodology`, `custom_9f3a` |
| `title` | string | display label |
| `position` | int | ordering within parent |
| `kind` | enum(`fixed`,`group`,`custom`), default `fixed` | `group` = the Methodology banner row |
| `guidance` | text nullable | the per-section helper text from the faculty guidance |
| `target_min_words` | int nullable | |
| `target_max_words` | int nullable | |
| `word_count_label` | string nullable | e.g. `As required` for References |
| `content` | longText nullable | sanitised HTML — the working draft |
| `word_count` | int default 0 | server-computed |
| `status` | enum(`locked`,`draft`,`submitted`,`accepted`,`conditional`,`revision_requested`,`rejected`), default `locked` | |
| `conditions_acknowledged_at` | timestamp nullable | |
| `submitted_at`, `decided_at` | timestamp nullable | |
| `current_version_id` | FK → section_versions nullable | last submitted snapshot |
| timestamps | | |
| indexes | `['document_id','status']`, `['parent_id','position']`, `unique(['document_id','key'])` | |

For a `group` row (Methodology): `content` stays null, it is not directly editable, and its displayed word count is the **sum of its children**.

### `defense_readiness_section_versions`
Immutable snapshots, one per submission.

| Column | Type |
|---|---|
| `id` | bigint PK |
| `section_id` | FK → sections, cascade |
| `document_id` | FK → documents, cascade (denormalised for queue queries) |
| `version_number` | int |
| `content` | longText (sanitised HTML as submitted) |
| `word_count` | int |
| `submitted_by_user_id` | FK → users |
| timestamps | |
| unique | `['section_id','version_number']` |

### `defense_readiness_reviews`
Supervisor decisions and standalone comments.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `section_id` | FK → sections, cascade | |
| `document_id` | FK → documents, cascade | |
| `version_id` | FK → section_versions nullable | which snapshot was reviewed (null for standalone comments) |
| `reviewer_user_id` | FK → users | |
| `action` | enum(`accepted`,`conditional`,`revision_requested`,`rejected`,`commented`) | |
| `comment` | longText | **required** for the four decision actions, optional for `commented` |
| `conditions` | text nullable | supervisor-stated conditions for `conditional` |
| timestamps | | |
| indexes | `['section_id','created_at']`, `['document_id','action']` | |

## Section Definitions

Add `config/defense_readiness.php` holding the section catalogue, target ranges, and guidance text. Seed sections from it on first document creation.

Top-level order and targets:

| # | key | Title | Target |
|---|---|---|---|
| 1 | `title_abstract` | Title, Abstract and Keywords | 250–300 |
| 2 | `introduction` | Introduction | 700–900 |
| 3 | `literature_review` | Literature Review and Theory | 1,000–1,300 |
| 4 | `methodology` | Methodology *(group)* | 700–900 (sum of children) |
| 5 | `findings` | Findings | 900–1,200 |
| 6 | `discussion` | Discussion | 900–1,100 |
| 7 | `conclusion` | Conclusion and Recommendations | 500–700 |
| 8 | `references` | References | *As required* (no badge) |

**Abstract guidance** (shown in section 1's helper panel and in the email/notification copy): rewrite the abstract as one paragraph of about 180–250 words, covering background/problem, purpose of the study, theory, method, sample, key findings, conclusion, recommendation.

**Methodology children** (position 4.1–4.8, each independently reviewed):

1. Research design
2. Study population and setting
3. Sampling procedure and sample size
4. Instrument
5. Data collection
6. Data analysis
7. Ethical considerations *(only created when `primary_data_collection` is true)*
8. Limitations

Guidance text for children 4, 5 and 6 varies by `study_approach`; the config holds one string per approach and the section's guidance is rewritten when the approach changes.

**Custom sections** are inserted at the end of the content body, **before** References, which always stays last. `kind = 'custom'`, `target_min_words` / `target_max_words` null. A custom section may be deleted only while it is `draft` and has zero versions.

**Changing the approach or the data-collection toggle** is permitted only while `documents.status === 'draft'` and no section has ever been submitted. After the first submission the two fields are read-only and the form shows a note explaining why.

## Services

### `App\Services\DefenseReadinessService`
Single place for all state transitions. Nothing else writes section status directly.

- `documentForStudent(Student $student): DefenseReadinessDocument` — `firstOrCreate`, seeding sections from config on create.
- `isUnlocked(Document $doc, Section $section): bool` — a top-level section is unlocked when every preceding top-level sibling is in `accepted|conditional`; a child is unlocked when every preceding sibling is in `accepted|conditional`. Always false when `$doc->status === 'halted'`.
- `refreshLocks(Document $doc): void` — walks sections in `parent_id`/`position` order and flips `locked` ⇄ `draft` per the rule. Never demotes a section that is `submitted`/`accepted`/`conditional`/`revision_requested`/`rejected`.
- `submitSection(Section $section, User $actor): Version` — validates the section is `draft` and the document is not `halted`; increments `version_number`; writes a `defense_readiness_section_versions` row; sets `status = submitted`, `submitted_at`, `current_version_id`; sets `documents.status = in_review`.
- `decide(Section $section, User $reviewer, string $action, ?string $comment, ?string $conditions): void` — writes the review row, then:
  - `accepted` / `conditional` → section status set, `decided_at`, `refreshLocks()`
  - `conditional` → `conditions` stored; `conditions_acknowledged_at` stays null
  - `revision_requested` → section status set; **no** `refreshLocks()` unlock
  - `rejected` → section status `rejected`; `documents.status = halted`, `halted_at`, `halted_section_id`, `halted_reason`
  - `commented` → no status change
  - finally, if the decided section is the last in document order and its new status is `accepted`, set `documents.status = completed`, `completed_at`.
- `releaseHalt(Document $doc): void` — when a supervisor issues `revision_requested` on the section named by `halted_section_id`, the document returns to `in_revision` and that section becomes editable again. Content and statuses of all other sections are untouched.

### `App\Support\HtmlSanitizer`
No sanitiser exists in the repo today. New class.

- Tag whitelist: `p, br, strong, b, em, i, u, s, h2, h3, h4, ul, ol, li, blockquote, code, pre, a, span`.
- Attribute whitelist: `a[href]` restricted to `http`, `https`, `mailto`; `span[class]` restricted to a fixed set of editor classes. **Drop** all `style`, `on*`, `id`, `data-*`, `src`, `form*`.
- Implement with `DOMDocument` (check `php -m | grep dom` first; fall back to `strip_tags($html, $allowed)` plus regex attribute stripping if the extension is absent).
- Used on every autosave, every submit snapshot, and defensively on render of supervisor-facing HTML.

### `App\Support\WordCounter`
`strip_tags` → normalise whitespace → `preg_match_all('/[\p{L}\p{N}]+(?:[\'’-][\p{L}\p{N}]+)*/u', $text)`. Returns int. Handles hyphenated words as one.

## API Routes

Add to the existing `EnsureStudentLogin` and `EnsureSupervisorLogin` groups in `routes/api.php`, inside the `CheckUniversity` wrapper.

### Student (`/api/student/defense-readiness/...`)

| Method | Path | Purpose |
|---|---|---|
| GET | `/` | Hydrate the whole page: document, settings, sections (with content, word count, status, conditions, review history), plus the existing checklist + score values |
| PATCH | `/settings` | `study_approach`, `primary_data_collection` — only while `draft` and nothing submitted |
| PUT | `/sections/{section}/content` | **Autosave.** Body `{content, last_saved_at}`. Returns `{saved_at, word_count, status}` |
| POST | `/sections/{section}/submit` | Snapshot + notify supervisor |
| POST | `/sections/{section}/acknowledge-conditions` | Tick "conditions addressed" |
| POST | `/sections` | Add custom section `{title, guidance?}` |
| DELETE | `/sections/{section}` | Delete a custom section (draft, zero versions only) |

### Supervisor (`/api/supervisor/manuscripts/...`)

| Method | Path | Purpose |
|---|---|---|
| GET | `/` | Queue: documents for `students.supervisor_id = session('supervisor_id')` and `university_id = session('university_id')`, with per-section status, sections awaiting review, and overall progress |
| GET | `/{document}` | Full read-only manuscript: every section with its latest version content, statuses, pinned conditions, and review history |
| POST | `/sections/{section}/decision` | Body `{action, comment, conditions?}`. Validates `action` enum; `comment` required unless `action = commented` |

Every endpoint repeats the tenancy guard from `ResourceController.php:174` before touching data.

## Autosave Design

- **Debounce** 1.5s after the last keystroke; also flush on `blur`, on `visibilitychange` to hidden, and on `beforeunload` via `navigator.sendBeacon`.
- **Save indicator** with four states: `idle` / `Saving…` / `Saved HH:MM` / `Not saved — retrying`. Use the same slate/purple palette as the existing page.
- **Optimistic concurrency**: the client sends `last_saved_at` (the timestamp returned by its last successful save). If the stored `updated_at` is later than `last_saved_at` by more than 5 seconds, respond `409` with `{success:false, conflict:true, server_updated_at}`. The UI shows "This section was changed in another tab" with **Reload** and **Overwrite** actions. Overwrite re-sends with `last_saved_at = null` to force the write.
- **Retry** up to 3 times with 2s/4s/8s backoff, then surface the error state and stop. Never silently drop the buffer.
- **Server-side gate** on autosave: reject with `403` if the section is not `draft`/`revision_requested`, or if the document is `halted`. Returning `403` makes the client flip the editor to read-only and show the reason.
- **Autosave never creates a notification, an email, or a version row.**

## Rich Text Editor Component

**`resources/views/components/rich-text-editor.blade.php`** — Blade component, props: `name`, `sectionId`, `value`, `placeholder`, `disabled`, `minWords`, `maxWords`.

**`resources/js/rich-text-editor.js`** — the behaviour, and **`resources/js/defense-readiness.js`** — the page controller (accordion, autosave scheduling, indicator, conflict modal, submit buttons, conditions tick). Add `resources/js/defense-readiness.js` to the `input` array in `vite.config.js`; load it from the defense-readiness views with `@vite(['resources/css/landing.css', 'resources/js/header.js', 'resources/js/defense-readiness.js'])`, matching the pattern in `resources/views/components/layouts/app.blade.php:19`.

Editor behaviour:
- `contenteditable="true"` div, `data-editor` root, plain `document.execCommand` calls.
- Toolbar: bold, italic, underline, H2, H3, blockquote, bullet list, numbered list, undo, redo. Icon-only buttons with `title` and `aria-label`, using the existing FontAwesome set.
- Toolbar buttons bind `mousedown` + `preventDefault()` so the caret/selection is never lost.
- **Auto-grow**: on `input` and on init, set `el.style.height = el.scrollHeight + 'px'` up to a `max-height` of 60vh, then `overflow-y: auto` beyond that.
- `beforeinput` intercepts `formatBlock` values outside the whitelist; `paste` intercepts and inserts `text/plain` only, so pasted content from Word/Google Docs cannot introduce rogue markup.
- Live word count is computed client-side for instant feedback, but the **server-computed value overwrites it on every save**.

Isolation note: `document.execCommand` is deprecated but universally supported and is the only zero-dependency option here. Keep it entirely inside this one component so it can be swapped for TipTap later without touching the rest of the page.

## Student Page

Rewrite `resources/views/student/defense-readiness.blade.php` and the closure at `routes/web.php:402-433`.

Keep the closure's existing checklist and score calculation verbatim. Add document hydration, then render:

1. **Header** — existing title and Dashboard link.
2. **Readiness Score panel** — unchanged.
3. **5-item checklist table** — unchanged.
4. **Manuscript readiness form** (new):
   - Study approach selector (Quantitative / Qualitative / Mixed) and a "primary data collection" toggle, locked once anything is submitted.
   - Progress rail showing section status pills and a document-level status banner (`Draft` / `Awaiting review` / `Revision requested` / `Halted` / `Complete`).
   - One collapsible card per section in order. Collapsed by default, except the first section that is `draft`/`revision_requested`. Each card header shows: title, status pill, live word count against target range, guidance toggle, and — when `conditional` — the pinned conditions plus the "I have addressed these conditions" tick.
   - `Submit for review` button, enabled only when the section is `draft` with non-empty content, and hidden/disabled when the document is `halted`.
   - Locked sections render as collapsed, read-only cards with a "Complete the previous section to unlock" note.
   - "Add section" button for custom sections, positioned before References.
5. **Submit for Defense** button — keep the 70% score gate, and additionally require `documents.status === 'completed'`. Show the unmet reason when disabled.

Page accepts `?section={id}` to auto-expand and scroll to a section (used by notification `action_url`).

## Supervisor Pages

**`routes/web.php`** (inside the existing `role:supervisor` group):
- `GET /supervisor/manuscripts` → `supervisor.manuscripts`
- `GET /supervisor/manuscripts/{document}` → `supervisor.manuscripts.show`

**`resources/views/supervisor/manuscripts/index.blade.php`** — queue table: student name, matric, programme, document status, per-section pills, sections awaiting review count, last activity. Filter tabs: All / Awaiting review / Revision requested / Halted / Complete.

**`resources/views/supervisor/manuscripts/show.blade.php`** — read-only manuscript reader. All sections expanded by default, rendered through `{!! $sanitizer->clean($content) !!}`. Each section shows its latest submitted version, word count vs target, and its review history. Action bar per section with Accept, Conditional approval, Request revision, Reject, Add comment:
- Accept / Conditional / Request revision / Reject each require a comment; Conditional and Reject require a confirmation dialog (Reject halts the whole document, so the dialog must say so explicitly).
- Add comment posts a comment with no status change.
- A halted document shows a prominent banner; issuing **Request revision** on the halted section releases the halt.

**`app/View/Components/AppHeader.php`** — add to the `supervisor` role navigation, after `Resources`:
`['label' => 'Manuscripts', 'href' => route('supervisor.manuscripts'), 'icon' => 'fa-file-lines', 'match' => ['supervisor.manuscripts*']]`, plus a `primary_actions` entry. Check `config/footer.php` (line 104 holds the student Defence readiness link) and add a matching supervisor entry if the supervisor footer has an equivalent list.

## Notifications

Add constants to `app/Models/Notification.php`:

```
TYPE_DEFENSE_SECTION_SUBMITTED   = 'defense_section_submitted'
TYPE_DEFENSE_SECTION_ACCEPTED    = 'defense_section_accepted'
TYPE_DEFENSE_SECTION_CONDITIONAL = 'defense_section_conditional'
TYPE_DEFENSE_SECTION_REVISION    = 'defense_section_revision'
TYPE_DEFENSE_SECTION_REJECTED    = 'defense_section_rejected'
TYPE_DEFENSE_COMMENT             = 'defense_comment'
TYPE_DEFENSE_CONDITIONS_ACKED    = 'defense_conditions_acked'
TYPE_DEFENSE_COMPLETED           = 'defense_completed'
```

Creation follows the `ResourceController.php:192-206` shape: `university_id`, `user_id` (the recipient's `users.id` — for students use `$student->user_id`, for the supervisor use `$supervisor->user_id`), `type`, `title`, `message`, `metadata` carrying `document_id`, `section_id`, `section_title`, `action`, `reviewer_name`, and `action_url` (`route('supervisor.manuscripts.show', $document)` for supervisor-bound rows, `route('student.defense-readiness', ['section' => $section->id])` for student-bound rows).

Event → recipient mapping:

| Event | Recipient |
|---|---|
| Student submits a section | Supervisor |
| Supervisor Accepts / Conditional / Requests revision / Rejects | Student |
| Supervisor adds a standalone comment | Student |
| Student ticks conditions addressed | Supervisor |
| Document completes | Student |
| Student adds a custom section | none |
| Autosave | none |

**Drawer**: `resources/js/header.js:106` `iconForType()` currently matches on `/approved/` and `/rejected/` substrings, which will not match the new type strings. Add explicit branches: accepted → `fa-circle-check text-emerald-500`, conditional → `fa-circle-half-stroke text-blue-500`, revision → `fa-rotate-right text-amber-500`, rejected and halted → `fa-circle-exclamation text-red-500`, submitted → `fa-file-arrow-up text-blue-500`, completed → `fa-award text-emerald-500`, comment → `fa-comment-dots text-slate-500`. The 60-second poll already picks these up with no other drawer change.

## Email

Extend `PortalEmail::resolveViewPayload()`'s `$viewMap` in `app/Mail/PortalEmail.php` with seven keys, and add matching standalone HTML templates under `resources/views/emails/portal/` in the existing inline-CSS style:

`defense-section-submitted`, `defense-section-accepted`, `defense-section-conditional`, `defense-section-revision`, `defense-section-rejected`, `defense-comment`, `defense-completed`.

Each template must render the supervisor's comment (escaped, and run through the sanitiser if it can contain markup) and a deep link to the relevant page. Pass `universityCode`, `portalName`, `studentName`, `matric`, `sectionTitle`, `comment`, `conditions`, `url` in `$data` so branding resolves exactly as it does for `topic-approved`.

Dispatch: `Mail::to($address)->send(new PortalEmail('defense-section-accepted', [...]))` inside `try/catch` with `Log::warning(...)`, matching `ResourceController.php:209-218`. **Keep it synchronous** — queueing would be a new pattern for this repo and there is no configured worker. Every dispatch call passes the recipient's `universityCode`.

**Out of scope, but worth flagging**: `config/universities.php` defines per-tenant `email_config.from_address` / `from_name` / `reply_to` that `PortalEmail` never reads. Wiring it would give each university a branded sender. Not included here.

## Sanitisation of Supervisor-Facing Content

Student content is HTML rendered inside the supervisor portal. Belt and braces:
1. Sanitise on every autosave and every submit snapshot.
2. Sanitise again on the supervisor read endpoint before returning content.
3. Render with `{!! $clean !!}` only after sanitisation; use `{{ }}` everywhere else, including inside email templates.

## Tests

Add under `tests/Feature/` and `tests/Unit/`, run with `php artisan test`. There is no JS test runner in this repo; editor and accordion behaviour is verified manually.

- `DefenseReadinessSeedingTest` — first load creates the document and all sections in order; the ethics sub-section only exists when `primary_data_collection` is true; changing the approach rewrites child guidance; settings lock after the first submission.
- `DefenseReadinessAutosaveTest` — persists sanitised HTML and the server word count; rejects `403` on a locked section, on a submitted section, and while halted; returns `409` on a stale `last_saved_at`; creates no notification, no email, and no version row.
- `DefenseReadinessGatingTest` — only the first section is `draft` initially; `accepted` and `conditional` unlock the next; `revision_requested` and `rejected` do not; children gate within their parent; `rejected` sets `documents.status = halted` and blocks edits document-wide; `releaseHalt` on `revision_requested` of the halted section restores editability without disturbing other sections' statuses.
- `DefenseReadinessSubmissionTest` — submit snapshots an immutable version with the correct `version_number`, sets `submitted`, flips the document to `in_review`, fires one supervisor notification and one email, and does not mutate the previous version row.
- `DefenseReadinessReviewTest` — all four decision actions plus standalone comment; `comment` required for decisions and optional for `commented`; each writes a `defense_readiness_reviews` row; each fires the correct notification type to the correct recipient; completing the final section sets `documents.status = completed` and fires `defense_completed`.
- `DefenseReadinessTenancyTest` — a supervisor cannot read or decide on another supervisor's student's manuscript; a student cannot edit another's sections; a student with no assigned supervisor is blocked from submitting with a clear message.
- `HtmlSanitizerTest` (unit) — strips `<script>`, `onerror=`, `onclick=`, `style=`, `javascript:` hrefs and `data-*` attributes; preserves the whitelisted formatting tags; `WordCounterTest` (unit) for hyphenated and accented words.
- Update `tests/Feature/FooterTest.php` and any header-nav assertion to cover the new supervisor Manuscripts entry.

## Rollout

1. Migrations, config, models, services, support classes.
2. API routes + controllers, with tests.
3. Sanitiser and its unit tests **before** any content is rendered anywhere.
4. Student page, then supervisor pages and nav.
5. Notifications and email templates, then `header.js` icon mapping.
6. Seed existing students: no document rows. A student creates their own manuscript on first visit, so no backfill is needed. The existing checklist and score are unaffected for everyone without a document.

## Risks

- **`document.execCommand` is deprecated.** Acceptable for this scope; contained in one Blade component and replaceable with TipTap without touching the page.
- **`ext-dom` availability.** Verify with `php -m` before relying on `DOMDocument`; the `strip_tags` fallback is weaker against obfuscated payloads, so prefer fixing the extension over using the fallback.
- **Per-section gating serialises progress.** A student stuck on revision blocks every later section. The supervisor queue therefore lists all statuses, not just `submitted`, so the supervisor can see where a student is stuck.
- **Reject is destructive-feeling** since it halts the whole document. Mitigated by requiring a comment, a confirmation dialog that states the consequence, and a `Request revision` escape hatch that releases the halt.
- **Sequential email sending.** A submit or decision now sends one synchronous email. Same as existing proposal/resource flows; revisit only if latency becomes visible.
- **Autosave conflict frequency.** Mitigated by the 409 handling and the explicit Reload/Overwrite choice.

## Open Questions

1. **Abstract word count conflict.** Your table gives "Title, abstract, keywords — 250–300", but your abstract guide says "one paragraph of about 180–250 words". The plan uses 250–300 for the section badge and 180–250 in the guidance panel. Confirm which is authoritative.
2. **Methodology subheading count.** Your guide lists 8 numbered subheadings; the option you selected was labelled "nine". The plan uses 8. Confirm.
3. **Custom section placement.** Assumed inserted before References, which always stays last. Confirm.
4. **No assigned supervisor.** A student with a null `supervisor_id` is blocked from submitting any section, with a prompt to contact the admin. Confirm this is the right behaviour rather than routing to an admin queue.
5. **"Submit for Defense".** Assumed it requires both the existing 70% readiness score and `documents.status === 'completed'`. Confirm whether the score gate should remain at all now that section review exists.
