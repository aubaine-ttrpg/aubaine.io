---
name: 04-symfony-best-practices
description: Symfony framework best practices. Applies when scaffolding controllers, services, forms, routing, security, templates, configuration, parameters, translations, tests, or assets.
---

# Rule 04 — Symfony Best Practices

Distilled from the upstream guide (<https://symfony.com/doc/current/best_practices.html>). These conventions govern every controller, service, form, template, config file, and asset in the project.

## Project creation and layout

- New applications are created with the Symfony binary (`symfony new …`) and keep the default directory layout: `src/`, `config/`, `templates/`, `public/`, `tests/`, `migrations/`, `translations/`, `var/`, `vendor/`.
- Application code is organized by PHP namespace under `App\`. Internal code does not live in custom bundles; bundles are reserved for genuinely reusable, standalone packages.

## Configuration

- **Environment variables** (`.env`, `.env.local`, per-environment overrides) carry **infrastructure**: database URL, mailer DSN, Redis host, API endpoints. These values change per machine without changing application behavior.
- **Parameters** in `config/services.yaml` carry **application behavior**: feature toggles, defaults, page sizes. Keys use the `app.` prefix and a short descriptive name (`app.contents_dir`, `app.items_per_page`).
- **Symfony secrets** carry **sensitive data**: API keys, passwords, signing keys. Raw secrets never land in `.env`.
- **PHP constants** on domain classes carry values that rarely change and need to be referenced from both Twig and entities (`Post::NUMBER_OF_ITEMS`). Constants reach everywhere; parameters are container-scoped.

## Services

- Autowiring and autoconfigure are enabled (on by default in `services.yaml`). Dependencies are type-hinted and injected.
- Services are private by default. `$container->get(...)` is an anti-pattern; injection is the single access path.
- Manual service definitions live in `config/services.yaml` — YAML, not XML or PHP.

## Doctrine mapping

- Entity mapping uses PHP attributes (`#[ORM\Entity]`, `#[ORM\Column]`), keeping declaration and configuration in the same file. See [Rule 02](02-doctrine-best-practices.md) for Doctrine specifics.

## Controllers

- Controllers extend `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`.
- Routing, caching, and authorization use attributes (`#[Route]`, `#[Cache]`, `#[IsGranted]`).
- Services are injected via constructor or action arguments. `$this->container->get(...)` is not used.
- `EntityValueResolver` handles simple route → entity fetches. Manual repository calls take over when the fetch logic becomes non-trivial.

## Templates

- Template file names use `snake_case.html.twig`.
- Partial templates intended for `include` are prefixed with an underscore: `_user_card.html.twig`.
- Variables and template paths use `snake_case`. See [Rule 03](03-twig-coding-standards.md) for Twig coding standards inside templates.

## Forms

- Every form is a PHP class in `src/Form/` extending `AbstractType`.
- Submit buttons are rendered in the template, not declared inside the form class, so one form type can back multiple actions.
- Validation constraints live on the underlying object (entity or DTO), not on form fields. Validation follows the object, not the UI.
- A single action handles both `GET` (render) and `POST` (process): one route, methods `['GET', 'POST']`, `$form->handleRequest($request)`, with branching on `isSubmitted() && isValid()`.

## Internationalization

- Translation files use XLIFF.
- Translation keys describe **purpose** (`label.username`), not **location** (`edit_form.label.username`).

## Security

- One firewall, unless two genuinely different auth systems and user bases coexist.
- The password hasher is `auto`, so Symfony selects the best available algorithm.
- Complex authorization lives in voters (`Symfony\Component\Security\Voter\Voter`), not in `#[IsGranted]` expressions with multi-operator logic.

## Assets

- CSS, JavaScript, and images are handled by AssetMapper. No bundler is used for the common case. Webpack Encore is introduced only when asset processing genuinely requires it (heavy Sass pipelines, TypeScript compilation, and similar).

## Tests

- Every URL is smoke-tested by a single `DataProvider`-driven functional test that covers route and controller wiring across the app.
- URLs in functional tests are hard-coded, not generated from route names. A failing test after a URL change is the signal to add a public redirect.

See [Rule 05](05-testing-philosophy.md), [Rule 06](06-test-conventions.md), and [Rule 07](07-test-groups.md) for the full testing setup.
