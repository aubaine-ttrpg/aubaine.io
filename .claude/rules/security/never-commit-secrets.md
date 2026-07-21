---
name: never-commit-secrets
description: No secrets in git; use the Symfony secrets vault or server env, keep .env.local gitignored, and rotate on leak.
severity: must
---
# Never commit secrets

**Rule:** No secret ever lands in git. Keep secrets in the Symfony secrets vault or in server environment variables. The committed `.env` holds only public, non-secret defaults; real values live in `.env.local` (gitignored) or the environment. If a secret leaks, rotate it before anything else.

**Why:** OWASP Top 10 A05 (Security Misconfiguration) and A07 (Identification and Authentication Failures). Git history is forever and often public-adjacent, so a committed key stays exploitable long after the file is deleted.

**Good / Bad:**
```yaml
# Bad:  a real secret committed in tracked config
framework:
    secret: 's3cr3t-a1b2c3-committed-value'

# Good:  reference the env, value never tracked
framework:
    secret: '%env(APP_SECRET)%'
```

**See also:** [[never-expose-secrets-to-the-frontend]], [[never-leak-internal-context-in-responses]].

**Enforced by:** review + secret scanning in `.githooks` and CI (a detected secret fails the push).
