---
name: never-hardcode-dynamic-lists
description: Do not hardcode dynamic inventories when a directory, manifest, registry, generated output, or canonical document owns the list.
severity: must
---
# Never hardcode dynamic lists

**Rule:** Do not list files under canonical directories unless maintaining an explicitly canonical
index. Do not list installed packages outside package-manager manifests. Do not list tasks, ADRs,
rules, skills, agents, scripts, workflows, or generated artifacts when a directory, manifest, registry,
or canonical document owns them. Prefer "all files under X" or "defined by Y manifest" over copied
inventories. Static lists are allowed only when the list itself is the canonical source of truth and is
maintained as such.

**Why:** Dynamic inventories change whenever files, packages, workflows, or agents are added or removed.
A copied list becomes stale immediately and misleads humans and coding agents.

**Good / Bad:**
```text
Bad:  "Run reviewer agents A, B, and C" in a skill while .claude/agents/ owns the agents.
Good: "Discover reviewer agents from .claude/agents/ metadata."

Bad:  "Installed packages are X, Y, Z" in a README.
Good: "Installed packages are defined by the package-manager manifests."
```

**See also:** [[own-canonical-sources-of-truth]], [[never-duplicate-reference-living-files]], ai/never-create-drift.

**Enforced by:** review; generated views must be regenerated from canonical sources.
