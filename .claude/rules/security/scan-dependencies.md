---
name: scan-dependencies
description: Run composer audit and npm audit for every workspace in CI, enable Dependabot, and justify every new dependency on necessity, maintenance health, license, and transitive cost.
severity: should
---
# Scan dependencies

**Rule:** Run `composer audit` for the PHP dependencies and `npm audit` for every npm workspace in the monorepo (catalyst, sigil, and almanach today) in CI, and fail the build on known vulnerabilities. Keep Dependabot enabled for updates. Justify every new dependency before adding it: is it necessary, is it actively maintained, is its license compatible, and what transitive weight does it pull in.

**Why:** A dependency is code you ship but do not control, so a vulnerable or abandoned package is a direct supply-chain risk. Weighing each addition on necessity and health rather than convenience keeps the tree small and auditable. Catching a known CVE in CI keeps it from reaching a release silently.

**Good / Bad:**
```yaml
# Bad: dependencies added on a hunch, never audited in CI.
# (no audit step; a transitive CVE ships unnoticed)

# Good: audit gates the pipeline, PHP and every npm workspace.
- run: composer audit --no-interaction
- run: npm audit --workspaces --audit-level=high
# plus .github/dependabot.yml watching composer and npm,
# and a one-line rationale in the PR for any new package.
```

**See also:** [[never-commit-secrets]], process/always-assess-cost-impact (the dependency-cost rationale).

**Enforced by:** CI (composer audit + npm audit) + Dependabot + review of the rationale.
