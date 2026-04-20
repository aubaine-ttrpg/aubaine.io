---
name: 03-twig-coding-standards
description: Twig coding standards (whitespace, naming, delimiters). Applies when writing or editing `.html.twig` templates, Twig components, Live components, or Twig macros.
---

# Rule 03 — Twig Coding Standards

Distilled from the upstream standard (<https://twig.symfony.com/doc/3.x/coding_standards.html>). Apply to every `.html.twig` file — including Twig Component and Live Component templates.

## Whitespace inside delimiters

Exactly one space after `{{`, `{%`, `{#` and before `}}`, `%}`, `#}`, when the content is non-empty.

```twig
{{ user }}
{# comment #}
{% if user %}{% endif %}
```

With whitespace control (`-`), no space between the dash and the delimiter:

```twig
{{- user -}}
{%- if user -%}
{#- comment -#}
```

## Spaces around operators

One space before and after: comparison (`==`, `!=`, `<`, `>`, `>=`, `<=`), math (`+`, `-`, `/`, `*`, `%`, `//`, `**`), logic (`not`, `and`, `or`), concatenation `~`, `is`, `in`, and the ternary `?:`.

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

Do not add spaces around: `.`, `|`, `[]`, `..`, parentheses in expressions, string delimiters, or the parentheses of filter/function calls.

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

Use `snake_case` for variables, functions, filters, tests, macro argument names, and named arguments — both when calling and when declaring. Applies to names provided by the application and names defined inside templates.

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

When calling, use `:` (not `=`) to pass named arguments; one space after the `:`:

```twig
{{ html_input(class: 'input') }}
{{ data|convert_encoding(from: 'iso-2022-jp', to: 'UTF-8') }}
```

## Indentation

Indent the contents of tags using the same indentation as the target language of the rendered template (HTML, plain text, etc.):

```twig
{% block content %}
    {% if true %}
        true
    {% endif %}
{% endblock %}
```

## File and fragment naming

Per Symfony best practices, template file names use `snake_case.html.twig`. Prefix partial templates (fragments intended for `{% include %}` / `{{ include() }}`) with an underscore: `_user_card.html.twig`, `_form_fields.html.twig`.

## Twig Components

Component class names are `PascalCase` (e.g. `SkillPlate`), invoked as `<twig:SkillPlate …/>`. Component templates still follow the rules above for their body — snake_case variables inside, standard whitespace, etc.
