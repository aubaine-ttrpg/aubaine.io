---
name: test-all-state-transitions
description: For stateful things, test that every legal transition works and every illegal one is rejected.
severity: should
---
# Test all state transitions

**Rule:** For anything stateful (a book draft -> published -> archived; a cached PDF fresh -> stale after an edit) test that every legal transition works and every illegal one is rejected, for example a published book cannot silently revert to draft without an explicit unpublish. Model the states as enums, per php/prefer-enums-over-constants.

**Why:** A status field is only safe if illegal moves are actually refused, not merely hidden in the UI. State-transition testing proves both directions: the legal path and the rejection. The PDF cache follows the version stamp described in catalyst/docs/adr/0001-book-version-stamp-and-pdf-cache, so an edit must make the cache stale rather than serve an old render as current.

**Good / Bad:**
```php
// Bad: only the happy transition is checked.
public function testBookCanBePublished(): void { /* draft -> published */ }

// Good: also prove the invalidation transition happens.
public function testEditingABookMarksItsCachedPdfStale(): void
{
    $book = $this->freshlyExportedBook();           // cache is fresh
    $book->renamePage(0, 'Combat basics');          // an edit bumps the version stamp
    self::assertTrue($book->pdfCache()->isStale());  // stale, never served as current
}
```

**See also:** php/prefer-enums-over-constants, [[always-cover-the-five-categories]].

**Enforced by:** PHPUnit (a case per legal transition and per blocked transition) + review.
