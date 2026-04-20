# Archive — `migrations/` (Doctrine migrations)

Six Doctrine migrations chronicling the v1 schema evolution (late Jan → early Feb 2026). These are **frozen snapshots** — they can no longer be run (the app is unplugged). Useful as a reference when designing the v2 schema.

## Chronological list

| Version | Description |
|---|---|
| `Version20260126080918.php` | **Initial schema.** Auto-generated, no description. Creates the original tables (skills, old_skills, tags, and their translation tables). |
| `Version20260126153219.php` | Follow-up schema tweaks. Auto-generated, no description. |
| `Version20260127075948.php` | Another schema adjustment. Auto-generated, no description. |
| `Version20260127120000.php` | **Data migration:** `Rename skill categories: common → basic, general → common.` Shows that v1 renamed the category vocabulary mid-flight. |
| `Version20260203120000.php` | **Feature:** `Add skill trees, nodes, links, and translations.` Creates `skill_trees`, `skill_tree_nodes`, `skill_tree_links`, `skill_tree_translations`. This is the big structural addition that pairs with `AdminSkillTreeController` + `skill_tree_builder_controller.js`. |
| `Version20260204104409.php` | Final schema touch-up. Auto-generated, no description. |

## What to read them for

- **Schema shape** — columns, types, FK relations, indexes. Faster than reconstructing from `src/Entity/*.php` when you want the SQL view.
- **The category rename** (`Version20260127120000`) — signal that "basic" / "common" / "general" terminology shifted. Match against `docs/aubaine.skills.md` when building v2 enums.
- **The skill-tree table design** (`Version20260203120000`) — if v2 keeps skill trees, this is the starting-point schema.

## What not to do with them

- Don't copy into the v2 migrations folder — they reference tables and types that won't match the new schema.
- Don't try to run them against the v2 database — Doctrine will complain about migration order, and the schema drift would corrupt state.

Regenerate fresh migrations with `php bin/console make:migration` after modeling the v2 entities.
