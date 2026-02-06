# Phase 3: Blog Features & SEO - Context

**Gathered:** 2026-02-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Reader-facing blog with dark mode, syntax highlighting, SEO meta tags, RSS feed, and sitemap. Controllers, routes, Blade views with TailwindCSS. Dark mode toggle for site-wide theme switching.

</domain>

<decisions>
## Implementation Decisions

### Layout structure
- Single-column layout, minimalist but rich experience
- Sticky table of contents on left (post pages)
- Reading progress bar at top
- Standard header: logo left, links right, hamburger on mobile
- Footer with links, copyright, social icons

### Site theme
- Rose Pine theme (rose-pine-main color palette)
- Warm, dark-leaning aesthetic with earthy rose tones

### Index design
- Grid of cards for post listing
- Minimal cards: title, date, tags only (no featured images on index)
- Infinite scroll for loading more posts
- No "Load More" button - auto-load on scroll

### Typography & code
- Body text: Inter font
- Headings: Claude's discretion (match Rose Pine theme)
- Code blocks: Rose Pine syntax highlighting theme
- Line numbers in left margin
- Copy-to-clipboard button on code blocks

### SEO & meta
- Full SEO implementation: basic meta + Open Graph + Twitter Cards + JSON-LD structured data
- Open Graph images: Hybrid approach - use featured image if available, auto-generate from title/branding as fallback
- Article structured data for Google rich snippets

### Dark mode
- Default: Dark (Rose Pine Moon)
- Toggle: Sun/moon icon in header
- Persistence: Cookie (works across devices)
- Light mode: Rose Pine Dawn

### Claude's Discretion
- Header font choice
- Exact spacing and typography scale
- TOC animation and behavior
- Progress bar styling
- Card hover states and transitions
- Mobile menu implementation
- Auto-generated OG image design
- JSON-LD schema details

</decisions>

<specifics>
## Specific Ideas

- Rose Pine theme for consistent dark-mode aesthetic
- Minimal cards on index - clean, not cluttered
- Infinite scroll for continuous reading experience
- Cookie persistence for theme preference (cross-device)

</specifics>

<deferred>
## Deferred Ideas

- RSS feed — not discussed, assumed standard implementation
- Sitemap — not discussed, assumed standard implementation
- Comments system — Phase 5
- Search functionality — Phase 6

</deferred>

---

*Phase: 03-blog-features-and-seo*
*Context gathered: 2026-02-06*
