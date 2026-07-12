---
name: own-canonical-sources-of-truth
description: Every policy, convention, workflow, or product fact has one canonical owner; references point to that owner, and unclear authority is fixed by assigning ownership.
severity: must
---
# Own canonical sources of truth

**Rule:** A policy, convention, workflow, or product fact must have one canonical owner. Other files
reference that owner instead of duplicating the content. When two files conflict, the higher-authority
source wins and the lower-authority file is repaired. If authority is unclear, assign ownership; do not
copy the rule again.

**Why:** Unclear authority creates drift. A reader should know where to change a rule and which source
wins during a conflict. This keeps documentation, skills, tasks, and generated views aligned with the
same repository discipline as code ownership.

**Good / Bad:**
```text
Bad:  A skill, README, and task template each define different test group names.
Good: The test group names are defined once in the testing convention files; other files link there.
```

**See also:** [[never-duplicate-reference-living-files]], [[never-hardcode-dynamic-lists]], [[never-context-dump-in-permanent-docs]].

**Enforced by:** review of documentation, rule, skill, task, and agent-instruction changes.
