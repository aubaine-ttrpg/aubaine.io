---
name: 05-testing-philosophy
description: Testing philosophy and layer selection. Applies when deciding whether a behavior is worth testing, which test layer to reach for, or how to keep a test suite stable over time.
---

# Rule 05 — Testing Philosophy

Tests exist to encode behavioral contracts. Once a test passes, it guards that behavior for the life of the codebase.

## When to write a test

- **Behavior** — a function, service method, or endpoint with a non-trivial contract.
- **Regressions** — a bug is worth a test that pins the fix so it cannot silently reappear.
- **Edge cases** — empty inputs, overflow, boundary conditions, concurrency, auth failures.
- **Public contracts** — endpoints returning JSON, commands with documented options, events other code subscribes to.

## When not to write a test

- Framework internals already covered by Symfony/Doctrine upstream tests.
- Trivial getters, setters, or passthroughs with no logic.
- Private methods — test the public surface that calls them; if a private method needs direct testing, it's asking to be extracted into its own class.

## Test layers

Three layers, in order of cost. Start as low as possible.

| Layer | Base class | Boots kernel | Touches DB | Use when |
|---|---|---|---|---|
| **Unit** | `TestCase` | no | no | Pure logic with stubs or real collaborators cheap to build |
| **Integration** | `KernelTestCase` | yes | yes | Container wiring, Doctrine repositories, service interactions that need the real container |
| **Functional** | `WebTestCase` | yes | yes | Full HTTP cycle — routing, controller, middleware, response |

Pick the **highest layer you can avoid**. A Unit test for pure logic catches the bug in milliseconds; a Functional test for the same thing takes orders of magnitude longer and couples the test to the transport.

## Stability

- **Fix the code, not the test.** A red test means the production code does not match its contract. Do not weaken assertions, raise thresholds, or add exceptions to make a test pass.
- **Tests change only when the contract changes.** If the behavior is deliberately redesigned, rewrite the test to the new contract. Otherwise leave it alone.
- **Never delete a test to unblock a merge.** A test that blocks a merge is doing its job.
- **Thresholds are calibrated against real UX targets** (Core Web Vitals, TTFB budgets). Raising them to accommodate slow code is hiding a performance regression.
- **Flaky is a bug.** A test that passes sometimes is not a passing test. Fix the underlying non-determinism — race condition, unordered results, time-dependent logic. Do not retry loops, do not `markTestSkipped` as a shortcut.

## Measurement before optimization

When a test fails on performance grounds, the diagnosis goes into the test message (`sprintf` in the failure message is fine — it is not user output). The test tells you *what* is slow at what volume; you use a profiler to find *why*. Do not tune code against a vibe.
