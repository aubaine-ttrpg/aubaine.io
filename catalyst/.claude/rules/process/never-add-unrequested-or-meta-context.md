---
name: never-add-unrequested-or-meta-context
description: "State what a thing is and how to use it, in docs AND code comments. Never justify choices not made, narrate the decision/drift/history, or explain what is absent. Tells: \"we deliberately…\", \"X is a Y, not a Z\", \"renamed from…\", \"the reason we chose…\"."
severity: must
---
# Never add unrequested or meta context

**Rule:** Documentation, copy, **and code comments** state what a thing IS and how to use it. Do not
justify choices that were not made ("we deliberately do not use X"), correct a misconception the reader
never had ("X is a kernel, not a distro"), narrate the decision process, the drift, the rename, or the
conversation that led here, or explain why something is absent. The reader did not share your context and
does not need it. A significant decision with a real tradeoff goes in an ADR (the one place a rationale
belongs); a `**Why:**` section in a rule file is the other. Nowhere else.

**Why:** A reader of a README, rule, or comment never thinks "good to know they rejected X"; they did
not know X was an option and do not care. Unrequested justification and backstory are noise written for
an imagined reader who shared your conversation. This is the context dumping the review doctrine §16.3
rejects, and a classic AI tell.

**Good / Bad:**
```
Bad (docs):    "We deliberately do not use 🚧/🩹/🚑. Gitmoji signals the kind of change;
                overloading it with status destroys that..."  (nobody asked; the reader
                never knew this was considered.)
Bad (comment): # "linux" is a kernel, not a distro. We target Alpine, so we detect the
               #  package manager, not "Linux".   (argues a choice and corrects a
               #  misconception the reader never had, pure backstory.)
Good:          name the branch and stop; the code (the apk check, the error message) is
               self-evident. If the choice is significant, write an ADR.
```

**See also:** [[never-write-redundant-content]], [[write-an-adr-for-significant-decisions]], ai/keep-agent-context-files-curated, ai/scrutinize-ai-coding-tells, security/never-leak-internal-context-in-responses.

**Enforced by:** review, specifically the meta/self-consistency review lens; plus the `athletis-ai-tell-remover` skill on prose. (Not script-checkable: a regex cannot judge meaning, so this is enforced by reading, not grep.)
