---
name: always-keep-focus-visible-and-keyboard-operable
description: Everything interactive is keyboard operable with a logical focus order and a visible focus indicator.
paths: ["templates/**", "assets/**", "tests/e2e/**"]
severity: must
---
# Always keep focus visible and keyboard operable

**Rule:** Everything interactive is reachable and operable by keyboard (Tab, Enter, Space, Esc) in a logical focus order, and the focus indicator stays visible. Never `outline: none` without a replacement. Modals and dialogs trap focus while open and restore it to the trigger on close.

**Why:** Keyboard-only and screen-reader users navigate by focus, so an invisible or trapped focus makes the UI unusable. Criteria: WCAG 2.1.1 Keyboard, 2.4.7 Focus Visible. Build interactions as progressive enhancement so they work before JavaScript loads, see frontend/always-progressive-enhance-with-stimulus.

**Good / Bad:**
```css
/* Bad: kills the focus indicator everywhere */
*:focus { outline: none; }

/* Good: clear, token-driven focus ring */
:focus-visible {
  outline: 2px solid var(--color-focus-ring);
  outline-offset: 2px;
}
```

**Enforced by:** axe plus a keyboard-navigation test in e2e.
