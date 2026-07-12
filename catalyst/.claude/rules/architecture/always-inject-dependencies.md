---
name: always-inject-dependencies
description: Get collaborators through constructor injection from the container; never new a service, use a static singleton, or locate services inside domain code.
paths: ["src/**/*.php"]
severity: must
---
# Always inject dependencies

**Rule:** Declare every collaborator as a constructor argument and let the container autowire it. Never `new` a service, never call a static singleton, and never pull a service from the container inside domain code (no `$container->get(...)`, no service locator in a service).

**Why:** the Dependency Inversion Principle (the D in SOLID). Injected dependencies can be swapped and mocked; a `new` or a static call hard-wires a concrete class and makes the code untestable in isolation. It also breaks under FrankenPHP worker mode, where state must come from the container, not from globals.

**Good / Bad:**
```php
// Bad - hard-wired collaborator, untestable, hidden dependency
final class InvoiceIssuer
{
    public function issue(IssueInvoice $c): Invoice
    {
        $mailer = new SmtpMailer();              // hard-wired
        $rate   = VatRegistry::getInstance()->for($c); // static singleton
    }
}

// Good - constructor injection of abstractions
final class InvoiceIssuer
{
    public function __construct(
        private MailerInterface $mailer,
        private VatCalculator $vat,
    ) {}
}
```

**See also:** [[always-use-solid]], [[prefer-composition-over-inheritance]].

**Enforced by:** PHPStan 9 (forbid `new` of services and service-locator calls in domain code) + review.
