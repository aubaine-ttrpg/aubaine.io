---
name: follow-column-and-table-naming
description: Doctrine mappings use snake_case columns, singular table names, and explicit foreign-key columns.
paths: ["catalyst/**"]
severity: should
---
# Follow column and table naming

**Rule:**
- Columns are `snake_case`. Let Doctrine's `UnderscoreNamingStrategy` map a `camelCase` property (`bookType`) to a `snake_case` column (`book_type`); do not name columns by hand.
- Tables are singular: `book`, `page`, `skill_tree`, `skill_node`. Declare the name with `#[ORM\Table(name: 'book')]`; never rely on the default class-name table.
- A foreign key is an explicit column named `<referenced_table>_id`: a `page` row points at its book through `book_id`, a `skill_node` through `skill_tree_id`.
- An ordered collection gets an explicit `position` column (the pages of a book keep their order in `position`, not in insert order).

**Why:** One predictable convention keeps the SQLite file, the migrations, and any hand-written query readable, and an explicit `book_id` makes a join self-describing instead of guessed. `UnderscoreNamingStrategy` is Doctrine's documented way to produce `snake_case` columns from PHP property names, so the mapping stays the single source and the column names are derived, not authored twice.

**Good / Bad:**
```php
// Bad: plural table, camelCase column, implicit/renamed FK, order left to chance.
#[ORM\Entity]
#[ORM\Table(name: 'pages')]                 // plural
class Page
{
    #[ORM\Column(name: 'pageType')]         // camelCase column
    private string $type;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    #[ORM\JoinColumn(name: 'ownerBook')]    // opaque FK name
    private Book $book;
}
```
```php
// Good: singular table, snake_case columns, explicit book_id + position.
#[ORM\Entity]
#[ORM\Table(name: 'page')]
class Page
{
    #[ORM\Column]                           // -> page_type via UnderscoreNamingStrategy
    private string $pageType;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    #[ORM\JoinColumn(name: 'book_id', nullable: false)]
    private Book $book;

    #[ORM\Column]                           // zero-based order inside the book
    private int $position;
}
```

**See also:** [[use-doctrine-traits-deliberately]], [[keep-migrations-forward-and-reviewed]], php/never-use-float-for-exact-quantities, php/always-use-immutable-utc-datetimes.

**Enforced by:** Doctrine `UnderscoreNamingStrategy` (maps `camelCase` properties to `snake_case` columns) + review.
