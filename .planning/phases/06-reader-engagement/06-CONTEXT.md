# Phase 6: Reader Engagement - Context

**Gathered:** 2026-02-08
**Status:** Ready for planning

<domain>
## Phase Boundary

Add interactive engagement features to existing blog posts: comments system, post reactions, and privacy-first analytics. Enhances reader interaction without changing post content or structure.

</domain>

<decisions>
## Implementation Decisions

### Comments System
- Self-hosted comments (not Giscus) — full control over data and moderation
- Comments appear below post content — standard placement after reading
- Comments support reactions: 👍 👎 😄 ❤️ 🎉 (GitHub-style emoji reactions on individual comments)
- Database storage for comments with moderation capabilities

### Analytics Approach
- Umami Analytics — open-source, modern UI, self-hosted
- Self-hosted deployment — free, full data ownership, requires maintenance
- Track all available metrics — page views, unique visitors, top posts, referrers, scroll depth, time on page, outbound clicks, device breakdown, real-time data, geographic data, entry/exit pages
- Analytics dashboard is private/admin-only — not visible to readers

### Reactions/Likes
- Multiple reaction types: 👍 ❤️ 🎉 🚀 (LinkedIn-style reaction picker)
- Anonymous visitors allowed — reactions tracked by IP/cookie to prevent duplicates
- Reactions appear at bottom of post — after reader finishes content
- Reaction counts are public — display how many people reacted with each emoji

### Claude's Discretion
- Exact database schema for comments (parent_id for threading, approval status, etc.)
- Spam filtering approach
- Real-time updates vs page reload for new comments
- Reaction button styling and animation
- Admin moderation interface design

</decisions>

<specifics>
## Specific Ideas

- Self-hosted approach chosen for data ownership over convenience of Giscus
- Umami selected for modern UI and comprehensive metrics over lighter alternatives
- Multiple reactions provide more expressive engagement than single like button

</specifics>

<deferred>
## Deferred Ideas

- Reading time enhancements — already calculated in Phase 3, no changes needed for this phase

</deferred>

---

*Phase: 06-reader-engagement*
*Context gathered: 2026-02-08*
