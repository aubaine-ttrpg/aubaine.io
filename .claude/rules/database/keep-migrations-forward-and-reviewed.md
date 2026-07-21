---
name: keep-migrations-forward-and-reviewed
description: Schema changes ship as reviewed, forward-only Doctrine migrations checked into git and generated from the mapping.
paths: ["catalyst/**"]
severity: should
---
# Keep migrations forward and reviewed

**Rule:** Every schema change is a Doctrine migration generated from the entity mapping (`doctrine:migrations:diff`), read before it is committed, and checked into git. Migrations run forward only: to undo a change, write a new forward migration. Never edit or delete a committed migration, and never hand-edit the SQLite schema (no `ALTER TABLE` at the `sqlite3` prompt) to make the database match the code. The mapping is the source; the migration is the derived, reviewed artifact. When the generated diff looks wrong, fix the mapping and regenerate, do not patch the SQL.

**Why:** A forward-only, committed history is the one reproducible path from an empty database to the current schema, so any checkout rebuilds the same SQLite file. A hand-edited schema desyncs the database from the mapping and from every other checkout, and editing a migration that has already run leaves databases that ran the old version permanently diverged. Record the decision to adopt Doctrine migrations as an ADR (process/write-an-adr-for-significant-decisions) so the "why" outlives the switch from JSON files.

**Good / Bad:**
```bash
# Bad: change the live schema by hand; the mapping and other checkouts now disagree.
sqlite3 var/catalyst.db "ALTER TABLE book ADD COLUMN subtitle TEXT"
# (and worse: reopen VersionXXXX.php and rewrite an up() that already ran)

# Good: change the mapping, generate the migration, read it, commit it.
# 1. add the mapped property to the Book entity
# 2. generate the forward migration from the diff
php bin/console doctrine:migrations:diff
# 3. review the generated up()/down() SQL, then commit the Version file with the change
php bin/console doctrine:migrations:migrate
```

**See also:** [[follow-column-and-table-naming]], process/write-an-adr-for-significant-decisions.

**Enforced by:** `doctrine:schema:validate` (mapping and migrations stay in sync) + review of the migration diff.
