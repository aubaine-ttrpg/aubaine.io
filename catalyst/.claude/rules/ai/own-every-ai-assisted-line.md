---
name: own-every-ai-assisted-line
description: The human author owns every AI-assisted line and must explain why it exists, why this design, what was checked, what changed, how it was tested, residual risks, and how to roll it back.
severity: must
---
# Own every AI-assisted line

**Rule:** The human author owns every line, whether typed, pasted, or produced by an agent. You must be able to explain why the change exists, why this design was chosen, what existing code you checked first, what behaviour changed and what did not, how it was tested, what production risks remain, and how to roll it back or delete it. "The agent generated it" is never a justification for architecture, naming, dependencies, tests, or behaviour.

**Why:** REVIEW_DOCTRINE §16.1. AI-assisted code must meet the same bar as senior human code; if nobody can explain, maintain, test, operate, and safely delete the change, it is not ready to merge. Ownership is the gate that stops plausible-but-unowned code entering the repository.

**Good / Bad:**
```php
// Bad: pasted as-is, author cannot say why this path exists or what it replaces.
final class SubscriptionService { public function doStuff(): void { /* agent output */ } }

// Good: the author can name the reuse checked (SubscriptionStateTransitioner),
// why this method exists, and how the duplicate-webhook test proves it.
final class CancelSubscription
{
    public function __construct(private SubscriptionStateTransitioner $transitioner) {}
}
```

**See also:** [[treat-ai-output-as-untrusted-until-integrated]], process/follow-contributing-for-commits.

**Enforced by:** review (the PR must carry an ownership narrative), CONTRIBUTING PR template.
