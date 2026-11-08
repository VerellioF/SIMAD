# SIMAD (Sistem Informasi Madrasah Terpadu) Core & Phase 1 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the complete core database architecture (15 tables), multi-panel authentication (Admin, Teacher, Parent) with Filament v3, and Phase 1 deliverables (Islamic Landing Page, PPDB Online registration, PDF proof generation, and application status tracker) optimized for shared hosting without Node.js on the server.

**Architecture:** Laravel 11.x + Filament v3 Multi-Panel (`/admin`, `/guru`, `/wali`) for backoffice & portals; Laravel Blade + Tailwind CSS for public web & PPDB; pre-compiled static assets for zero-Node.js shared hosting runtime.

**Tech Stack:** PHP 8.4, Laravel 11.x, Filament PHP v3 (TALL stack: Livewire 3, Alpine.js, Tailwind CSS), SQLite (local dev) / MySQL 8.0 (production), Barryvdh DomPDF, Maatwebsite Excel.

**Spec:** [prd.md](file:///d:/MIRM/prd.md), [technical.md](file:///d:/MIRM/technical.md), [skema_basis_data_simad.md](file:///d:/MIRM/skema_basis_data_simad.md).

---

## Global Constraints

- PHP 8.4 compatibility with strict type declarations (`declare(strict_types=1);`).
- Zero Node.js dependency on the production server (all CSS/JS assets compiled locally via Vite into `public/build`).
- Local database uses SQLite (`database/database.sqlite`), fully compatible with MySQL 8.0+ production DDL.
- Role-based separation: `admin` -> `/admin`, `teacher` -> `/guru`, `parent` -> `/wali`.
- File uploads (Akta, KK, Bukti Bayar) strictly validated (mime types: pdf, jpg, png; max size: 2MB).
- All tests must use PHPUnit / Pest and run via `php artisan test`.

---

## Review Focus

1. **Shared Hosting Asset Serving:** `public/build/manifest.json` missing or incorrect asset path on cPanel shared hosting without Node.js.
2. **Phone / WhatsApp Number Format:** Normalization of WhatsApp numbers (e.g. `08...` vs `628...`) for login and notification consistency.
3. **PPDB Registration Number Collision:** Race condition when generating sequential `PPDB-YYYY-XXXX` registration numbers under concurrent requests.
4. **Panel Authorization Leakage:** Teacher or Parent accounts attempting direct access to `/admin` routes must receive strict 403 Forbidden.
5. **Private Document Exposure:** PPDB attachments (KK, Akta) stored on public disk without access control.

---

## Task Decomposition

### Task 1: Environment, Dependencies & Shared Hosting Build Pipeline

**Files:**
- Modify: `composer.json`
- Modify: `.env.example`, `.env`
- Modify: `.gitignore`
- Create: `config/simad.php`
- Modify: `vite.config.js`
- Test: `tests/Feature/EnvironmentSetupTest.php`

**Steps:**
- [ ] Create failing test `tests/Feature/EnvironmentSetupTest.php` checking config keys and packages.
- [ ] Run test to verify failure: `php artisan test --filter=EnvironmentSetupTest`
- [ ] Install required auxiliary packages:
  `composer require barryvdh/laravel-dompdf maatwebsite/excel`
- [ ] Configure `config/simad.php` for madrasah identity and default parameters.
- [ ] Configure `.gitignore` to allow tracked production build assets (`public/build`) for shared hosting deployment.
- [ ] Run test to verify pass: `php artisan test --filter=EnvironmentSetupTest`
- [ ] Commit changes: `git commit -m "chore: setup dependencies, simad config, and shared hosting build pipeline"`

---

### Task 2: Database Migrations for 15 Tables

**Files:**
- Create: `database/migrations/2026_11_08_000001_create_academic_years_table.php`
- Create: `database/migrations/2026_11_08_000002_create_classrooms_table.php`
- Create: `database/migrations/2026_11_08_000003_create_subjects_table.php`
- Create: `database/migrations/2026_11_08_000004_create_students_table.php`
- Create: `database/migrations/2026_11_08_000005_create_ppdb_registrations_table.php`
- Create: `database/migrations/2026_11_08_000006_create_attendances_table.php`
- Create: `database/migrations/2026_11_08_000007_create_leave_requests_table.php`
- Create: `database/migrations/2026_11_08_000008_create_tahfidz_records_table.php`
- Create: `database/migrations/2026_11_08_000009_create_academic_grades_table.php`
- Create: `database/migrations/2026_11_08_000010_create_tuition_bills_table.php`
- Create: `database/migrations/2026_11_08_000011_create_tuition_payments_table.php`
- Create: `database/migrations/2026_11_08_000012_create_articles_table.php`
- Create: `database/migrations/2026_11_08_000013_create_school_galleries_table.php`
- Create: `database/migrations/2026_11_08_000014_create_announcements_table.php`
- Modify: `database/migrations/0001_01_01_000000_create_users_table.php`
- Test: `tests/Feature/DatabaseSchemaTest.php`

**Steps:**
- [ ] Create failing test `tests/Feature/DatabaseSchemaTest.php` asserting all 15 tables and their primary indexes exist in schema.
- [ ] Run test to verify failure: `php artisan test --filter=DatabaseSchemaTest`
- [ ] Update `users` migration with `phone_number`, `role` enum (`admin`, `teacher`, `parent`), `avatar_url`, and `is_active`.
- [ ] Implement migrations for the other 14 tables strictly adhering to `skema_basis_data_simad.md`.
- [ ] Run `php artisan migrate:fresh` to verify migration syntax and foreign key integrity.
- [ ] Run test to verify pass: `php artisan test --filter=DatabaseSchemaTest`
- [ ] Commit changes: `git commit -m "feat(database): implement 15-table relational schema migrations"`

---

### Task 3: Eloquent Models, Relationships, Enums & Casts

**Files:**
- Modify: `app/Models/User.php`
- Create: `app/Models/AcademicYear.php`
- Create: `app/Models/Classroom.php`
- Create: `app/Models/Subject.php`
- Create: `app/Models/Student.php`
- Create: `app/Models/PpdbRegistration.php`
- Create: `app/Models/Attendance.php`
- Create: `app/Models/LeaveRequest.php`
- Create: `app/Models/TahfidzRecord.php`
- Create: `app/Models/AcademicGrade.php`
- Create: `app/Models/TuitionBill.php`
- Create: `app/Models/TuitionPayment.php`
- Create: `app/Models/Article.php`
- Create: `app/Models/SchoolGallery.php`
- Create: `app/Models/Announcement.php`
- Create Enums in `app/Enums/`: `UserRole.php`, `PpdbStatus.php`, `AttendanceStatus.php`, `LeaveStatus.php`, `TahfidzFluency.php`, `TahfidzTajweed.php`, `TuitionStatus.php`, `PaymentMethod.php`.
- Test: `tests/Unit/Models/ModelRelationshipsTest.php`

**Steps:**
- [ ] Create failing unit test `tests/Unit/Models/ModelRelationshipsTest.php` asserting model associations (User -> Students, Classroom -> Students, etc.).
- [ ] Run test to verify failure: `php artisan test --filter=ModelRelationshipsTest`
- [ ] Create enums with label helpers for Filament forms and Blade views.
- [ ] Implement all Eloquent models with typed properties, fillable attributes, casts, and relationship methods.
- [ ] Run test to verify pass: `php artisan test --filter=ModelRelationshipsTest`
- [ ] Commit changes: `git commit -m "feat(models): implement eloquent models, relationships, and typed enums"`

---

### Task 4: Filament v3 Multi-Panel Scaffolding & RBAC Authorization

**Files:**
- Create: `app/Providers/Filament/AdminPanelProvider.php`
- Create: `app/Providers/Filament/TeacherPanelProvider.php`
- Create: `app/Providers/Filament/ParentPanelProvider.php`
- Modify: `app/Models/User.php` (implement `FilamentUser` interface with `canAccessPanel`)
- Modify: `bootstrap/providers.php`
- Test: `tests/Feature/FilamentMultiPanelAccessTest.php`

**Steps:**
- [ ] Create failing test `tests/Feature/FilamentMultiPanelAccessTest.php` verifying:
  - Admin user can access `/admin`, denied from `/guru` and `/wali`.
  - Teacher user can access `/guru`, denied from `/admin`.
  - Parent user can access `/wali`, denied from `/admin` and `/guru`.
  - Unauthenticated requests are redirected to panel login.
- [ ] Run test to verify failure: `php artisan test --filter=FilamentMultiPanelAccessTest`
- [ ] Generate and configure `AdminPanelProvider` (`/admin`), `TeacherPanelProvider` (`/guru`), and `ParentPanelProvider` (`/wali`).
- [ ] Implement `canAccessPanel(Panel $panel): bool` on `User` model using `UserRole`.
- [ ] Customize login forms to support login via `phone_number` or `email`.
- [ ] Run test to verify pass: `php artisan test --filter=FilamentMultiPanelAccessTest`
- [ ] Commit changes: `git commit -m "feat(filament): configure multi-panel architecture with strict RBAC"`

---

### Task 5: Phase 1 — Public Landing Page & Blitar Prayer Times Widget

**Files:**
- Create: `app/Http/Controllers/PublicController.php`
- Create: `app/Services/PrayerTimeService.php`
- Create: `resources/views/layouts/public.blade.php`
- Create: `resources/views/public/home.blade.php`
- Create: `resources/views/components/prayer-times.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/PublicLandingPageTest.php`

**Steps:**
- [ ] Create failing test `tests/Feature/PublicLandingPageTest.php` testing `/` route status, madrasah profile content, and prayer times component.
- [ ] Run test to verify failure: `php artisan test --filter=PublicLandingPageTest`
- [ ] Build `PrayerTimeService` calculating / caching Islamic prayer times for Blitar coordinates (Latitude: -8.0983, Longitude: 112.1681).
- [ ] Create responsive Blade template with modern Islamic styling (green/emerald palette, elegant typography, responsive navbar, hero section, facilities, activity gallery, and CTA button to PPDB).
- [ ] Run test to verify pass: `php artisan test --filter=PublicLandingPageTest`
- [ ] Commit changes: `git commit -m "feat(frontend): create public landing page and Blitar prayer times widget"`

---

### Task 6: Phase 1 — PPDB Online Registration Flow & Secure Document Uploads

**Files:**
- Create: `app/Http/Controllers/PpdbController.php`
- Create: `app/Http/Requests/StorePpdbRegistrationRequest.php`
- Create: `app/Services/PpdbService.php`
- Create: `resources/views/public/ppdb/register.blade.php`
- Create: `resources/views/public/ppdb/success.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/PpdbRegistrationTest.php`

**Steps:**
- [ ] Create failing test `tests/Feature/PpdbRegistrationTest.php` testing validation rules, registration number generation, secure file storage, and redirection to success page.
- [ ] Run test to verify failure: `php artisan test --filter=PpdbRegistrationTest`
- [ ] Create `StorePpdbRegistrationRequest` with strict validation rules (NIK 16 digits, WhatsApp valid format, JPG/PNG/PDF uploads max 2MB).
- [ ] Implement `PpdbService` with atomic sequential number generation (`PPDB-YYYY-XXXX`).
- [ ] Implement multi-step styled registration form in Blade with Alpine.js step transitions.
- [ ] Store uploaded documents in dedicated storage disk (`storage/app/ppdb_documents`).
- [ ] Run test to verify pass: `php artisan test --filter=PpdbRegistrationTest`
- [ ] Commit changes: `git commit -m "feat(ppdb): implement online registration flow and secure document storage"`

---

### Task 7: Phase 1 — PPDB Registration Proof PDF Generator

**Files:**
- Create: `app/Services/PpdbPdfService.php`
- Create: `resources/views/pdf/ppdb-proof.blade.php`
- Modify: `app/Http/Controllers/PpdbController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/PpdbPdfDownloadTest.php`

**Steps:**
- [ ] Create failing test `tests/Feature/PpdbPdfDownloadTest.php` asserting `/ppdb/cetak/{registration_number}` returns PDF stream with appropriate headers.
- [ ] Run test to verify failure: `php artisan test --filter=PpdbPdfDownloadTest`
- [ ] Design formal registration proof layout in Blade (`resources/views/pdf/ppdb-proof.blade.php`):
  - Official madrasah letterhead (Kop Surat MI Plus Raudlatul Muta'allimin).
  - Registration number & date.
  - Complete santri & parent biodata.
  - Verification checklist for document submission.
  - Panitia signature box.
- [ ] Render PDF using `barryvdh/laravel-dompdf`.
- [ ] Run test to verify pass: `php artisan test --filter=PpdbPdfDownloadTest`
- [ ] Commit changes: `git commit -m "feat(ppdb): implement official PDF registration proof generator"`

---

### Task 8: Phase 1 — Self-Service PPDB Status Tracker

**Files:**
- Modify: `app/Http/Controllers/PpdbController.php`
- Create: `resources/views/public/ppdb/check-status.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/PpdbStatusCheckTest.php`

**Steps:**
- [ ] Create failing test `tests/Feature/PpdbStatusCheckTest.php` testing lookup by NIK and Registration Number, handling found and not-found states.
- [ ] Run test to verify failure: `php artisan test --filter=PpdbStatusCheckTest`
- [ ] Implement search view with rate-limiting to prevent brute force lookups.
- [ ] Display registration status badges (Pending: Kuning, Verified: Biru, Accepted: Hijau, Rejected: Merah) along with panitia notes and download proof button.
- [ ] Run test to verify pass: `php artisan test --filter=PpdbStatusCheckTest`
- [ ] Commit changes: `git commit -m "feat(ppdb): implement self-service status tracking with rate limiting"`

---

### Task 9: Phase 1 — Filament Admin PPDB Verification Resource & EMIS Export

**Files:**
- Create: `app/Filament/Admin/Resources/PpdbRegistrationResource.php`
- Create: `app/Filament/Admin/Resources/PpdbRegistrationResource/Pages/ListPpdbRegistrations.php`
- Create: `app/Filament/Admin/Resources/PpdbRegistrationResource/Pages/ViewPpdbRegistration.php`
- Create: `app/Exports/PpdbEmisExport.php`
- Test: `tests/Feature/AdminPpdbResourceTest.php`

**Steps:**
- [ ] Create failing test `tests/Feature/AdminPpdbResourceTest.php` asserting Admin can view list, inspect uploaded documents, update status, and export to Excel.
- [ ] Run test to verify failure: `php artisan test --filter=AdminPpdbResourceTest`
- [ ] Implement `PpdbRegistrationResource` with Filament table filters (status, school origin), document viewer action, and status change modal.
- [ ] Implement `PpdbEmisExport` using `maatwebsite/excel` formatting data according to EMIS Kemenag template.
- [ ] Run test to verify pass: `php artisan test --filter=AdminPpdbResourceTest`
- [ ] Commit changes: `git commit -m "feat(admin): implement Filament PPDB verification resource and EMIS exporter"`

---

### Task 10: Shared Hosting Deployment Preparation & Verification Gate

**Files:**
- Create: `bin/build-for-production.ps1`
- Create: `public/.htaccess`
- Create: `docs/deployment/shared-hosting-guide.md`
- Test: `tests/Feature/ProductionReadinessTest.php`

**Steps:**
- [ ] Compile static assets locally: `npm run build`
- [ ] Configure `public/.htaccess` with security headers, Gzip compression, and SPA/Laravel routing.
- [ ] Create PowerShell build script `bin/build-for-production.ps1` that runs tests, builds assets, clears and caches config/routes/views, and packages clean zip for cPanel upload.
- [ ] Run full test suite: `php artisan test`
- [ ] Verify test results pass 100%.
- [ ] Commit changes: `git commit -m "chore(deployment): prepare shared hosting build automation and deployment documentation"`
