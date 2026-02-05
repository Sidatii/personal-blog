# Personal Blog & Portfolio

## What This Is

A personal blog and portfolio site where I can write and share technical articles while showcasing my professional work. Built with Laravel and Blade, readers discover content through social media, search engines, and RSS feeds, and can engage through comments and reactions.

## Core Value

The ability to publish articles through a git-based workflow (write markdown files locally, push to publish automatically) while maintaining a comprehensive professional portfolio presence.

## Requirements

### Validated

(None yet — ship to validate)

### Active

- [ ] Can write articles in markdown files locally and publish via git push
- [ ] Blog homepage displays published articles in reverse chronological order
- [ ] Article pages render markdown with proper formatting and styling
- [ ] Portfolio section with About Me page (bio, photo, professional summary)
- [ ] Portfolio section with Projects showcase (descriptions, links, screenshots)
- [ ] Portfolio section with Resume/CV (work history, skills, education)
- [ ] Portfolio section with Contact information and contact form
- [ ] Readers can comment on articles
- [ ] Readers can react/like articles
- [ ] Analytics dashboard to track article views and engagement
- [ ] SEO optimization (meta tags, Open Graph, structured data)
- [ ] RSS feed for article subscriptions
- [ ] Social sharing buttons on articles

### Out of Scope

- Multi-author support — Single author blog, simpler architecture
- Content moderation dashboard — Not needed for single author with low initial traffic
- Email newsletter system — Can integrate later if audience grows
- Mobile native app — Web-first approach, responsive design sufficient
- Draft preview URLs — Can use local development for previews
- Content scheduling — Publish manually when ready, no automation needed initially

## Context

**Starting Point:**
- Greenfield project with no existing content to migrate
- User is starting fresh with writing publicly
- No legacy systems or integrations to consider

**User Workflow:**
- Developer-centric workflow preferred
- Write markdown files in local IDE/editor
- Git push triggers automatic deployment and publishing
- Manage portfolio content through the application

**Discovery Channels:**
- Social media sharing (Twitter, LinkedIn, etc.)
- Organic search traffic (Google, SEO)
- RSS feed subscribers
- Direct traffic from bookmarks and shared links

**Professional Goals:**
- Build audience through quality content
- Establish expertise and thought leadership
- Maintain professional online presence
- Showcase technical projects and experience

## Constraints

- **Tech Stack**: Laravel (PHP framework), Blade (templating), TailwindCSS (styling), PostgreSQL (database), Nginx (web server) — User's preferred stack
- **Hosting**: VPS/Cloud environment — User has existing infrastructure
- **Deployment**: Git-based automatic deployment — Push to main branch publishes immediately
- **Future Enhancement**: Cloudflare integration planned for CDN and caching
- **Content Format**: Markdown for articles — Preserves portability and developer workflow

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Laravel full-stack over static site generator | Comments, reactions, and analytics require backend; user has VPS available | — Pending |
| Git-based publishing workflow | Developer-friendly, version controlled, aligns with user's preferred workflow | — Pending |
| PostgreSQL over MySQL | More advanced features for potential future needs, user preference | — Pending |
| Markdown for article content | Developer-friendly, portable, easy to version control in git | — Pending |

---
*Last updated: 2026-02-05 after initialization*
