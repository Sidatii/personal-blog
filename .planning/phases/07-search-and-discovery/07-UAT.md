---
status: testing
phase: 07-search-and-discovery
source: 07-01-SUMMARY.md, 07-02-SUMMARY.md, 07-03-SUMMARY.md
started: 2026-02-08T22:45:00Z
updated: 2026-02-08T22:47:00Z
---

## Current Test

number: 8
name: TOC Visible in Blog Posts
expected: |
  Navigate to a blog post with headings (like "Getting Started with Laravel"). A table of contents should appear in the sidebar showing all headings from the article.
awaiting: user response

## Tests

### 1. Search Box Visible in Header
expected: Navigate to the blog. The search box appears in the header. On desktop, you should see the search input with autocomplete capability. On mobile, you should see a simplified search form.
result: pass

### 2. Autocomplete Shows Results
expected: Click the search box and start typing (e.g., "laravel"). Within 300ms, a dropdown should appear showing matching posts and projects. The dropdown should show up to 3 posts and 3 projects.
result: pass

### 3. Clicking Autocomplete Result Navigates
expected: From the autocomplete dropdown, click on a search result. The browser should navigate to that post or project's detail page using slugs (e.g., /posts/getting-started-laravel not /posts/1).
result: issue
reported: "the dropdown search result when clicked redirects to a url using id not slugs, so gives 404"
severity: major

### 4. View All Results Link Works
expected: In the autocomplete dropdown, click "View all results" or press Enter. The browser should navigate to /search?q=your-search-term showing the full search results page.
result: pass

### 5. Search Results Page Displays
expected: Visit /search?q=test (or any term). The page should display search results in a grid layout for posts and masonry for projects. Results should show title, excerpt/description, and be paginated.
result: pass

### 6. No Results State
expected: Search for a term that doesn't exist in any content. The results page should show a friendly "No results found" message.
result: pass

### 7. Published Posts Only in Search
expected: Search for a term. Only published posts should appear in results. Unpublished (draft) posts should not be searchable.
result: pass

### 8. TOC Visible in Blog Posts
expected: Navigate to a blog post with headings (like "Getting Started with Laravel"). A table of contents should appear in the sidebar showing all headings from the article.
result: [pending]

### 9. TOC Click Scrolls to Section
expected: Click on a TOC link. The page should smoothly scroll to the corresponding heading section. The URL should update to include the heading's ID (e.g., #installation).
result: [pending]

### 10. Newsletter Signup Form Visible
expected: Scroll to the footer. The newsletter signup form should be visible with an email input field and subscribe button.
result: [pending]

### 11. Newsletter Submit Shows Loading State
expected: Enter an email in the newsletter form and click subscribe. The button should show a loading state, then redirect to the "Check Your Email" confirmation page.
result: [pending]

### 12. Confirmation Email Arrives
expected: After subscribing, check your email inbox. You should receive a confirmation email from the blog with a link to activate your subscription.
result: [pending]

### 13. Confirmation Link Activates Subscription
expected: Click the confirmation link in the email. The browser should navigate to the subscription confirmed page showing "Subscription Confirmed!" message.
result: [pending]

### 14. Unsubscribe Link Works
expected: When you receive newsletters, the email should include an unsubscribe link. Clicking it should navigate to the unsubscribe confirmation page and opt you out of future emails.
result: [pending]

## Summary

total: 14
passed: 6
issues: 1
pending: 7
skipped: 0

## Gaps

- truth: "Clicking autocomplete result navigates to correct post/project detail page using slugs"
  status: failed
  reason: "User reported: the dropdown search result when clicked redirects to a url using id not slugs, so gives 404"
  severity: major
  test: 3
  artifacts: []
  missing: []

