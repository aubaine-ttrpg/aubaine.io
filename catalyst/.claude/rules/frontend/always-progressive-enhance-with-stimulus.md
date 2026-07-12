---
name: always-progressive-enhance-with-stimulus
description: Pages work server-rendered without JS; Stimulus controllers enhance behavior; they are never required for core function.
paths: ["templates/**", "assets/**", "assets/controllers/**"]
severity: should
---
# Always progressive-enhance with Stimulus

**Rule:** Every page works server-rendered with JavaScript off. Forms submit, links navigate. Stimulus controllers enhance that baseline, they do not replace it.

**Why:** Progressive enhancement keeps the app usable when JS fails to load, on flaky mobile, and for assistive tech. A real `<form>` or `<a>` is keyboard-operable and crawlable for free; a `div` with a click handler is none of those. It also keeps navigation Turbo-driven, which is what Hotwire Native needs.

**Good / Bad:**
```twig
{# Bad - only works if the JS click handler runs #}
<div data-action="click->cart#submit">Pay</div>

{# Good - a real form, Stimulus only enhances it #}
<form method="post" action="{{ path('checkout') }}" data-controller="cart">
  <button type="submit">Pay</button>
</form>
```

**See also:** accessibility/always-keep-focus-visible-and-keyboard-operable, [[keep-app-native-ready]].

**Enforced by:** review.
