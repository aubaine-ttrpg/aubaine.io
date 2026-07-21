---
name: never-leak-internal-context-in-responses
description: No user-facing response (page or problem+json error) may expose internal context: secrets, internal IDs, paths, SQL, stack traces, framework debug output, or raw AI/LLM output.
paths: ["catalyst/src/**", "catalyst/templates/**", "catalyst/config/packages/**"]
severity: must
---
# Never leak internal context in responses

**Rule:** Nothing internal crosses the boundary into a user-facing response, a rendered page, or a `problem+json` error: no secrets, internal IDs, file paths, SQL, stack traces, or framework debug output. If a feature renders text produced by an AI/LLM, treat that output as untrusted too: never echo the prompt back, withhold the reasoning, and escape what you render. In production, errors return a generic `problem+json` with the detail logged server-side behind a correlation id; the profiler and verbose traces stay in dev only.

**Why:** OWASP A01 (information disclosure) and A05 (verbose errors / misconfiguration), plus the LLM prompt-leak class. Internal context handed to a user is free reconnaissance. This is the runtime counterpart of ai/keep-agent-context-files-curated, which keeps internals out of context files at author time.

**Good / Bad:**
```php
// Bad: the user sees the internal message and stack trace.
catch (\Throwable $e) {
    return new Response($e->getMessage()."\n".$e->getTraceAsString(), 500);
}

// Good: log the detail server-side, return a generic problem+json with a reference id.
catch (\Throwable $e) {
    $correlationId = (string) Uuid::v7();
    $this->logger->error('pdf export failed', ['exception' => $e, 'correlation_id' => $correlationId]); // server-side
    return $this->problem(500, 'Une erreur interne est survenue.', ['ref' => $correlationId]);
}
```
```twig
{# Bad: an AI reply rendered raw, echoing its own system prompt. #}
{{ ai_reply | raw }}
{# Good: escaped, prompt and reasoning withheld. #}
{{ ai_reply.visibleText }}
```

**See also:** [[never-expose-secrets-to-the-frontend]], ai/treat-ai-output-as-untrusted-until-integrated, observability/never-swallow-exceptions, http-and-caching/use-correct-verbs-codes-and-problem-json.

**Enforced by:** review; `APP_DEBUG=0` and no profiler in prod; the `problem+json` error handler; structured server-side logging.
