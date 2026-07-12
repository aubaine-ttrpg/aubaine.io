---
name: always-make-empty-states-teach-the-next-step
description: Every collection, list, and dashboard view ships a first-class empty/zero-result state that explains why it is empty and offers the primary next action, via the shared EmptyState component, never a blank page.
paths: ["templates/**", "assets/**", "src/**"]
severity: should
---
# Always make empty states teach the next step

**Rule:** Every collection, list, and dashboard view renders a first-class **empty state** (and a distinct zero-results state for filtered/searched views) that names *why* it is empty and offers the **primary next action**, built from the shared `EmptyState` component (ADR 0008, ATHLETIS-008). Pair it with clear loading and error states. The empty state teaches; it is never a blank page or a bare "No data".

**Why:** For a self-serve SaaS the empty state *is* the onboarding surface. It is where a new coach learns what to do without a product tour. We deliberately chose great UI + teaching empty states + the 3-step wizard (ATHLETIS-027) and dashboard onboarding cards (ATHLETIS-024) over heavier guidance machinery, so the empty state has to carry that weight. This complements `always-give-user-feedback` (which governs *action* feedback). It does not duplicate it.

**Good / Bad:**
```twig
{# Bad: the list renders nothing, so a new coach sees a blank screen and is stuck. #}
{% for client in clients %}{{ client.name }}{% endfor %}

{# Good: a teaching empty state with the primary next action. #}
{% if clients is empty %}
    <twig:EmptyState
        title="{{ 'clients.empty.title'|trans }}"          {# « Aucun client pour l'instant » #}
        body="{{ 'clients.empty.body'|trans }}"             {# « Ajoutez votre premier client ou importez un CSV. » #}
        primaryHref="{{ path('client_new') }}"  primaryLabel="{{ 'clients.empty.add'|trans }}"
        secondaryHref="{{ path('client_import') }}"  secondaryLabel="{{ 'clients.empty.import'|trans }}" />
{% else %}
    {# … the list … #}
{% endif %}
```

**See also:** [[always-give-user-feedback]] (action feedback: pending/success/error), [[always-use-design-tokens-not-magic-values]], accessibility/always-meet-wcag-aa-and-rgaa (the empty state is axe-clean), i18n/* (FR + EN copy via translation keys, run through the ai-tell-remover).

**Enforced by:** review (the spec-checklist UX item: empty / loading / error states designed, not just the happy path) + the view's e2e (the empty state renders its primary CTA, is keyboard-operable and axe-clean).
