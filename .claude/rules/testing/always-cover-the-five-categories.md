---
name: always-cover-the-five-categories
description: Every feature carries tests in all five categories, happy path, negative and error, edge cases, non-functional, and data integrity.
severity: must
---
# Always cover the five categories

**Rule:** Every feature carries tests in all five categories:
1. **happy path**: the pinned journey (see [[always-pin-the-canonical-journey]]);
2. **negative and error**: invalid input is rejected with a clear result and no crash (a catalyst request returns `application/problem+json`, see http-and-caching/use-correct-verbs-codes-and-problem-json; a lab function raises, it does not return a wrong number);
3. **edge cases**: the boundaries, per [[use-equivalence-partitioning-and-boundary-values]];
4. **non-functional**: accessibility where there is a UI (accessibility/always-meet-wcag-aa-and-rgaa) and performance (see [[always-assert-query-count-does-not-scale]]);
5. **data integrity**: round-trip and export determinism, per [[always-test-critical-paths]].

**Why:** This is the umbrella over the other testing rules. A happy-path-only change is half-tested: the failures that actually bite (bad input, empty and boundary data, a query that scales, a nondeterministic export) all live in the other four categories.

**Good / Bad:**
```text
Bad: one happy-path test for exporting a book to PDF.

Good: five tests for the book PDF export.
1. happy: a book with pages exports to a PDF (the pinned journey)
2. negative: a book with no pages returns a clear error, not a 500 or a blank PDF
3. edge: empty book, one page, the largest book, one page past the layout limit
4. non-functional: the export screen passes axe; the query count does not scale with page count
5. data integrity: re-exporting the same book reproduces the same PDF (the version stamp is stable)
```

**See also:** [[always-pin-the-canonical-journey]], [[always-test-critical-paths]], [[use-equivalence-partitioning-and-boundary-values]], [[always-assert-query-count-does-not-scale]], accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** review (a change missing a category does not merge).
