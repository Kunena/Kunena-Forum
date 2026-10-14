# Test Replay

Select the failed `attemptNumber` from `attempts[]` before every replay query. See
[investigation.md](investigation.md) if that attempt is not yet known.

Check whether replay resolution succeeds and inspect the selected attempt:

```bash
cy-cloud replay info --testId <uuid> --attempt <failedAttemptNumber> 2>/dev/null
```

`replay info` has no availability boolean. A successful response means replay data resolved; an
error means it is not yet available or inaccessible.

With no filters, `replay timeline` returns the 25 commands leading to the failure, the failed
command, and XHR/Fetch network events. Start with this default:

```bash
cy-cloud replay timeline --testId <uuid> --attempt <failedAttemptNumber> 2>/dev/null
```

Inspect each event's `category`, `name`, `message`, `state`, `method`, `url`, and `response`.

Then narrow or widen only as needed:

```bash
# Failed command
cy-cloud replay timeline --testId <uuid> --attempt <failedAttemptNumber> --failedOnly 2>/dev/null

# More commands around failure
cy-cloud replay timeline --testId <uuid> --attempt <failedAttemptNumber> \
  --aroundFailure 100 2>/dev/null

# Network diagnosis
cy-cloud replay timeline --testId <uuid> --attempt <failedAttemptNumber> --network All 2>/dev/null

# Application console output
cy-cloud replay timeline --testId <uuid> --attempt <failedAttemptNumber> --logs 2>/dev/null

# Full timeline; page because this can be large
cy-cloud replay timeline --testId <uuid> --attempt <failedAttemptNumber> \
  --all --limit 200 --page 1 2>/dev/null

# Compare a passing retry without pulling task and code-coverage payloads
cy-cloud replay timeline --testId <uuid> --attempt <passingAttemptNumber> \
  --commands --limit 100 --page 1 2>/dev/null
```

For network events, inspect `method`, `url`, and `response.status`. Log events store console output
as a JSON string in `payload`; parse that string and read `type` plus each argument's `value` or
`description`. For command comparisons, inspect `name`, `message`, and `state`.

Any explicit filter disables the default failure context. For example, `--logs` returns only logs.
To add logs while retaining useful failure context, specify every desired category:

```bash
cy-cloud replay timeline --testId <uuid> --attempt <failedAttemptNumber> \
  --commands --aroundFailure 25 --network XHR,Fetch --logs 2>/dev/null
```

`replay timeline` accepts `--page` and `--limit` but returns only `.events`, with no pagination
metadata. For an unfiltered `--all` query, compare returned event counts across pages with
`replay info` totals (`commandEvents + networkEvents + logs`). Continue while a page returns the
requested limit or the accumulated count is below that total.

Timeline events share an ordered `.events[]` array and use `category` values `command`, `network`,
and `log`. Preserve array order when correlating events: command events may not expose a usable
timestamp field. Use timestamps when an event provides one and compare timestamp-bearing logs and
network events with `replay info.failedAt`; otherwise use array position relative to the failed
command. Ignore events after the failure unless the question specifically concerns teardown or
post-failure behavior. Dev-server messages emitted after `failedAt`, such as
`Invalid Host/Origin header`, are not evidence for the assertion failure.

The default network filter can silently return zero events for applications whose traffic is
recorded under resource types other than XHR or Fetch. If the default timeline has no network
events, rerun with `--network All` before concluding there was no network activity for an end-to-end
spec. For a unit or component-testing spec, first inspect `replay info.networkEvents` and the test
setup. If `networkEvents` is `0` and dependencies are mocked or the component is not exercising an
application backend, skip `--network All`; it adds noise without new evidence.

Network events do not include a `resourceType` field in their output. `--network` can filter by
resource type, but the returned events cannot identify that type. If the type distribution matters,
query the relevant types separately and compare each result's `.events | length`.

The first replay request downloads and caches a SQLite database, often for an entire spec, and
that download counts as a Cloud API request. Later `replay info` and `replay timeline` queries
for that spec are local, do not count against the rate limit, and can be chatty — but only when
that cache directory still exists.

## Replay recovery

- `Replay not available`: use test details and the screenshot when one exists; no replay can be
  fetched.
- `No matching records found for testId and attempt`: verify the 1-indexed attempt with
  `replay info`.
- Empty replay events: rerun with no filters, then `--commands`; use paged `--all` only when the
  investigation needs every category because task events can contain large payloads.

When the timeline shows a symptom (missing/stale element, wrong layout) but not which command caused
it, open a live viewer and inspect the app directly: read [replay-view.md](replay-view.md).

To turn replay evidence into a root-cause conclusion, read [diagnosis.md](diagnosis.md).
