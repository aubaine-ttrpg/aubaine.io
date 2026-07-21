---
name: cross-consumer-consistency
description: Have every consumer import the shared web entry and only compose on top, never redefining tokens or brand values or adding a second theme toggle, with logo SVGs owned once in sigil.
paths: ["sigil/src/web.css"]
severity: should
---
# Cross-consumer consistency

**Rule:** `web.css` is the single entry Catalyst and Almanach import, and a consumer only composes on top of it. A consumer never redefines a `--color-*` semantic token or a `brand.css` value in its own stylesheet, and never invents a second theme toggle: the `data-theme` and `prefers-color-scheme` mechanism sigil ships is the only one, and a consumer sets `data-theme` on `<html>` rather than building a parallel switch. Consumer CSS adds app-specific layout and one-off classes; it does not re-implement the shared layer. The logo SVGs live once, in `src/brand/logo/`, and each consumer's bundler emits them from there; a consumer does not keep its own copy of a mark.

**Why:** one entry plus compose-only is what makes two surfaces look like one product instead of two look-alikes that drift apart (docs/adr/0001-shared-design-system-sigil.md). A redefined token or a second toggle is a fork of the design system that the shared package can no longer keep in step, and it breaks the token swap that both themes depend on ([[tokens-single-source-of-truth]]). One home for the logo files means a mark changes in one place and both consumers follow; a copied SVG goes stale. Importing the shared entry by package name is the contract each consumer routes through its own bundler (frontend/route-all-css-and-js-through-encore).

**Good / Bad:**
```css
/* Bad (a consumer stylesheet): redefining a shared token and forking the theme. */
:root { --color-accent: #6d28d9; }
[data-mode='night'] { --color-bg: #000; }   /* a second, incompatible toggle */

/* Good (a consumer stylesheet): import the shared entry, then compose only. */
@import '@aubaine/sigil/web.css';
.book-editor__grid { display: grid; gap: var(--space-4); }
```

**See also:** [[tokens-single-source-of-truth]], [[stable-public-api-and-versioning]], frontend/route-all-css-and-js-through-encore.

**Enforced by:** review.
