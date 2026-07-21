---
name: optimize-images-and-fonts
description: Serve images through astro:assets with explicit dimensions and modern formats, lazy-load below the fold, preload the LCP image, and self-host the sigil fonts with font-display swap.
paths: ["almanach/src/**", "almanach/astro.config.*"]
severity: should
---
# Optimize images and fonts

**Rule:**

- Render every content image with the `astro:assets` `<Image>` or `<Picture>` component, always with `width` and `height`, so the browser reserves the box and the page does not shift. Let Astro emit modern formats (`avif`, `webp`).
- Lazy-load images below the fold with `loading="lazy"`. Load the one largest above-the-fold image eagerly and preload it (`<link rel="preload" as="image">`) so it paints first.
- Self-host the font files behind sigil's role tokens and declare each `@font-face` with `font-display: swap`. Do not link a third-party font stylesheet.

**Why:** an `<Image>` with fixed dimensions holds Cumulative Layout Shift near zero, and preloading the largest image improves Largest Contentful Paint (both are Core Web Vitals). `font-display: swap` (CSS Fonts Module) renders text in a fallback immediately instead of blocking paint with invisible text. Self-hosting drops a third-party origin, which is faster and keeps the strict Content-Security-Policy tight with no extra host to allow (security/always-set-security-headers-and-csp). Sigil owns the font role tokens and each surface owns its own `@font-face` loading (docs/adr/0001-shared-design-system-sigil.md), so almanach self-hosts the families those tokens name.

**Good / Bad:**
```astro
---
// Bad: raw <img> with no dimensions (the layout jumps), render-blocking third-party font.
---
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Spectral" />
<img src="/cover.png" alt="Cover" />
```
```astro
---
// Good: astro:assets with fixed dimensions; preload and eagerly load the LCP image.
import { Image, getImage } from 'astro:assets';
import cover from '../assets/cover.png';
import thumb from '../assets/thumb.png';
const lcp = await getImage({ src: cover, width: 640, height: 960, format: 'avif' });
---
<link rel="preload" as="image" href={lcp.src} />
<Image src={cover} alt="The Great Codex cover" width={640} height={960} loading="eager" fetchpriority="high" />
<Image src={thumb} alt="Skill card thumbnail" width={160} height={240} loading="lazy" />
```
```css
/* Good: self-hosted font, swap so text paints immediately in a fallback. */
/* The family name matches the sigil role token --font-body; sigil owns the token, almanach owns the loading. */
@font-face {
  font-family: 'Spectral';
  src: url('/fonts/spectral.woff2') format('woff2');
  font-display: swap;
}
```

**See also:** [[keep-the-javascript-budget-near-zero]], frontend/always-use-design-tokens-not-magic-values, security/always-set-security-headers-and-csp, http-and-caching/fingerprint-and-immutable-static-assets.

**Enforced by:** Lighthouse (LCP and CLS) + review.
