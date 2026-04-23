---
name: TEST_GROUPS
description: Canonical test-group taxonomy (PHPUnit #[Group]). Applies when picking the `#[Group]` attribute for a test class, or when introducing a new domain or concern group.
---

# Test Groups

Every test class carries at least one **domain group**. **Concern groups** layer cross-cutting aspects on top.

```bash
make test CMD="--group skill"                      # one group
make test CMD="--group skill --group regression"   # intersection — both must match
make test CMD="--exclude-group performance"        # skip slow tests
```

## Domain groups

Aligned with the game design (`_archive/docs/aubaine.*.md`). The list is seeded small and grows as features land.

| Group | Scope |
|---|---|
| `skill` | Individual Skills: creation, validation, resolution, tagging, effects |
| `skill-tree` | Skill trees, nodes, connections, progression paths |
| `tag` | Tag taxonomy (Nature / Element / Arcane School / etc.) and weight-based ordering |
| `character` | Character sheets, abilities, aptitudes, HP/Energy/Memory resources |
| `resolution` | Dice resolution (d20 + Ability + Aptitude), DCs, opposed rolls, critical results |

A new domain is added to this table before `#[Group]` is applied in code.

## Concern groups

Cross-cutting. Any test class carries zero or more, in addition to its domain group.

| Group | Scope |
|---|---|
| `performance` | Scalability, response-time ceilings, N+1 detection (see [test conventions](TEST_CONVENTIONS.md) § Performance) |
| `translation` | Gedmo translatable behavior, multilingual entities, XLIFF catalogs |
| `security` | Authentication, authorization, voters, CSRF |
| `regression` | Pins a previously broken behavior; docblock carries the ticket or commit reference |
| `api` | JSON endpoints — token or auth-based APIs consumed by clients outside the browser |

## Naming rules

- Group names are **lowercase kebab-case** (`skill-tree`, not `SkillTree` or `skill_tree`).
- One test class = **1 domain group** + **0–N concern groups**.
- Method-level `#[Group]` is valid for individual tests inside a class (for example, a single method pinning a specific regression).

## Examples

```php
#[Group('skill')]
#[Group('translation')]
final class SkillNameTranslationTest extends KernelTestCase { }

#[Group('skill-tree')]
#[Group('performance')]
final class SkillTreeListingPerformanceTest extends WebTestCase { }

#[Group('skill')]
#[Group('regression')]
final class SkillResolverRegressionTest extends TestCase { }
```

## Adding a new group

1. Add the group to the appropriate table in this file with its scope.
2. Apply `#[Group]` in the test classes.
3. Verify: `make test CMD="--group new-group"` runs the expected tests.
