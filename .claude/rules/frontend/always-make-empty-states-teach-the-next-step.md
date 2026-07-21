---
name: always-make-empty-states-teach-the-next-step
description: Every collection, list, and dashboard view ships a first-class empty and zero-result state that explains why it is empty and offers the primary next action, never a blank page.
paths: ["catalyst/templates/**", "catalyst/assets/**", "catalyst/src/**"]
severity: should
---
# Always make empty states teach the next step

**Rule:** Every collection, list, and dashboard view renders a first-class **empty state** (and a distinct zero-results state for filtered or searched views) that names *why* it is empty and offers the **primary next action**. Build it from a shared empty-state partial so every list looks and behaves the same. Pair it with clear loading and error states. The empty state teaches; it is never a blank page or a bare "No data".

**Why:** the empty state is the first thing an author sees in a fresh list of books, pages, or skill trees. It is where they learn the next step without a tour. This complements [[always-give-user-feedback]] (which governs *action* feedback: pending, success, error); it does not duplicate it.

**Good / Bad:**
```twig
{# Bad: the list renders nothing, so a new author sees a blank screen and is stuck. #}
{% for book in books %}{{ book.title }}{% endfor %}

{# Good: a teaching empty state with the primary next action. #}
{% if books is empty %}
    <div class="empty-state">
        <h2>{{ 'book.empty.title'|trans }}</h2>       {# « Aucun livre pour l'instant » #}
        <p>{{ 'book.empty.body'|trans }}</p>          {# « Creez votre premier livre pour commencer. » #}
        <a class="btn btn--primary" href="{{ path('app_book_new') }}">{{ 'book.empty.create'|trans }}</a>
    </div>
{% else %}
    {# ... the list ... #}
{% endif %}
```

**See also:** [[always-give-user-feedback]] (action feedback: pending/success/error), [[always-use-design-tokens-not-magic-values]], accessibility/always-meet-wcag-aa-and-rgaa (the empty state is axe-clean), i18n/never-hardcode-user-facing-strings (FR and EN copy via translation keys, run through the aubaine-content-writer skill).

**Enforced by:** review + axe (the empty state renders its primary action, is keyboard-operable and axe-clean).
