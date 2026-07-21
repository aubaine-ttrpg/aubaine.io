---
name: always-keep-focus-visible-and-keyboard-operable
description: Everything interactive is keyboard operable with a logical focus order and a visible focus indicator.
paths: ["catalyst/templates/**", "catalyst/assets/**", "almanach/src/**/*.astro"]
severity: must
---
# Always keep focus visible and keyboard operable

**Rule:** Everything interactive is reachable and operable by keyboard (Tab, Enter, Space, Esc) in a logical focus order, and the focus indicator stays visible and unobscured. Never set `outline: none` without a replacement ring. Dialogs and menus trap focus while open and restore it to the trigger on close.

**Why:** Keyboard-only and screen-reader users navigate by focus, so an invisible, trapped, or hidden-behind-a-sticky-header focus makes the UI unusable. Criteria: WCAG 2.2 2.1.1 Keyboard, 2.4.7 Focus Visible, 2.4.11 Focus Not Obscured (Minimum). Build interactions as progressive enhancement so they work before JavaScript loads (see frontend/always-progressive-enhance-with-stimulus).

**Good / Bad:**
```css
/* Bad: kills the focus indicator everywhere */
*:focus { outline: none; }

/* Good: clear focus ring driven by a sigil token */
:focus-visible {
  outline: 2px solid var(--color-focus-ring);
  outline-offset: 2px;
}
```

**See also:** [[always-meet-wcag-aa-and-rgaa]], [[always-use-correct-aria-roles]], frontend/always-progressive-enhance-with-stimulus, frontend/always-use-design-tokens-not-magic-values.

**Enforced by:** axe (Playwright end-to-end) plus a keyboard-navigation assertion, and review.
