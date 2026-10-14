# Investigation

Start broad only when the user did not provide a Cloud URL or test ID. A copied test overview URL
can already contain the project identifier, run number, and test ID, including when it has a query
string, so try it directly before discovery.

```bash
# Discover a project
cy-cloud org list
cy-cloud project list --orgId <orgUuid>

# Find recent failing runs
cy-cloud run list --projectId <projectId> --status failed --limit 5

# Find failed specs and tests
cy-cloud spec list --projectId <projectId> --runNumber <runNumber> --status failed
cy-cloud test list --projectId <projectId> --runNumber <runNumber> --status failed
```

From those responses, inspect organization `uuid` and `name`; project `projectId` and `name`; run
`runNumber`, `status`, `branch`, and `createdAt`; spec `id`, `path`, and `status`; and test
`projectId`, `testId`, `specFilepath`, `testName`, `status`, and `attempts`.

When the user supplies a Cloud test overview or test-results URL, skip discovery:

```bash
cy-cloud test get --testResultUrl "<url>"
```

Inspect `projectId`, `runNumber`, `specFilepath`, `testName`, `status`, `attempt`, `attempts`,
`failureScreenshotPath`, and `failureScreenshotReason`.

Choose the failed `attemptNumber` from `attempts[]` before any replay query; never rely on the
final/default attempt for a flake. Then read [test-replay.md](test-replay.md).

Accepted paths include `/runs/<run>/overview/<testId>` and
`/runs/<run>/test-results/<testId>`, with optional trailing paths and query strings. Pass
`--testId` or `--testResultUrl`, never both.

## Inspect the failure

Get the test details first:

```bash
cy-cloud test get --testId <uuid>
```

Inspect `projectId`, `runNumber`, `specFilepath`, `testName`, `status`, `attempt`, `attempts`,
`failureScreenshotPath`, and `failureScreenshotReason`.

`attempts[]` provides `attemptNumber`, `errorName`, `errorMessage`, and `stackTrace`. Select the
failed `attemptNumber` explicitly for every replay query. `test get` can report the final passing
retry in `attempt`, so its default attempt is unsafe for flaky-test diagnosis.

Only request a screenshot when `test get` identifies a failed selected result for which a failure
screenshot can exist:

```bash
cy-cloud test get --testId <uuid> --screenshot ./artifacts
```

Inspect `status`, `attempt`, `attempts`, `failureScreenshotPath`, and `failureScreenshotReason`.

Read `failureScreenshotPath` only when the response actually returns a path. `test get` has no
attempt selector, so a flaky test may select its final passing retry even though `attempts[]`
contains an earlier failure. In that case, `--screenshot` returns
`failureScreenshotReason: "test passed"` and cannot retrieve the failed-attempt screenshot; continue
with the failed attempt's replay and source inspection instead of treating the absent file as
missing evidence. Record this as an evidence gap in the conclusion: the CLI currently has no
workaround for fetching that earlier attempt's screenshot, and its contents must not be inferred.

There is no explicit flake status, `--status flaky` filter, or `flakyCount` field. Flake can only be
inferred from `attempts[]`, so interpret retries carefully:

- A final pass after an earlier failed attempt is evidence of flake.
- Repeated failed attempts indicate a reproducible failure.
- A single failed attempt does not prove the test is non-flaky; retries may be disabled.

Cloud UI URLs can include filters such as `isFlaky=true`, but that query parameter is a UI filter,
not a flake field exposed by the CLI. Reconstruct the UI's classification from `attempts[]`: find an
earlier failed attempt followed by a passing retry.
