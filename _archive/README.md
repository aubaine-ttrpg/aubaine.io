# Aubaine v1 Archive

Frozen snapshot of the first Aubaine web app. The entire v1 codebase was moved here on 2026-04-20 so master could start fresh on a minimal Symfony 8 skeleton (Twig + Doctrine + SQLite, nothing else) without carrying the legacy skill/skill-tree data model and the organic growth around it.

Nothing in this folder is compiled, linted, or executed by the live app. It's a reference. The design docs (`docs/`) were archived with everything else — see `_archive/docs/` for the authoritative game-design canon that will drive v2.

## Stack snapshot (v1)

- **Backend:** Symfony 8, PHP 8.4, Doctrine ORM, SQLite (`data/db/dev.db`)
- **Frontend:** Stimulus (Hotwired) + Turbo + Tailwind 3.4 + Webpack Encore 5
- **Bundles of note:** `StofDoctrineExtensionsBundle` (Gedmo Translatable), Symfony UX (Icons, TwigComponent, StimulusBundle, Turbo), WebpackEncoreBundle
- **Translations:** Multilingual entities via Gedmo — every domain entity has a `*Translation` sibling
- **Primary keys:** ULIDs throughout
- **No tests.** PHPStan configured, no unit/integration suite.

## Directory map

| Path | What's there | See |
|---|---|---|
| `docs/` | Game design canon: `aubaine.principles.md`, `aubaine.system.md`, `aubaine.rolls.md`, `aubaine.skills.md` — the authoritative source for v2 | — |
| `src/` | Backend: Entities, Controllers, Services, Enums, Forms, Commands, Twig components, EventSubscriber | [src/README.md](src/README.md) |
| `assets/` | Frontend: Stimulus controllers, custom CSS (Tailwind + grid/tree styles) | [assets/README.md](assets/README.md) |
| `templates/` | Twig templates: admin CRUD UI, shared components, dev-only pages | [templates/README.md](templates/README.md) |
| `migrations/` | Six Doctrine migrations chronicling the v1 schema evolution | [migrations/README.md](migrations/README.md) |
| `data/` | SQLite dev DB (`db/dev.db`) + JSON export/import dump (`json/`) | [data/README.md](data/README.md) |
| `config/` | Symfony config: bundles, packages, routes, services | [config/README.md](config/README.md) |
| `public/` | Symfony web root (entry point `index.php`, generated build assets) | — |
| `translations/` | Symfony translation catalogs (messages in en/fr for admin UI) | — |
| `bin/` | Symfony console entry point | — |
| `composer.json` / `composer.lock` | PHP dependency manifest from v1 | — |
| `package.json` / `package-lock.json` | JS dependency manifest from v1 | — |
| `webpack.config.js` / `postcss.config.js` / `tailwind.config.js` | v1 build pipeline config | — |
| `Makefile` | v1 dev shortcuts (`make install`, `make dev`, `make build`, `make migrate`, `make export`, `make import`) | — |
| `symfony.lock` / `phpstan.dist.neon` / `.symfony.local.yaml` | Misc tooling | — |
| `.env` / `.env.dev` | v1 environment files | — |

## Notable salvageable pieces

These are the bits most likely worth pulling forward. Full per-directory notes in the sub-READMEs; this is the quick-reference list.

- **Database serializer (generic).** `src/Service/DatabaseExporter.php` (243 LOC) + `DatabaseImporter.php` (311 LOC) walk Doctrine metadata to serialize/deserialize the whole DB to/from `data/json/`, handling ULIDs, datetimes, and Gedmo translations. Genuinely reusable on any Doctrine project.
- **Skill Tree grid editor.** `assets/controllers/skill_tree_builder_controller.js` (927 LOC) — the most complex piece of v1. Interactive drag-drop grid, click-to-connect nodes, live search, inline skill creation, CSRF. If the new app keeps a visual tree editor, this is the reference implementation.
- **Gedmo Translatable pattern.** Every entity has a translation sibling; `config/packages/` wires up `StofDoctrineExtensionsBundle`. See `src/Entity/Skills.php` + `SkillsTranslation.php` for the template.
- **Enum vocabulary.** `src/Enum/` holds `Ability`, `Aptitude`, `SkillType`, `SkillCategory`, `SkillDuration`, `SkillLimitPeriod`, `SkillRange`, `Source`, `TagCategory`. The values reflect v1's reading of the game — check against current `docs/aubaine.system.md` before copying.
- **Stimulus utility controllers.** `csrf_protection_controller.js`, `multi_select_controller.js`, `locale_toggle_controller.js`, `icon_preview_controller.js`, `auto_dismiss_controller.js`, `content_nav_controller.js`, `action_fields_controller.js` — small, self-contained, portable.
- **Grid & tree CSS.** `assets/styles/skill-grid.css`, `skill-tree.css`, `skill-plate.css` — pairs with the builder JS and `SkillPlate` Twig component.
- **Twig components.** `templates/components/SkillPlate.html.twig`, `SkillExportCard.html.twig`, `AnonSkillExportCard.html.twig` — pattern for reusable render units via `UXTwigComponent`.
- **JSON export format.** `data/json/` — per-entity file + `_meta.json` manifest. Useful as a content-migration source if the new schema can map from it.

## Resurrecting a file

Since this archive lives in the working tree (not a separate branch), no git gymnastics needed:

```bash
# Copy a file back for reference alongside new code
cp _archive/src/Service/DatabaseExporter.php src/Service/DatabaseExporter.php

# Or just open it in place and copy-paste what you need
```

The old code will not run as-is against the new codebase — namespaces, dependencies, and schema will diverge. Treat it as a reading reference, not a drop-in library.

## What's intentionally missing

- `vendor/` — deleted (regenerate with `composer install` inside `_archive/` if you ever need to run v1 locally).
- `node_modules/` — deleted (regenerate with `npm install`).
- `var/` — cache + logs, deleted.
- `public/build/` — compiled asset output, deleted (rebuild with `npm run build`).
- `.env.local` — never committed; it was user-specific.
