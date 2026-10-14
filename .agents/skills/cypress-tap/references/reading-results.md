# Reading results: `specs`, `run`, `reporter`, and `command`

## Choose and run a spec

```bash
npx cypress tap specs
npx cypress tap run cypress/e2e/login.cy.ts
```

`specs` lists project-relative paths for the session's current testing type, most recently
modified first. Copy a listed path exactly; files outside `specPattern` do not appear. Under
`--json`, each entry carries the path as `relativePath`.

`run` confirms dispatch only. Use the fresh-verdict workflow in `SKILL.md` before trusting
status or reading the app. A build failure reaches `failed` with its diagnostic in
`status.error`, possibly before tests exist.

## Read the spec overview

```bash
npx cypress tap reporter
```

The overview contains run counts, suites, tests, retries, and test ids such as `r4`. Use those
ids with `reporter --test-id`, `command`, and `pin`. Test ids depend on the suite structure, so
never infer one from declaration order or from examples; copy it from `reporter`.

`reporter` and `command` are readable once the runner exists, including while tests are
running. During `loading` and before the first run, both report `No spec has run yet.`

`reporter` cannot report a build failure. Its spec-level result carries no error field, so a spec
that failed to compile renders as `No tests were found in this spec.` — indistinguishable from a
spec that genuinely declares no tests. If no runner ever started, `reporter` instead fails with
`SPEC_NOT_STARTED`. Never diagnose an empty-looking `reporter` on its own: read `status.error`
first.

## Read one test attempt

```bash
npx cypress tap reporter --test-id r4
npx cypress tap reporter --test-id r4 --attempt 1
```

The test view includes routes, hooks, command-log rows, retries, and failure output. `--attempt`
is 1-based and defaults to the latest attempt.

Read the full test view before deciding which row failed. Hook errors and uncaught-exception
events may be the cause even when the test body's final command looks suspicious.

When validating a newly authored test, also confirm:

- command subjects and assertion messages match the intended elements;
- stubbed routes have nonzero match counts;
- the commands describe the user flow the test was meant to exercise.

## Command ids

Each reporter row has an id accepted by `command` and `pin`:

- A number such as `5` chooses the test-body row first, then a unique hook match.
- A hook-qualified id such as `h1:3` selects row 3 in hook `h1`.
- `r4:2` explicitly selects row 2 in test body `r4`.
- Event and system rows use attempt-wide ids such as `e1`.
- Route registrations are listed in `ROUTES`; they are not command rows.

Numbering restarts in each hook. If an unqualified number matches multiple hook rows and no
test-body row, the command lists candidates; rerun with a qualified id.

## Inspect one command row

```bash
npx cypress tap command --test-id r4 --command-id 5
npx cypress tap command -t r4 -c h1:3 -a 1
```

Depending on the row, output can include network details, DOM snapshots, console properties,
mouse events, and an error. `SNAPSHOTS` identifies whether the row can be pinned.

### Console-property depth and JSON

- Default human output summarizes nested and long values.
- `--depth <n>` or `--depth all` expands nesting for human output.
- `--json` returns the raw result and requests complete console properties.

`--depth` does not reveal withheld long strings; use `--json`. Redirect JSON to a file because
command and test payloads can be large:

```bash
npx cypress tap command -t r4 -c 3 --json > .tap-command.json
node -p "require('./.tap-command.json').consoleProps.error"
```

Check the first command's exit code before parsing. A failing command's serialized error is
under `consoleProps.error`, not a top-level `error` field.
