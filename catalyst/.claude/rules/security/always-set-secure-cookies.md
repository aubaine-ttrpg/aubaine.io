---
name: always-set-secure-cookies
description: Session and auth cookies must be Secure, HttpOnly, SameSite, and host-only per host.
paths: ["config/packages/framework.yaml", "config/packages/security.yaml"]
severity: must
---
# Always set secure cookies

**Rule:** Session and authentication cookies are `Secure`, `HttpOnly`, and `SameSite=Lax` (use `Strict` where the flow allows). Scope each cookie host-only per host so `my.`, `admin.`, and `[slug].` never share a session (ADR 0009).

**Why:** OWASP Top 10 A05 (Security Misconfiguration). `Secure` keeps the cookie off plaintext HTTP, `HttpOnly` keeps it out of JavaScript so an XSS cannot steal it, and `SameSite` blunts cross-site requests as a partner to [[never-create-a-form-without-csrf]]. Host-only cookies stop one domain's session from leaking to another.

**Good / Bad:**
```yaml
# Bad:  defaults leave the cookie readable by JS and sent over HTTP
framework:
    session:
        handler_id: ~

# Good:  config/packages/framework.yaml
framework:
    session:
        cookie_secure: true
        cookie_httponly: true
        cookie_samesite: lax   # no leading domain dot -> host-only
```

**Enforced by:** Symfony native config + review.
