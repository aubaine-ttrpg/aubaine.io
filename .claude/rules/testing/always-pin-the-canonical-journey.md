---
name: always-pin-the-canonical-journey
description: Pin the desired journey, the ordered happy-path sequence from Example Mapping, as a Playwright e2e so it can never silently drift.
paths: ["catalyst/tests/**", "catalyst/src/**"]
severity: must
---
# Always pin the canonical journey

**Rule:** Turn the happy-path examples in order from Example Mapping into one end-to-end test (Playwright) that exercises the whole journey through catalyst. This is the canonical journey; pin it so a change anywhere in the flow that breaks it shows up as a red e2e.

**Why:** The ordered happy-path examples from Example Mapping (see [[always-example-map-before-coding]]) are the one path a user actually walks through the tool. Unit tests prove pieces in isolation and let the assembled flow drift unnoticed; a pinned e2e is the contract that the real path, open a book, edit a page, export the PDF, still works.

**Good / Bad:**
```php
// Bad: only unit tests, the assembled journey is never exercised.
public function testPageRendererFormatsOnePage(): void { /* ... */ }
```
```js
// Good: one e2e pins the ordered journey (Playwright).
test('an author exports a book end to end', async ({ page }) => {
  await openBook(page, 'Combat');
  await addPage(page, 'Combat basics');
  await exportToPdf(page);
  await expect(page.getByText('PDF ready')).toBeVisible();
});
```

**See also:** [[always-example-map-before-coding]], [[always-cover-the-five-categories]], [[always-test-critical-paths]].

**Enforced by:** test (pinned Playwright e2e) + review.
