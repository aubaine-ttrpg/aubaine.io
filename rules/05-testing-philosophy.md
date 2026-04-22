---
name: 05-testing-philosophy
description: Testing philosophy and layer selection. Applies when deciding whether a behavior is worth testing, which test layer to reach for, or how to keep a test suite stable over time.
---

# Rule 05 — Testing Philosophy

Tests encode behavioral contracts. Once a test passes, it guards that behavior for the life of the codebase.

## What gets a test

- **Behavior** — a function, service method, or endpoint with a non-trivial contract.
- **Regressions** — each fixed bug is pinned by a test so the defect cannot silently reappear.
- **Edge cases** — empty inputs, overflow, boundary conditions, concurrency, auth failures.
- **Public contracts** — endpoints returning JSON, commands with documented options, events other code subscribes to.

## What does not

- Framework internals already covered by Symfony or Doctrine upstream tests.
- Trivial getters, setters, and passthroughs with no logic.
- Private methods. The public surface that calls them is the contract under test; a private method that needs direct coverage is a signal to extract it into its own class.

## Test layers

Three layers, in order of cost. Tests live at the lowest layer that can express their contract.

| Layer | Base class | Boots kernel | Touches DB | Scope |
|---|---|---|---|---|
| **Unit** | `TestCase` | no | no | Pure logic with stubs or cheap real collaborators |
| **Integration** | `KernelTestCase` | yes | yes | Container wiring, Doctrine repositories, service interactions requiring the real container |
| **Functional** | `WebTestCase` | yes | yes | Full HTTP cycle — routing, controller, middleware, response |

A Unit test for pure logic catches the bug in milliseconds. A Functional test for the same logic takes orders of magnitude longer and couples the contract to the transport.

## Stability

- **Failing tests reveal production bugs.** A red test means the production code no longer matches its contract. Assertions are not weakened, thresholds are not raised, and exceptions are not added to make a test pass.
- **Tests change only when contracts change.** Deliberate behavioral redesigns rewrite the test to the new contract. Unrelated test changes are out of scope for a feature commit.
- **Tests are not deleted to unblock a merge.** A test that blocks a merge is doing its job.
- **Thresholds are calibrated against real UX targets** (Core Web Vitals, TTFB budgets). A threshold raised to accommodate slow code hides a performance regression.
- **Flakiness is a bug.** A test that passes sometimes is not a passing test. The underlying non-determinism — race conditions, unordered results, time-dependent logic — is fixed at the source. Retry loops and `markTestSkipped` are not remedies.

## Measurement before optimization

Performance assertions include a failure message (`sprintf` inside the assertion) that names *what* is slow at what volume. Profiler output identifies *why*. Performance tuning is driven by measurements, not by intuition.
