---
phase: 07-search-and-discovery
plan: "02"
subsystem: content
tags: [markdown, toc, navigation, php]

# Dependency graph
requires:
  - phase: 03-blog-features-seo
    provides: TOC component and MarkdownParser service
provides:
  - Functional TOC navigation with heading ID anchors
  - Click-to-scroll behavior for all blog post headings
  - URL hash synchronization with section navigation
affects: [blog, reader-experience]

# Tech tracking
tech-stack:
  added: []
  patterns: [DOMDocument HTML post-processing, duplicate ID handling with counters]

key-files:
  created: []
  modified: [app/Services/Content/MarkdownParser.php]

key-decisions:
  - "Use DOMDocument for safe HTML manipulation instead of regex"
  - "Handle duplicate headings with counter suffix (-1, -2, etc.)"
  - "Process heading IDs after code highlighting to ensure correct order"
  - "Use str_replace for XML declaration removal (more reliable than regex)"

patterns-established:
  - "Post-processing pipeline in MarkdownParser: convert → highlight → add IDs"
  - "Duplicate ID prevention using tracking array"

# Metrics
duration: 5min
completed: 2026-02-08
---

# Phase 7 Plan 2: TOC Anchor Links Summary

**HTML heading ID injection using DOMDocument for functional table of contents navigation**

## Performance

- **Duration:** 5 min
- **Started:** 2026-02-08T21:30:31Z
- **Completed:** 2026-02-08T21:35:37Z
- **Tasks:** 1
- **Files modified:** 1

## Accomplishments
- Added `addHeadingIds()` method to MarkdownParser for automatic ID attribute injection
- All HTML headings now have unique, URL-safe ID attributes matching TOC link format
- TOC navigation fully functional - clicking links scrolls to corresponding sections
- URL hash updates when navigating via TOC links
- Edge cases handled: duplicate headings, special characters, UTF-8 content

## Task Commits

**Note:** Implementation was already completed in commit `c1a9c67` (feat(07-01): create SearchController and results view) along with other Phase 7 Plan 1 work. The MarkdownParser enhancement was bundled with the search feature implementation.

**Existing commit:** `c1a9c67` - Added addHeadingIds() method and integrated into parse() pipeline

## Files Created/Modified
- `app/Services/Content/MarkdownParser.php` - Added addHeadingIds() method (58 lines), called in parse() method

## Decisions Made

**Use DOMDocument instead of regex for HTML manipulation**
- Rationale: Safer and more reliable for parsing/modifying HTML structure than regex patterns
- DOMDocument handles edge cases like nested elements and attributes correctly

**Handle duplicate headings with counter suffix**
- Rationale: Prevents ID collisions when multiple headings have same text (e.g., "Introduction" → "introduction", "introduction-1")
- Tracks used IDs in array to detect duplicates

**Process heading IDs after code highlighting**
- Rationale: Ensures final HTML structure is complete before adding IDs
- Order: markdown → HTML → highlight code → add heading IDs

**Use str_replace for XML declaration removal**
- Rationale: DOMDocument adds XML comment wrapper that's predictable, str_replace more reliable than regex

## Deviations from Plan

None - implementation matches plan specification exactly. However, work was completed earlier than scheduled (bundled with Plan 07-01 instead of executed as separate plan).

## Issues Encountered

**Pre-existing implementation**
- Issue: Plan 07-02 work was already completed in commit c1a9c67 as part of Plan 07-01
- Resolution: Verified implementation meets all plan requirements and success criteria
- Impact: No additional work needed, plan objectives already achieved

**Cache invalidation during verification**
- Issue: BlogController caches parsed content for 30 days, initial verification showed missing IDs
- Resolution: Ran `php artisan cache:clear` to invalidate cached content
- Impact: Minor verification delay, no code changes needed

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness

TOC navigation complete and functional. Ready for:
- Plan 07-03: Newsletter subscription system
- Plan 07-04: Advanced search features
- All blog posts automatically benefit from TOC navigation (no migration needed)

## Self-Check: PASSED

All claims verified:
- Modified file exists: app/Services/Content/MarkdownParser.php ✓
- Commit exists: c1a9c67 ✓
- addHeadingIds method present and functional ✓
- TOC navigation verified working in browser ✓

---
*Phase: 07-search-and-discovery*
*Completed: 2026-02-08*
