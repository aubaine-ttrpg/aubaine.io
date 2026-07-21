---
name: never-hardcode-user-facing-strings
description: Every user-facing string is a translation key present in both the FR and EN catalogs, never inline literal text, in catalyst and almanach alike.
paths: ["catalyst/templates/**", "catalyst/src/**", "catalyst/translations/**", "almanach/src/**"]
severity: must
---
# Never hardcode user-facing strings
**Rule:** Every user-facing string (UI labels, buttons, errors, flash and validation messages, nav and breadcrumb text) goes through a translation catalog as a message key, present in both the FR and EN catalogs. Keys are descriptive (`book.create.title`), never English text used as the key. Use ICU for plurals and placeholders.

- Catalyst: resolve keys through Symfony Translation, `|trans` in Twig and `TranslatorInterface` in PHP; catalogs live under `catalyst/translations/`.
- Almanach: resolve keys through Astro's i18n (a per-locale translations dictionary, plus `astro:i18n` for locale routing); keep the same key names as catalyst where a string is shared.

This rule covers interface chrome only. The rendered game text comes from `codex/` and `content/`, which are the single source, so almanach never restates or re-translates that content (see ai/never-create-drift).

**Why:** Both surfaces are bilingual FR (primary) / EN, and inline literals break one language and slip past review. Microcopy is run through the aubaine-content-writer skill (its `docs/no-ai-tells.md`), so placeholders (`%name%`, `{{ count }}`, ICU `{n, plural, ...}`) are preserved byte-for-byte. Form control labels are translated too, see accessibility/always-label-form-controls.

**Good / Bad:**
```twig
{# Bad (catalyst) #}
<button>Save</button>

{# Good (catalyst) #}
<button>{{ 'action.save'|trans }}</button>
```
```astro
---
// Bad (almanach)
---
<a href="/books">Books</a>

---
// Good (almanach): key from the active locale dictionary
import { t } from '../i18n';
---
<a href="/books">{t('nav.books')}</a>
```
```yaml
# catalyst/translations/messages.fr.yaml
action.save: Enregistrer
book.count: "{count, plural, one {# livre} other {# livres}}"
# catalyst/translations/messages.en.yaml
action.save: Save
book.count: "{count, plural, one {# book} other {# books}}"
```

**Enforced by:** review, plus a missing-translation-key check asserting every key exists in both FR and EN catalogs (a `translation:lint`-style check for catalyst, and the equivalent dictionary-parity check for almanach).
