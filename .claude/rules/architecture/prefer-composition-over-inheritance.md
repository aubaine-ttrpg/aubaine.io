---
name: prefer-composition-over-inheritance
description: Build behavior by composing injected collaborators and small traits rather than extending deep abstract base classes.
severity: prefer
---
# Prefer composition over inheritance

**Rule:** Reach for an injected collaborator (or a focused trait) before you reach for `extends`. Keep inheritance for genuine "is-a" types; use composition for "needs-a" behavior. Avoid abstract base classes that grow shared helpers over time.

**Why:** GoF "favor object composition over class inheritance" and SOLID (OCP, LSP). A deep base class couples every subclass to its internals, and a change near the root ripples down. A collaborator is swappable, mockable, and reusable outside the hierarchy.

**Good / Bad:**
```php
// Bad - behavior trapped in a base class every exporter must extend
abstract class AbstractExporter
{
    protected function fingerprint(Book $book): string { /* ... */ }
}
final class JsonExporter extends AbstractExporter { /* locked into the base */ }

// Good - inject the behavior you need
final class JsonExporter
{
    public function __construct(private BookVersionStamp $versionStamp) {}
}
```

**See also:** [[always-use-solid]], [[always-inject-dependencies]].

**Enforced by:** review + PHPStan 9.
