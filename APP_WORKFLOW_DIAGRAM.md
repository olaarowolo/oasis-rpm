# Application Workflow Diagram

This document shows the current end-to-end workflow of the Research Supervision Portal, based on the implemented login, MFA, session, role routing, admin, and password reset flows.

## High-Level Flow

```mermaid
flowchart TD
    A[Visitor opens portal] --> B{Chooses login path}

    B -->|Student| S1[Student login form]
    B -->|Supervisor| V1[Supervisor login form]
    B -->|Admin| A1[Admin login form]
    B -->|Demo| D1[Demo request modal]

    S1 --> S2[POST /api/auth/student/login]
    V1 --> V2[POST /api/auth/supervisor/login]
    A1 --> A2[POST /api/auth/admin/login]

    S2 --> S3{Credentials valid?}
    V2 --> V3{Credentials valid?}
    A2 --> A3{Credentials valid?}

    S3 -->|No| S4[Return invalid credentials]
    V3 -->|No| V4[Return invalid credentials]
    A3 -->|No| A4[Return invalid credentials]

    S3 -->|Yes| S5[Regenerate session]
    V3 -->|Yes| V5[Regenerate session]
    A3 -->|Yes| A5[Create MFA challenge]

    S5 --> S6[Store user_id role student university_id]
    V5 --> V6[Store user_id role supervisor university_id]
    A5 --> A6[Send 6-digit MFA code by email]

    S6 --> S7[Student dashboard]
    V6 --> V7[Supervisor dashboard]
    A6 --> A7[Admin enters MFA code]

    A7 --> A8[POST /api/auth/admin/verify-mfa]
    A8 --> A9{Code valid and unexpired?}
    A9 -->|No| A10[Reject verification]
    A9 -->|Yes| A11[Regenerate session]

    A11 --> A12[Store user_id role admin or super_admin university_id]
    A12 --> A13[Redirect to /admin/dashboard]

    S7 --> R1[Role middleware and session guard]
    V7 --> R1
    A13 --> R1

    R1 --> R2{Session valid and role allowed?}
    R2 -->|No| R3[Redirect to login or matching portal]
    R2 -->|Yes| R4[Allow access]

    R4 --> P1[Protected dashboard or module]

    D1 --> D2[Demo request submitted]
    D2 --> D3[Stored / emailed for review]
```

## Auth And Security Sequence

```mermaid
sequenceDiagram
    actor User
    participant UI as Login Page
    participant Auth as AuthController
    participant Cache as Cache
    participant Mail as Mailer
    participant Session as Session

    User->>UI: Enter credentials
    UI->>Auth: POST login request
    Auth->>Auth: Validate input
    Auth->>Auth: Verify credentials

    alt Student or Supervisor
        Auth->>Session: Regenerate session
        Auth->>Session: Store role and identity
        Auth-->>UI: JSON success response
        UI-->>User: Open dashboard
    else Admin
        Auth->>Session: Regenerate session
        Auth->>Cache: Store MFA challenge
        Auth->>Mail: Send verification code
        Auth-->>UI: requires_mfa = true
        UI-->>User: Show MFA modal
        User->>UI: Enter verification code
        UI->>Auth: POST verify-mfa
        Auth->>Cache: Load challenge
        Auth->>Auth: Verify code, IP, UA, expiry
        Auth->>Session: Regenerate session
        Auth->>Session: Store admin session values
        Auth-->>UI: dashboard_url
        UI-->>User: Redirect to admin dashboard
    end
```

## Route And Access Flow

```mermaid
flowchart LR
    subgraph Public
        L1[/ /login /]
        L2[/ /api/auth/* /]
        L3[/ /password/* /]
    end

    subgraph SessionProtected
        W1[/ /home /]
        W2[/ /admin/dashboard /]
        W3[/ /admin/users /]
        W4[/ /admin/config /]
        W5[/ /supervisor/dashboard /]
        W6[/ /student/dashboard /]
    end

    L1 --> L2
    L2 --> W1
    W1 --> W2
    W1 --> W5
    W1 --> W6

    W2 --> M1[auth + role admin,super_admin]
    W3 --> M1
    W4 --> M1
    W5 --> M2[auth + role supervisor]
    W6 --> M3[auth + role student]
```

## Password Reset Flow

```mermaid
flowchart TD
    P0[User requests password reset] --> P1[POST /password/reset-link]
    P1 --> P2{Email exists?}
    P2 -->|No| P3[Return success message without revealing user]
    P2 -->|Yes| P4[Check 3/day rate limit]
    P4 --> P5[Generate 64-char token]
    P5 --> P6[Store token in cache for 1 hour]
    P6 --> P7[Bind token to IP address]
    P7 --> P8[Send reset email]
    P8 --> P9[User opens reset link]
    P9 --> P10[POST /password/verify-token]
    P10 --> P11{Token valid, unexpired, same IP?}
    P11 -->|No| P12[Reject token]
    P11 -->|Yes| P13[Show reset form]
    P13 --> P14[POST /password/reset]
    P14 --> P15{Password meets policy?}
    P15 -->|No| P16[Return complexity errors]
    P15 -->|Yes| P17[Hash new password]
    P17 --> P18[Invalidate session state]
    P18 --> P19[Forget token]
    P19 --> P20[Return success]
```

## Admin Portal Workflow

```mermaid
flowchart TD
    A0[Admin dashboard] --> A1[Recent users table]
    A0 --> A2[Quick actions]
    A0 --> A3[System snapshot]
    A0 --> A4[Gemini assistant panel]

    A2 --> U1[Manage Users]
    A2 --> C1[System Configuration]
    A2 --> S1[Supervisor filter]

    U1 --> U2[/admin/users/]
    C1 --> C2[/admin/config/]

    U2 --> U3[Create user via admin API]
    U2 --> U4[Edit user via admin API]
    U2 --> U5[Delete user via admin API]

    C2 --> C3[Save settings per config key]
    C2 --> C4[Switch university scope]
```

## Security Controls Summary

```mermaid
mindmap
  root((Security))
    Authentication
      Student login
      Supervisor login
      Admin login
      Admin MFA
    Session
      Regeneration
      Idle timeout
      UA/IP markers
      Secure logout
    Access Control
      Role middleware
      Protected dashboards
      Admin vs supervisor vs student
    Passwords
      12+ chars
      Uppercase
      Lowercase
      Number
      Special char
      Reset token expiry
    Headers
      HSTS
      CSP
      X-Frame-Options
      X-Content-Type-Options
      Referrer-Policy
      Permissions-Policy
    Rate Limiting
      Login failures
      Password reset requests
      Lockouts
```

## Notes 

- Admin sign-in is a two-step flow: password first, then email MFA.
- Student and supervisor login complete in one step, followed by session-based route protection.
- The app uses custom middleware and controllers rather than a single centralized auth package.
- The diagram reflects the current codebase state, not the earlier aspirational security document.
