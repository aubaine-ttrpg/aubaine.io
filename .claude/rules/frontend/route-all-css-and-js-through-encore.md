---
name: route-all-css-and-js-through-encore
description: All CSS and JS go through the Webpack Encore pipeline; never hand-link or inline assets that skip autoprefixer, fingerprinting, and the CSP nonce.
paths: ["catalyst/assets/**", "catalyst/templates/**", "catalyst/public/**"]
severity: must
---
# Route all CSS and JS through Encore

**Rule:** Every stylesheet and script goes through the **Webpack Encore** pipeline (PostCSS plus
autoprefixer, Babel, minify, fingerprint) and is referenced through the manifest with
`encore_entry_link_tags()` / `encore_entry_script_tags()`. Do **not** hand-place a `<link>` or `<script>`
to a file in `public/`, and do **not** inline a `<style>` or `<script>` block that bypasses the pipeline
(it would miss autoprefixing, minification, cache-busting, and the CSP nonce).

**Why:** assets outside the pipeline silently lose vendor prefixes ([[use-browserslist-and-autoprefixer]]),
minification, content-hash cache-busting (http-and-caching/fingerprint-and-immutable-static-assets), and
CSP-nonce handling (security/always-set-security-headers-and-csp). One pipeline is one source of truth for
browser support and caching.

**Good / Bad:**
```twig
{# Bad: hand-linked, unfingerprinted, no autoprefixer, breaks CSP. #}
<link rel="stylesheet" href="/css/custom.css">

{# Good: through the Encore manifest. #}
{{ encore_entry_link_tags('app') }}
```

**See also:** [[use-browserslist-and-autoprefixer]], http-and-caching/fingerprint-and-immutable-static-assets, [[always-use-design-tokens-not-magic-values]], security/always-set-security-headers-and-csp.

**Enforced by:** Webpack Encore build + review (the CSP blocks unhandled inline assets).
