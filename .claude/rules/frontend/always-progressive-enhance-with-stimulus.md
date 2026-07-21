---
name: always-progressive-enhance-with-stimulus
description: Pages work server-rendered without JS; Stimulus controllers enhance behavior and are never required for core function.
paths: ["catalyst/templates/**", "catalyst/assets/**", "catalyst/assets/controllers/**"]
severity: should
---
# Always progressive-enhance with Stimulus

**Rule:** Every page works server-rendered with JavaScript off. Forms submit, links navigate. Stimulus controllers enhance that baseline, they do not replace it.

**Why:** progressive enhancement keeps the app usable when JS fails to load and for assistive tech. A real `<form>` or `<a>` is keyboard-operable for free; a `div` with a click handler is not. It also keeps navigation as real links, which is what Swup needs to swap the shell and cross-fade the stage (docs/adr/0002-page-transitions-and-play-once-intro.md); Stimulus and Live Components reconnect on content replace.

**Good / Bad:**
```twig
{# Bad: only works if the JS click handler runs. #}
<div data-action="click->page#save">Enregistrer</div>

{# Good: a real form, Stimulus only enhances it. #}
<form method="post" action="{{ path('app_book_page_edit', {id: page.id}) }}" data-controller="page">
  <button type="submit">Enregistrer</button>
</form>
```

**See also:** accessibility/always-keep-focus-visible-and-keyboard-operable, [[keep-navigation-swup-friendly]].

**Enforced by:** review.
