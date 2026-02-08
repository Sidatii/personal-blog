# Phase 5: Admin Panel & Authentication - Research

## Research Date: 2026-02-08
## Phase: 05-admin-panel-and-auth

---

## Authentication Options Analysis

### 1. Laravel Breeze ✓ RECOMMENDED
**Why:** Simple, lightweight, perfect for single-admin personal blog

**Pros:**
- Minimal boilerplate, easy to customize
- Built on Blade + TailwindCSS (matches existing stack)
- Full control over UI/UX
- No Livewire/Inertia dependency
- Easy to integrate with existing Rose Pine theme

**Cons:**
- No built-in 2FA (not needed for personal blog)
- No team management (not needed)

**Use case:** Personal blog with single admin user

### 2. Laravel Jetstream
**Why NOT:** Overkill for personal blog

**Pros:**
- 2FA, teams, API tokens, profile management

**Cons:**
- Heavy dependency (Livewire or Inertia)
- Too many features we don't need
- Harder to customize for simple use case

### 3. Laravel Fortify
**Why NOT:** Headless, requires building all UI

**Use case:** API-first apps, SPAs

---

## Admin Panel Options Analysis

### 1. Custom TailwindCSS Admin ✓ RECOMMENDED
**Why:** Consistent with existing design, full control

**Pros:**
- Matches existing Rose Pine theme perfectly
- No additional dependencies
- Full control over layout and features
- Can reuse existing components (header, footer patterns)
- Lightweight

**Cons:**
- Need to build CRUD manually (but it's straightforward)

### 2. Filament
**Why NOT:** Overkill for simple admin needs

**Pros:**
- Rapid CRUD generation
- Beautiful UI out of the box
- Built on TALL stack

**Cons:**
- Additional large dependency
- May conflict with custom styling
- Learning curve for customization
- Overkill for basic CRUD operations

### 3. Laravel Backpack
**Why NOT:** Bootstrap-based, doesn't match stack

---

## Architecture Decisions

### Authentication Flow
```
1. Single admin user (created via seeder)
2. Session-based authentication (not API tokens)
3. Login form at /admin/login
4. Dashboard at /admin
5. Logout via POST to /admin/logout
```

### Admin Panel Structure
```
/admin                 → Dashboard (stats, recent activity)
/admin/posts          → Posts list with CRUD
/admin/categories     → Categories list with CRUD
/admin/tags          → Tags list with CRUD
/admin/projects      → Projects list with CRUD
/admin/contacts      → Contact submissions (view, mark read, delete)
/admin/settings      → Basic settings (optional)
```

### Security Considerations
- CSRF protection on all forms
- Rate limiting on login (5 attempts per minute)
- Session timeout (configurable)
- Secure password hashing (bcrypt)
- HTTPS enforcement in production

### Database Schema Additions
```sql
-- users table already exists from Laravel
-- Add role column (admin vs viewer)
-- Add activity log table for audit trail
```

---

## Implementation Approach

### Wave 1: Authentication Foundation
1. Install Laravel Breeze (minimal)
2. Create AdminUser model/seeder
3. Login/logout functionality
4. Auth middleware for admin routes

### Wave 2: Dashboard
1. Admin layout with sidebar navigation
2. Dashboard controller with stats
3. Recent activity widget

### Wave 3: Content Management
1. Post CRUD (list, create, edit, delete)
2. Category CRUD
3. Tag CRUD

### Wave 4: Portfolio Management
1. Project CRUD
2. Contact submissions viewer

### Wave 5: Polish
1. Activity logging
2. Settings management
3. Visual consistency check

---

## Tech Stack Additions

**No new major dependencies required:**
- Uses existing Laravel Auth
- Uses existing TailwindCSS
- Uses existing Alpine.js
- Uses existing PostgreSQL

**Optional additions:**
- `laravel/fortify` only if API tokens needed later
- Activity log package if built-in not sufficient

---

## Risk Assessment

| Risk | Likelihood | Mitigation |
|------|-----------|------------|
| Breeze overrides existing styles | Medium | Install carefully, customize views |
| Session conflicts with existing auth | Low | Use separate guard for admin |
| Route conflicts | Low | Prefix all admin routes with /admin |

---

## Recommendation Summary

**Use Laravel Breeze for authentication** + **Custom TailwindCSS admin panel**

This approach:
- Maintains design consistency (Rose Pine theme)
- Adds minimal dependencies
- Provides full control over admin features
- Matches the "personal blog" simplicity philosophy
- Can be extended later if needed

**Alternative:** If rapid CRUD is preferred over custom design, Filament v4 would be the next best choice.
