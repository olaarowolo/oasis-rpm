# Plan: Super Admin Platform-Level Authentication & Admin University Selection Flow

## Executive Summary

Transform the authentication architecture so that:
1. **Super Admin** = platform-level (no university affiliation, no university selection)
2. **Admin** = university-scoped, but university selection happens AFTER OTP verification (not before)
3. **Student/Supervisor** = unchanged (university selection before OTP)

---

## Current State Analysis

### Database
- `users.university_id`: FK to universities, **NOT NULL** for all roles
- `users.role`: `student`, `supervisor`, `admin`, `super_admin`

### Login Flow (Current)
| Role | Step 1 | Step 2 | Step 3 | Step 4 |
|------|--------|--------|--------|--------|
| Student | Select University | Email → OTP | Verify OTP | Matric + Surname |
| Supervisor | Select University | Email → OTP | Verify OTP | PIN + Passphrase |
| Admin | Select University | Email → OTP | Verify OTP | Password (+ MFA) |
| Super Admin | Uses Admin tab | Email → OTP | Verify OTP | Password (+ MFA) |

### Key Files
- **Controller**: `app/Http/Controllers/AuthController.php`
- **Routes**: `routes/web.php` (api/auth/*)
- **Frontend**: `resources/views/partials/auth/login-gate.blade.php`, `resources/views/partials/auth/inline-script.blade.php`
- **Middleware**: `AuthGuard`, `EnsureRole`, `EnsureSuperAdminLogin`
- **Models**: `User`, `University`

---

## Proposed Architecture

### Database Changes
1. **Make `university_id` nullable** for `super_admin` role
2. Add migration to:
   - Drop NOT NULL constraint on `university_id`
   - Set `university_id = NULL` for existing `super_admin` users
   - Add index optimization

### Authentication Flow (New)

| Role | Step 1 | Step 2 | Step 3 | Step 4 |
|------|--------|--------|--------|--------|
| **Super Admin** | **Email → OTP** | **Verify OTP** | **Password (+ MFA)** | — |
| **Admin** | **Email → OTP** | **Verify OTP** | **Select University** | **Password (+ MFA)** |
| Student | Select University | Email → OTP | Verify OTP | Matric + Surname |
| Supervisor | Select University | Email → OTP | Verify OTP | PIN + Passphrase |

### Key Differences

#### Super Admin (Platform-Level)
- No university selector at any step
- No `university_id` stored in session
- Can access all universities' data via query filters
- Separate login endpoint: `POST /api/auth/super-admin/login`

#### Admin (University-Scoped, Post-OTP Selection)
- Email lookup determines possible universities (via `User::where('email', ...)` → may match multiple universities if same email used across tenants)
- After OTP verified, show university selector populated with user's universities
- Store selected `university_id` in session
- Existing endpoint: `POST /api/auth/admin/login` (but flow changes)

---

## Implementation Plan

### Phase 1: Database Migration (Foundation)

**Files to create/modify:**
- `database/migrations/XXXX_make_super_admin_university_nullable.php`

**Migration steps:**
1. Change `university_id` to nullable on `users` table
2. Update existing `super_admin` records: `university_id = NULL`
3. Add partial index for performance: `WHERE role = 'super_admin' AND university_id IS NULL`

### Phase 2: Backend AuthController Changes

**File: `app/Http/Controllers/AuthController.php`**

#### New Methods:
1. `sendSuperAdminOtp(Request $request)` - same as admin but role='super_admin'
2. `verifySuperAdminOtp(Request $request)` - same as admin but role='super_admin'
3. `loginSuperAdmin(Request $request)` - simplified, no university_id in session

#### Modified Methods:
1. `sendAdminOtp` - keep existing (lookup by email across all universities for admin/super_admin)
2. `verifyAdminOtp` - keep existing
3. `loginAdmin` - **NEW FLOW**: 
   - After OTP verified, expect `university_code` in request
   - Validate user belongs to that university
   - Set `university_id` in session
   - Return MFA challenge if needed

#### Removed/Changed:
- Remove `loginSuperAdmin` that expects university_id (replace with new flow)

### Phase 3: Routes

**File: `routes/web.php`**

```php
// Super Admin (platform-level) - NEW
Route::prefix('api/auth/super-admin')->group(function () {
    Route::post('/send-otp', [AuthController::class, 'sendSuperAdminOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifySuperAdminOtp']);
    Route::post('/login', [AuthController::class, 'loginSuperAdmin']);
});

// Admin (university-scoped) - MODIFIED flow
Route::prefix('api/auth/admin')->group(function () {
    Route::post('/send-otp', [AuthController::class, 'sendAdminOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyAdminOtp']);
    Route::post('/login', [AuthController::class, 'loginAdmin']); // now expects university_code
    Route::post('/verify-mfa', [AuthController::class, 'verifyAdminMfa']);
});
```

### Phase 4: Frontend Login Gate

**File: `resources/views/partials/auth/login-gate.blade.php`**

#### Changes:
1. **Add Super Admin tab** (4th tab: Student, Supervisor, Admin, Super Admin)
2. **Super Admin tab structure:**
   - NO university selector
   - Email → OTP → Verify OTP → Password (+ MFA)
3. **Admin tab structure (MODIFIED):**
   - **REMOVE** university selector from email step
   - Email → OTP → Verify OTP → **University Selector** → Password (+ MFA)
4. **Update tab switcher** to include Super Admin tab

#### New Blade Components:
- `@include('partials.auth.university-selector', ['type' => 'admin', 'step' => 'post-otp'])` - for post-OTP university selection

### Phase 5: Frontend JavaScript

**File: `resources/views/partials/auth/inline-script.blade.php`**

#### New Variables/State:
```javascript
let pendingSuperAdminEmail = '';
let superAdminOtpTimerInterval = null;
let superAdminOtpTimeLeft = 30;
```

#### New Functions (Super Admin):
- `showSuperAdminEmailStep()`
- `showSuperAdminOtpStep()`
- `showSuperAdminCredentialsStep()`
- `handleSuperAdminEmailSubmit()`
- `handleSuperAdminOtpSubmit()`
- `handleSuperAdminLogin()`
- `startSuperAdminOtpTimer()`, `stopSuperAdminOtpTimer()`
- `resendSuperAdminOtpCode()`

#### Modified Functions (Admin):
- `handleAdminEmailSubmit()` - **no university_code in request**
- `handleAdminOtpSubmit()` - **show university selector step after success**
- `handleAdminLogin()` - **expect university_code, validate membership**

#### New Function:
- `showAdminUniversityStep()` - render university selector after OTP verified
- Populate with universities where user has admin/super_admin role

### Phase 6: Middleware & Guards

**File: `app/Http/Middleware/AuthGuard.php`**

Modify `checkAuthentication()`:
- For `super_admin` role: **skip university_id validation** (allow NULL)
- For `admin` role: require `university_id` in session
- Update session expiry logic accordingly

**File: `app/Http/Middleware/EnsureSuperAdminLogin.php`**
- Already checks `session('role') === 'super_admin'` - no change needed

**File: `app/Http/Middleware/EnsureRole.php`**
- No change needed (checks session role)

### Phase 7: SuperAdminWebController

**File: `app/Http/Controllers/Web/SuperAdminWebController.php`**

- `actingUser()` - returns User without university eager load for super_admin
- Methods that use `$this->actingUser()?->university` - handle NULL case
- `auditContextUniversity()` - return first university or NULL for super_admin
- `buildUserQuery()` - no default university filter for super_admin

### Phase 8: Tests

**Files:**
- `tests/Feature/AuthSuperAdminTest.php` - new test file
- `tests/Feature/AuthAdminTest.php` - update for new flow
- Update existing tests that assume `university_id` for super_admin

---

## Migration Strategy

### Step 1: Deploy Migration
```bash
php artisan migrate
```
- Sets `university_id = NULL` for all `super_admin` users

### Step 2: Deploy Backend Changes
- New controller methods
- Updated routes
- Middleware updates

### Step 3: Deploy Frontend Changes
- New login gate tabs
- Updated JavaScript flow

### Step 4: Verify
- Super admin can login without university
- Admin flow: email → OTP → university select → password
- All existing tests pass

---

## Edge Cases & Considerations

### 1. Existing Super Admin Users
- Migration sets `university_id = NULL`
- They can login immediately with new flow

### 2. Admin with Multiple University Memberships
- If admin email exists in multiple universities, post-OTP selector shows all
- Admin selects which university to access for this session

### 3. Super Admin Accessing University-Scoped Features
- `auditContextUniversity()` returns first university or NULL
- Controllers handle NULL gracefully (show all universities)

### 4. API Endpoints Expecting `university_id`
- Super admin requests: `X-University-ID` header optional
- If missing, default to "all universities" scope

### 5. Session Storage
- Super admin: no `university_id` in session
- Admin: `university_id` set after university selection step

---

## File Change Summary

| Phase | Files | Count |
|-------|-------|-------|
| 1. Migration | 1 new migration | 1 |
| 2. AuthController | 1 modified | 1 |
| 3. Routes | 1 modified | 1 |
| 4. Login Gate | 1 modified | 1 |
| 5. Inline Script | 1 modified | 1 |
| 6. Middleware | 1 modified | 1 |
| 7. SuperAdminWebController | 1 modified | 1 |
| 8. Tests | 2 new, 2 modified | 4 |
| **Total** | | **~11 files** |

---

## Rollback Plan

If issues arise:
1. Revert migration: `php artisan migrate:rollback`
2. Revert controller/routes/middleware to previous version
3. Revert frontend templates
4. All existing super_admin users retain their `university_id` (was NULL, can be restored)

---

## Acceptance Criteria

✅ Super admin can login WITHOUT selecting university
✅ Super admin has NO `university_id` in session
✅ Super admin can access all universities' data in dashboard
✅ Admin login: email → OTP → **then** university selection → password
✅ Admin university selector shows only universities where they have admin role
✅ Student/Supervisor flows unchanged
✅ All existing tests pass
✅ New tests cover super admin platform-level flow
✅ No regression in admin MFA flow