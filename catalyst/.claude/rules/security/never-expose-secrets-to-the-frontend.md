---
name: never-expose-secrets-to-the-frontend
description: Treat every secret as server-only; only env values explicitly designated publishable (Turnstile site key, PostHog public key) may reach the browser, exposed one by one, never a blanket env dump.
paths: ["assets/**", "webpack.config.js", "templates/**", "config/**"]
severity: must
---
# Never expose secrets to the frontend

**Rule:** Every secret is server-only by default. The only values that may reach the browser, inlined into the JS bundle, rendered into Twig, or returned in a response, are env values explicitly designated publishable by a clear naming convention and an allowlist. The vendor's own split decides this: the Cloudflare Turnstile **site** key and a PostHog **public/project** key are publishable; their counterparts (`TURNSTILE_SECRET_KEY`, any private/personal API key) never leave the server. Expose each public key by name; never dump `process.env` or the whole env into the bundle.

**Why:** OWASP A05. Anything in a JS bundle or a rendered page is world-readable, so a private key shipped to the client leaks the instant it loads, and lives on in caches and devtools. The vendor's site-vs-secret / public-vs-personal naming is exactly the signal for what is safe to publish. ISO 27001 secrets management; sibling to [[never-commit-secrets]] (git). This is the runtime/client side of the same discipline.

**Good / Bad:**
```twig
{# Bad: the secret key rendered into the page. #}
<script>window.cf = "{{ turnstile_secret_key }}";</script>
{# Good: only the publishable site key crosses to the browser. #}
<div class="cf-turnstile" data-sitekey="{{ turnstile_site_key }}"></div>
```
```js
// Bad: blanket-inline every env var into the bundle.
new webpack.EnvironmentPlugin(Object.keys(process.env));
// Good: name the public keys explicitly; nothing else can leak.
new webpack.EnvironmentPlugin(['TURNSTILE_SITE_KEY', 'POSTHOG_PUBLIC_KEY']);
```

**See also:** [[never-commit-secrets]], [[never-leak-internal-context-in-responses]], [[always-set-security-headers-and-csp]], frontend/route-all-css-and-js-through-encore.

**Enforced by:** review; secret scanning (`.githooks` + CI); an explicit public-key allowlist in the Encore config (no env-wide inlining); a CSP that needs no inline secrets.
