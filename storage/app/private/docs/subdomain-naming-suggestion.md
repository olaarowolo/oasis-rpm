# AfriScribe Product Naming Suggestion — TheOAsis Research Supervision Portal

**Date:** 2026-09-24
**Project:** TheOAsis UG-Research Project Management System (v1.0)
**Working Directory:** `/Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm`

---

## Project Overview

This is **TheOAsis UG-Research Supervision Portal** (`theaoasis/research-pms`), a Laravel 10 application already configured with `afriscribe.org` email infrastructure. It is a multi-tenant undergraduate research supervision and project management system featuring:

- Four-tier RBAC: **Super Admin**, **Admin** (university), **Supervisor**, **Student**
- Six-stage research lifecycle management per student
- Proposal submission & review workflows
- Meeting scheduling & logging between supervisors and students
- Learning resource tracking with approval workflows
- Weighted **defense readiness scoring** (topic approval, progress, final document, meetings, resources)
- Analytics dashboard for supervisors
- AI assistant integration (Gemini)
- MFA-protected admin access, bulk student CSV import
- University registry (multi-tenant)

---

## Naming Convention Analysis

AfriScribe's existing products follow the pattern **`AfriScribe [Feature]`** with corresponding single-word subdomains:

| Existing Product | Product Name | Subdomain (implied) |
|---|---|---|
| Manuscripts | AfriScribe Manuscripts | manuscripts.afriscribe.org |
| Proofread | AfriScribe Proofread | proofread.afriscribe.org |
| Insights | AfriScribe Insights | insights.afriscribe.org |
| Connect | AfriScribe Connect | connect.afriscribe.org |
| Archive | AfriScribe Archive | archive.afriscribe.org |
| Editor | AfriScribe Editor | editor.afriscribe.org |

---

## Recommended Naming

**Product name:** `AfriScribe Supervise`
**Subdomain:** `supervise.afriscribe.org`

This follows the existing naming convention exactly — a single, action-oriented word after "AfriScribe" — and directly describes the system's core function: managing the supervision relationship between faculty and undergraduate researchers.

---

## Alternatives

| Option | Product Name | Subdomain | Rationale |
|---|---|---|---|
| 1 | AfriScribe Supervise | supervise.afriscribe.org | Strongest match to existing convention; concise, descriptive |
| 2 | AfriScribe Oasis | oasis.afriscribe.org | Honours the original project name while integrating under the AfriScribe umbrella |
| 3 | AfriScribe Research | research.afriscribe.org | Broader academic research scope; aligns with "AfriScribe Research" |
| 4 | AfriScribe Supervision | supervision.afriscribe.org | Noun form of "supervise"; slightly more formal |
| 5 | AfriScribe Scholars | scholars.afriscribe.org | Emphasises the student/researcher user base |

---

## Why `supervise.afriscribe.org`

1. **Convention match** — Slots cleanly into the existing product suite without renaming
2. **Descriptive** — Immediately communicates the system's purpose
3. **Concise** — Single word, easy to type and remember
4. **Brand consistency** — Maintains the AfriScribe product family identity

---

## Implementation Notes

- The application is already configured to use `afriscribe.org` email infrastructure (`mail.afriscribe.org`, `admin@afriscribe.org`)
- The `.env` file uses `APP_NAME="TheOAsis Research Supervision Portal"` — this can be updated to reflect the new product branding
- The `composer.json` references `theaoasis/research-pms` — package name can remain as-is for internal identification
