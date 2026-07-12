---
name: scan-dependencies
description: Run composer audit and npm audit in CI, enable Dependabot, and justify every new dependency on necessity, maintenance health, license, and transitive cost.
paths: ["composer.json", "composer.lock", "package.json", "package-lock.json", ".github/**"]
severity: should
---
# Scan dependencies

**Rule:** Run `composer audit` and `npm audit` in CI and fail the build on known vulnerabilities, keep Dependabot enabled for updates, and justify every new dependency before adding it: is it necessary, is it actively maintained, is its license compatible, and what transitive weight does it pull in.

**Why:** A dependency is code you ship but do not control, so a vulnerable or abandoned package is a direct supply-chain risk (ISO 27001 A.8, ADR 0013). The doctrine Dependency Test asks whether each addition is warranted and healthy rather than convenient (REVIEW_DOCTRINE §6 Dependency Test, §9 "dependency vulnerabilities when packages are added or upgraded"). Catching this in CI keeps a known CVE from reaching production silently.

**Good / Bad:**
```yaml
# Bad: dependencies added on a hunch, never audited in CI.
# (no audit step; a transitive CVE ships unnoticed)

# Good: audit gates the pipeline.
- run: composer audit --no-interaction
- run: npm audit --audit-level=high
# plus .github/dependabot.yml watching composer and npm,
# and a one-line rationale in the PR for any new package.
```

**See also:** [[never-commit-secrets]], process/always-assess-cost-impact (the Dependency Test rationale).

**Enforced by:** CI (composer audit + npm audit) + Dependabot + review of the rationale.
