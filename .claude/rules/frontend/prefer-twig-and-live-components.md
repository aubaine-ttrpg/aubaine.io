---
name: prefer-twig-and-live-components
description: Build UI server-side with Twig plus Symfony UX Live Components and Swup; do not reach for a heavy JS framework.
paths: ["catalyst/templates/**", "catalyst/assets/**", "catalyst/src/Twig/**"]
severity: prefer
---
# Prefer Twig and Live Components

**Rule:** Render UI on the server with Twig and Symfony UX Live Components, navigate with Swup. Reach for a Live Component before you reach for a client-side framework.

**Why:** a second framework duplicates state and routing the server already owns, adds a build and a bundle, and pulls logic off the tested PHP layer. Live Components give reactive UI without that cost. The live book editor is the first adoption of this stack (catalyst/docs/adr/0002-live-book-editor.md).

**Good / Bad:**
```twig
{# Bad: a React widget for a live-editing page list. #}
<div id="page-editor-root"></div>
<script src="/build/page-editor-widget.js"></script>

{# Good: a Live Component, state stays server-side. #}
<twig:PageEditor :bookId="book.id" />
```

**See also:** [[always-progressive-enhance-with-stimulus]].

**Enforced by:** review.
