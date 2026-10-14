---
name: cypress-tap
description: >-
  Drives a running Cypress open-mode session through `cypress tap` to run and
  rerun specs, wait for fresh results, inspect reporter and command logs, and
  query or rewind the app under test through DOM, accessibility, styles, and
  snapshots. Use when authoring or debugging Cypress e2e or component specs,
  discovering selectors, diagnosing failures, or verifying test behavior
  without GUI interaction. Not for one-shot headless runs. Requires Cypress
  15.21+, a Chromium-family browser, and a running `cypress open` session.
metadata:
  version: 1.0.0
---

# Driving Cypress with `cypress tap`

`cypress tap` controls an already-running `cypress open` session. Use it to iterate on specs,
inspect the reporter and command log, and read the app under test without GUI interaction.
Use `cypress run` instead for a one-shot headless batch.

## Prerequisites

- Confirm from package metadata or the lockfile that the resolved Cypress is 15.21.0+ and
  contains `tap`. Do not use `tap --help` to probe an unknown older build; prereleases below the
  version floor may attempt session discovery instead of printing help.
- The session must use Electron, Chrome, Chromium, or Edge. Firefox and WebKit are unsupported.
- `cypress open` and any configured `baseUrl` dev server must already be running.
- The cwd chooses both the Cypress binary and the automatically selected session. When the
  target project pins an older Cypress, run `tap` from a compatible checkout and pass
  `--session <pid>` on every call.

## Route by task

- **Start, select, or poll a session:** read
  [session-lifecycle.md](references/session-lifecycle.md).
- **Run a spec or read its results:** read [session-lifecycle.md](references/session-lifecycle.md)
  and [reading-results.md](references/reading-results.md).
- **Author or inspect a spec:** read [recipes.md](references/recipes.md) and
  [reading-the-app.md](references/reading-the-app.md).
- **Diagnose a failure:** read [recipes.md](references/recipes.md),
  [reading-results.md](references/reading-results.md), and
  [reading-the-app.md](references/reading-the-app.md).
- **Command failure, hang, wrong project, or surprising output:** read
  [troubleshooting.md](references/troubleshooting.md).
- **One noninteractive batch:** use `cypress run`, not `tap`.

Read only the references required for the current task.

## Core commands

- `sessions`: reachable sessions, project roots, testing types, and browsers; JSON adds support
  and renderer health.
- `status`: lifecycle stage, selected spec, run identity, counts, build error, and active pin.
- `specs`: runnable project-relative spec paths for the session's testing type.
- `run <spec>`: dispatches a spec and returns immediately.
- `reporter`: spec overview and test ids; with `--test-id`, the complete test attempt.
- `command`: one command-log row with network data, snapshots, and console properties.
- `pin`: rewinds the app frame to a command snapshot.
- `dom`, `aria`, `inspect`: read the settled app or currently pinned snapshot.

All commands accept `--session <pid>`, `--json`, and `--timeout <ms>`. On a confirmed supported
build, use `npx cypress tap <command> --help` for command-specific flags.

## The non-negotiable verdict rule

`run` confirms dispatch, not execution, and returns before the new run starts. During that gap,
`status` and app reads can still return the previous run's plausible verdict and page.

For every explicit run:

1. Read the current `startedAt`.
2. Dispatch exactly one spec.
3. Poll one `status --json` response at a time.
4. Accept only `passed` or `failed` for the expected `spec` with a non-empty, changed
   `startedAt`. `startedAt` is null only when no run has ever started for that spec in the
   session — a build failure on first selection. A build that fails on rerun or on a watcher
   rebuild still advances `startedAt`, so it takes the normal path. Keep the null fallback
   (a changed observable baseline, or a preceding `loading`/`running` observation) for the
   first-selection case only.
5. Bound the loop and fail if no matching fresh verdict arrives.

Saving the active spec triggers an automatic watcher run. After editing, either use that run or
let it settle before taking a baseline and dispatching another. Never intentionally put two runs
in flight.

Blank and partial payloads from a successful `status` call occur transiently. Treat missing
fields as "keep waiting," not as a state change. A nonzero `status` exit is a command failure,
not a partial read: stop polling and report it.

Only `passed` and `failed` are verdicts. A build failure is `failed` with the diagnostic in
`status.error`, possibly before any tests exist. `status` is the only surface that carries that
diagnostic — `reporter` renders a failed build as an empty spec.

## Critical correctness rules

1. **Target the intended session.** If several sessions exist, or auto-selection behaves oddly,
   inspect `sessions` and pass `--session <pid>`. Auto-selection can choose another project or an
   unresponsive session.
2. **Preserve the binary location.** Cwd controls `npx` resolution on every call. When the
   project pins an older Cypress, run commands from a compatible checkout and pass
   `--session <pid>`.
3. **Do not parse failed commands.** Check the exit code before parsing JSON. Supported-build
   failures generally use stderr, but older compatibility failures may use stdout. An ambiguous
   selector is the intentional exception: it exits `1` and lists matches on stdout.
4. **Do not discard dispatch stdout while checking compatibility.** An older Cypress may print
   `Unknown command "tap"` and usage text to stdout; redirecting it hides the cause.
5. **Redirect potentially large JSON.** `reporter --test-id --json` and `command --json` can be
   hundreds of kilobytes. Save them to a file and parse the file.
6. **Check truncation before concluding absence.** `dom` and `aria` cap output. Narrow the
   selector or raise the limit when `(output truncated)` appears.
7. **Sanity-check the live frame before concluding absence.** A trailing pending/skipped test
   can leave the settled runner on a blank placeholder while app reads still exit `0`. Confirm a
   known app anchor. If the frame is blank, pin a snapshot from the last real command and read
   that state instead.
8. **Read results before editing or deleting the spec.** Results and snapshots live in the
   Cypress app's memory and can disappear on rerun, restart, rename, or deletion.
9. **Clear pins.** After inspecting a command snapshot, run `pin --clear`; otherwise later app
   reads continue to describe the pinned past. An exit-`0` `cleared:false` result is a benign
   no-op even if human output says `FAILED TO CLEAR PIN`.
10. **Use the right reader for live state.** Use `aria` for current form-control values:
    `dom` and `inspect` can show the initial HTML `value` attribute, and `inspect` may omit the
    accessibility value. Use `dom` for exact live-region, toast, status, and label text because
    `aria` may omit descendant text.

## Output contract

- Human output is for reading; `--json` is for parsing and may contain much more data.
- `status` exits `0` for known lifecycle stages, including `not connected`. Discovery,
  compatibility, unsupported-browser, and renderer failures exit `1`, sometimes with no stdout;
  a poller must fail fast on that nonzero exit.
- `dom`, `aria`, and `inspect` require exactly one selected element. Ambiguity exits `1` with
  candidate selectors. A miss is not a CLI failure: `dom` and `inspect` report `found:false`;
  `aria` returns an empty tree both for a miss and for an element with no accessibility node.
- Failures are prose without stable error codes. Branch on exit status, not message text.

## Performance defaults

- Prefer one `reporter --test-id` read over one `command` call per row.
- After a fresh verdict and live-frame sanity check, independent app reads may run concurrently.
- If bounded status polling fails, inspect `sessions` for `rendererResponsive: false`; restart a
  wedged renderer instead of increasing `--timeout`.
