# Aubaine

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?logo=opensourceinitiative&logoColor=white)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-%E2%89%A5%208.4-777bb4.svg?logo=php&logoColor=white)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-8.0-000000.svg?logo=symfony&logoColor=white)](https://symfony.com/)
[![Twig](https://img.shields.io/badge/Twig-3-0c0c0c.svg?logo=symfony&logoColor=white)](https://twig.symfony.com/)
[![Tailwind](https://img.shields.io/badge/Tailwind-4.1-38bdf8.svg?logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Stimulus](https://img.shields.io/badge/Stimulus-3.2-77e8b9.svg?logo=symfony&logoColor=white)](https://stimulus.hotwired.dev/)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-13-366488.svg?logo=php&logoColor=white)](https://phpunit.de/)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-2d87a7.svg?logo=php&logoColor=white)](https://phpstan.org/)

**Aubaine** is a fiction-first tabletop RPG. This repository hosts the game's web companion — a character and skill tool built on the same principles that drive the game itself: simple, flexible, and narrative-led.

## Stack

| Layer | Choice |
|---|---|
| Language | PHP 8.4 |
| Framework | Symfony 8 |
| ORM + DB | Doctrine ORM + SQLite (file at [`db/app.db`](db/)) |
| Templating | Twig + Twig Components + Live Components |
| JS | Stimulus (via Symfony StimulusBundle) + Swup for page transitions, all served through AssetMapper |
| CSS | Tailwind CSS 4 (via `symfonycasts/tailwind-bundle`, standalone binary, no Node) |
| Tests | PHPUnit 13 (strict mode, random order) |
| Static analysis | PHPStan level 9 with Symfony + Doctrine + PHPUnit extensions |

## Requirements

- PHP **8.4+**
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download) — for the local dev server
- `make` (ships with macOS and most Linux distros)

## Install

```bash
composer install
make hooks      # install the .githooks/ pre-commit hook
```

## Database

SQLite file lives at `db/app.db` (gitignored):

```bash
touch db/app.db
php bin/console doctrine:schema:update --force   # once entities exist
```

## Run

```bash
make dev        # = symfony server:start
```

Then open <http://127.0.0.1:8000/>.

Compile Tailwind (watch mode) in a second terminal while developing:

```bash
php bin/console tailwind:build --watch
```

## Tests & static analysis

```bash
make test                              # full PHPUnit suite
make test CMD="--testsuite Unit"       # one suite (Unit / Integration / Functional)
make test CMD="--group skill"          # one group
vendor/bin/phpstan analyse             # PHPStan at level 9
```

The pre-commit hook (installed by `make hooks`) runs both on every commit that touches PHP-related files. Bypass only in emergencies with `git commit --no-verify`.

## Project docs

- [`rules/`](rules/) — mandatory project rules (commit convention, testing conventions, Doctrine / Twig / Symfony best practices, …). Each rule starts with YAML frontmatter so the catalog is self-describing.
- [`wiki/`](wiki/) — game-content documentation (TTRPG mechanics, math, design concepts).

## License

[MIT](LICENSE) © 2026 Kori-San
