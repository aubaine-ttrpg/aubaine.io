---
name: always-log-structured-with-context
description: Log through Monolog as structured data with context keys (org_id, user_id, correlation_id, event), never as interpolated string blobs, and never log PII or secrets.
paths: ["src/**"]
severity: must
---
# Always log structured with context

**Rule:** Use the Monolog logger and pass an event name as the message plus a context array (`org_id`, `user_id`, `correlation_id`, `event`). Do not build log lines by interpolating values into a string. Never put PII (email, name, phone) or secrets in the message or the context (see rgpd/always-minimize-personal-data and security/never-commit-secrets). Sensitive actions also emit an audit-log entry.

**Why:** Structured logs are queryable and correlatable in Sentry and the log pipeline; string blobs are not. This is OWASP A09 (Security Logging and Monitoring Failures) and the ISO 27001 audit-trail control.

**Good / Bad:**
```php
// Bad:  PII leaked, unstructured, unsearchable
$logger->info("User $email created client $name");

// Good:  event name plus structured, PII-free context
$logger->info('client.created', [
    'org_id' => $org->getId(),
    'user_id' => $user->getId(),
    'client_id' => $client->getId(),
    'correlation_id' => $correlationId,
]);
```

**Enforced by:** review + PHPStan 9 (a custom rule flags interpolated arguments to logger calls).
