# Recipes

Read this file when authoring a spec, hunting a flake, or diagnosing a failed run. All recipes
assume the session is selected and the fresh-verdict polling workflow from `SKILL.md` is in use.

## Author a spec against the real app

`tap` can read the app only after a spec has settled. Start with a temporary probe spec that
navigates to the state you need. Create `cypress/e2e/_probe.cy.js`:

```javascript
it('probe', () => {
  cy.visit('/checkout')
})
```

Run the probe using the fresh-verdict workflow in `SKILL.md`. After it reaches a matching
`passed` or `failed` verdict, read the app:

```bash
npx cypress tap aria
npx cypress tap dom --selector 'form'
npx cypress tap inspect --selector '[data-cy=pay]'
```

Read `aria` first for roles, accessible names, live form-control values, and control states.
Use `dom` for exact text—including live-region and toast text—and HTML attributes. Use
`inspect` for attributes, styles, box geometry, and the accessibility node, but not for a
control's live value.

To discover selectors, deliberately make a broad read:

```bash
npx cypress tap inspect --selector 'form button'
```

The expected exit `1` ambiguity response lists unique selectors for the matches. A selector
shown as `-` could not be made unique; prefer a semantic query or add a stable `data-*` hook.

Grow the probe to create deeper state, then write the real spec. After it passes, read
`reporter --test-id <id>` and verify that commands yielded the intended subjects, assertions
read what you meant, and stubbed routes actually matched. Keep the probe until inspection is
done: deleting or renaming the selected spec drops its in-memory results.

Saving the active spec triggers Cypress's file watcher. Let that run settle before taking a
new `startedAt` baseline and dispatching another run, or use the watcher's run as the run you
inspect. Do not put both in flight.

## Report a run

After the fresh-verdict poll completes:

```bash
npx cypress tap status
npx cypress tap reporter
```

Report the `status.results` counts. For a build failure, use `status.error`; no tests may exist.
For a failed test, get its id from `reporter`, then read:

```bash
npx cypress tap reporter --test-id r4
```

## Hunt a flake

Rerun in a bounded loop and stop on the first failure. Another run overwrites the result you
need to diagnose. Apply the fresh-verdict workflow before evaluating each attempt, preserve the
first failed result, and return a nonzero outcome when the sought failure occurs. Completing the
requested attempts without a failure is not proof that the spec is stable.

If Cypress retries a test, `reporter --test-id` defaults to the latest attempt, which may pass:

```bash
npx cypress tap reporter --test-id r4
npx cypress tap reporter --test-id r4 --attempt 1
```

Read the failed attempt before editing. Re-read each failure rather than assuming it is the
same one; fixing one flake may expose another. A moving failure site can indicate environmental
load rather than an assertion that needs a longer timeout.

## Diagnose a failure

First capture the failed test id:

```bash
npx cypress tap reporter
```

Then inspect the test, failing command, and app at that command's snapshot:

```bash
npx cypress tap reporter --test-id r4
npx cypress tap command --test-id r4 --command-id 4
npx cypress tap command --test-id r4 --command-id 4 --json > .tap-command.json
npx cypress tap pin --test-id r4 --command-id 4
npx cypress tap inspect --selector '.action-disabled'
npx cypress tap pin --clear
```

Read the complete test view before assuming its last command is the cause; an uncaught-exception
event or hook error earlier in the log may be the real failure. Always clear the pin, or later
app reads continue to describe the past.

## Inspect app state at a step

Use `command` to confirm that the row has snapshots, then `pin` it. While pinned:

- `aria` shows structure and control state.
- `dom` shows markup and text.
- `inspect` shows one element's attributes, styles, geometry, and accessibility node, but may
  omit its live form-control value.

Run `pin --clear` afterward. If uncertain, `status` reports the active pin as `⚲ PINNED` in the
human output and as `pinned` in JSON.
