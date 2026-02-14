---
phase: "08"
plan: "08"
subsystem: "public-certifications-page"
tags: ["certifications", "public-page", "blade-component", "navigation"]
dependency_graph:
  requires: ["08-01", "08-02", "08-03"]
  provides: ["public certifications page", "certification-card component"]
  affects: ["header navigation", "admin certification forms"]
tech_stack:
  added: []
  patterns: ["Repository pattern via CertificationRepositoryInterface", "Blade component (certification-card)", "Active nav state via routeIs()"]
key_files:
  created:
    - app/Http/Controllers/CertificationController.php
    - resources/views/components/certification-card.blade.php
    - resources/views/certifications/index.blade.php
  modified:
    - routes/web.php
    - resources/views/components/header.blade.php
decisions:
  - "Task 4 (badge image wiring) was already fully complete from 08-02/08-03: both admin forms had x-admin-image-upload, and the admin controller handled store/update/destroy badge paths with ImageUploadService"
metrics:
  duration: "~2 minutes"
  completed: "2026-02-14"
---

# Phase 08 Plan 08: Public Certifications Page Summary

**One-liner:** Public certifications page with featured/standard split, certification-card Blade component, and header nav link — all wired to the existing repository and admin CRUD.

## What Was Built

Public-facing `/certifications` page that reads from the `CertificationRepositoryInterface`, splits entries into featured (highlighted section with gold styling) and standard, and renders each using the new `certification-card` Blade component. A Certifications link was added to both desktop and mobile navigation with proper active state detection.

## Tasks Completed

| # | Task | Status | Commit |
|---|------|--------|--------|
| 1 | Public CertificationController and route | Done | d0b8631 |
| 2 | certification-card Blade component | Done | 0eb4d31 |
| 3 | certifications/index.blade.php view | Done | a4efb99 |
| 4 | Badge image upload wiring in admin forms | Already complete | — |
| 5 | Certifications nav link in header | Done | 6fa29b6 |

## Key Implementation Details

### CertificationController (public)
- Injects `CertificationRepositoryInterface` via constructor
- Calls `->all()` once, then filters into `$featured` / `$standard` collections in PHP
- Sets SEO title/description via `seo()` helper

### certification-card Component
- Conditionally renders badge image or fallback graduation cap emoji
- Featured cards: gold border, overlay background, shadow, "Featured" pill badge
- Expired cards: `opacity-60` applied; expired date shown in red
- Credential URL renders as "Verify credential →" external link

### Navigation
- Desktop nav: Certifications between Projects and About
- Mobile menu: same position
- Active state: `request()->routeIs('certifications.*')` brightens link text

## Deviations from Plan

### Pre-completed Work

**Task 4 already done from 08-02/08-03:** When reviewing the admin forms and controller, badge image upload was fully wired:
- `create.blade.php` already had `<x-admin-image-upload name="badge_image" directory="uploads/certifications" label="Badge Image" />`
- `edit.blade.php` already had the component with `:current="$certification->badge_url"`
- Admin `CertificationController` already had `ImageUploadService` injected and handled store/update/destroy correctly (preserving badge on empty submission, deleting old on replacement, deleting on destroy)

No action required for Task 4.

## Verification

```bash
php artisan route:list | grep certifications
# GET|HEAD certifications  certifications.index › CertificationController@index
```

- `/certifications` renders empty state when no certifications exist
- Featured certifications appear in "Highlighted" section with gold styling
- Standard certifications appear below in "All Certifications" section
- Header shows "Certifications" nav link between Projects and About on desktop and mobile

## Self-Check: PASSED

Files verified:
- `app/Http/Controllers/CertificationController.php` — exists
- `resources/views/components/certification-card.blade.php` — exists
- `resources/views/certifications/index.blade.php` — exists
- Commits d0b8631, 0eb4d31, a4efb99, 6fa29b6 — all present in git log
