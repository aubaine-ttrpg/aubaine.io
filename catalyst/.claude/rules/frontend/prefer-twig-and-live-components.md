---
name: prefer-twig-and-live-components
description: Build UI server-side with Twig plus Symfony UX Live Components and Turbo; do not reach for a heavy JS framework.
paths: ["templates/**", "assets/**", "src/**/Twig/**", "src/**/Component/**"]
severity: prefer
---
# Prefer Twig and Live Components

**Rule:** Render UI on the server with Twig and Symfony UX Live Components, navigate with Turbo. Reach for a Live Component before you reach for a client-side framework. Hotwire suffices.

**Why:** "No heavy frontend framework" is an Athletis anti-pattern (ADR 0001). A second framework duplicates state and routing the server already owns, adds a build and a bundle, and pulls logic off the tested PHP layer. Live Components give reactive UI without that cost.

**Good / Bad:**
```twig
{# Bad - a React widget for a live-updating list #}
<div id="payments-root"></div>
<script src="/build/payments-widget.js"></script>

{# Good - a Live Component, state stays server-side #}
<twig:PaymentsList :clientId="client.id" />
```

**See also:** [[always-progressive-enhance-with-stimulus]].

**Enforced by:** review.
