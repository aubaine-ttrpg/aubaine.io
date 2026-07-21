---
name: prefer-enums-over-constants
description: Model closed sets of values as PHP backed enums instead of class constants or magic strings.
paths: ["catalyst/**/*.php"]
severity: prefer
---
# Prefer enums over constants

**Rule:** For any closed set of values (a `Page` type, a `SkillTree` node kind, a `Pdf` render state) use a PHP 8.1+ backed enum, not `const STATUS_* = '...'` strings or bare literals scattered through the code. Type-hint the enum so the value is checked, and use `match` so a missing case is caught: PHPStan flags a non-exhaustive `match`, and at runtime an unhandled case throws `\UnhandledMatchError`.

**Why:** A backed enum gives a real type (you cannot pass a typo), a `match` that PHPStan checks for exhaustiveness (a new case forces every switch to be revisited), and a single home for the domain vocabulary. This is the PHP 8.1 Enumerations RFC working as intended. Pairs with testing/test-all-state-transitions for state machines like the `Pdf` render lifecycle.

**Good / Bad:**
```php
// Bad: magic strings, no type safety, typos compile fine.
class Pdf
{
    public const STATE_READY = 'ready';
    public string $state = 'raedy'; // typo, nothing catches it
}
```
```php
// Good: backed enum, exhaustive match.
enum PdfState: string
{
    case Queued = 'queued';
    case Rendering = 'rendering';
    case Ready = 'ready';
}

$labelKey = match ($state) {
    PdfState::Queued => 'pdf.state.queued',
    PdfState::Rendering => 'pdf.state.rendering',
    PdfState::Ready => 'pdf.state.ready',
}; // add a case and PHPStan flags this match until it is handled (an unhandled case throws \UnhandledMatchError at runtime)
```

**Enforced by:** review + PHPStan (it flags non-exhaustive `match` and wrong enum types). Doctrine maps backed enums natively via `enumType`.
