---
name: PHP_TESTING
description: PHPUnit conventions for PHP tests under tests/{Unit,Integration,Functional}/ — class and method naming ({ProductionClass}Test, test{Behavior}), attribute-based discovery (#[DataProvider], #[Group]), setUp() patterns (real objects when cheap, createStub() for external dependencies, no stubs for final classes), strict mode (failOnDeprecation, failOnWarning, failOnNotice, random execution order), performance tests (response-time ceilings, logarithmic volume tiers, query-count ratios for N+1). Applies when writing or editing a `.php` test file, configuring `phpunit.dist.xml`, or adding a regression or performance test.
---

# PHP Testing

Mechanics of writing tests that fit this repo. See [testing philosophy](TESTING_PHILOSOPHY.md) for when to write them and [PHP test groups](PHP_TEST_GROUPS.md) for the `#[Group]` taxonomy.

## Layout

The `tests/` tree mirrors `src/` under `tests/{Unit,Integration,Functional}/`:

```
src/Service/Skill/Registry.php        →  tests/Unit/Service/Skill/RegistryTest.php
src/Controller/Admin/SkillController  →  tests/Functional/Controller/Admin/SkillControllerTest.php
```

`.gitkeep` files sit in `tests/Unit/`, `tests/Integration/`, and `tests/Functional/` so the empty suites remain tracked from day one.

## Naming

- **Class:** `{ProductionClass}Test` — e.g. `SkillResolver` → `SkillResolverTest`.
- **Method:** `test{Behavior}` for single-shape tests, or `test{What}_{When}_{Expected}` when input or state matters (`testResolveSkill_WhenTagUnknown_ReturnsNull`).
- **DataProvider:** `public static` method returning `iterable`, keyed with readable labels (`yield 'empty input' => [...];`). Labels appear in test output.

## Attributes (PHPUnit 11+)

Discovery uses attributes. Annotation-based discovery (`@dataProvider`, `@group`) is not used:

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

Every test class carries at least one domain `#[Group]` from [PHP test groups](PHP_TEST_GROUPS.md).

## Structure

Tests follow Arrange-Act-Assert, with blank lines between the three blocks when that aids readability:

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

- **Real objects when cheap.** Entities with public setters, value objects, and pure-logic services are instantiated directly.
- **Stubs for fire-and-forget dependencies.** `createStub(LoggerInterface::class)` when the test does not verify interaction.
- **Stubs for expensive or external dependencies.** `EntityManagerInterface`, HTTP clients, file systems.
- **Final classes are not stubbed.** PHPUnit raises `ClassIsFinalException`. The real class is instantiated with stubbed constructor dependencies instead.
- **Stubs are promoted to properties only when tests configure them.** Fire-and-forget stubs stay inline.

## Strict mode

`phpunit.dist.xml` enables:

- `failOnDeprecation="true"`
- `failOnWarning="true"`
- `failOnNotice="true"`
- `beStrictAboutOutputDuringTests="true"`
- `executionOrder="random"`

Every test produces zero warnings, zero deprecations, and zero stdout output. A test that passes under deterministic order also passes under random order; setup leakage between tests is a bug.

Assertion failure messages (`$this->assertSame($expected, $actual, sprintf('…'))`) are not stdout output and are allowed.

## Running

```bash
make test                              # full suite
make test CMD="--testsuite Unit"       # one suite
make test CMD="--group skill"          # one group
make test CMD="--filter testResolve"   # one method
```

The `test` target invokes `php bin/phpunit` directly.

## Performance (when relevant)

Endpoints that hit the database are guarded by scalability tests that pin the contract against N+1 and O(n²) regressions:

- **Response-time ceilings** — constants aligned with Core Web Vitals: API under 0.5s, page under 1.0s, heavy operation under 3.0s. Every data volume stays under the same ceiling.
- **Logarithmic volume tiers** — 0, 1, 10, 100, 1,000, 10,000 rows. Each order of magnitude exposes a different class of failure.
- **Query-count ratio** — `queries(10_000_rows) / queries(1_row)` stays at ×3 or under. Ratios of ×10 or more are N+1 symptoms.

When performance tests enter the repo, a `tests/PerformanceThresholds.php` constants class and a `tests/Traits/PerformanceTestTrait.php` with reusable assertions accompany them. The trait methods are imported `as final` (PHP 8.3+) so child classes cannot override assertion logic.
