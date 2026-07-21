---
name: always-sanitize-user-html
description: Sanitize any user-supplied HTML with HTMLPurifier on an allow-list before storage and render, and never |raw untrusted content.
paths: ["catalyst/src/**", "catalyst/templates/**/*.html.twig"]
severity: must
---
# Always sanitize user HTML

**Rule:** Any HTML authored in the GrapesJS live book editor, or pasted into a Book or Page, passes through HTMLPurifier with an allow-list before it is stored and before it is rendered. Never pipe that content through Twig's `|raw`; leave Twig autoescaping on, which is the default.

**Why:** OWASP Top 10 A03 (Injection), stored XSS. Book and Page HTML is saved as content of record, then rendered in the editor preview, exported to the content JSON, and published to the public site and PDFs. `|raw` on unsanitized markup runs whatever `<script>` or `onerror` handler slipped in, in a reader's browser. An allow-list strips scripts and event handlers while keeping the layout the author built. See catalyst/docs/adr/0002-live-book-editor for the editor design.

**Good / Bad:**
```twig
{# Bad:  renders authored markup verbatim #}
{{ pageHtml|raw }}

{# Good:  purified server-side, then rendered #}
{{ purifier.purify(pageHtml)|raw }}  {# allow-list applied, scripts stripped #}
```

**See also:** [[always-set-security-headers-and-csp]], [[always-validate-input-server-side]].

**Enforced by:** review + functional test (a `<script>` in the payload is absent from the rendered output).
