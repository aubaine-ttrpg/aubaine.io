---
name: always-factorize
description: Keep one canonical home for each piece of logic; extract anything duplicated into a shared service, trait, or method.
severity: should
---
# Always factorize shared logic

**Rule:** When the same logic appears twice, give it one home (a service method, a private method, a Twig component, a trait) and call it from both places. Copying a third time is the signal you already missed the first extraction.

**Why:** DRY. Duplicated logic drifts: a fix lands in one copy and not the other. This applies to docs too, see [[process/never-duplicate-reference-living-files]] (one canonical home, everything else links).

**Good / Bad:**
```php
// Bad - the same version-stamp hash pasted into two services
$stamp = substr(hash('xxh128', $book->contentJson()), 0, 12); // in PdfRenderer
$stamp = substr(hash('xxh128', $book->contentJson()), 0, 12); // again in BookExporter

// Good - one home, both callers depend on it
final class BookVersionStamp
{
    public function of(Book $book): string
    {
        return substr(hash('xxh128', $book->contentJson()), 0, 12);
    }
}
```

**See also:** process/never-duplicate-reference-living-files for the same principle across docs and rules; catalyst/docs/adr/0001-book-version-stamp-and-pdf-cache for the version stamp itself.

**Enforced by:** review + PHPStan 9 (and your judgment: prefer one good shared method over three drifting copies).
