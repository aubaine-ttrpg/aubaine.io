---
name: keep-it-simple
description: Build the simplest thing that satisfies the spec; do not add abstraction, indirection, or config for needs that do not exist yet.
severity: should
---
# Keep it simple (KISS / YAGNI)

**Rule:** Solve the problem in front of you with the least machinery that works and reads clearly. Do
not add layers, generic frameworks, interfaces-of-one, config switches, or "for later" hooks for needs
that do not exist yet. Add the abstraction when the second real caller arrives, not in anticipation of it.

**Why:** Speculative generality is the most expensive debt for a solo team: every unused abstraction is
code to read, test, and maintain forever, and it usually guesses the future wrong. A single-user local
authoring tool has no need for strict-DDD, CQRS, or event-sourcing, so do not reach for them. Simple code
is faster to change, which is the only future-proofing that actually pays off. Pairs with
[[always-factorize]] (factor real duplication, not imagined) and [[prefer-composition-over-inheritance]].

**Good / Bad:**
```php
// Bad: a strategy interface, a factory, and a registry for the one export format we have.
interface ExportStrategy { public function format(Book $book): string; }
final class ExportStrategyFactory { /* ... 40 lines ... */ }

// Good: the one exporter we actually have, ready to grow when a second format is real.
final class JsonBookExporter { public function export(Book $book): string { /* ... */ } }
```

**See also:** [[always-factorize]], architecture/always-use-dtos-at-boundaries, process/always-assess-cost-impact.

**Enforced by:** review.
