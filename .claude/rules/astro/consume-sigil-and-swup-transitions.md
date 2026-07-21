---
name: consume-sigil-and-swup-transitions
description: Depend on @aubaine/sigil via file:../sigil, import only its published exports, and wire the intro and navigation with sigil's fx module plus Swup, not astro:transitions.
paths: ["almanach/src/**", "almanach/package.json", "almanach/astro.config.*"]
severity: must
---
# Consume Sigil and Swup transitions

**Rule:**
- Depend on the shared design system with `"@aubaine/sigil": "file:../sigil"`.
- Import only its published exports: `@aubaine/sigil/web.css`, `@aubaine/sigil/tokens/*`, `@aubaine/sigil/fx/*`.
- Never redefine a sigil token, and never hardcode a brand or game-data colour.
- Wire the play-once intro and page navigation with sigil's `fx/blackhole.js` plus `fx/page-transition.css` and Swup, exactly as docs/adr/0002-page-transitions-and-play-once-intro.md specifies.
- Do not use Astro's `astro:transitions` (`<ClientRouter />`, `transition:*`).

**Why:** One design system owns the brand so both sites read as one product (docs/adr/0001-shared-design-system-sigil.md); redefining a token or naming a hex forks the brand and breaks the light and dark swap (frontend/always-use-design-tokens-not-magic-values, frontend/always-support-light-and-dark-mode). The entrance and navigation are a monorepo-level decision, not a per-app choice: docs/adr/0002-page-transitions-and-play-once-intro.md names Swup plus the sigil fx module as the shared layer, with the intro controller mounted outside the swap container so it does not replay between pages. `astro:transitions` is a separate client router that would bypass that contract and fight or replay the intro.

**Good / Bad:**
```astro
---
// Bad: Astro's own view transitions, and a redefined sigil token.
import { ClientRouter } from 'astro:transitions';
---
<ClientRouter />
<style>:root { --color-accent: #8a5cf6; }</style>
```
```astro
---
// Good: sigil's published exports, Swup, and the sigil intro module (ADR 0002).
import '@aubaine/sigil/web.css';
import '@aubaine/sigil/fx/page-transition.css';
---
<script>
  import '@aubaine/sigil/fx/blackhole.js';   // play-once intro, mounted on <html>, outside the swap
  import Swup from 'swup';
  new Swup();                                // container swap, not astro:transitions
</script>
```
```js
// Good: package.json depends on sigil through the local file path.
"dependencies": { "@aubaine/sigil": "file:../sigil" }
```

**See also:** [[static-first-and-island-discipline]], [[print-friendly-pages]], frontend/keep-navigation-swup-friendly, frontend/always-use-design-tokens-not-magic-values, frontend/always-support-light-and-dark-mode.

**Enforced by:** astro build + review.
