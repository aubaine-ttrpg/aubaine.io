---
name: never-expose-secrets-to-the-frontend
description: Treat every secret as server-only; only env values explicitly designated publishable may reach the browser, exposed one by one, never a blanket env dump.
paths: ["catalyst/assets/**", "catalyst/webpack.config.js", "catalyst/templates/**", "catalyst/config/**"]
severity: must
---
# Never expose secrets to the frontend

**Rule:** Every secret is server-only by default. The only values that may reach the browser, inlined into the JS bundle, rendered into Twig, or returned in a response, are env values explicitly designated publishable by a clear naming convention and an allowlist. A public value like the book version stamp is safe to publish; `APP_SECRET`, `DATABASE_URL`, and any outbound API token never leave the server. Expose each public value by name; never dump `process.env` or the whole env into the bundle.

**Why:** OWASP A05 (Security Misconfiguration). Anything in a JS bundle or a rendered page is world-readable, so a server secret shipped to the client leaks the instant it loads, and lives on in caches and devtools. Naming the publishable values one by one is the signal for what is safe to ship. Sibling to [[never-commit-secrets]] (git): this is the runtime and client side of the same discipline.

**Good / Bad:**
```twig
{# Bad: a server secret rendered into the page. #}
<script>window.db = "{{ database_url }}";</script>
{# Good: only a value meant to be public crosses to the browser. #}
<meta name="book-version" content="{{ book_version_stamp }}">
```
```js
// Bad: blanket-inline every env var into the bundle.
new webpack.EnvironmentPlugin(Object.keys(process.env));
// Good: name the public values explicitly; nothing else can leak.
new webpack.EnvironmentPlugin(['BOOK_VERSION']);
```

**See also:** [[never-commit-secrets]], [[never-leak-internal-context-in-responses]], [[always-set-security-headers-and-csp]], frontend/route-all-css-and-js-through-encore.

**Enforced by:** review; secret scanning (`.githooks` + CI); an explicit public-value allowlist in the Encore config (no env-wide inlining); a CSP that needs no inline secrets.
