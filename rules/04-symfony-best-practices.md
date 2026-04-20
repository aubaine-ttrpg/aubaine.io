---
name: 04-symfony-best-practices
description: Symfony framework best practices. Applies when scaffolding controllers, services, forms, routing, security, templates, configuration, parameters, translations, tests, or assets.
---

# Rule 04 — Symfony Best Practices

Distilled from the upstream guide (<https://symfony.com/doc/current/best_practices.html>). Apply to every new controller, service, form, template, config file, and asset.

## Project creation & layout

- Use the Symfony binary to create new applications (`symfony new …`) and keep the default directory layout: `src/`, `config/`, `templates/`, `public/`, `tests/`, `migrations/`, `translations/`, `var/`, `vendor/`.
- Organize application code by PHP namespaces under `App\`. Do not create custom bundles for internal code — bundles are only for genuinely reusable, standalone packages.

## Configuration

- **Environment variables** (`.env`, `.env.local`, per-environment overrides) for **infrastructure**: database URL, mailer DSN, Redis host, API endpoints. Values that change per machine and do not change application behavior.
- **Parameters** in `config/services.yaml` for **application behavior**: feature toggles, defaults, page sizes. Use the `app.` prefix and a short, descriptive name (`app.contents_dir`, `app.items_per_page`).
- **Symfony secrets** for **sensitive data**: API keys, passwords, signing keys. Never commit raw secrets to `.env`.
- **PHP constants** in domain classes for values that rarely change and need to be referenced from Twig and entities (`Post::NUMBER_OF_ITEMS`). Constants work everywhere; parameters only work inside the container.

## Services

- Enable **autowiring** and **autoconfigure** (on by default in `services.yaml`). Type-hint dependencies; Symfony injects them.
- Services are **private** by default. Fetching via `$container->get(...)` is an anti-pattern — inject instead.
- Manual service definitions go in `config/services.yaml` (YAML, not XML or PHP).

## Doctrine mapping

- Use PHP **attributes** for entity mapping (`#[ORM\Entity]`, `#[ORM\Column]`). Keeps the mapping next to the class. See also [Rule 02](02-doctrine-best-practices.md) for Doctrine specifics.

## Controllers

- Extend `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`.
- Use **attributes** for `#[Route]`, caching, and `#[IsGranted]`.
- Inject services by type-hinting action arguments or the constructor. No `$this->container->get(...)`.
- Use `EntityValueResolver` for simple route → entity fetches; drop to manual repository calls when the fetch logic gets real.

## Templates

- File names in `snake_case.html.twig`.
- Prefix partial templates (intended for `include`) with an underscore: `_user_card.html.twig`.
- Snake_case for variables and template paths. See [Rule 03](03-twig-coding-standards.md) for Twig coding standards inside templates.

## Forms

- Every form is a PHP class in `src/Form/` extending `AbstractType`.
- Do not add submit buttons inside the form class — render them in the template so the same form type can back different actions.
- Attach **validation constraints on the underlying object** (the entity or DTO), not on form fields. Validation follows the object, not the UI.
- Single action handles both `GET` (render) and `POST` (process): one route, methods `['GET', 'POST']`, `$form->handleRequest($request)`, branch on `isSubmitted() && isValid()`.

## Internationalization

- XLIFF for translation files; translation keys describe **purpose** (`label.username`), not **location** (`edit_form.label.username`).

## Security

- **One firewall** unless you have two genuinely different auth systems and user bases.
- Password hasher: `auto` — Symfony picks the best available algorithm.
- Complex authorization: write **voters** (`Symfony\Component\Security\Voter\Voter`), not inline `#[IsGranted]` expressions with three operators.

## Assets

- **AssetMapper** for CSS, JavaScript, and images. No bundler for the common case. Drop to Webpack Encore only when asset processing genuinely requires it (heavy Sass pipelines, TS compilation, etc.).

## Tests

- Smoke-test every URL with a single `DataProvider`-driven functional test — cheap, catches route/controller wiring regressions across the app.
- Hard-code URLs in functional tests instead of generating them from route names. When a URL changes and the test fails, you have a signal to add a redirect (which the public needs anyway).

See [Rule 05](05-testing-philosophy.md), [Rule 06](06-test-conventions.md), and [Rule 07](07-test-groups.md) for the full testing setup.
