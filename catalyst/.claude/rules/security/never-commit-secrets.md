---
name: never-commit-secrets
description: No secrets in git; use the Symfony secrets vault or server env, keep .env.local gitignored, and rotate on leak.
severity: must
---
# Never commit secrets

**Rule:** No secret ever lands in git. Keep secrets in the Symfony secrets vault or in server environment variables. The committed `.env` holds only public, non-secret config plus the maintenance flag; real values live in `.env.local` (gitignored) or server env. If a secret leaks, rotate it before anything else.

**Why:** OWASP Top 10 A05 (Security Misconfiguration) and A07. Git history is forever and often public-adjacent, so a committed key stays exploitable long after the file is deleted. ISO 27001 secrets-management control.

**Good / Bad:**
```yaml
# Bad:  a real key committed in tracked config
mollie:
    api_key: live_a1b2c3realsecret

# Good:  reference the vault/env, value never tracked
mollie:
    api_key: '%env(MOLLIE_API_KEY)%'
```

**See also:** [[never-expose-secrets-to-the-frontend]], [[never-leak-internal-context-in-responses]].

**Enforced by:** review + secret scanning in `.githooks` and CI (a detected secret fails the push).
