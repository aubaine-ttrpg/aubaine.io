---
name: always-pin-the-canonical-journey
description: Pin the parcours souhaité, the ordered happy-path sequence from Example Mapping, as an e2e so it can never silently drift.
paths: ["tests/**", "src/**"]
severity: must
---
# Always pin the canonical journey

**Rule:** Turn the parcours souhaité, the happy-path examples in order from Example Mapping, into one end-to-end test (Playwright) that exercises the whole journey. This is the canonical journey; pin it so a change anywhere in the flow that breaks it shows up as a red e2e.

**Why:** The ordered happy-path examples are the canonical journey defined in `tests/TEST_DESIGN.md`. Unit tests prove pieces in isolation and let the assembled flow drift unnoticed; a pinned e2e is the contract that the user's real path still works.

**Good / Bad:**
```php
// Bad: only unit tests, the assembled journey is never exercised.
public function testClientFactoryBuildsClient(): void { /* ... */ }

// Good: one e2e pins the ordered journey (Playwright).
test('coach onboards a client end to end', async ({ page }) => {
  await signInAsCoach(page);
  await createClient(page, 'Sophie');
  await issueFirstInvoice(page);
  await expect(page.getByText('Facture émise')).toBeVisible();
});
```

**Enforced by:** test (pinned e2e) + CI.
