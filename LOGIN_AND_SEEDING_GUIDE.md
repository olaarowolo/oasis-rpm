# Login & Seeding Guide

## Overview

This guide covers how to log in to the Research Supervision Portal and seed the database with initial data.

## Quick Start

1. **Run migrations first** (if not already done):
```bash
php artisan migrate
```

2. **Seed the database**:
```bash
php artisan db:seed
```

3. **Start the server** (if not already running):
```bash
php artisan serve
```

4. **Access the application** at `http://localhost:8000`

---

## Database Seeding

### Running All Seeders

To seed the entire database with default data:

```bash
php artisan db:seed
```

This will run all seeders defined in `DatabaseSeeder.php`:
- UniversitySeeder (creates 2 universities: LASU, UI)
- UserSeeder (creates 20 users across all roles)
- SupervisorSeeder (creates 2 supervisors)
- StudentSeeder (creates 10 students)
- ResourceSeeder
- ProposalSeeder
- MeetingLogSeeder
- StageHistorySeeder

### Running Individual Seeders

Seed specific data:

```bash
# Seed only universities
php artisan db:seed --class=UniversitySeeder

# Seed only users
php artisan db:seed --class=UserSeeder

# Seed only students
php artisan db:seed --class=StudentSeeder

# Seed only supervisors
php artisan db:seed --class=SupervisorSeeder

# Seed only resources
php artisan db:seed --class=ResourceSeeder

# Seed only proposals
php artisan db:seed --class=ProposalSeeder

# Seed only meeting logs
php artisan db:seed --class=MeetingLogSeeder

# Seed only stage history
php artisan db:seed --class=StageHistorySeeder
```

---

## Login Credentials

### Super Admin

- **Email**: `superadmin@afriscribe.org`
- **Password**: `SuperAdmin@2026`
- **Access**: Full platform-level access across all universities

### Platform Admin (LASU)

- **Email**: `admin@afriscribe.org`
- **Password**: `Admin@2026`
- **University**: Lagos State University (LASU)
- **Access**: Administrative access for LASU

### Alternative Admin (LASU)

- **Email**: `olaarowolo.ng@gmail.com`
- **Password**: `Admin@2026`
- **University**: Lagos State University (LASU)
- **Access**: Administrative access for LASU

### Supervisor (LASU)

- **Email**: `supervisor@lasu.edu.ng`
- **Password**: `Supervisor@2026`
- **University**: Lagos State University (LASU)
- **PIN Code**: `2026`
- **Passphrase**: `LASU-Arowolo-2026`
- **Access**: Manage students, proposals, meetings, resources for LASU

### Supervisor (UI)

- **Email**: `supervisor@ui.edu.ng`
- **Password**: `Supervisor@2026`
- **University**: University of Ibadan (UI)
- **PIN Code**: `2026`
- **Passphrase**: `UI-Doe-2026`
- **Access**: Manage students, proposals, meetings, resources for UI

---

## Student Login (LASU)

Students login using **matriculation number** and **surname**.

| Matric Number | Surname | Password |
|--------------|---------|----------|
| LASU/2021/0001 | Arowolo | Student@2026 |
| LASU/2021/0002 | Okafor | Student@2026 |
| LASU/2021/0003 | Adeyemi | Student@2026 |
| LASU/2021/0004 | Ibrahim | Student@2026 |
| LASU/2021/0005 | Adekunle | Student@2026 |

**Note**: Students don't have an email/password login. They use their matric number and surname.

---

## Student Login (UI)

| Matric Number | Surname | Password |
|--------------|---------|----------|
| UI/2021/0001 | Osinbajo | Student@2026 |
| UI/2021/0002 | Adeyemi | Student@2026 |
| UI/2021/0003 | Bello | Student@2026 |
| UI/2021/0004 | Olawale | Student@2026 |
| UI/2021/0005 | Fasusi | Student@2026 |

---

## Login Flow by Role

### Student Login (API)
```bash
curl -X POST http://localhost:8000/api/auth/login-student \
  -H "Content-Type: application/json" \
  -d '{
    "university_code": "LASU",
    "matric_number": "LASU/2021/0001",
    "lastname": "Arowolo"
  }'
```

### Supervisor Login (API)
```bash
curl -X POST http://localhost:8000/api/auth/login-supervisor \
  -H "Content-Type: application/json" \
  -d '{
    "university_code": "LASU",
    "pin_code": "2026",
    "passphrase": "LASU-Arowolo-2026"
  }'
```

### Admin Login (API)
```bash
curl -X POST http://localhost:8000/api/auth/login-admin \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@afriscribe.org",
    "password": "Admin@2026"
  }'
```

### Super Admin Login (API)
```bash
curl -X POST http://localhost:8000/api/auth/login-super-admin \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@afriscribe.org",
    "password": "SuperAdmin@2026"
  }'
```

---

## OTP Login Flow

If OTP login is enabled, use the sendOtp endpoint first:

```bash
curl -X POST http://localhost:8000/api/auth/send-otp \
  -H "Content-Type: application/json" \
  -d '{
    "role": "student",
    "university_code": "LASU",
    "matric_number": "LASU/2021/0001",
    "lastname": "Arowolo"
  }'
```

Then verify the OTP:

```bash
curl -X POST http://localhost:8000/api/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "role": "student",
    "email": "student1@lasu.edu.ng",
    "otp": "123456"
  }'
```

---

## User Management (Admin Only)

### List All Users
```bash
curl -X GET http://localhost:8000/api/admin/users \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create New User
```bash
curl -X POST http://localhost:8000/api/admin/users \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "university_id": 1,
    "email": "newuser@lasu.edu.ng",
    "name": "New User",
    "password": "NewPassword@2026",
    "role": "supervisor"
  }'
```

### Update User
```bash
curl -X PUT http://localhost:8000/api/admin/users/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated User Name"
  }'
```

### Delete User
```bash
curl -X DELETE http://localhost:8000/api/admin/users/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Resetting the Database

To completely reset and reseed:

```bash
# Drop all tables and run migrations
php artisan migrate:fresh

# Run all seeders
php artisan db:seed
```

Or use the complete reset command:

```bash
php artisan migrate:fresh --seed
```

---

## Verifying Seeded Data

### Check Universities
```bash
php artisan tinker
>>> App\Models\University::all()
```

### Check Users by Role
```bash
php artisan tinker
>>> App\Models\User::where('role', 'student')->get()
>>> App\Models\User::where('role', 'supervisor')->get()
>>> App\Models\User::where('role', 'admin')->get()
>>> App\Models\User::where('role', 'super_admin')->get()
```

### Check Student-User Relationships
```bash
php artisan tinker
>>> App\Models\Student::with('user')->get()
```

### Check Supervisor-User Relationships
```bash
php artisan tinker
>>> App\Models\Supervisor::with('user')->get()
```

---

## Troubleshooting

### "University not found" Error
Make sure you've seeded universities first:
```bash
php artisan db:seed --class=UniversitySeeder
```

### "Invalid credentials" Error
- Verify the credentials match the tables above
- Ensure you're using the correct university code (LASU or UI)
- Check that the user exists in the database

### "Session expired" Error
Re-authenticate using the appropriate login endpoint for your role.

---

## Default Universities

### Lagos State University (LASU)
- Code: `LASU`
- Email: `research@lasu.edu.ng`
- Department: Journalism and Media Studies

### University of Ibadan (UI)
- Code: `UI`
- Email: `research@ui.edu.ng`
- Department: Academic Affairs
