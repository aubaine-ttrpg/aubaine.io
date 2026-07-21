---
name: always-inject-dependencies
description: Get collaborators through constructor injection from the container; never new a service, use a static singleton, or locate services inside domain code.
paths: ["catalyst/src/**/*.php"]
severity: must
---
# Always inject dependencies

**Rule:** Declare every collaborator as a constructor argument and let the container autowire it. Never `new` a service, never call a static singleton, and never pull a service from the container inside domain code (no `$container->get(...)`, no service locator in a service).

**Why:** the Dependency Inversion Principle (the D in SOLID). Injected dependencies can be swapped and mocked; a `new` or a static call hard-wires a concrete class and makes the code untestable in isolation. A static singleton also hides the dependency and pins global state that a test cannot replace.

**Good / Bad:**
```php
// Bad - hard-wired collaborator, untestable, hidden dependency
final class PdfRenderer
{
    public function render(Book $book): Pdf
    {
        $html  = new TwigHtmlRenderer();                 // hard-wired
        $stamp = BookVersionStamp::getInstance()->of($book); // static singleton
    }
}

// Good - constructor injection of abstractions
final class PdfRenderer
{
    public function __construct(
        private HtmlRendererInterface $html,
        private BookVersionStamp $versionStamp,
    ) {}
}
```

**See also:** [[always-use-solid]], [[prefer-composition-over-inheritance]].

**Enforced by:** PHPStan 9 (forbid `new` of services and service-locator calls in domain code) + review.
