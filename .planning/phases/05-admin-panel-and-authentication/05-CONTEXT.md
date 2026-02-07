# Phase 5: Admin Panel & Authentication - Context

**Gathered:** 2026-02-07
**Status:** Ready for planning

<domain>
## Phase Boundary

Build an admin panel with passwordless authentication (magic links), dashboard with stats and activity, and full CRUD content management for posts, projects, categories, tags, and contact submissions. Provides secure backend for managing the site. Features like public user accounts, team collaboration, or advanced analytics belong in other phases.

</domain>

<decisions>
## Implementation Decisions

### Authentication approach
- **Magic link (passwordless)**: Email a login link that expires, no password to remember
- **Remember me option**: Default short session (24 hours), or 30-day session with "remember me" checkbox
- **Logout behavior**: Redirect to login page after logout
- **Rate limiting**: Moderate protection - lock out after 5 failed login attempts in 15 minutes

### Dashboard design
- **Key metrics displayed**: Content counts, Recent activity, Contact submissions, Publishing stats
- **Layout style**: Card grid (cards in responsive grid, 2-3 columns)
- **Time-based analytics**: Yes, with period selector (7 days, 30 days, 90 days, all time)
- **Recent Activity display**: Separate sections per type - Recent Posts (5), Recent Contacts (5), Recent Projects (5)

### Content management UX
- **Listing view**: Card grid with preview/thumbnail (more visual, similar to frontend)
- **Edit approach**: Full edit page (navigate away to dedicated edit page with full form)
- **Bulk actions**: Yes, full suite - checkboxes to select multiple items, bulk delete/publish/categorize
- **Filtering/search**: Text search, date range filters, category/tag filters (no status filters)

### Access control model
- **Role system**: Single admin role (all access) - simple, suitable for personal blog with trusted admins
- **Activity logging**: Yes, critical actions only - log delete, publish, login events with user and timestamp
- **Admin protection**: Dedicated /admin route prefix, IP whitelist option (config-based), HTTPS requirement in production
- **User management**: Yes, add/remove only - invite new admins by email and revoke access (no email editing)

### Claude's Discretion
- Magic link expiration time (suggest 15-30 minutes)
- Magic link email template design
- Session storage mechanism (database vs cache)
- Dashboard card colors and icons
- Publishing stats visualization (charts vs numbers)
- Card grid breakpoints and spacing
- Edit form field layout and validation messages
- Bulk action UI patterns (dropdown vs action bar)
- Text search implementation (full-text vs simple LIKE)
- Date range picker UI component
- Activity log table schema and retention
- IP whitelist configuration format
- Admin user invitation email template
- Error messages and success flash messages

</decisions>

<specifics>
## Specific Ideas

**Authentication:**
- Magic links are more secure and eliminate password reset flows
- Remember me balances convenience with security for personal use
- Rate limiting protects against brute force without being too strict

**Dashboard:**
- Card grid makes stats scannable at a glance
- Period selector helps track growth over time
- Separate activity sections organized by content type are clearer than mixed feed
- Contact submissions need visibility to respond quickly

**Content management:**
- Card grid view mirrors the public-facing UI for consistency
- Full edit pages provide space for complex forms (markdown editor, metadata)
- Bulk actions save time when managing multiple posts or projects
- Text search + date range + category filters cover main use cases without UI clutter

**Access control:**
- Single admin role keeps it simple for a personal blog
- Critical action logging provides accountability without excessive noise
- IP whitelist adds optional extra security layer for production
- /admin route prefix clearly separates admin area from public site
- HTTPS requirement protects credentials and session tokens

</specifics>

<deferred>
## Deferred Ideas

None — discussion stayed within phase scope

</deferred>

---

*Phase: 05-admin-panel-and-authentication*
*Context gathered: 2026-02-07*
