---
status: testing
phase: 06-reader-engagement
source: [06-01-SUMMARY.md, 06-02-SUMMARY.md, 06-03-SUMMARY.md, 06-04-SUMMARY.md, 06-05-SUMMARY.md]
started: 2026-02-08T06:00:00Z
updated: 2026-02-08T07:00:00Z
---

## Current Test

number: 2
name: Submit New Comment
expected: |
  Fill out comment form with name, email (optional), and comment text. Submit creates new comment. If auto_approve disabled, shows "Awaiting moderation" message. If enabled, comment appears immediately.
awaiting: user response

## Tests

### 1. View Comments on Post
expected: Navigate to a blog post. Comments section should display below post content showing count and list of approved comments.
result: pass
notes: Modern compact layout, pagination (5 per load), newest first, vector reaction icons

### 2. Submit New Comment
expected: Fill out comment form with name, email (optional), and comment text. Submit creates new comment. If auto_approve disabled, shows "Awaiting moderation" message. If enabled, comment appears immediately.
result: issue
reported: "comment submission returns \"Undefined array key \"parent_id\"\". internal server error."
severity: blocker

### 3. Honeypot Spam Protection
expected: Bots filling hidden honeypot field should be blocked silently. Form should display generic error without revealing honeypot detection.
result: skipped
reason: submission blocked by server error (parent_id)

### 4. Comment Rate Limiting
expected: After submitting 5 comments within an hour, the 6th attempt should be blocked with a "Too many requests" error.
result: skipped
reason: submission blocked by server error (parent_id)

### 5. Reply to Comment
expected: Click Reply button on existing comment. Inline reply form should appear below parent comment. Submitting creates nested reply with visual indentation.
result: [pending]

### 6. Threaded Comment Display
expected: Multi-level comment threads (3+ levels) should display with visual hierarchy using left borders and indentation. Depth should be limited to configured max (default 5 levels).
result: [pending]

### 7. Post Reactions
expected: Click reaction emoji (👍 ❤️ 🎉 🚀) on a post. Reaction count increments and button shows active state with Rose Pine styling (bg-love/20 ring-love). Click again to remove reaction.
result: [pending]

### 8. Comment Reactions
expected: Click reaction emoji on a comment. Same toggle behavior as post reactions with count updates and visual feedback.
result: [pending]

### 9. Admin Comment Moderation Queue
expected: Login to admin panel at /admin. Navigate to Comments. Should see moderation queue with status filter tabs (Pending, Approved, Spam, Rejected) and comment count for each.
result: [pending]

### 10. Approve Comment
expected: In admin moderation queue, click Approve on pending comment. Comment status changes to approved, appears in Approved tab, and becomes visible on public blog post.
result: [pending]

### 11. Mark Comment as Spam
expected: Click Spam button on comment. Comment moves to Spam tab, no longer visible on public site.
result: [pending]

### 12. Delete Comment
expected: Click Delete button with confirmation. Comment is permanently removed from database.
result: [pending]

### 13. Admin Dashboard Comment Stats
expected: Admin dashboard should show pending comments stat card. When pending count > 0, card should be highlighted with ring styling. Recent comments section shows latest submissions with status badges.
result: [pending]

### 14. Sidebar Pending Badge
expected: Admin sidebar Comments link should show pending count badge only when count > 0 to reduce visual noise.
result: [pending]

### 15. Umami Analytics Setup
expected: Run `docker-compose -f docker-compose.umami.yml up -d`. Access http://localhost:3000 with admin/umami credentials. Create website and copy Website ID.
result: [pending]

### 16. Analytics Tracking Script
expected: Add UMAMI_WEBSITE_ID to .env. Visit blog in production mode. View page source should show Umami tracking script loaded from configured host.
result: [pending]

### 17. Analytics Admin Integration
expected: Admin dashboard should display analytics status card. If configured, shows link to Umami dashboard. If not configured, shows setup instructions.
result: [pending]

### 18. HTMX Real-time Comment Submission
expected: Submit comment form. Page should NOT reload. New comment appears in list immediately via HTMX DOM update. Loading spinner shows during submission.
result: [pending]

### 19. Comment Form Validation
expected: Submit comment form with empty required fields. Should show validation errors without page reload (HTMX). Honeypot fields remain hidden.
result: [pending]

### 20. Mobile Responsive Comments
expected: View comments section on mobile device. Reply forms, comment cards, and reaction buttons should be properly sized with adequate touch targets. Threading should remain readable on narrow screens.
result: [pending]

## Summary

total: 20
passed: 1
issues: 1
pending: 17
skipped: 2

## Gaps

- truth: "Fill out comment form with name, email (optional), and comment text. Submit creates new comment. If auto_approve disabled, shows \"Awaiting moderation\" message. If enabled, comment appears immediately."
  status: failed
  reason: "User reported: comment submission returns \"Undefined array key \"parent_id\"\". internal server error."
  severity: blocker
  test: 2
  root_cause: ""
  artifacts: []
  missing: []
  debug_session: ""
