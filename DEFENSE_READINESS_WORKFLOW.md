# Defense Readiness Workflow

## Overview
The Defense Readiness system manages the manuscript preparation and review process for research students, guiding them from initial checklist through supervisor review to defense submission.

---

## Mermaid Diagram

```mermaid
flowchart TD
    %% Student Phase
    A[Student opens /student/defense-readiness] --> B{Checklist Score >= 70%?}
    B -->|No| C[Manuscript Form Hidden]
    B -->|Yes| D[Submit for Defense Button Enabled]
    
    C --> E[Student clicks Manuscript Review]
    E --> F[GET /api/student/defense-readiness]
    F --> G[Returns document + sections + checklist]
    G --> H[JS renders section editors]
    
    H --> I[Student writes in unlocked section]
    I --> J[Autosave PUT /sections/{id}/content]
    J --> K[Sanitize HTML + count words]
    K --> L[Optimistic lock check]
    L --> M[Save to section.content]
    
    M --> N{Meets word count?}
    N -->|Yes| O[Student clicks Submit]
    N -->|No| I
    
    O --> P[POST /sections/{id}/submit]
    P --> Q[Create SectionVersion snapshot]
    Q --> R[section.status = submitted]
    R --> S[document.status = in_review]
    S --> T[Notify Supervisor]
    
    %% Supervisor Phase
    T --> U[Supervisor sees manuscript in queue]
    U --> V[GET /api/supervisor/manuscripts]
    V --> W[GET /api/supervisor/manuscripts/{id}]
    W --> X[Returns sanitized latest version + reviews]
    
    X --> Y[Supervisor reads + decides]
    Y --> Z{Decision Action}
    
    Z -->|accepted| AA[section.status = accepted]
    AA --> AB[Unlock next section]
    AB --> AC[Check if last required section]
    AC -->|Yes| AD[document.status = completed]
    AC -->|No| H
    
    Z -->|conditional| AE[section.status = conditional]
    AE --> AF[Store conditions]
    AF --> AB
    
    Z -->|revision_requested| AG[section.status = revision_requested]
    AG --> AH[If halted, release halt]
    AH --> H
    
    Z -->|rejected| AI[section.status = rejected]
    AI --> AJ[document.status = halted]
    AJ --> AK[halted_section_id = section]
    AK --> AL[Student must revise]
    AL --> H
    
    Z -->|commented| AM[Add review comment only]
    AM --> H
    
    %% Completion
    AD --> AN[Defense Ready - Submit for Defense]
    AN --> AO[Student clicks Submit for Defense]
    AO --> AP[Defense scheduled]

    classDef student fill:#e3f2fd,stroke:#1976d2,stroke-width:2px
    classDef supervisor fill:#fff3e0,stroke:#f57c00,stroke-width:2px
    classDef system fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
    classDef decision fill:#e8f5e9,stroke:#388e3c,stroke-width:2px
    
    class A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,AL student
    class U,V,W,X,Y supervisor
    class Z,AA,AB,AC,AD,AE,AF,AG,AH,AI,AJ,AK,AM decision
    class K,L,Q,R,S,T system
```

---

## Phase Breakdown

### Phase 1: Checklist Assessment (Web Route)
**Route:** `GET /student/defense-readiness`  
**Controller:** `routes/web.php:435-468`  
**View:** `resources/views/student/defense-readiness.blade.php`

| Requirement | Weight | Source |
|-------------|--------|--------|
| Topic Approval | 20% | `research_topic_approved_date` |
| Progress Completion | 30% | `progress_percentage` |
| Final Document | 20% | `archiveSubmission` exists |
| Meeting Logs | 15% | 6 approved meetings |
| Resource Completion | 15% | 80% of assigned resources |

**Score < 70%** → Manuscript form hidden, only checklist visible  
**Score ≥ 70%** → "Submit for Defense" button enabled

---

### Phase 2: Manuscript Editor (Student API)
**Endpoint:** `GET /api/student/defense-readiness`  
**Controller:** `DefenseReadinessController::studentIndex()`

Returns:
```json
{
  "id": 1,
  "status": "draft",
  "study_approach": "quantitative",
  "primary_data_collection": false,
  "settings_locked": false,
  "sections": [...],
  "checklist": {...}
}
```

**JS renders:** `resources/js/defense-readiness.js` + `rich-text-editor.js`

---

### Phase 3: Section Writing & Autosave

**Autosave Endpoint:** `PUT /api/student/defense-readiness/sections/{id}/content`  
**Controller:** `DefenseReadinessController::studentAutosave()`

Process:
1. Validate student owns section
2. Sanitize HTML (strip `<script>`, keep allowed tags)
3. Count words
4. Optimistic lock: reject if `last_saved_at` < server `updated_at`
5. Update `section.content`, `section.word_count`, `section.updated_at`

**Debounce:** 1-2 seconds in JS

---

### Phase 4: Submit for Review

**Endpoint:** `POST /api/student/defense-readiness/sections/{id}/submit`  
**Controller:** `DefenseReadinessController::studentSubmitSection()`  
**Service:** `DefenseReadinessService::submitSection()`

Actions:
1. Create `DefenseReadinessSectionVersion` (immutable snapshot)
2. Set `section.status = 'submitted'`
3. Set `section.submitted_at = now()`
4. Set `document.status = 'in_review'`
5. Notify supervisor (email + in-app)

---

### Phase 5: Supervisor Review

**List Endpoint:** `GET /api/supervisor/manuscripts`  
**Detail Endpoint:** `GET /api/supervisor/manuscripts/{id}`  
**Controller:** `DefenseReadinessController::supervisorIndex()` / `supervisorShow()`

Detail returns sanitized latest version content + full review history.

---

### Phase 6: Supervisor Decision

**Endpoint:** `POST /api/supervisor/sections/{id}/decision`  
**Controller:** `DefenseReadinessController::supervisorDecision()`  
**Service:** `DefenseReadinessService::decide()`

| Action | Section Status | Document Effect | Next Section |
|--------|----------------|-----------------|--------------|
| `accepted` | `accepted` | Unlock next | ✅ Unlock |
| `conditional` | `conditional` | Unlock next | ✅ Unlock |
| `revision_requested` | `revision_requested` | If halted → release halt | ✅ Unlock |
| `rejected` | `rejected` | **Halt entire document** | ❌ Locked |
| `commented` | unchanged | No change | — |

**Creates:** `DefenseReadinessReview` record

---

### Phase 7: Section Status Flow

```
┌─────────┐
│ locked  │  ← Initial state (all except first)
└────┬────┘
     │ unlock (sequential)
     ▼
┌─────────┐
│  draft  │  ← Student can edit
└────┬────┘
     │ submit
     ▼
┌──────────────┐
│  submitted   │  ← Awaiting supervisor
└──────┬───────┘
       │
       ├─ accepted ──────────► ┌─────────┐
       │                       │ accepted│ → unlock next
       ├─ conditional ────────► │conditional│ → unlock next
       │                       └─────────┘
       ├─ revision_requested ──► ┌─────────────────┐
       │                         │revision_requested│ → back to draft
       ├─ rejected ─────────────► ┌─────────┐
       │                         │ rejected│ → HALT document
       │                         └─────────┘
       └─ commented ─────────────► (no status change)
```

---

### Phase 8: Document Completion

**Trigger:** Last required section → `accepted`  
**Service:** `DefenseReadinessService::checkCompletion()` (called from `decide()`)

Actions:
1. `document.status = 'completed'`
2. `document.completed_at = now()`
3. Notify student

**Result:** Student can now click "Submit for Defense" (score ≥ 70% guaranteed)

---

## API Reference

### Student Endpoints
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | `/api/student/defense-readiness` | `studentIndex` |
| GET | `/api/student/defense-readiness/settings` | `studentSettings` |
| PATCH | `/api/student/defense-readiness/settings` | `studentUpdateSettings` |
| PUT | `/api/student/defense-readiness/sections/{id}/content` | `studentAutosave` |
| POST | `/api/student/defense-readiness/sections/{id}/submit` | `studentSubmitSection` |
| POST | `/api/student/defense-readiness/sections/{id}/acknowledge-conditions` | `studentAcknowledgeConditions` |
| POST | `/api/student/defense-readiness/sections` | `studentAddCustomSection` |
| DELETE | `/api/student/defense-readiness/sections/{id}` | `studentDeleteSection` |

### Supervisor Endpoints
| Method | Endpoint | Controller Method |
|--------|----------|-------------------|
| GET | `/api/supervisor/manuscripts` | `supervisorIndex` |
| GET | `/api/supervisor/manuscripts/{id}` | `supervisorShow` |
| POST | `/api/supervisor/sections/{id}/decision` | `supervisorDecision` |

---

## Key Models

| Model | Key Fields |
|-------|------------|
| `DefenseReadinessDocument` | `student_id`, `university_id`, `study_approach`, `primary_data_collection`, `status`, `halted_at`, `halted_section_id`, `halted_reason`, `completed_at` |
| `DefenseReadinessSection` | `document_id`, `parent_id`, `key`, `title`, `position`, `kind`, `guidance`, `target_min_words`, `target_max_words`, `content`, `word_count`, `status`, `current_version_id`, `submitted_at`, `decided_at`, `conditions_acknowledged_at` |
| `DefenseReadinessSectionVersion` | `section_id`, `document_id`, `version_number`, `content`, `word_count`, `submitted_by_user_id` |
| `DefenseReadinessReview` | `section_id`, `document_id`, `version_id`, `reviewer_user_id`, `action`, `comment`, `conditions` |

---

## Middleware Chain

```
Request
   │
   ▼
CheckUniversity (X-University-ID or session)
   │
   ▼
AuthGuard (session validation, MFA, token)
   │
   ▼
EnsureStudentLogin / EnsureSupervisorLogin (role check)
   │
   ▼
Controller
```

---

## Notifications

| Event | Recipient | Channel |
|-------|-----------|---------|
| Section submitted | Supervisor | Email + In-app |
| Section accepted | Student | Email + In-app |
| Section conditional | Student | Email + In-app |
| Section revision requested | Student | Email + In-app |
| Section rejected | Student | Email + In-app |
| Section commented | Student | In-app |
| Document completed | Student | Email + In-app |

---

## Configuration

Sections defined in `config/defense-readiness.php`:
- Top-level sections (Introduction, Literature Review, Methodology, etc.)
- Methodology children (Design, Population, Instrument, Procedure, Ethics*)
- *Ethics only when `primary_data_collection = true`

Word count targets, guidance text, and requirement flags configured there.