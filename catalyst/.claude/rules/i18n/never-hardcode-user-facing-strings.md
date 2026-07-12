---
name: never-hardcode-user-facing-strings
description: Every user-facing string must be a translation key resolved through Symfony Translation, never inline literal text.
paths: ["templates/**", "src/**", "translations/**"]
severity: must
---
# Never hardcode user-facing strings
**Rule:** Every user-facing string (UI labels, buttons, emails, errors, flash and validation messages) goes through Symfony Translation as a message key, present in both the FR and EN catalogs under `translations/`. Use `|trans` in Twig and `TranslatorInterface` in PHP. Keep keys descriptive (`client.create.title`), never English text as the key. Use ICU for plurals and placeholders.

**Why:** The app is bilingual FR (primary) / EN, and inline literals break one language and slip past review (ADR 0010, Symfony Translation best practice). Microcopy is run through the [athletis-ai-tell-remover](../../skills/athletis-ai-tell-remover/SKILL.md) skill, so placeholders (`%name%`, `{{ count }}`, ICU `{n, plural, ...}`) must be preserved byte-for-byte. Form control labels are translated too, see [always-label-form-controls](../accessibility/always-label-form-controls.md).

**Good / Bad:**
```twig
{# Bad #}
<button>Save</button>

{# Good #}
<button>{{ 'action.save'|trans }}</button>
```
```php
// Bad
$this->addFlash('success', 'Client saved');
// Good
$this->addFlash('success', $this->translator->trans('client.flash.saved'));
```
```yaml
# translations/messages.fr.yaml
action.save: Enregistrer
client.flash.saved: Le client a bien été enregistré.
client.count: "{count, plural, one {# client} other {# clients}}"
# translations/messages.en.yaml
action.save: Save
client.flash.saved: Client saved.
client.count: "{count, plural, one {# client} other {# clients}}"
```

**Enforced by:** review, plus a missing-translation-key test (`translation:lint`-style check) asserting every key exists in both FR and EN catalogs.
