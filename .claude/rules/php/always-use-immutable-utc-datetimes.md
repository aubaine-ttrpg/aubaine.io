---
name: always-use-immutable-utc-datetimes
description: Store and manipulate all timestamps as UTC DateTimeImmutable; SQLite carries no timezone, so store UTC and convert to the reader's timezone only at display.
paths: ["catalyst/**/*.php", "catalyst/templates/**"]
severity: must
---
# Always use immutable UTC datetimes

**Rule:** Persist every point-in-time as **UTC**, mapped to `DateTimeImmutable` (`datetime_immutable`
Doctrine type), and compute in UTC. SQLite has no dedicated date type: the `datetime_immutable` type
stores an ISO-8601 string with no offset, so the value is only unambiguous if it is UTC. Convert to the
reader's timezone and locale format **only at render time**. Never store local/naive times, never use
mutable `\DateTime` for stored values, and never format dates with hand-built strings. A pure calendar
day with no time component (e.g. a book's publication date) uses a `date_immutable` column, not a
timestamp.

**Why:** SQLite stores the string you hand it and carries no timezone, so a local/naive value loses its
offset and breaks across DST; mutable `\DateTime` causes aliasing bugs when one instance is shared and
mutated. UTC-at-rest + convert-at-edge keeps stored values unambiguous and keeps sorting and range
queries correct, and it lets the public almanach site render each timestamp in the visitor's own
timezone. Display formatting is a localization concern (i18n/never-hardcode-user-facing-strings), not a
storage one.

**Good / Bad:**
```php
// Bad: mutable, server-local, naive; wrong the moment two timezones exist.
private \DateTime $createdAt;                       // mutable + no tz intent
$createdAt = new \DateTime('now');                  // server local time

// Good: immutable, UTC.
#[ORM\Column(type: 'datetime_immutable')]            // stored as an ISO-8601 UTC string in SQLite
private \DateTimeImmutable $createdAt;
$createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
```
```twig
{# Convert at display only #}
{{ book.createdAt|format_datetime(locale=app.request.locale, timezone='Europe/Paris') }}
```

**See also:** i18n/never-hardcode-user-facing-strings.

**Enforced by:** PHPStan 9, review.
