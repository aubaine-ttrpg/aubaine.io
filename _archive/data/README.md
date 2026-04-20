# Archive — `data/`

v1 runtime data: the SQLite dev database and the export/import dump folder.

## `db/dev.db`

The SQLite file that v1's Symfony app connected to (via `DATABASE_URL="sqlite:///%kernel.project_dir%/data/db/dev.db"` in `.env`). Committed for dev convenience — not a production dataset.

Contains the last-known state of v1's entities: Skills, SkillTrees, SkillTreeNodes, SkillTreeLinks, Tags, OldSkills, plus their `*_translations` siblings.

### Opening it

Nothing in v2 is connected to this file. To inspect:

```bash
sqlite3 _archive/data/db/dev.db
.tables          # list tables
.schema skills   # inspect a table
SELECT * FROM skills LIMIT 5;
```

Or use any SQLite GUI (DB Browser for SQLite, TablePlus, DBeaver) and point it at the file.

### Migrating content to v2

If v2 wants to keep any content from here (e.g., existing Skill text), the realistic paths are:

1. Hand-pick rows with `sqlite3` queries and transform them to match the v2 schema.
2. Or run v1 one more time (`cd _archive && composer install && ... && php bin/console app:export-database`) to produce fresh JSON in `data/json/`, then write a v2 importer.

## `json/`

The target directory for `php bin/console app:export-database` (powered by `src/Service/DatabaseExporter.php`). In this archive the folder is present but **empty** — no dump was captured at archive time.

### Expected format (when populated)

- `_meta.json` — manifest listing entity types exported, counts, timestamp, schema version hints
- `<EntityName>.json` — one file per entity class (e.g., `Skills.json`, `SkillTree.json`, `Tag.json`), each holding an array of serialized rows with ULID IDs preserved and relations referenced by ID

See `_archive/src/Service/DatabaseExporter.php` for the exact serialization logic.

## Using this for v2

- `db/dev.db` is the only source of truth for any content that existed in v1 but isn't in docs. If the user cares about preserving specific skill text, pull it from here.
- `json/` is currently useless (empty) unless v1 is temporarily reanimated to generate a dump.
