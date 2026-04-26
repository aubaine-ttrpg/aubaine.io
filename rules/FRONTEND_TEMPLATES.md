---
name: FRONTEND_TEMPLATES
description: Server-side rendered frontend templates — Twig syntax (whitespace inside delimiters, operator spacing, snake_case variables and template paths, named-argument colons), Twig Components (PascalCase class names, `<twig:Component …/>` invocation), Live Components, the SSR-first rendering model (pages render server-side; Stimulus and Live Components add per-component interactivity, no SPA replacement), the props contract (templates receive scalars, value objects, and view-model arrays — not raw entities), and the presentation/domain split (Twig carries presentation logic only). Applies when writing or editing `.html.twig` files, defining or invoking a Twig or Live Component, naming a partial (`_user_card.html.twig`), or writing a macro.
---

# Frontend Templates

Pages render server-side. Twig is the rendering path for every view; Twig Components and Live Components build on that base. This rule covers both the editorial conventions (whitespace, naming) and the boundary between what belongs in a template and what stays in PHP.

Syntax conventions distilled from the upstream Twig standard (<https://twig.symfony.com/doc/3.x/coding_standards.html>). They govern every `.html.twig` file in the project, including Twig Component and Live Component templates.

## Rendering

Pages render server-side. Interactive behavior is added per-component through Twig Live Components; client-side navigation does not replace server rendering.

## Presentation and domain

Templates carry presentation logic only. Domain decisions, query building, and state changes stay in PHP. Twig Components receive pre-computed props — scalars, value objects, and view-model arrays — not raw entities.

## Whitespace inside delimiters

Exactly one space after `{{`, `{%`, `{#` and before `}}`, `%}`, `#}`, when the content is non-empty:

```twig
{{ user }}
{# comment #}
{% if user %}{% endif %}
```

Whitespace-control markers (`-`) sit flush against the delimiter:

```twig
{{- user -}}
{%- if user -%}
{#- comment -#}
```

## Spaces around operators

One space before and after:

- Comparison: `==`, `!=`, `<`, `>`, `>=`, `<=`
- Math: `+`, `-`, `/`, `*`, `%`, `//`, `**`
- Logic: `not`, `and`, `or`
- Concatenation: `~`
- Tests and membership: `is`, `in`
- Ternary: `?:`

```twig
{{ 1 + 2 }}
{{ first_name ~ ' ' ~ last_name }}
{{ is_correct ? 'yes' : 'no' }}
```

One space after `:` in mappings and after `,` in sequences and mappings:

```twig
[1, 2, 3]
{'name': 'Fabien'}
```

## No spaces around these

No spaces around `.`, `|`, `[]`, `..`, parentheses in expressions, string delimiters, or the parentheses of filter and function calls:

```twig
{{ name|upper|lower }}          {# not name | upper | lower #}
{{ user.name }}                 {# not user . name #}
{{ user[name] }}                {# not user [ name ] #}
{% for i in 1..12 %}{% endfor %} {# not 1 .. 12 #}
{{ 1 + (2 * 3) }}               {# not ( 2 * 3 ) #}
{{ 'Twig' }}                    {# not ' Twig ' #}
{{ name|default('Fabien') }}    {# not default ('Fabien') #}
[1, 2, 3]                       {# not [ 1, 2, 3 ] #}
```

## Naming

Variables, functions, filters, tests, macro argument names, and named arguments use `snake_case` — both at the call site and in the declaration. This applies equally to names provided by the application and names defined inside templates:

```twig
{% set first_name = 'Fabien' %}
{{ 'Fabien'|to_lower_case }}
{{ generate_random_number() }}
{% macro html_input(class_name) %}{% endmacro %}
{{ html_input(class_name: 'pwd') }}
```

## Macro and named arguments

One space before and after `=` in macro argument defaults:

```twig
{% macro html_input(class = 'input') %}{% endmacro %}
```

Named arguments are passed with `:` (not `=`), with one space after the `:`:

```twig
{{ html_input(class: 'input') }}
{{ data|convert_encoding(from: 'iso-2022-jp', to: 'UTF-8') }}
```

## Indentation

Tag contents follow the indentation of the target language (HTML, plain text, etc.):

```twig
{% block content %}
    {% if true %}
        true
    {% endif %}
{% endblock %}
```

## File and fragment naming

Template file names use `snake_case.html.twig`, per Symfony best practices. Partial templates intended for `{% include %}` or `{{ include() }}` are prefixed with an underscore: `_user_card.html.twig`, `_form_fields.html.twig`.

## Twig Components

Component class names are `PascalCase` (e.g. `SkillPlate`) and are invoked as `<twig:SkillPlate …/>`. Component template bodies follow every rule above: `snake_case` variables, standard whitespace, standard delimiter spacing.
