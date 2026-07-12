---
name: keep-app-native-ready
description: Keep navigation Turbo-driven so it runs inside Hotwire Native, gate native-only affordances behind ux_is_native(), and cache views offline.
paths: ["templates/**", "assets/**", "native/**", "src/**"]
severity: should
---
# Keep the app native-ready

**Rule:** Drive navigation with Turbo links so it works unchanged inside the Hotwire Native shells. Gate native-only affordances behind `ux_is_native()`. Consider offline service-worker caching for the view and its data. There is no JSON API: native loads server-rendered Turbo with the session cookie.

**Why:** ADR 0007 (mobile strategy) keeps the app native-ready. A custom client-side router breaks Turbo's navigation, so the native shell cannot follow it. Turbo links plus a `ux_is_native()` gate keep one server-rendered codebase serving web and native.

**Good / Bad:**
```twig
{# Bad - a client-side router Turbo Native cannot drive #}
<a href="#" data-spa-route="/clients">Clients</a>

{# Good - a Turbo link, native affordance gated #}
<a href="{{ path('clients') }}">Clients</a>
{% if ux_is_native() %}<button data-controller="bridge--share">Share</button>{% endif %}
```

**See also:** [[always-progressive-enhance-with-stimulus]].

**Enforced by:** review.
