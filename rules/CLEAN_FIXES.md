---
name: CLEAN_FIXES
description: Changes go to the root cause, not around it. Applies when a rule drifts out of sync, a file carries stale content, or a patch would hide the real issue rather than fix it.
---

# Clean Fixes

Every change in this repo addresses the root cause. When a problem surfaces during a task — a rule out of sync with the code, a file carrying stale content, a workaround that would hide the real issue — the fix covers the root, even when that expands the change beyond what was originally in scope.

- **Retire completely.** Deleted content is removed; no tombstones, no "deprecated" placeholders, no empty shells kept around for sentiment.
- **Update the rule at the point of drift.** A rule that proves wrong mid-task is rewritten before it is applied — never worked around in the file that exposed the drift.
- **Code and docs move together.** When they disagree, both land in the same change. A fix that updates code and leaves docs stale is not yet finished.
- **No silent exceptions.** If a case doesn't fit an existing rule, either the rule accommodates the case or the rule is updated. A comment saying "we do X here because the rule's wrong" is a rule change not taken.
- **Localized patches earn their scope.** When the patch is genuinely the right scope — a typo fix, a one-line obvious bug — that's the clean fix. The default is root-cause.
