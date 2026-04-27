---
name: BACKEND_CONVENTIONS
description: Server-side Symfony conventions — project layout (src/, config/, templates/, public/, tests/, migrations/, translations/, var/), configuration (.env for infrastructure, `app.*` parameters in services.yaml for behavior, Symfony secrets for sensitive data, PHP constants for cross-domain values), services (autowiring, private, stateless, constructor injection), controllers (AbstractController, #[Route], #[IsGranted], EntityValueResolver), forms (AbstractType in src/Form/, validation on the underlying entity or DTO, single GET/POST action), security (single firewall, voters for complex authorization, `auto` password hasher), translations (XLIFF, purpose-keyed), assets (AssetMapper), Doctrine mapping attributes, functional smoke tests. Applies when scaffolding or editing PHP under src/ or YAML/PHP under config/.
---

# Backend Conventions

Distilled from the upstream guide (<https://symfony.com/doc/current/best_practices.html>). These conventions govern every controller, service, form, config file, and asset in the project.

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
- Services hold no per-request state. Constructor-injected dependencies are immutable references; request-scoped data flows through method arguments.

## Doctrine mapping

- Entity mapping uses PHP attributes (`#[ORM\Entity]`, `#[ORM\Column]`), keeping declaration and configuration in the same file. See [database and orm](DATABASE_AND_ORM.md) for Doctrine specifics.

## Controllers

- Controllers extend `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`.
- Routing, caching, and authorization use attributes (`#[Route]`, `#[Cache]`, `#[IsGranted]`).
- Services are injected via constructor or action arguments. `$this->container->get(...)` is not used.
- `EntityValueResolver` handles simple route → entity fetches. Manual repository calls take over when the fetch logic becomes non-trivial.

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

See [testing philosophy](TESTING_PHILOSOPHY.md), [PHP testing](PHP_TESTING.md), and [PHP test groups](PHP_TEST_GROUPS.md) for the full testing setup.
