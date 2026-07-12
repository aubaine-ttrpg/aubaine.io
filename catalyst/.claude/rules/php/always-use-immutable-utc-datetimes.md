---
name: always-use-immutable-utc-datetimes
description: Store and manipulate all timestamps as UTC DateTimeImmutable in timestamptz columns; convert to the user's timezone only at display.
paths: ["**/*.php", "templates/**"]
severity: must
---
# Always use immutable UTC datetimes

**Rule:** Persist every point-in-time as **UTC** in a Postgres `timestamptz` column, mapped to
`DateTimeImmutable` (`datetime_immutable` Doctrine type), and compute in UTC. Convert to the user's
timezone and locale format **only at render time**. Never store local/naive times, never use mutable
`\DateTime` for stored values, and never format dates with hand-built strings. A pure calendar day with
no time component (e.g. an invoice issue date) uses a `date_immutable` column, not a timestamp.

**Why:** Storing local time loses the offset and breaks across DST and across a coach's clients in
different timezones; mutable `\DateTime` causes aliasing bugs when one instance is shared and mutated.
UTC-at-rest + convert-at-edge is the only model that stays correct as we add timezones, and it keeps
sorting and range queries unambiguous. Display formatting is a localization concern
(i18n/never-hardcode-user-facing-strings), not a storage one.

**Good / Bad:**
```php
// Bad: mutable, server-local, naive; wrong the moment two timezones exist.
private \DateTime $createdAt;                       // mutable + no tz intent
$createdAt = new \DateTime('now');                  // server local time

// Good: immutable, UTC, timestamptz.
#[ORM\Column(type: 'datetime_immutable')]            // timestamptz in Postgres
private \DateTimeImmutable $createdAt;
$createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
```
```twig
{# Convert at display only #}
{{ payment.createdAt|format_datetime(locale=app.request.locale, timezone=app.user.timezone) }}
```

**See also:** database/always-use-gedmo-and-useful-traits, i18n/never-hardcode-user-facing-strings.

**Enforced by:** PHPStan 9, review.
