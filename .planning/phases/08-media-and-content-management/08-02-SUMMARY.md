---
phase: "08"
plan: "02"
subsystem: "admin"
tags: ["admin", "certifications", "crud", "blade"]
dependency_graph:
  requires: ["08-01", "08-03"]
  provides: ["admin certification management"]
  affects: ["admin sidebar", "admin routes"]
tech_stack:
  added: []
  patterns: ["Admin CRUD controller", "resource routes with named routes", "admin-image-upload component", "ActivityLogger"]
key_files:
  created:
    - app/Http/Controllers/Admin/CertificationController.php
    - resources/views/admin/certifications/index.blade.php
    - resources/views/admin/certifications/create.blade.php
    - resources/views/admin/certifications/edit.blade.php
  modified:
    - routes/admin.php
    - resources/views/components/admin-sidebar.blade.php
decisions:
  - "Used Certification model directly for paginate() since CertificationRepositoryInterface has no paginate method (same pattern as ProjectController)"
  - "Update logic: if badge_image input is empty string (no new upload), keep existing DB value rather than overwriting with null"
metrics:
  duration: "~20 minutes"
  completed: "2026-02-14"
---

# Phase 08 Plan 02: Admin Certifications CRUD Summary

## One-liner

Admin backoffice CRUD for certifications: list/create/edit/delete with image upload and activity logging, following the Admin ProjectController pattern.

## What Was Built

A complete admin CRUD interface for managing certifications:

- **CertificationController** — 5 actions (index, create, store, edit, update, destroy) injecting `CertificationRepositoryInterface`, `ImageUploadService`, `ActivityLogger`
- **Admin routes** — 6 resource routes registered in `routes/admin.php` under the `admin` middleware group
- **3 Blade views** — index (table), create (form), edit (pre-filled form) following the admin projects pattern exactly
- **Sidebar link** — "Certifications" nav item with badge SVG icon and active state detection

## Implementation Notes

### Controller design
The controller validates all fields inline (not via FormRequest classes, matching the plan spec). The `store()` method accepts `badge_image` as a nullable string — the `x-admin-image-upload` component uploads the file first via AJAX and then submits the resulting storage path. The `update()` method preserves the existing badge_image when no new upload is made (empty input = keep existing).

### Pagination
`CertificationRepositoryInterface` does not expose a `paginate()` method. The controller calls `Certification::ordered()->paginate(15)` directly (same approach as `ProjectController` using `Project::query()`).

### Image handling on update
```php
if ($newBadgePath && $newBadgePath !== $certification->badge_image) {
    $this->imageService->delete($certification->badge_image); // delete old
    $data['badge_image'] = $newBadgePath;
} elseif (! $newBadgePath) {
    $data['badge_image'] = $certification->badge_image; // keep existing
}
```

## Deviations from Plan

### Auto-fixed Issues

None.

**Deviation 1: Certification model used directly for pagination**
- **Found during:** Task 1
- **Issue:** `CertificationRepositoryInterface::all()` returns a Collection (no pagination support). Plan says "paginate 15/page".
- **Fix:** Called `Certification::ordered()->paginate(15)` directly in the controller, matching how `ProjectController` uses `Project::query()->paginate()`. No interface change needed.
- **Files modified:** `CertificationController.php`

## Verification

```
php artisan route:list | grep certification
```
Output: 6 routes (index, create, store, edit, update, destroy).

## Self-Check: PASSED

Files verified present:
- app/Http/Controllers/Admin/CertificationController.php - FOUND
- resources/views/admin/certifications/index.blade.php - FOUND
- resources/views/admin/certifications/create.blade.php - FOUND
- resources/views/admin/certifications/edit.blade.php - FOUND

Commits verified:
- fbd5aa7 feat(08-02): add Admin CertificationController
- b87f45d feat(08-02): register admin certifications resource routes
- 7d21653 feat(08-02): add admin certification views (index, create, edit)
- 7f09425 feat(08-02): add Certifications link to admin sidebar
