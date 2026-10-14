# Sessions and the run lifecycle

## Start a compatible session

`tap` drives an existing `cypress open` process; it does not start Cypress:

```bash
npx cypress open --e2e --browser electron
npx cypress open --component --browser electron
```

Electron, Chrome, Chromium, and Edge are supported. Firefox and WebKit are listed by
`sessions` but refused by other commands. Ensure the configured `baseUrl` server is running
and the chosen testing type exists in the Cypress config.

The `--browser electron` examples attach a browser immediately and normally reach
`spec not selected`; they do not normally expose `browser not selected`. If Cypress was opened
without an attached browser, `run` launches one when necessary. The requested path must appear
in `tap specs`, whose list belongs to the session's current testing type.

## Find and select a session

```bash
npx cypress tap sessions
npx cypress tap sessions --json
```

Human output identifies each session's pid, project, testing type, and browser; unsupported
browsers carry an `(unsupported)` suffix. JSON additionally provides `browserSupported` and,
when a runner page exists, `rendererResponsive`. When no sessions exist, `sessions` prints
guidance rather than a JSON array and exits `0`; use `status --json` for readiness polling.

Session discovery can be internally partial during startup. For example, JSON may briefly show
`browserAttached:true` while `browserName` is null and `rendererResponsive` is absent. Treat
that row as not ready and keep waiting; do not infer browser support or renderer health from
missing fields.

Without `--session`, commands choose:

1. the only live supported session;
2. otherwise, the lowest-pid session whose project root equals the cwd;
3. otherwise, the lowest pid.

Unsupported browsers are excluded before selection; unresponsive sessions are not. Therefore:

- run commands from the intended project directory;
- when multiple sessions exist, bind `--session <pid>` on every call;
- if calls unexpectedly fail or stall, use `sessions` and select a row that is responsive.

`status --session <unknown-pid>` reports `not connected` and exits `0`. Other commands reject an
unknown pid. Get valid pids from `sessions`.

## Lifecycle stages

`status` exits `0` whenever it can determine a lifecycle stage:

| Stage                  | Meaning                            | Action                                       |
| ---------------------- | ---------------------------------- | -------------------------------------------- |
| `not connected`        | No matching reachable session      | Start Cypress or keep waiting                |
| `browser not selected` | Session is ready without a browser | `run` a spec; uncommon with `--browser`       |
| `spec not selected`    | Browser is ready; nothing has run  | `specs`, then `run`                          |
| `loading`              | The selected spec is building      | Wait                                         |
| `running`              | Tests are executing                | Reporter data is readable; app reads are not |
| `passed` / `failed`    | Terminal verdict                   | Read results                                 |

Unsupported browsers, unresponsive renderers, and other discovery or compatibility failures
are not lifecycle stages: `status` exits `1`, and stdout may be empty. Stop polling on a
nonzero exit; only an exit-`0` payload with transiently missing fields should be retried.

From `loading` onward, status includes the selected `spec` and `startedAt` (`null` while
loading). It can also include counts, a build `error`, and the active `pinned` snapshot.
A build failure is terminal `failed`; read `error` because no tests may exist.

## Wait for the dispatched run

`run` returns after dispatch. Until the incoming run starts, `status` can still show the
previous run's complete verdict. For every explicit run:

1. Capture a structurally valid baseline status.
2. Dispatch exactly one spec.
3. Poll one `status --json` response at a time.
4. Accept only `passed` or `failed` for the requested `spec`, with either:
   - a non-empty `startedAt` different from the baseline, including for a build that fails on
     rerun or on a watcher rebuild; or
   - only for a build failure on first selection, when no run has ever started for that spec
     and the terminal `startedAt` is null, a non-empty `error` after the baseline changed from a
     different observable state or that spec was observed in `loading` or `running`.
5. Bound the polling loop.

Within exit-`0` responses, blank, malformed, and partial payloads can occur transiently. Reject
them rather than treating missing fields as changed values. In particular, retry baseline
capture before dispatch; a blank baseline would make the previous verdict appear fresh.

Saving the active spec causes an automatic watcher run. After editing, either use that run or
wait for it to settle before taking a baseline and dispatching another. Keep only one run in
flight.

The canonical fresh-verdict rules are in [SKILL.md](../SKILL.md).

## Timeouts and polling cost

For connection-backed commands, `--timeout <ms>` replaces both the runner-discovery timeout
and the limit for each bounded CDP call in that invocation; it does not bound the whole spec.
The `runSpec` dispatch has its own 60-second GraphQL timeout. Use a bounded outer loop for the
run itself. A two-second sleep between status calls is a sensible floor because each invocation
starts Node and probes the session.

Do not raise `--timeout` in the run-polling workflow. If bounded `status` polling fails, inspect
`rendererResponsive` with `sessions`; restart a wedged renderer. Raise `--timeout` only for a
specific read against a known-responsive, unusually heavy page.

For specific failure messages and recovery steps, read
[troubleshooting.md](troubleshooting.md).
