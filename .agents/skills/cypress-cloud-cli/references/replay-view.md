# Replay View

`replay view` opens Test Replay in a real browser and lets you inspect the application under test at
any moment, instead of only reading command/network/log events. Reach for it when
[test-replay.md](test-replay.md) tells you a symptom happened but not why: an element is missing, has
a stale value, or its layout is wrong, and you need to compare DOM/ARIA/computed-style state before
and after a specific command.

Requires a Chromium-family browser on the machine. The CLI tries to find one automatically; point it
at a specific binary with `CYCLOUD_CHROMIUM_PATH` if it cannot, or a specific CDP debugging port with
`CYCLOUD_CHROMIUM_PORT` if the randomly-selected one conflicts with something else on the machine.

## Session model

Every subcommand targets one open viewer, identified by `--testId` or `--testResultUrl` (never both).
Only one viewer can be open per `testId` at a time; commands aimed at that test share it.

- Omit the test target and the command applies to the most-recently-opened viewer.
- Give a test target with no viewer open for it, and one is opened automatically — an explicit `open`
  step is a convenience, not a requirement.
- `--headless` only takes effect when a command actually launches a new viewer; it is ignored when an
  existing one is reused. Its default is not a fixed value: a new viewer launches headless when
  `CI` is set or an AI agent is detected running the CLI, headed otherwise. Pass `--no-headless`
  explicitly to force a headed browser even when running as an agent (useful when the user wants to
  watch), or `--headless` to force headless in an interactive shell.

```bash
cy-cloud replay view open --testId <uuid>
cy-cloud replay view list
cy-cloud replay view close --testId <uuid>
```

`open` reports `reused: true` when it handed back an already-running viewer instead of launching one.
`list` returns `.tests[]`, one entry per open viewer, with `testId`, `headless`, `launchedAt`,
`lastCommandAt`, `idleTimeoutMs`, and — once resolved from the local cache — `spec` and `testName`.
Use `list` to recover a `testId` you didn't note down, or to confirm a viewer is still open before
issuing a command that would otherwise open (and pay for) a new one.

Opening a viewer is comparatively slow and each idle viewer holds a browser process. Open a test once
and issue multiple `move`/query commands against it rather than reopening per query, and `close` it as
soon as you are done. An idle viewer closes itself after `CYCLOUD_REPLAY_IDLE_TIMEOUT` seconds
(default 300).

## Navigating a viewer

`move` changes what moment/attempt a viewer is showing; `status` reports where it currently is. Both
return the same shape: `testId`, `attempt`, `time`, `pin` (the pinned `commandId`, or `null`),
`pinnedCommand` (`commandId`/`type`/`name`/`state`/`message`, or `null` when nothing is pinned),
`startTime`/`endTime` for the attempt, `commandEvents`/`networkEvents`/`logs` counts, and `failedAt`
when the attempt failed. `--time` and `--pin` are mutually exclusive, and `move` rejects the call
before opening or touching any session if neither `--time`, `--pin`, nor `--attempt` is given:

```bash
# Jump to the point of failure
cy-cloud replay view move --testId <uuid> --time failure

# Jump to a specific timestamp, or the start/end of the attempt
cy-cloud replay view move --testId <uuid> --time 1753063204120
cy-cloud replay view move --testId <uuid> --time start

# Switch attempt (1-indexed, matching Cloud) without moving time
cy-cloud replay view move --testId <uuid> --attempt 2

# Pin to immediately before/after a specific command
cy-cloud replay view move --testId <uuid> --pin '<commandId>|before'
cy-cloud replay view move --testId <uuid> --pin '<commandId>|after'
```

Get `commandId` values from [`replay timeline`](test-replay.md), not from `replay view` itself. A pin
suffix is a 0-based snapshot index, or `before`/`after`; omit the suffix to use the command's default
snapshot.

## Querying the app at a moment

Move first, then query. `dom`, `aria`, and `inspect` all take a required `--selector` (CSS) and an
optional `--at <index>`, and every response — found, missing, or ambiguous — carries the `attempt`
and `time` the query actually ran against, so you can confirm which moment produced the data. The
response itself is one of three flat shapes (not nested under any wrapper key):

- `{ "exists": false, "attempt": ..., "time": ... }` — no match.
- `{ "ambiguous": true, "selector": "...", "count": <n>, "selectors": [{ "index": 0, "selector":
  "..." }, ...], "attempt": ..., "time": ... }` — more than one match. `selectors` covers only the
  first 10 matches in document order; re-query with a returned `selector`, or with `--at <index>`
  using any index up to `count - 1` (not just one of the first 10) to read a specific match directly.
- `{ "exists": true, ..., "attempt": ..., "time": ... }` — exactly one match (or `--at` picked one),
  with the subcommand's own data merged in.

```bash
cy-cloud replay view dom --testId <uuid> --selector '[data-cy="cart-total"]'
cy-cloud replay view aria --testId <uuid> --selector '[data-cy="cart-total"]'
cy-cloud replay view inspect --testId <uuid> --selector '[data-cy="cart-total"]'

# Disambiguate a known multi-match selector without a second round trip
cy-cloud replay view inspect --testId <uuid> --selector '.cart-line-item' --at 2
```

- `dom` returns the element's serialized HTML (including descendants) in `.dom`, and adds
  `truncated: true` when it was clipped to a size cap.
- `aria` returns the accessibility tree rooted at the element in `.aria`.
- `inspect` returns `.inspect`: `tag`, `attributes`, `box` (`x`/`y`/`width`/`height`), `styles`
  (computed), and `aria` (`role`/`name`/`states`) — the most useful single call for "what changed"
  questions.

```bash
cy-cloud replay view screenshot --testId <uuid> --path ./screenshots
```

`screenshot` returns `{ "screenshot": "<path>", "attempt": ..., "time": ... }`. `--path` may be a
directory (file named from the viewer's current timestamp) or an exact file path (must not already
exist); when omitted it saves to the OS temp directory, matching `test get --screenshot`, not the
current working directory. Filesystem `EPERM` means the path is not writable from here.

## Pinpointing which command broke an element

The pattern that makes `replay view` worth the cost over reading a screenshot or timeline: pin
immediately before and after the same suspect command and diff the `inspect` output.

1. Find a suspect command from the failure timeline (`replay timeline --aroundFailure`).
2. Open a viewer for the test.
3. Pin before the suspect command and inspect the element.
4. Pin after the same command and inspect again.
5. Diff `box`/`styles`/`attributes`/`aria` between the two. A change there is the exact effect of that
   command, not just correlation with the later assertion failure.
6. Close the viewer.

```bash
cy-cloud replay view open --testId <uuid>
cy-cloud replay view move --testId <uuid> --pin '<commandId>|before'
cy-cloud replay view inspect --testId <uuid> --selector '[data-cy="cart-total"]'
cy-cloud replay view move --testId <uuid> --pin '<commandId>|after'
cy-cloud replay view inspect --testId <uuid> --selector '[data-cy="cart-total"]'
cy-cloud replay view close --testId <uuid>
```

Report the before/after `inspect` diff as evidence in [diagnosis.md](diagnosis.md), not just the
element's final state.

## Relation to caching and rate limits

The first `replay view` for a test triggers the same replay download and cache-count-toward-rate-limit
behavior as `replay info`/`replay timeline` (see [test-replay.md](test-replay.md)). Once cached, asset
requests made by `replay view` for that test are batched and cached, so repeated `move`/query calls
against an already-open viewer are cheap.

## Recovery

- No viewer open and no test target given: `No Test Replay viewers are open. Provide a testId to open
  a new one.` — pass `--testId` or `--testResultUrl`.
- `move` with none of `--time`, `--pin`, `--attempt`: rejected before any session is opened; supply at
  least one.
- Launch failures point at a missing Chromium install; set `CYCLOUD_CHROMIUM_PATH` to a specific
  binary and retry. A CDP port conflict is rare but can be worked around with `CYCLOUD_CHROMIUM_PORT`.
- If a query never returns, or a viewer looks stuck, check `status`, then `close` and reopen rather
  than waiting past `CYCLOUD_VIEWER_TIMEOUT_MS` (default 30000).
