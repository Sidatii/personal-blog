---
phase: 08-media-and-content-management
plan: "04"
subsystem: ui
tags: [image-upload, alpine-js, blade, storage, project-portfolio]

# Dependency graph
requires:
  - phase: 08-03
    provides: ImageUploadService, admin-image-upload Blade component, admin.images.store route
  - phase: 04-portfolio-features
    provides: Project model with thumbnail/screenshots columns, project admin CRUD
provides:
  - Project thumbnail upload via admin-image-upload component in create/edit forms
  - Multi-screenshot upload with Alpine.js-managed live preview in admin forms
  - getThumbnailUrlAttribute() accessor on Project model resolving storage disk URLs
  - Old image cleanup on thumbnail replace or project delete via ImageUploadService
  - Public project card placeholder when no thumbnail uploaded
  - Screenshots gallery on project detail page with clickable full-size links
affects:
  - 08-05
  - any future phase touching Project admin CRUD or public display

# Tech tracking
tech-stack:
  added: []
  patterns:
    - "Use getThumbnailUrlAttribute() accessor pattern for Storage-backed image fields"
    - "Alpine.js multi-upload: loop files, POST each to admin.images.store, build path+url array, hidden inputs per item"
    - "Controller diff pattern for screenshot deletion: array_diff(old, new) → imageService->delete each"
    - "Destroy cleanup: delete all associated images before record deletion"

key-files:
  created: []
  modified:
    - app/Http/Controllers/Admin/ProjectController.php
    - app/Models/Project.php
    - resources/views/admin/projects/create.blade.php
    - resources/views/admin/projects/edit.blade.php
    - resources/views/components/project-card.blade.php
    - resources/views/projects/show.blade.php

key-decisions:
  - "Use thumbnail_url accessor rather than raw thumbnail path in views — decouples DB storage path from public URL"
  - "Screenshots submitted as screenshots[] hidden inputs managed by Alpine array — no server-side session state"
  - "Edit form: empty thumbnail submission preserves existing path; non-empty different path triggers old file deletion"
  - "project-card placeholder uses HTML entity for monitor emoji to avoid encoding issues in Blade"

patterns-established:
  - "Image accessor: getThumbnailUrlAttribute() → Storage::disk('public')->url($this->thumbnail)"
  - "Multi-upload Alpine component: upload(event) loops files, pushes {path, url} to array, resets input value"
  - "Screenshot diff deletion: array_diff($existing, $new) → foreach imageService->delete()"

# Metrics
duration: 20min
completed: 2026-02-14
---

# Phase 8 Plan 4: Project Image Uploads Summary

**ImageUploadService wired into project admin CRUD with thumbnail accessor, Alpine.js multi-screenshot upload, and storage-URL rendering on all public project views**

## Performance

- **Duration:** ~20 min
- **Started:** 2026-02-14T19:36:18Z
- **Completed:** 2026-02-14T19:56:00Z
- **Tasks:** 3
- **Files modified:** 6

## Accomplishments
- ProjectController now injects ImageUploadService and handles image lifecycle: new uploads stored, replaced thumbnails cleaned up, removed screenshots deleted on update, all images deleted on project destroy
- Project model has `getThumbnailUrlAttribute()` accessor converting storage-relative paths to full public URLs
- Admin create and edit forms have working upload UI: x-admin-image-upload for thumbnail, Alpine.js multi-file upload with live preview grid for screenshots
- Edit form pre-populates existing screenshots from DB for visual confirmation before saving
- Public project cards show thumbnail or a styled placeholder; show page renders screenshots as clickable links to full-size images

## Task Commits

Each task was committed atomically:

1. **Task 1: ProjectController + Project model** - `76d8e8b` (feat)
2. **Task 2: Admin create/edit forms** - `dfa78de` (feat)
3. **Task 3: Public project views** - `9cd808d` (feat)

## Files Created/Modified
- `app/Http/Controllers/Admin/ProjectController.php` - Injected ImageUploadService; store/update/destroy image lifecycle
- `app/Models/Project.php` - Added Storage import and getThumbnailUrlAttribute() accessor
- `resources/views/admin/projects/create.blade.php` - Replaced text thumbnail input with x-admin-image-upload; added Alpine.js multi-screenshot uploader
- `resources/views/admin/projects/edit.blade.php` - Same as create + pre-populates existing screenshots from project model
- `resources/views/components/project-card.blade.php` - Switched to thumbnail_url accessor; added placeholder div when no thumbnail
- `resources/views/projects/show.blade.php` - Switched thumbnail to thumbnail_url; screenshots rendered as asset('storage/...') links

## Decisions Made
- Used `thumbnail_url` accessor throughout views rather than constructing `asset('storage/'.$path)` inline — keeps URL generation in one place and makes future disk changes transparent to views
- Edit controller: empty thumbnail submission (no new upload) preserves existing DB value via else-branch, avoiding unintended thumbnail removal
- Screenshot deletion uses `array_diff` on the old vs new path arrays — only paths removed from the UI get deleted from disk

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 1 - Bug] Updated project-card and show.blade.php to use thumbnail_url accessor**
- **Found during:** Task 3 (public project views)
- **Issue:** Both existing views used `$project->thumbnail` (raw DB path) directly as `src`. After adding the accessor and switching to storage-relative paths from the upload component, raw paths would break image rendering.
- **Fix:** Updated project-card to use `thumbnail_url`, updated show.blade.php thumbnail and screenshots to use `thumbnail_url` / `asset('storage/...')` consistently
- **Files modified:** resources/views/components/project-card.blade.php, resources/views/projects/show.blade.php
- **Verification:** `php artisan view:cache` compiled all views without errors
- **Committed in:** 9cd808d (Task 3 commit)

---

**Total deviations:** 1 auto-fixed (Rule 1 - bug fix)
**Impact on plan:** Required for correctness — storage paths would have rendered as broken image URLs without this fix. No scope creep.

## Issues Encountered
None - views compiled cleanly on first attempt after updates.

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- Project image uploads fully functional end-to-end
- Pattern established for other admin entities needing image support (certifications, blog posts)
- No blockers for remaining phase 08 plans

## Self-Check: PASSED

- FOUND: app/Http/Controllers/Admin/ProjectController.php
- FOUND: app/Models/Project.php
- FOUND: resources/views/admin/projects/create.blade.php
- FOUND: resources/views/admin/projects/edit.blade.php
- FOUND: resources/views/components/project-card.blade.php
- FOUND: resources/views/projects/show.blade.php
- FOUND: .planning/phases/08-media-and-content-management/08-04-SUMMARY.md
- FOUND: commit 76d8e8b (Task 1)
- FOUND: commit dfa78de (Task 2)
- FOUND: commit 9cd808d (Task 3)

---
*Phase: 08-media-and-content-management*
*Completed: 2026-02-14*
