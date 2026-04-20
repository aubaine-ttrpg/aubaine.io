---
name: 06-test-conventions
description: How tests are written and organized in this repo. Applies when creating a test file, naming test classes and methods, picking attributes, writing setUp, or configuring PHPUnit strict mode.
---

# Rule 06 — Test Conventions

Mechanics of writing tests that fit this repo. See [Rule 05](05-testing-philosophy.md) for when to write them and [Rule 07](07-test-groups.md) for the `#[Group]` taxonomy.

## Layout

Tests mirror `src/` under `tests/{Unit,Integration,Functional}/`:

```
src/Service/Skill/Registry.php      →  tests/Unit/Service/Skill/RegistryTest.php
src/Controller/Admin/SkillController →  tests/Functional/Controller/Admin/SkillControllerTest.php
```

`.gitkeep` files sit in `tests/Unit/`, `tests/Integration/`, `tests/Functional/` so the empty suites track in git from day one.

## Naming

- **Class:** `{ProductionClass}Test` — e.g. `SkillResolver` → `SkillResolverTest`.
- **Method:** `test{Behavior}` for single-shape tests, or `test{What}_{When}_{Expected}` when the input/state matters (`testResolveSkill_WhenTagUnknown_ReturnsNull`).
- **DataProvider:** `public static` method returning `iterable`, keyed with readable labels (`yield 'empty input' => [...];`). Labels show up in test output.

## Attributes (PHPUnit 11+)

Use attributes. Annotation-based discovery (`@dataProvider`, `@group`) is not used:

```php
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

#[Group('skill')]
final class SkillResolverTest extends TestCase
{
    #[DataProvider('inputs')]
    public function testResolve(string $input, ?string $expected): void
    {
        $this->assertSame($expected, (new SkillResolver())->resolve($input));
    }

    public static function inputs(): iterable
    {
        yield 'known tag'   => ['fire', 'FireSkill'];
        yield 'unknown tag' => ['banana', null];
    }
}
```

Every test class carries **at least one domain `#[Group]`** from [Rule 07](07-test-groups.md).

## Structure

Use Arrange-Act-Assert, with blank lines between the three blocks when it clarifies:

```php
public function testRejectsBlankName(): void
{
    // Arrange
    $validator = new SkillNameValidator();

    // Act
    $result = $validator->validate('');

    // Assert
    $this->assertFalse($result->isValid());
    $this->assertContains('blank', $result->errors());
}
```

## `setUp()` patterns

- **Real objects when cheap.** An entity with public setters, a value object, a pure-logic service — instantiate it directly.
- **Stub for fire-and-forget deps.** `createStub(LoggerInterface::class)` when the test does not verify interaction with it.
- **Stub for expensive or external deps.** `EntityManagerInterface`, HTTP clients, file systems — stubs.
- **Final classes cannot be stubbed.** PHPUnit throws `ClassIsFinalException`. Instantiate the real class with stubbed constructor dependencies instead.
- **Store stubs as properties only when tests configure them.** Fire-and-forget stubs stay inline.

## Strict mode

`phpunit.dist.xml` enables:

- `failOnDeprecation="true"`
- `failOnWarning="true"`
- `failOnNotice="true"`
- `beStrictAboutOutputDuringTests="true"`
- `executionOrder="random"`

Every test must produce zero warnings, zero deprecations, zero stdout output. Tests passing under deterministic order must also pass under random order — setup leakage between tests is a bug.

Assertion failure messages (`$this->assertSame($expected, $actual, sprintf('…'))`) are not stdout output and are fine.

## Running

```bash
make test                             # full suite
make test CMD="--testsuite Unit"       # one suite
make test CMD="--group skill"          # one group
make test CMD="--filter testResolve"   # one method
```

The `test` target calls `php bin/phpunit` directly.

## Performance (when relevant)

For endpoints that hit the database, scalability tests are the contract that guards against N+1 and O(n²) regressions:

- **Response-time ceilings** — constants aligned with Core Web Vitals: API under 0.5s, page under 1.0s, heavy operation under 3.0s. Every data volume must stay under the same ceiling.
- **Logarithmic volume tiers** — 0, 1, 10, 100, 1,000, 10,000 rows. Each order of magnitude reveals a different class of failure.
- **Query-count ratio** — `queries(10_000_rows) / queries(1_row)` must stay at x3 or under. Ratios of x10+ are N+1 symptoms.

When performance tests enter the repo, add a `tests/PerformanceThresholds.php` constants class and a `tests/Traits/PerformanceTestTrait.php` with reusable assertions — importing the trait methods `as final` (PHP 8.3+) so child classes cannot override the assertion logic.
