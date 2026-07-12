---
name: clean-up-stimulus-on-disconnect
description: Stimulus controllers must be idempotent in connect() and tear down every timer, interval, observer, and event listener in disconnect().
paths: ["assets/**/*.js", "assets/controllers/**"]
severity: should
---
# Clean up Stimulus on disconnect

**Rule:** Make `connect()` idempotent (calling it twice sets up no duplicate state) and tear down everything it created in `disconnect()`: clear timers and intervals, disconnect observers, and remove event listeners. Hold references to anything you start so you can stop it.

**Why:** Turbo navigations connect and disconnect controllers repeatedly within one long-lived page, so a timer or listener left running on `disconnect()` survives the navigation and stacks up. The result is double-bound handlers firing twice, observers watching detached nodes, and memory that grows every navigation (REVIEW_DOCTRINE §10.3 "Stimulus controllers are idempotent on connect() and clean up timers, observers, and event listeners on disconnect()").

**Good / Bad:**
```js
// Bad: interval started but never cleared; leaks on every Turbo visit.
connect() {
  setInterval(() => this.refresh(), 5000);
}

// Good: keep the handle, clear it on disconnect.
connect() {
  this.timer = setInterval(() => this.refresh(), 5000);
}
disconnect() {
  clearInterval(this.timer);
}
```

**See also:** [[always-progressive-enhance-with-stimulus]], [[keep-app-native-ready]].

**Enforced by:** review + functional test (repeated Turbo navigation does not multiply timers or listeners).
