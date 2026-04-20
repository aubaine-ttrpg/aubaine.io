# Archive — `config/` (Symfony config)

v1 Symfony configuration. Shows which bundles were enabled and how they were tuned.

## Bundles enabled (`bundles.php`)

Full framework + a lot of UX/developer-experience bundles:

- **Core:** FrameworkBundle, DoctrineBundle, DoctrineMigrationsBundle, TwigBundle, SecurityBundle, MonologBundle
- **Dev only:** DebugBundle, WebProfilerBundle, MakerBundle
- **Symfony UX family:** StimulusBundle, Turbo, Icons, TwigComponent
- **Frontend pipeline:** WebpackEncoreBundle
- **Translations:** StofDoctrineExtensionsBundle (wraps Gedmo doctrine-extensions — this is what powers `#[Gedmo\Translatable]` on entities)
- **Extras:** TwigExtraBundle

## Package configs (`packages/`)

21 YAML files, one per bundle concern. Highlights:

| File | What it configures |
|---|---|
| `doctrine.yaml` | DB connection, entity mappings, ULID type setup |
| `doctrine_migrations.yaml` | Migration directory, namespace |
| `stof_doctrine_extensions.yaml` | **Enables Gedmo translatable** — the key bit to replicate in v2 if multilingual entities come back |
| `twig.yaml` / `twig_component.yaml` | Twig globals, TwigComponent registration |
| `webpack_encore.yaml` | Asset pipeline config |
| `ux_turbo.yaml` / `ux_icons.yaml` | Symfony UX bundle configs |
| `security.yaml` | Auth setup (firewalls, providers) — v1 was dev-only, check before reusing |
| `routing.yaml` | Route loader config |
| `translation.yaml` | Locale fallbacks (en/fr) |
| `framework.yaml` | Session, CSRF, rate limiter, etc. |
| `cache.yaml` / `csrf.yaml` / `debug.yaml` / `validator.yaml` / `property_info.yaml` / `monolog.yaml` / `mailer.yaml` / `messenger.yaml` / `notifier.yaml` / `web_profiler.yaml` | Standard Symfony subsystems |

## Routes (`routes/`)

- `routes.yaml` — top-level route loader
- `routes/` subdir — per-bundle route imports (`annotations.yaml` for controller attributes, dev-only routes for WebProfiler, etc.)

Controller routes are defined via `#[Route(...)]` attributes inside `src/Controller/**` — not in YAML.

## Service wiring (`services.yaml`)

Default autowiring + autoconfigure for `src/`, plus a couple of parameter bindings. Nothing exotic.

## Preload + reference

- `preload.php` — opcache preload (prod performance)
- `reference.php` — generated config reference dump

## Pulling forward into v2

v2 starts with a **minimal** Symfony 8 skeleton (Twig + Doctrine + SQLite). Do **not** mirror this config folder. Instead, cherry-pick by feature:

- When adding multilingual entities → copy the patterns from `stof_doctrine_extensions.yaml` and the translatable entity setup in `src/Entity/SkillsTranslation.php`.
- When adding an asset pipeline → decide fresh between AssetMapper (Symfony default now) and Webpack Encore; don't reflexively copy `webpack_encore.yaml`.
- When adding security → revisit `security.yaml`; the v1 version assumed dev-only admin gating.

Most of the 21 package configs exist because their bundles were installed. In v2, they'll be generated automatically by `composer require` as bundles are added — no need to port them manually.
