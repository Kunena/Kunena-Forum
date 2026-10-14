# Diagnosis

Do not merely restate Cloud fields. Use this evidence order after reading
[investigation.md](investigation.md) and, when replay is available, [test-replay.md](test-replay.md).
When the timeline narrows the failure to an element but not to the command that changed it, add
[replay-view.md](replay-view.md) to pin before/after a suspect command and diff the element's state.

1. Test details and all attempts.
2. Select the failed attempt and pass its `attemptNumber` via `--attempt` to every replay query.
3. Failure screenshot, only when a path exists for a failed selected result.
4. Default replay failure context for that failed attempt.
5. For end-to-end specs, if the default has no network events, rerun with `--network All`; skip this
   for mocked unit/component specs whose `replay info.networkEvents` is `0`.
6. Network failures, missing requests, or incomplete responses before the failed command.
7. Application console errors at or before `failedAt`, parsing the JSON string in `payload`.
8. A passing attempt or passing run for comparison when the cause remains ambiguous. Start with
   `--commands`; do not use `--all` for the initial comparison because task events can contain very
   large code-coverage payloads.
9. If replay establishes only the assertion symptom, use the stack trace to read the spec and
   relevant source. Inspect the immediately preceding and following tests plus their hooks,
   especially when tests pass alone but fail in sequence. Check module-level state, globals,
   aliases, mocks, and fixtures that an adjacent test mutates and `beforeEach` does not reset. Cloud
   artifacts can reveal the symptom without revealing why the state already had that value.

For "element never appeared" failures, check in order:

1. A preceding 4xx or 5xx response.
2. A request that never completed.
3. An expected request that never started.
4. A console exception.
5. Selector, timing, or rendering behavior visible in the screenshot and command timeline.
6. If none of the above is conclusive, open a [replay view](replay-view.md), pin before/after each
   suspect command near the element, and compare `inspect` output to find the command that left it
   missing, stale, or misrendered.

Report:

- the project/run/spec/test that was inspected;
- the failing attempt and Cypress command;
- the visible symptom and assertion;
- the most likely root cause, with timeline/network/log evidence;
- whether retry history suggests flake, or `unknown` when attempts are insufficient;
- uncertainty and the next discriminating check when evidence is incomplete.
