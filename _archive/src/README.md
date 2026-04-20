# Archive — `src/` (Backend)

v1 Symfony backend: entities, controllers, services, enums, forms, commands, Twig components.

## Entities (`src/Entity/`)

All entities use **ULID primary keys** and most have a paired `*Translation` entity for Gedmo multilingual fields.

| Entity | Purpose | Notes |
|---|---|---|
| `Skills.php` | Current skill model: code, name, description, category, ability, aptitude | Paired with `SkillsTranslation` |
| `SkillTree.php` | A skill tree (grid container with columns/rows config) | Paired with `SkillTreeTranslation` |
| `SkillTreeNode.php` | One placed skill on a tree, with grid coordinates | — |
| `SkillTreeLink.php` | A connection between two `SkillTreeNode`s | — |
| `Tag.php` | Categorical metadata (element, school, discipline, etc.), with SVG icon upload | Paired with `TagTranslation` |
| `OldSkills.php` (14.9 KB) | **Legacy.** Older richer skill format (energy, duration, range, limitations) that was being phased out in favor of `Skills` | Paired with `OldSkillsTranslation` |

The split between `Skills` and `OldSkills` exists because v1 mid-flight decided to simplify the schema. The v2 rebuild does not inherit this split — start from `docs/aubaine.skills.md`.

## Controllers (`src/Controller/`)

### `Admin/` — dev-gated CRUD UI

- `AdminController.php` — base class with shared helpers
- `AdminDashboardController.php` — admin landing page
- `AdminSkillController.php` — Skills CRUD
- `AdminSkillTreeController.php` (**21 KB, heaviest controller**) — tree CRUD + the grid-builder JSON endpoints used by `skill_tree_builder_controller.js`
- `AdminOldSkillController.php` — legacy skill CRUD
- `AdminTagController.php` — tag CRUD with icon upload
- `Factory/` — (empty / stubs) was meant for procedural entity factories

Routes in `config/routes/` gate admin by `APP_ENV=dev`.

### `Dev/` — sandbox routes

- `SkillTreeBuilderController.php` — standalone builder page used during development
- `TestGridController.php` — throwaway grid layout tests

## Services (`src/Service/`)

**The reusable crown jewels.** These are framework-agnostic enough to lift wholesale.

- `DatabaseExporter.php` (243 LOC) — walks Doctrine metadata, serializes every entity to `data/json/<Entity>.json`, handles ULIDs, datetimes, relations, Gedmo translations. Writes a `_meta.json` manifest.
- `DatabaseImporter.php` (311 LOC) — inverse operation. Hydrates entities from the JSON dump, respects FK order, reconstructs ULIDs.
- `DatabaseExportResult.php` / `DatabaseImportResult.php` — DTOs for reporting counts/errors back to CLI + controllers.

Paired CLI commands live in `src/Command/`:

- `ExportDatabaseCommand.php` — `php bin/console app:export-database`
- `ImportDatabaseCommand.php` — `php bin/console app:import-database`

And Symfony forms for filtered exports live in `src/Form/`:

- `SkillExportFilterType.php` / `OldSkillExportFilterType.php` — let admins pick subsets before exporting

## Enums (`src/Enum/`)

PHP 8.1 backed enums, safer than string constants. **Check these against current `docs/aubaine.system.md` before copying — v1's values may be outdated.**

| Enum | Values reflect |
|---|---|
| `Ability.php` | The 7 Abilities (Strength, Dexterity, Constitution, Intelligence, Perception, Charisma, Spirit) |
| `Aptitude.php` | The 24 Aptitudes (Athletics, Stealth, …) |
| `SkillType.php` | Spell / Maneuver / Shout / Stance / Reaction / Ritual / Passive / Technique |
| `SkillCategory.php` | Active / Passive / Ultimate |
| `SkillDuration.php` | Instant / Concentration / minutes / hours / etc. — **v1-specific, may not match current system** |
| `SkillLimitPeriod.php` | Short Rest / Long Rest / Day / etc. |
| `SkillRange.php` | Contact / Close / Standard / Far bands |
| `Source.php` | Origin/source-book labeling |
| `TagCategory.php` | Element / Arcane School / Martial Discipline / etc. — reflects the weighted tag system in `docs/aubaine.skills.md` |

## Forms (`src/Form/`)

Symfony form types. Patterns worth noting but bound to v1 entities.

- `SkillFormType.php` — main skill editor
- `SkillTreeFormType.php` (64 LOC) — grid dimensions + metadata
- `TagFormType.php` — includes icon upload (SVG validation)
- `OldSkillFormType.php` (151 LOC) — legacy, dense with all the extra fields
- `*ExportFilterType.php` — filter UIs for partial exports

## Twig components (`src/Twig/Component/`)

- `SkillExportCard` — backs `templates/components/SkillExportCard.html.twig` via UXTwigComponent bundle

## Event subscribers (`src/EventSubscriber/`)

Minor — locale handling, request lifecycle hooks. Inspect directly if needed.

## Commands (`src/Command/`)

See Services section above — the two commands wrap `DatabaseExporter` / `DatabaseImporter`.

## What to reuse vs. rewrite

- **Reuse as-is:** `DatabaseExporter` / `DatabaseImporter` and their CLI wrappers — the logic is generic.
- **Reuse the pattern:** Gedmo translatable entity setup, ULID PKs, Twig component structure.
- **Rewrite from `docs/`:** Every enum, the entire Skill / SkillTree / Tag schema. The game design moved past what v1 encoded.
- **Drop:** `OldSkills*` and everything scoped to it (controller, form, templates).
