---
name: never-assume-a-single-os-or-browser
description: The app reads and behaves correctly on every target OS and browser; show OS-correct affordances, feature-detect browser APIs with fallbacks, and verify across the browserslist matrix.
paths: ["catalyst/templates/**", "catalyst/assets/**"]
severity: should
---
# Never assume a single OS or browser

**Rule:** Catalyst is a web authoring tool used on macOS, Windows, and Linux, across Chrome, Firefox,
Safari, and Edge. Do not bake in one platform's or browser's assumptions:
- **OS affordances:** show the OS-correct keyboard shortcut (Cmd K on macOS, Ctrl K elsewhere) and handle
  both `metaKey` and `ctrlKey`; never hardcode a Cmd-only or Ctrl-only label or handler. Detect the
  platform once (`navigator.userAgentData` / `navigator.platform`) and render accordingly.
- **Browser APIs:** feature-detect before using a non-universal JS API and provide a graceful fallback;
  never depend on a Chrome-only API. Use `Intl` for date and number formatting rather than environment
  defaults.
- **CSS:** write standard CSS and let [[use-browserslist-and-autoprefixer]] add prefixes for the target
  matrix; never ship a `-webkit-`-only style with no fallback, and never tune a layout for one engine.
- **Verify across engines:** check a change against the browsers in `catalyst/.browserslistrc` before
  shipping, not on the developer's one browser.

**Why:** the developer's machine (often macOS plus Chrome) must not leak into the product. A Windows
author told to "Press Cmd K", or a Safari user handed a broken layout, is a real and avoidable failure.
Autoprefixer covers most CSS parity; this rule covers what it does not: OS hints, JS feature parity, and
actually checking the other engines.

**Good / Bad:**
```twig
{# Bad: a macOS-only hint shown to everyone. #}
<kbd>Cmd K</kbd> to search books

{# Good: platform-aware label (computed server-side or by a Stimulus controller). #}
<kbd data-controller="shortcut-hint" data-shortcut-hint-keys-value="k">Ctrl K</kbd>
```
```js
// Bad: only Cmd works, so Windows and Linux users cannot trigger it.
if (event.metaKey && event.key === 'k') { openSearch(); }
// Good: accept either modifier.
if ((event.metaKey || event.ctrlKey) && event.key === 'k') { openSearch(); }
```

**See also:** [[use-browserslist-and-autoprefixer]], [[always-progressive-enhance-with-stimulus]], [[route-all-css-and-js-through-encore]], accessibility/always-keep-focus-visible-and-keyboard-operable.

**Enforced by:** review.
