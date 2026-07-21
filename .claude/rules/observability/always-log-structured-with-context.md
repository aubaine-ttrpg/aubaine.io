---
name: always-log-structured-with-context
description: Log through Monolog as structured data with context keys (event name plus book_id, page_id, correlation_id), never as interpolated string blobs, and never log secrets.
paths: ["catalyst/**"]
severity: must
---
# Always log structured with context

**Rule:** Use the Monolog logger and pass an event name as the message plus a context array (`book_id`, `page_id`, `correlation_id`). Do not build log lines by interpolating values into a string. Never put secrets (tokens, keys) in the message or the context (see security/never-commit-secrets).

**Why:** Structured logs are queryable and correlatable across the Monolog stream; string blobs are not. This is OWASP A09 (Security Logging and Monitoring Failures).

**Good / Bad:**
```php
// Bad:  interpolated blob, unstructured, unsearchable
$logger->info("Rebuilt PDF for book $title ($id)");

// Good:  event name plus structured context
$logger->info('book.pdf_rebuilt', [
    'book_id' => $book->getId(),
    'page_count' => $book->getPageCount(),
    'correlation_id' => $correlationId,
]);
```

**See also:** [[never-swallow-exceptions]], security/never-commit-secrets.

**Enforced by:** review + PHPStan 9 (a custom rule flags interpolated arguments to logger calls).
