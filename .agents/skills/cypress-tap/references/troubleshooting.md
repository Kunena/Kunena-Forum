# Troubleshooting

On supported builds, `tap` failures are generally prose on stderr with exit `1`; branch on exit
status before parsing output. Selector ambiguity intentionally lists matches on stdout, and
older compatibility failures may also use stdout.

## Session problems

| Symptom                 | Action                                                           |
| ----------------------- | ---------------------------------------------------------------- |
| No session found        | Start `cypress open`, select a testing type, then retry          |
| Unknown `--session` pid | Run `tap sessions` and use a listed pid                          |
| Session unreachable     | Confirm Cypress and its browser are still open                   |
| No browser attached     | Run a listed spec; `run` can launch the browser                  |
| Unsupported browser     | Reopen in Electron, Chrome, Chromium, or Edge                    |
| Renderer not responding | Check `tap sessions`; select a responsive pid or restart Cypress |

`status` reports `not connected` with exit `0` for no session, a stale session, or an unknown
pid. It exits `1` for errors such as an explicitly selected unsupported-browser session. Treat
that as a failure, not a transient empty status. `sessions --json` distinguishes live pids and
reports renderer health.

Without `--session`, an unresponsive process can still win automatic selection. If healthy and
wedged sessions coexist, bind `--session <pid>` to a responsive row. Do not raise `--timeout`
for polling; it does not repair a wedged renderer.

## Version and binary mismatch

Schema mismatch errors name which side is older; update that Cypress installation and restart
the session when needed.

If `npx cypress tap` prints `Unknown command "tap"`, cwd resolved a Cypress version without
`tap`. Resolve commands from a compatible checkout and pass the target pid:

```bash
npx --prefix "PATH_TO_TAP_CHECKOUT" cypress tap --session 73952 reporter
```

Do not discard dispatch stdout: older Commander versions may print unknown-command evidence
there. Confirm the resolved Cypress version from package metadata or the lockfile before using
`tap --help`; older prereleases may attempt session discovery and print an error on stdout
instead of help.

## Spec and run problems

| Symptom                                        | Action                                                                                                      |
| ---------------------------------------------- | ----------------------------------------------------------------------------------------------------------- |
| No spec has run                                | `tap specs`, dispatch one, then wait for a fresh verdict                                                    |
| Spec is loading or running                     | Continue the bounded status poll                                                                            |
| No matching spec path                          | Copy the exact path from `tap specs`; check `specPattern`                                                   |
| Testing type is not configured                 | Configure it or open Cypress in a supported project type                                                    |
| Session has no project                         | Open the project in Cypress                                                                                 |
| Build failure                                  | Read `status.error`; reporter data may be empty                                                             |
| Dispatch succeeds but no fresh verdict arrives | Confirm the expected `spec`, session pid, and watcher activity; restart Cypress if the bounded loop expires |

Open mode automatically reruns the active spec when it is saved. After editing, let that run
settle before taking a baseline and explicitly dispatching, or use the watcher run itself.

Results and snapshots live in the runner's memory. A rerun, restart, interrupted run, or
renaming/deleting the selected spec can remove them; inspect results before editing again.

## Test, command, and snapshot ids

| Symptom                 | Action                                                                |
| ----------------------- | --------------------------------------------------------------------- |
| Test id not found       | Run `tap reporter`                                                    |
| Attempt not found       | Use a 1-based attempt shown by reporter, or omit it for latest        |
| Command id not found    | Run `tap reporter --test-id <id>`                                     |
| Command id is ambiguous | Qualify it, for example `h1:3`                                        |
| Snapshot not found      | Use a listed name or 1-based snapshot index                           |
| No snapshot available   | Rerun the spec or choose a row whose `command` output lists snapshots |

## App-read problems

- A selector miss is successful: `dom` and `inspect` return `found:false`; `aria` returns an
  empty tree.
- Selector ambiguity exits `1` with candidates on stdout. Use a unique selector or
  `--at <0-based-index>`.
- For a live form-control value, use `aria`; `dom` and `inspect` can show the initial HTML
  `value` attribute instead. For live-region or toast text, use `dom`; `aria` may omit its text.
- If everything appears absent after a verdict, verify that the live frame contains the app.
  Pin a retained command snapshot when the final frame is blank.
- `pin --clear` with no active pin exits `0` with `cleared:false`. Its human
  `FAILED TO CLEAR PIN` wording describes a benign no-op; do not retry it as a failure.
- On a confirmed supported build, invalid options and missing companion flags print generated
  command help; use `npx cypress tap <command> --help` for current flags.

## Unclassified failures

Retry once after checking `status`. If the failure remains generic, collect diagnostics:

```bash
DEBUG=cypress:cli:tap,cypress:cli:cypress-sessions npx cypress tap <command> …
```

Include that output and the invocation in a Cypress issue.
