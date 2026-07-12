---
name: always-set-security-headers-and-csp
description: Send a strict Content-Security-Policy plus HSTS and the standard hardening headers, using a CSP nonce for inline scripts.
paths: ["src/EventListener/**", "config/packages/**", "Caddyfile"]
severity: should
---
# Always set security headers and CSP

**Rule:** Send a strict `Content-Security-Policy` on every response, plus `Strict-Transport-Security`, `X-Content-Type-Options: nosniff`, a `Referrer-Policy`, and `frame-ancestors` (or `X-Frame-Options`). Allow inline scripts only through a per-request CSP nonce, which works with Stimulus and Turbo.

**Why:** OWASP Top 10 A05 (Security Misconfiguration). A nonce-based CSP is the strongest browser-side backstop against XSS, HSTS forces HTTPS, `nosniff` stops MIME confusion, and frame controls block clickjacking. ISO 27001 secure-configuration control.

**Good / Bad:**
```nginx
# Bad:  no policy, inline scripts run freely, framing allowed
# (no security headers set)

# Good:  Caddy header (or a Symfony response listener injecting the nonce)
header {
    Content-Security-Policy "default-src 'self'; script-src 'self' 'nonce-{nonce}'; frame-ancestors 'none'"
    Strict-Transport-Security "max-age=31536000; includeSubDomains"
    X-Content-Type-Options "nosniff"
    Referrer-Policy "strict-origin-when-cross-origin"
}
```

**Enforced by:** review + functional test (responses carry the CSP and hardening headers).
