---
name: always-set-security-headers-and-csp
description: Send a strict Content-Security-Policy plus the standard hardening headers, using a CSP nonce for inline scripts.
paths: ["catalyst/src/EventListener/**", "catalyst/config/packages/**"]
severity: should
---
# Always set security headers and CSP

**Rule:** Send a strict `Content-Security-Policy` on every response, plus `X-Content-Type-Options: nosniff`, a `Referrer-Policy`, and `frame-ancestors` (or `X-Frame-Options`). Allow inline scripts only through a per-request CSP nonce, which works with Stimulus and Live Components. Add `Strict-Transport-Security` when the tool is served over HTTPS.

**Why:** OWASP Top 10 A05 (Security Misconfiguration). A nonce-based CSP is the strongest browser-side backstop against XSS, so it backs up [[always-sanitize-user-html]] on the GrapesJS editor and its rendered output. `nosniff` stops MIME confusion, and `frame-ancestors` blocks clickjacking.

**Good / Bad:**
```php
// Bad:  no policy, inline scripts run freely, framing allowed
// (no response listener; the editor's rendered HTML has no CSP backstop)

// Good:  a Symfony response listener sets the headers and the per-request nonce
$response->headers->set(
    'Content-Security-Policy',
    "default-src 'self'; script-src 'self' 'nonce-{$nonce}'; frame-ancestors 'none'",
);
$response->headers->set('X-Content-Type-Options', 'nosniff');
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
```

**See also:** [[always-sanitize-user-html]], [[never-expose-secrets-to-the-frontend]].

**Enforced by:** review + functional test (responses carry the CSP and hardening headers).
