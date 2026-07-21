---
name: respect-reduced-motion
description: Ship a prefers-reduced-motion escape with every sigil animation and transition, and keep the intro effect a progressive enhancement the page works without.
paths: ["sigil/src/fx/**", "sigil/src/brand/logo.css", "sigil/src/base/reset.css"]
severity: must
---
# Respect reduced motion

**Rule:** Every animation and transition, in CSS and in JS, ships a `prefers-reduced-motion: reduce` escape that stops it. In CSS, add a `@media (prefers-reduced-motion: reduce)` block that sets `animation: none` / `transition: none` on the effect (the reset already neutralises motion globally; effect files still cut their own keyframed animations). In JS, read `window.matchMedia('(prefers-reduced-motion: reduce)')` and skip straight to the end state instead of animating. The light-to-void intro is a progressive enhancement: the page is fully usable with `fx/blackhole.js` absent or disabled, so nothing behind the overlay depends on the animation running or completing.

**Why:** motion can trigger vestibular disorders, so an animation that ignores the user's stated preference is an accessibility failure (WCAG 2.3.3 Animation from Interactions, 2.2.2 Pause Stop Hide). The reduced-motion branch in `mountBlackhole` reveals the page immediately, which is exactly why the intro must be enhancement, not a gate: if the effect never ran, the content is still there. Effect-local escapes back up the global reset for the keyframed pieces the global rule only shortens.

**Good / Bad:**
```css
/* Bad: a keyframed effect with no escape; it runs regardless of the preference. */
.ab-intro__star { animation: ab-pulse 2.4s ease-in-out infinite; }

/* Good: the effect file cuts its own motion under the preference. */
.ab-intro__star { animation: ab-pulse 2.4s ease-in-out infinite; }
@media (prefers-reduced-motion: reduce) {
    .ab-intro, .ab-intro * { animation: none !important; transition: none !important; }
}
```
```js
// Good: the JS engine honours the preference and jumps to the revealed state.
if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    finish();          // reveal the page now, no collapse or bloom
    return;
}
```

**See also:** [[css-architecture-bem-and-tokens]], frontend/always-progressive-enhance-with-stimulus, accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** review.
