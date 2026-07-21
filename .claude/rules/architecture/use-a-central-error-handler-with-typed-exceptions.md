---
name: use-a-central-error-handler-with-typed-exceptions
description: Handle failures through one kernel.exception handler with typed domain exceptions; every error becomes a mapped, content-negotiated, localized, logged, user-safe outcome, never a crash, never a redirect-to-200, never HTML on a JSON endpoint.
paths: ["catalyst/src/**", "catalyst/templates/**", "catalyst/config/packages/**"]
severity: must
---
# Use a central error handler with typed exceptions

**Rule:** Failures are handled in one place, a `kernel.exception` listener, not by `try/catch` scattered at every call site. Throw **typed domain exceptions** (`ValidationException`→422, `NotFoundException`→404, `ConflictException`→409); the listener maps type→status, **negotiates content** (HTML → a branded localized error page or in-place re-render; JSON/AJAX → `problem+json`), logs the real error server-side with a correlation id, and shows the user a safe, localized outcome carrying that id as a reference, never a stack trace. The **true status code survives to the client**; do not redirect errors. Catch locally only to recover, retry, or translate an infrastructure exception into a domain one, then rethrow (see observability/never-swallow-exceptions).

**Why:** Correct behaviour must be automatic, not opt-in. Scattered `try/catch` swallows bugs and drifts; no handler at all crashes to a white page that can leak internals (security/never-leak-internal-context-in-responses). The client contract: 422 re-renders the form in place with inline errors (the non-2xx status is what makes the live component re-render, http-and-caching/use-correct-verbs-codes-and-problem-json), 409 shows a flash or toast (frontend/always-give-user-feedback), 404 and 500 render a branded page. Messages are localized in EN and FR (i18n/never-hardcode-user-facing-strings).

**Good / Bad:**
```php
// Bad: redirect every error to a /500 page. The redirect turns it into a 200
// (hiding the real failure from the logs), discards context, and serves HTML to JSON clients.
catch (\Throwable $e) { return new RedirectResponse('/500'); }

// Good: throw a typed domain exception; the central listener maps + negotiates + logs.
throw new ConflictException('This page changed since you opened it.'); // → 409, problem+json or flash, logged
```

**See also:** [[always-thin-controllers]], observability/never-swallow-exceptions, http-and-caching/use-correct-verbs-codes-and-problem-json, frontend/always-give-user-feedback, security/never-leak-internal-context-in-responses, i18n/never-hardcode-user-facing-strings, catalyst/docs/adr/0002-live-book-editor.

**Enforced by:** review; a functional test that an erroring JSON request gets `problem+json` with the true status (never HTML, never 200).
