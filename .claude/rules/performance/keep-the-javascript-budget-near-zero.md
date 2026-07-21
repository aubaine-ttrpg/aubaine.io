---
name: keep-the-javascript-budget-near-zero
description: Ship no client framework for the content site, add a client directive only to a genuinely interactive island, and enable Astro prefetch tuned to coexist with Swup.
paths: ["almanach/src/**", "almanach/astro.config.*"]
severity: should
---
# Keep the JavaScript budget near zero

**Rule:**

- Almanach is a content site: render to static HTML and ship no client-side framework runtime. Do not add a framework to hydrate a whole page.
- Add a `client:*` directive only on a component that is genuinely interactive, and pick the narrowest one (`client:visible` or `client:idle`). Never put `client:load` on a component that renders fine as static HTML.
- Enable Astro's built-in `prefetch` in `astro.config`, tuned to coexist with Swup (docs/adr/0002-page-transitions-and-play-once-intro.md): Swup owns the navigation swap, so keep prefetch to a hover or viewport hint that only warms the cache, and do not add a second router. Reserve space for any deferred content so nothing shifts.

**Why:** an Astro content site is HTML by default. A client framework adds a runtime and a hydration cost to pages that only need to be read, which hurts Total Blocking Time and Largest Contentful Paint (Core Web Vitals). Islands keep the shipped JavaScript proportional to real interactivity. ADR 0002 makes Swup the navigation layer, so prefetch stays a passive hint rather than a competing click handler. Not inserting content late keeps Cumulative Layout Shift near zero, matching [[optimize-images-and-fonts]].

**Good / Bad:**
```js
// Bad: a whole client framework for pages that only display content.
import { defineConfig } from 'astro/config';
import react from '@astrojs/react';
export default defineConfig({ integrations: [react()] });
```
```js
// Good: no framework; enable prefetch as a hover hint that coexists with Swup.
import { defineConfig } from 'astro/config';
export default defineConfig({
  site: 'https://almanach.aubaine.io',
  prefetch: { prefetchAll: true, defaultStrategy: 'hover' },
});
```
```astro
---
// Bad: client:load on a component that never needed JS to render.
import SkillFilter from '../components/SkillFilter.jsx';
---
<SkillFilter client:load />
```
```astro
---
// Good: static HTML everywhere; hydrate only the one interactive island, lazily.
import SkillFilter from '../components/SkillFilter.jsx';
---
<SkillFilter client:visible />
```

**See also:** [[optimize-images-and-fonts]], frontend/keep-navigation-swup-friendly, frontend/always-progressive-enhance-with-stimulus.

**Enforced by:** Astro build output (shipped JS) + Lighthouse (Total Blocking Time) + review.
