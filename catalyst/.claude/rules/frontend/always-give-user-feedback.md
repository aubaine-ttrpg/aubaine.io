---
name: always-give-user-feedback
description: Every user action must produce clear, timely feedback; show a pending state, confirm success, and on failure show a human message and a way forward. Never leave the user clicking with no idea what happened.
paths: ["templates/**", "assets/**", "src/Controller/**"]
severity: should
---
# Always give the user feedback

**Rule:** Every action a user takes gets visible, timely feedback:
- **Pending:** on submit or a slow action, show a pending state (disable the button, show a spinner) so
  the user knows it is working and cannot double-submit.
- **Success:** confirm it worked (a flash, a toast, an inline confirmation, or the updated view). The
  user should never wonder whether it saved.
- **Failure:** show a clear, human message ("Ce client n'a pas pu être enregistré. Réessayez.") and keep
  the UI usable; never a silent no-op, a stuck spinner, or a blank Turbo frame. Validation errors
  re-render the form with field errors (422); server errors surface a friendly message, never a raw
  stack trace or PII.
- **Critical flows (payments, account deletion, invoicing) MUST confirm the outcome explicitly**
  (succeeded, failed, or pending); silence there is dangerous.
- Announce async / Turbo-Stream updates in an `aria-live` region so assistive-tech users get the same feedback.

**Why:** a dead click (no spinner, no message) makes the user retry, double-submit, or abandon, and reads
as a broken product on a paid B2B tool. Feedback also prevents duplicate writes. The right HTTP status
drives the right UI (http-and-caching/use-correct-verbs-codes-and-problem-json); the message must
be human and PII-free (rgpd/always-minimize-personal-data, run copy through the ai-tell-remover).
Accessible status messages are part of WCAG.

**Good / Bad:**
```twig
{# Bad: the button does nothing visible on failure; the user clicks again, confused. #}
<button>Enregistrer</button>

{# Good: a pending state plus a place for the outcome, announced to assistive tech. #}
<button data-action="form#submit" data-form-target="submit">Enregistrer</button>
<div data-form-target="status" role="status" aria-live="polite"></div>
```
On the server, a failed save returns 422 and re-renders with errors (never a 200 hiding a failure, never
a 500 page); a success flashes a confirmation.

**See also:** [[never-assume-a-single-os-or-browser]], [[always-progressive-enhance-with-stimulus]], http-and-caching/use-correct-verbs-codes-and-problem-json, accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** review + e2e (assert the success and error messages appear; assert no dead click leaves the user without feedback).
