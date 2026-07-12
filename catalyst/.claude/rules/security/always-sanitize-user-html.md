---
name: always-sanitize-user-html
description: Sanitize any user-supplied HTML with HTMLPurifier on an allow-list before storage and render, and never |raw untrusted content.
paths: ["src/**", "templates/**/*.html.twig"]
severity: must
---
# Always sanitize user HTML

**Rule:** Any user-supplied HTML, including GrapesJS coach-site content, passes through HTMLPurifier with an allow-list before it is stored and before it is rendered. Never pipe untrusted content through Twig's `|raw`; leave Twig autoescaping on, which is the default.

**Why:** OWASP Top 10 A03 (Injection), stored and reflected XSS. `|raw` on attacker-controlled HTML runs their script in a coach's or visitor's browser, hijacking sessions and forms. An allow-list strips scripts and event handlers while keeping the layout the coach built.

**Good / Bad:**
```twig
{# Bad:  renders attacker markup verbatim #}
{{ userHtml|raw }}

{# Good:  purified server-side, then rendered #}
{{ purifier.purify(userHtml)|raw }}  {# allow-list applied, scripts stripped #}
```

**Enforced by:** review + functional test (a `<script>` in the payload is absent from the rendered output).
