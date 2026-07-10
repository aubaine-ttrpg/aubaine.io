# Aubaine

> A fiction-first, progression-oriented tabletop RPG. Free to read, download, and print.

**Aubaine** is a tabletop roleplaying game where the story leads and the rules follow. Play is driven by fiction first. Mechanics exist to give that fiction weight, and characters grow through meaningful, lasting progression.

This repository is a **monorepo**. The game's data lives in the repo as the source of truth. A local tool authors it, and CI publishes it as a static site.

| Piece | Stack | Role |
| --- | --- | --- |
| **`codex/`** | Markdown | Rules, theory, and calculations, hand-authored as prose. Committed. |
| **`content/`** | JSON | Structured game data (skills, tags, archetypes, and so on), exported from Catalyst. Committed as the persistent data of record. |
| **Catalyst** | Symfony | Local-only authoring tool. Edits a local SQLite db, then exports to `content/`. |
| **Almanach** | Astro | Public static site. CI builds it from `codex/` and `content/`. |

---

## How it works

```
   Catalyst (Symfony)                          Almanach (Astro)
   local-only authoring                        CI-built static site
   var/db/$APP_ENV.sqlite                      reads content/* and codex/,
   (local db, git-ignored)                     then builds the site (CI)
          │  ▲                                          ▲
   export │  │ sync                              build  │
          ▼  │                                          │
   ═══════════════════ committed to git ═══════════════════
   content/   skills/ · tags/ · archetypes/ · …   (one JSON per entity)
   codex/     rules and theory (hand-authored Markdown)
```

1. **The repo is the database.** `codex/` (prose rules and theory) and `content/` (structured game data, one JSON file per entity) are committed and versioned.
2. **Catalyst authors it locally.** You edit through a local SQLite db at `apps/catalyst/var/db/$APP_ENV.sqlite`. `catalyst:export` writes the db out to `content/{skills,tags,archetypes,…}`, which you commit. `catalyst:sync` reads `content/` and updates the db.
3. **CI publishes it.** Astro builds a fast, no-backend static site from the committed `codex/` and `content/`, without Catalyst, PHP, or a database in the pipeline.

The game is **free**. Everything on Almanach is meant to be read online, downloaded, and printed.

---

## `codex/` (rules and theory)

The living rules and theory of Aubaine, authored as plain Markdown. It serves as documentation for developers and AI on the project, and as source content that both apps render.

- **Plain Markdown with frontmatter** (not MDX), so both a JS renderer (Astro) and a PHP renderer (Symfony / `league/commonmark`) read it the same way.
- **Single source of truth for numbers.** Structured values live in `content/`. Codex prose references them instead of restating them, so nothing drifts.
- Read directly on GitHub, rendered on Almanach, and pulled into books by Catalyst.

## `content/` (game data)

The structured game entities, serialized as JSON and split by type:

```
content/
├── skills/
├── tags/
├── archetypes/
└── …             # one JSON file per entity
```

- **Committed to the repo.** It is the persistent data of record, and what CI builds Almanach from without running Catalyst.
- **One file per entity, with deterministic output**, so changes diff, review, and merge like code.
- **Written by `catalyst:export`**, which serializes the local db out to `content/`.
- **Read by two consumers that do different things.** Almanach reads it at build time to generate the static site (one Astro collection per subfolder). `catalyst:sync` reads it to update the local db. Almanach does not touch the db.

## Catalyst (Symfony, the authoring tool)

A local-only application. It is not deployed publicly, and it is where the game is made.

- Create and edit **skills, tags, archetypes, characters, adventures sheets, etc...**.
- Backed by a **local SQLite db** at `apps/catalyst/var/db/$APP_ENV.sqlite`. The committed `content/` holds the truth.
- **`catalyst:export`** writes the db out to `content/`. **`catalyst:sync`** reads `content/` and updates the db.
- **Renders the `codex/`** into finished books.

## Almanach (Astro, the player site)

A static website built by CI from the committed `codex/` and `content/`.

- Built for players: browse the game, jump to any rule, skill, or adventure at a touch.
- **Download and print.** Sheets, books, and materials are meant to leave the screen and hit the table.
- **Read-only.** It reads `content/` and `codex/` at build time and does not write back to Catalyst or its db.
- No backend and no database, just static files that host anywhere.

---

## Repository layout

```
aubaine.io/
├── apps/
│   ├── catalyst/     # Symfony, local authoring tool
│   │   └── var/db/   # $APP_ENV.sqlite, local db (git-ignored)
│   └── almanach/     # Astro, public static site, built in CI
├── content/          # structured game data (committed)
│   ├── skills/       #   one JSON file per entity
│   ├── tags/
│   └── archetypes/   #   …
├── codex/            # rules and theory, hand-authored Markdown (committed)
└── docs/             # code and contributor documentation
```

Conventions that keep it clean:

- **The repo is the database.** All persistent data (`codex/` and `content/`) is committed and versioned, and CI builds the site straight from it.
- **The SQLite db stays local.** Rebuild it from `content/` with `catalyst:sync`, and never commit it.
- **Each app reads the root data.** Neither app reaches into the other's source.

---

## Getting started

```
make install    # install both apps' dependencies
make dev        # run Almanach locally
make build      # export from Catalyst, then build the static site
```

Run `make help` for all tasks.

---

## License

Aubaine is dual-licensed so the software and the game stay separate:

- **Code** (Catalyst, Almanach, and supporting tooling) is under the [MIT License](LICENSE).
- **Game content** (`codex/`, `content/`, and all other Aubaine material) is under [Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)](LICENSE-CONTENT).

You can read, download, print, and remix the game for non-commercial use with credit, and any shared derivatives carry the same license. The tooling is free for you to use.
