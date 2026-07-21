---
name: keep-navigation-swup-friendly
description: Drive navigation with real server-rendered links so Swup can swap the shell and Stimulus and Live Components reconnect; never build a client-side router.
paths: ["catalyst/templates/**", "catalyst/assets/**"]
severity: should
---
# Keep navigation Swup-friendly

**Rule:** Navigate with real `<a href>` links so Swup intercepts them, swaps the shell containers, and cross-fades the stage. Opt an individual link out with `data-no-swup` where a full document load is needed (the PDF download, the print view). Never build a client-side router that Swup cannot see.

**Why:** docs/adr/0002-page-transitions-and-play-once-intro.md drives continuous page transitions with Swup: it swaps the crumbs, language switcher, rail, sidebar, and stage while the theme and chrome persist. A custom client-side router bypasses Swup, so the shell never swaps and Stimulus and Live Components are left disconnected. Real links stay keyboard-operable and work with JS off, which is the [[always-progressive-enhance-with-stimulus]] baseline.

**Good / Bad:**
```twig
{# Bad: a client-side route Swup cannot drive. #}
<a href="#" data-spa-route="/books">Livres</a>

{# Good: a real link Swup swaps in place, and a full-load link opting out. #}
<a href="{{ path('app_book_index') }}">Livres</a>
<a href="{{ path('app_book_pdf', {id: book.id, download: 1}) }}" data-no-swup>Telecharger le PDF</a>
```

**See also:** [[always-progressive-enhance-with-stimulus]], [[clean-up-stimulus-on-disconnect]].

**Enforced by:** review.
