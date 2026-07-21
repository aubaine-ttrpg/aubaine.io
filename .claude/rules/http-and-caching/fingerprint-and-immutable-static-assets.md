---
name: fingerprint-and-immutable-static-assets
description: Serve content-hashed static assets with a one-year immutable cache so deploys never serve stale files.
paths: ["catalyst/webpack.config.js", "catalyst/config/packages/**", "catalyst/templates/**/*.twig", "catalyst/public/build/**", "almanach/**"]
severity: should
---
# Fingerprint and immutable static assets
**Rule:** Both build tools write each asset with a content hash in the filename (Webpack Encore's `app.abc123.css` in catalyst, Astro's hashed output in almanach). Serve those files with `Cache-Control: public, max-age=31536000, immutable`. A new build produces a new filename, so clients fetch the new file instead of a cached old one.

**Why:** RFC 9110 lets a client reuse an `immutable` response for the whole `max-age` window without revalidating, which is safe only because the hashed name changes when the content does. A short `max-age` on a stable filename forces needless revalidation and can still serve stale bytes.

**Good / Bad:**
```
# Bad: stable name, short TTL, revalidates constantly and can go stale.
GET /build/app.css
Cache-Control: public, max-age=300

# Good: fingerprinted name, cached for a year, immutable.
GET /build/app.abc123.css
Cache-Control: public, max-age=31536000, immutable
```

**See also:** [[prefer-etag-and-conditional-requests]].

**Enforced by:** Encore manifest and `asset()` versioning (catalyst), Astro build hashing (almanach) + review.
