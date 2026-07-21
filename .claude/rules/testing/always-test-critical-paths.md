---
name: always-test-critical-paths
description: The critical paths, book and PDF export, JSON to SQLite round-trip, content export determinism, and the sigil token dump, always ship with automated tests.
severity: must
---
# Always test critical paths

**Rule:** These critical paths never ship without automated tests: book and PDF export, the JSON to SQLite migration round-trip, the `content/` JSON export determinism, and the sigil design-token dump. No feature or fix is done until its tests are green, and a bug fix starts with a failing regression test, per [[never-weaken-a-failing-test]].

**Why:** These paths carry the data of record and the published output, so a silent regression corrupts a book, loses data across the migration, or ships a wrong token set, and each is expensive to unwind. Automated tests make the behaviour verified instead of hoped-for, and the book and PDF path in particular relies on the version stamp in catalyst/docs/adr/0001-book-version-stamp-and-pdf-cache.

**Good / Bad:**
```php
// Bad: the JSON to SQLite migration ships with no test, the round-trip unverified.
public function migrate(Book $book): void { /* ... */ }

// Good: a test pins that the round-trip preserves the book exactly.
public function testJsonToSqliteRoundTripPreservesTheBook(): void
{
    $before = $this->loadBookFromJson('combat.json');
    $this->migrateToSqlite($before);
    self::assertEquals($before, $this->loadBookFromSqlite($before->id()));
}
```
```python
# Good: regenerating committed content reproduces the checked-in data exactly.
def test_committed_summary_matches_the_formula():
    on_disk = json.loads((DATA / "summary.json").read_text())
    assert on_disk == generate.summary()
```

**See also:** [[never-weaken-a-failing-test]], [[always-cover-the-five-categories]], [[guard-generated-data-against-drift]], catalyst/docs/adr/0001-book-version-stamp-and-pdf-cache.

**Enforced by:** PHPUnit and pytest + review (no merge of a critical path without covering tests).
