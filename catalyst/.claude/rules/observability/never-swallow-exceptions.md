---
name: never-swallow-exceptions
description: Never catch and ignore; handle meaningfully, log with context and rethrow, or let it bubble to Sentry, and preserve the previous exception when wrapping.
paths: ["src/**"]
severity: must
---
# Never swallow exceptions

**Rule:** Do not catch and ignore. An empty catch, or a catch that silently returns null, hides failures from Sentry and from the operator. Either handle the error meaningfully, or log it with context (see [[always-log-structured-with-context]]) and rethrow, or let it bubble. When wrapping, pass the original as `previous` so the stack trace survives. At the HTTP boundary map to the right status (see http-and-caching/use-correct-verbs-codes-and-problem-json), never a silent 200.

**Why:** A swallowed exception is OWASP A09 (Security Logging and Monitoring Failures): the new failure path never reaches Sentry, so nobody knows it broke. Preserving `previous` keeps the audit trail intact for ISO 27001.

**Good / Bad:**
```php
// Bad:  failure vanishes, Sentry sees nothing
try {
    $this->gateway->charge($invoice);
} catch (\Throwable $e) {
}

// Good:  log with context, wrap preserving previous, rethrow
try {
    $this->gateway->charge($invoice);
} catch (\Throwable $e) {
    $logger->error('payment.charge_failed', [
        'org_id' => $org->getId(),
        'invoice_id' => $invoice->getId(),
        'correlation_id' => $correlationId,
    ]);
    throw new PaymentException('Charge failed', previous: $e);
}
```

**Enforced by:** review + PHPStan 9 (flags empty catch blocks and dropped exception variables).
