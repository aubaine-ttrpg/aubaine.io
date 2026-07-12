---
name: always-use-solid
description: Apply the five SOLID principles to every service, with one reason to change per class and dependencies on interfaces.
paths: ["src/**/*.php"]
severity: should
---
# Always use SOLID

**Rule:** Each service has one reason to change (SRP), is open to extension via new collaborators rather than edited switch-statements (OCP), keeps subtypes substitutable (LSP), exposes narrow interfaces (ISP), and depends on abstractions you inject (DIP). When a class grows a second responsibility, split it.

**Why:** SOLID (Robert C. Martin). SOLID services stay small, testable, and swappable, which is what keeps thin controllers thin and entities anemic.

**Good / Bad:**
```php
// Bad - two reasons to change, depends on a concrete class
final class InvoiceService
{
    public function __construct(private SmtpMailer $mailer) {}
    public function issue(Order $o): void { /* build + persist + format PDF + email */ }
}

// Good - one job, depends on an abstraction
final class InvoiceIssuer
{
    public function __construct(private MailerInterface $mailer) {}
    public function issue(IssueInvoice $command): Invoice { /* build + persist */ }
}
```

**See also:** [[always-inject-dependencies]] (DIP), [[prefer-composition-over-inheritance]] (OCP/LSP), [[never-put-business-logic-in-entities]] (SRP).

**Enforced by:** PHPStan 9 + review.
