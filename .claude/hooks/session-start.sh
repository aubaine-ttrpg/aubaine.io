#!/usr/bin/env bash
# SessionStart hook — inject the current rules/ index. No checklist (that's CLAUDE.md).
set -euo pipefail

cd "$(dirname "$0")/../.."

{
  echo "=== AUBAINE RULES INDEX (session-start injection) ==="
  echo ""
  echo "Per CLAUDE.md session-start sequence: call Read on every rule"
  echo "below whose description could plausibly apply to the task."
  echo "When in doubt, Read it. Full contract: CLAUDE.md."
  echo ""
  for f in rules/*.md; do
    [ -f "$f" ] || continue
    name=$(awk -F': *' '/^name:/ {print $2; exit}' "$f")
    desc=$(awk '/^description:/ {sub(/^description: */, ""); print; exit}' "$f")
    echo "  • ${name} — ${desc}"
  done
} | jq -Rsa '{hookSpecificOutput: {hookEventName: "SessionStart", additionalContext: .}}'
