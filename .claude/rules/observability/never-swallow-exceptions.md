---
name: never-swallow-exceptions
description: Never catch and ignore; handle meaningfully, log with context and rethrow, or let it bubble, and preserve the previous exception when wrapping.
paths: ["catalyst/**", "codex/**/*.py"]
severity: must
---
# Never swallow exceptions

**Rule:** Do not catch and ignore. An empty catch, or a catch that silently returns null, hides failures from the logs and from the operator. Either handle the error meaningfully, or log it with context (see [[always-log-structured-with-context]]) and rethrow, or let it bubble. When wrapping, pass the original as `previous` so the stack trace survives (in Python, re-raise bare to keep the traceback, or `raise NewError(...) from exc` when wrapping). At the HTTP boundary map to the right status (see http-and-caching/use-correct-verbs-codes-and-problem-json), never a silent 200.

**Why:** A swallowed exception is OWASP A09 (Security Logging and Monitoring Failures): the new failure path never reaches the logs, so nobody knows it broke. Preserving `previous` keeps the full stack trace intact for debugging.

**Good / Bad:**
```php
// Bad:  failure vanishes, the logs see nothing
try {
    $this->renderer->buildPdf($book);
} catch (\Throwable $e) {
}

// Good:  log with context, wrap preserving previous, rethrow
try {
    $this->renderer->buildPdf($book);
} catch (\Throwable $e) {
    $logger->error('book.pdf_build_failed', [
        'book_id' => $book->getId(),
        'correlation_id' => $correlationId,
    ]);
    throw new PdfBuildException('PDF build failed', previous: $e);
}
```
```python
# Bad: the traceback vanishes, the caller sees None
try:
    result = solve_balance(params)
except Exception:
    result = None

# Good: log, then re-raise so the traceback survives
try:
    result = solve_balance(params)
except Exception:
    logger.exception("balance.solve_failed")
    raise
```

**Enforced by:** review + PHPStan 9 (flags empty catch blocks and dropped exception variables in PHP); ruff (`BLE001` blind-except, `E722` bare `except`) in Python.
