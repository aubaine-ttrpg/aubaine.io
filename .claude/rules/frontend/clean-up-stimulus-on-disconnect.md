---
name: clean-up-stimulus-on-disconnect
description: Stimulus controllers are idempotent in connect() and tear down every timer, interval, observer, and event listener in disconnect().
paths: ["catalyst/assets/**/*.js", "catalyst/assets/controllers/**"]
severity: should
---
# Clean up Stimulus on disconnect

**Rule:** Make `connect()` idempotent (calling it twice sets up no duplicate state) and tear down everything it created in `disconnect()`: clear timers and intervals, disconnect observers, and remove event listeners. Hold references to anything you start so you can stop it.

**Why:** Swup swaps the shell containers and reconnects Stimulus controllers repeatedly within one long-lived page, so a timer or listener left running on `disconnect()` survives the navigation and stacks up. The result is double-bound handlers firing twice, observers watching detached nodes, and memory that grows on every navigation.

**Good / Bad:**
```js
// Bad: interval started but never cleared; leaks on every Swup visit.
connect() {
  setInterval(() => this.refreshPreview(), 5000);
}

// Good: keep the handle, clear it on disconnect.
connect() {
  this.timer = setInterval(() => this.refreshPreview(), 5000);
}
disconnect() {
  clearInterval(this.timer);
}
```

**See also:** [[always-progressive-enhance-with-stimulus]], [[keep-navigation-swup-friendly]].

**Enforced by:** review + functional test (repeated Swup navigation does not multiply timers or listeners).
