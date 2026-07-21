---
name: always-give-user-feedback
description: Every user action produces clear, timely feedback; show a pending state, confirm success, and on failure show a human message and a way forward.
paths: ["catalyst/templates/**", "catalyst/assets/**", "catalyst/src/Controller/**"]
severity: should
---
# Always give the user feedback

**Rule:** Every action a user takes gets visible, timely feedback:
- **Pending:** on submit or a slow action (saving a page, regenerating a PDF), show a pending state
  (disable the button, show a spinner) so the user knows it is working and cannot double-submit.
- **Success:** confirm it worked (a flash, a toast, an inline confirmation, or the updated view). The
  user should never wonder whether the page saved.
- **Failure:** show a clear, human message ("Cette page n'a pas pu etre enregistree. Reessayez.") and
  keep the UI usable; never a silent no-op, a stuck spinner, or a blank swapped container. Validation
  errors re-render the form with field errors (422); server errors surface a friendly message, never a
  raw stack trace.
- **Destructive or slow flows (deleting a book or page, regenerating a PDF, exporting content JSON) MUST
  confirm the outcome explicitly** (succeeded, failed, or pending); silence there is dangerous.
- Announce async and Live Component updates in an `aria-live` region so assistive-tech users get the
  same feedback.

**Why:** a dead click (no spinner, no message) makes the user retry, double-submit, or abandon, and
reads as a broken tool. Feedback also prevents duplicate writes. The right HTTP status drives the right
UI (http-and-caching/use-correct-verbs-codes-and-problem-json); the message must be human, so run copy
through the aubaine-content-writer skill's `docs/no-ai-tells.md`. Accessible status messages are part of
WCAG (4.1.3 Status Messages).

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

**Enforced by:** review + functional test (assert the success and error messages appear; assert no dead click leaves the user without feedback).
