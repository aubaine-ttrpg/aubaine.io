---
name: fingerprint-and-immutable-static-assets
description: Serve Encore-fingerprinted static assets with a one-year immutable cache so deploys never serve stale files.
paths: ["webpack.config.js", "config/packages/**", "templates/**/*.twig", "public/build/**"]
severity: should
---
# Fingerprint and immutable static assets
**Rule:** Webpack Encore writes each asset with a content hash in the filename (`app.abc123.css`). Serve those files with `Cache-Control: public, max-age=31536000, immutable`. A new build produces a new filename, so clients fetch the new file instead of a cached old one.

**Why:** RFC 9110 lets a client reuse an `immutable` response for the whole `max-age` window without revalidating, which is safe only because the hashed name changes when the content does. A short `max-age` on a stable filename forces needless revalidation and can still serve stale bytes. See ADR 0008 (frontend tooling).

**Good / Bad:**
```
# Bad: stable name, short TTL, revalidates constantly and can go stale.
GET /build/app.css
Cache-Control: public, max-age=300

# Good: fingerprinted name, cached for a year, immutable.
GET /build/app.abc123.css
Cache-Control: public, max-age=31536000, immutable
```

**See also:** [[cache-public-coach-sites-with-s-maxage]].

**Enforced by:** Symfony native (Encore manifest + asset() versioning) + review.
