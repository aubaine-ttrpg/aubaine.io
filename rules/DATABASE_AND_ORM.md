---
name: DATABASE_AND_ORM
description: Database and Doctrine ORM standards — entities (single-value primary keys via auto-increment/UUID/ULID, ASCII-only names, no SQL reserved words, collections initialized with ArrayCollection in __construct(), FKs exposed as association objects not ID scalars), associations (unidirectional preferred, cascade reserved for compositions), mapping (PHP attributes only — no XML or YAML mixing), queries (DQL preferred, raw SQL only when DQL cannot express, JOIN FETCH to prevent N+1, fetch=EAGER only when truly unavoidable), transactions and persistence (multi-operation flushes wrapped in explicit transactions, batch flushes), lifecycle events (cross-cutting only — timestamps, audit, soft-delete; no chained event modifications), gedmo extensions (Timestampable for createdAt/updatedAt, Timeable for time-ranged entities), append-only migrations (generated via make:migration, every diff reviewed, never edited after commit). Applies when designing or editing entities, repositories, DQL queries, raw SQL, gedmo trait usage, migrations, or Doctrine configuration.
---

# Database and ORM

Distilled from the upstream guide (<https://www.doctrine-project.org/projects/doctrine-orm/en/3.6/reference/best-practices.html>). Doctrine is the sole database access path in this project; "database" and "ORM" are treated as one concern. Standards apply to every new entity, repository, migration, query, and Doctrine config change.

## Entities

- **Single-value primary keys only.** Auto-increment, UUID, or ULID. Composite natural uniqueness is expressed as a unique constraint; the primary key stays a single-column surrogate.
- **Initialize collections in the constructor.** Every `#[ORM\OneToMany]` and `#[ORM\ManyToMany]` field is assigned a `new ArrayCollection()` in `__construct()`, so a freshly built entity exposes an iterable collection rather than `null`.
- **Relationships are objects, not scalars.** Foreign keys are exposed as association properties (`$user`), never as ID scalars (`$userId`). Doctrine owns the FK; the domain owns the object.
- **ASCII-only names.** Class, field, table, and column names use `[a-zA-Z0-9_]`. Non-ASCII characters are transliterated.
- **No SQL reserved words as identifiers.** Names are chosen so identifier quoting is unnecessary. Reserved-word collisions break silently across databases.

## Associations

- **Prefer unidirectional associations.** The inverse side is added only when the domain genuinely uses it. Every bidirectional link doubles synchronization work and tracking overhead.
- **Cascade is reserved for compositions.** `cascade: ['persist']` and `cascade: ['remove']` apply only when the parent fully owns the child's lifecycle (User owns Address, SkillTree owns SkillTreeNode). Weak associations manage their own persistence.

## Mapping

- **One mapping driver, project-wide.** Attributes are the default for new code. Attributes, XML, and YAML are not mixed.
- **Mapping attributes sit on the entity class.** Declaration and configuration stay in one file, matching the guidance in [symfony best practices](SYMFONY_BEST_PRACTICES.md).

## Queries, transactions, and persistence

- **Queries use DQL.** Raw SQL is reserved for cases DQL cannot express, not as a shortcut.
- **N+1 is prevented at the query.** List views use `JOIN FETCH` on collections pulled eagerly; `fetch=EAGER` on a mapping is reserved for associations that are truly unavoidable at hydration time.
- **Multi-operation flushes run inside an explicit transaction.** Every query already runs inside an implicit transaction; atomic multi-operation flows wrap the whole sequence:

    ```php
    $em->getConnection()->beginTransaction();
    try {
        // persist / update / remove several entities
        $em->flush();
        $em->getConnection()->commit();
    } catch (\Throwable $e) {
        $em->getConnection()->rollBack();
        throw $e;
    }
    ```

- **Batch flushes.** A single `flush()` is preferred over many — each flush round-trips to the database.

## Lifecycle events

- **Lifecycle events handle cross-cutting concerns** only: timestamps, audit trails, soft-delete. Domain logic lives in services, not in `#[ORM\PrePersist]` callbacks.
- **Events stay flat.** Events that modify related entities become hard to reason about and expensive to execute. When that coupling is needed, a domain service replaces the chain.

## Time and lifecycle extensions

- **Timestamped entities use `gedmo/doctrine-extensions` Timestampable.** `createdAt` and `updatedAt` are populated by the extension; per-entity timestamp plumbing is not repeated.
- **Time-ranged entities use Timeable.** Start and end bounds are managed by the extension.

## Migrations

- Migrations are generated via `make:migration`. Every generated diff is reviewed before committing; only statements matching the intended schema change remain.
- Migrations form an append-only history. A committed migration is never edited; a new migration corrects a prior mistake.
