# Foundation

Architectural overview of the Aubaine codebase — what's in place, how the pieces fit, and the conventions a new developer should internalize before writing code here.

## Stack at a glance

| Layer | Choice |
|---|---|
| Runtime | PHP 8.4 |
| Framework | Symfony 8 |
| ORM + DB | Doctrine ORM + SQLite (`db/app.db`, per-developer) |
| Templates | Twig + Twig Components + Live Components |
| Frontend | Stimulus (Symfony StimulusBundle) + Swup, served through AssetMapper |
| CSS | Tailwind CSS 4 via `symfonycasts/tailwind-bundle` (standalone binary — no Node toolchain) |
| Static analysis | PHPStan level 9 + `phpstan-symfony` / `phpstan-doctrine` / `phpstan-phpunit` |
| Tests | PHPUnit 13 in strict mode, random execution order |

Minimalism is the default: packages arrive only when a concrete feature needs them. The stack above is the current floor.

## Rules system

Project policy lives in [`rules/`](../rules). Every `.md` file in that folder is a mandatory directive and carries a YAML frontmatter block:

```yaml
---
name: NN-kebab-case-slug
description: One-sentence activation hint. States the domain and typical triggers.
---
```

The `description` is activation-oriented: it tells a reader when the rule applies, without having to open the file. There is no separate index — the frontmatter *is* the catalog. To know what rules exist, glob `rules/*.md` and read each frontmatter. To follow one, open its body.

Rule numbering is monotonic and never reused. `00-*.md` is reserved for meta-rules (rules about the rules system itself). Application rules start at `01`.

[`CLAUDE.md`](../CLAUDE.md) at repo root declares the folder mandatory and describes agent-specific behavior (commit rituals, documentation synthesis). It points to rules; it never restates their content.

## Commit convention

See [Rule 01](../rules/01-commit-convention.md). Every commit subject starts with one Gitmoji (the character, not the `:code:` form), a space, and an imperative sentence-case description:

```
✨ Add Skill entity with translatable name and description
```

Commits are single-responsibility and independently revertible. Unrelated changes split into separate commits (tests alongside features, `.gitignore` tweaks, refactors each get their own). Files are staged by explicit path.

## Testing

Tests mirror `src/` under [`tests/Unit/`](../tests/Unit), [`tests/Integration/`](../tests/Integration), and [`tests/Functional/`](../tests/Functional). Each directory is a separate PHPUnit test suite, so `make test CMD="--testsuite Unit"` works.

Strict mode is enforced in [`phpunit.dist.xml`](../phpunit.dist.xml): any PHP deprecation, notice, or warning fails the run; unexpected stdout fails the test; execution order is randomized. A test that passes deterministically but fails randomly is not a passing test.

Three rules carry the policy:

- [Rule 05 — Testing philosophy](../rules/05-testing-philosophy.md): when a test is worth writing, how to pick the right layer, and why test stability is non-negotiable.
- [Rule 06 — Test conventions](../rules/06-test-conventions.md): file layout, naming, `#[DataProvider]`/`#[Group]` attributes, `setUp` patterns, performance-test conventions.
- [Rule 07 — Test groups](../rules/07-test-groups.md): the canonical domain + concern group taxonomy.

Every test class carries at least one domain `#[Group]` drawn from Rule 07; concern groups layer on top.

## Static analysis

PHPStan runs at **level 9**, the strictest level this stack supports without producing noise. Configuration in [`phpstan.dist.neon`](../phpstan.dist.neon); scans `src/` and `tests/`.

Three first-party extensions give the analyzer framework awareness: `phpstan-symfony` (container, services, routes, parameters), `phpstan-doctrine` (entities, repositories, QueryBuilder), and `phpstan-phpunit` (assertion types, mock/stub returns). They auto-load via `phpstan/extension-installer`.

`tests/bootstrap.php` is excluded from analysis — it is recipe-generated and contains an intentional `method_exists` guard that the analyzer would otherwise flag as always-true.

## Pre-commit hook

[`.githooks/pre-commit`](../.githooks/pre-commit) runs PHPStan then PHPUnit whenever the staged diff touches anything PHP-adjacent: `src/`, `tests/`, `config/`, `templates/`, `migrations/`, `public/`, `assets/`, any `*.php` or `*.twig`, `composer.*`, the PHPStan or PHPUnit config, or any `.env*` file. Commits that touch only docs, rules, or the license skip the hook and land fast.

Install once per clone: `make hooks`. This wires `core.hooksPath` at `.githooks/` and marks the scripts executable. In emergencies, bypass with `git commit --no-verify`.

## Makefile

Six workflow targets:

| Target | Purpose |
|---|---|
| `all` | Default — prints `help`. |
| `help` | Lists every target and its `##`-annotated description. |
| `autophony` | Regenerates the `.PHONY` list from the current targets. |
| `dev` | `symfony server:start` — boots the local dev server. |
| `test` | `php bin/phpunit $(CMD)` — runs the test suite. |
| `hooks` | Installs `.githooks/`. |

## Frontend pipeline

AssetMapper is the asset pipeline. JavaScript modules live under [`assets/`](../assets), are declared in [`importmap.php`](../importmap.php), and are served without bundling in development. Stimulus is loaded via Symfony StimulusBundle. Swup is present in the importmap, ready to be wired when page transitions are needed.

CSS is Tailwind 4, imported once in [`assets/styles/app.css`](../assets/styles/app.css). The `symfonycasts/tailwind-bundle` ships the standalone Tailwind binary, so the CSS pipeline does not require Node. Build once with `php bin/console tailwind:build`; watch with `php bin/console tailwind:build --watch`.

[`templates/base.html.twig`](../templates/base.html.twig) wires both: the stylesheet via `asset('styles/app.css')` and the JS entrypoint via `importmap('app')`.

## Documentation layout

Two folders at the repo root, two audiences:

- **[`docs/`](.)** — technical documentation for developers (this page lives here). Architecture overviews and convention references. See [Rule 08](../rules/08-technical-documentation.md) for filename and structure guidelines.
- **[`wiki/`](../wiki)** — game-content documentation aimed at players, GMs, and system designers (TTRPG mechanics, math, design concepts).
