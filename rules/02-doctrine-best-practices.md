---
name: 02-doctrine-best-practices
description: Doctrine ORM best practices. Applies when designing entities, defining relationships, mapping tables, writing queries, configuring cascade or lifecycle events, or adding migrations.
---

# Rule 02 — Doctrine ORM Best Practices

Distilled from the upstream guide (<https://www.doctrine-project.org/projects/doctrine-orm/en/3.6/reference/best-practices.html>). Follow these in every new entity, repository, migration, and Doctrine config change.

## Entities

- **Single-value primary keys only.** Use auto-increment, UUID, or ULID. No composite primary keys — map composite natural uniqueness as a unique constraint instead, but keep a single-column surrogate PK.
- **Initialize collections in the constructor.** Any `#[ORM\OneToMany]` / `#[ORM\ManyToMany]` field gets assigned a `new ArrayCollection()` in `__construct()`. Prevents null-dereference bugs the moment a new entity is built.
- **Never map foreign key columns as entity fields.** Expose the relationship as an association object (`$user`), not as an ID scalar (`$userId`). Doctrine owns the FK; the domain owns the object.
- **ASCII-only names.** Class, field, table, and column names must be `[a-zA-Z0-9_]`. Transliterate anything else. Doctrine's Unicode handling is incomplete in enough places to matter.
- **Avoid SQL reserved words.** Pick names that do not need identifier quoting — reserved-word collisions break silently across databases and complicate debugging.

## Associations

- **Minimize relationships.** Prefer unidirectional associations. Only add the inverse side when the domain genuinely uses it. Each bidirectional link doubles synchronization work and tracking overhead.
- **Cascade deliberately.** `cascade: ['persist']` and `cascade: ['remove']` are only for **compositions** — when the parent fully owns the child's lifecycle (User owns Address, SkillTree owns SkillTreeNode). For weak associations, cascade is wrong. Never cascade everything by default.

## Mapping

- **One mapping driver, project-wide.** Attributes are the default for new code. Do not mix attributes, XML, and YAML.
- **Put mapping attributes on the entity class.** Keeps the declaration and the configuration co-located in one file — per Symfony best practices too.

## Queries, transactions, and persistence

- **Demarcate explicit transactions around multi-operation flushes.** Every query already runs inside an implicit transaction; when you need several operations to be atomic, wrap them explicitly:

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

- **Batch where possible.** Prefer a single `flush()` over many — each flush round-trips to the database.

## Lifecycle events

- **Reserve lifecycle events for cross-cutting concerns** (timestamps, audit trails, soft-delete). Domain logic belongs in services, not in `#[ORM\PrePersist]` callbacks.
- **Avoid event chains.** Events that modify related entities become hard to reason about and expensive to execute. If you find yourself needing that, use a domain service instead.

## Migrations

- Let `make:migration` generate the diff. Review every migration before committing — keep only statements that match intended schema changes, strip anything Doctrine added speculatively.
- Migrations are an append-only history. Never edit a committed migration; add a new one to correct a prior mistake.
