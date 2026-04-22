---
name: 02-doctrine-best-practices
description: Doctrine ORM best practices. Applies when designing entities, defining relationships, mapping tables, writing queries, configuring cascade or lifecycle events, or adding migrations.
---

# Rule 02 — Doctrine ORM Best Practices

Distilled from the upstream guide (<https://www.doctrine-project.org/projects/doctrine-orm/en/3.6/reference/best-practices.html>). These standards apply to every new entity, repository, migration, and Doctrine config change.

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
- **Mapping attributes sit on the entity class.** Declaration and configuration stay in one file, matching the Symfony best-practice guidance in [Rule 04](04-symfony-best-practices.md).

## Queries, transactions, and persistence

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

## Migrations

- Migrations are generated via `make:migration`. Every generated diff is reviewed before committing; only statements matching the intended schema change remain.
- Migrations form an append-only history. A committed migration is never edited; a new migration corrects a prior mistake.
