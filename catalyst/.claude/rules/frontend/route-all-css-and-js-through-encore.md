---
name: route-all-css-and-js-through-encore
description: All build-time CSS and JS go through the Webpack Encore pipeline; never hand-link or inline assets that skip autoprefixer, fingerprinting and the CSP nonce. Runtime-generated coach-site CSS gets an equivalent server-side autoprefixer pass at publish.
paths: ["assets/**", "templates/**", "public/**", "src/Service/SiteRenderer/**"]
severity: must
---
# Route all CSS and JS through Encore

**Rule:** Every build-time stylesheet and script goes through the **Webpack Encore** pipeline
(PostCSS + autoprefixer, Babel, minify, fingerprint) and is referenced through the manifest with
`encore_entry_link_tags()` / `encore_entry_script_tags()`. Do **not** hand-place a `<link>`/`<script>`
to a file in `public/`, and do **not** inline a `<style>`/`<script>` block that bypasses the pipeline
(it would miss autoprefixing, minification, cache-busting, and the CSP nonce).

**Exception (must be handled, not ignored):** coach-site CSS is generated at **runtime** by GrapesJS,
so it never reaches Encore. It must get an **equivalent server-side PostCSS + autoprefixer pass in the
`SiteRenderer` at publish** (ATHLETIS-030 / ATHLETIS-031), against the same `.browserslistrc`, so public
coach sites get the same browser support as the app.

**Why:** Assets outside the pipeline silently lose vendor prefixes ([[use-browserslist-and-autoprefixer]]),
minification, content-hash cache-busting (http-and-caching/fingerprint-and-immutable-static-assets),
and CSP-nonce handling (security/always-set-security-headers-and-csp). One pipeline is one source
of truth for browser support and caching.

**Good / Bad:**
```twig
{# Bad: hand-linked, unfingerprinted, no autoprefixer, breaks CSP. #}
<link rel="stylesheet" href="/css/custom.css">

{# Good: through the Encore manifest. #}
{{ encore_entry_link_tags('app') }}
```

**See also:** [[use-browserslist-and-autoprefixer]], http-and-caching/fingerprint-and-immutable-static-assets, [[always-use-design-tokens-not-magic-values]].

**Enforced by:** review, CSP (blocks unhanded inline), CI build.
