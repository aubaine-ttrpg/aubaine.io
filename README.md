# Aubaine

Web companion for **Aubaine**, a fiction-first tabletop RPG.

This is v2 — a minimal Symfony 8 skeleton. The entire v1 codebase (and the design docs that drove it) was archived under [`_archive/`](_archive/) on 2026-04-20. See [`_archive/README.md`](_archive/README.md) for what's in there and how to salvage specific pieces.

## Stack

- Symfony 8, PHP 8.4
- Twig (server-side templates)
- Doctrine ORM + SQLite (file in [`db/`](db/))
- Nothing else yet — additional bundles will be added only when a concrete feature needs them.

## Install

```bash
composer install
```

## Database

The SQLite file lives at `db/app.db` (gitignored). Create it when you first need it:

```bash
touch db/app.db
# or, once entities exist:
php bin/console doctrine:schema:update --force
```

## Run

```bash
symfony server:start
```

Then open <http://127.0.0.1:8000/>.

## Design canon

See [`_archive/docs/`](_archive/docs/) for the game's principles, resolution framework, system overview, and skill system. Those documents drive everything that gets built here.
