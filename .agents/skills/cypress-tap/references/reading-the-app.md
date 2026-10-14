# Reading the app under test: `dom`, `aria`, `inspect`, and `pin`

These commands read the app frame, not the Cypress UI.

## Wait for a settled run

`dom`, `aria`, and `inspect` require a `passed` or `failed` run. Creating a pin requires
retained snapshots and is refused while a spec is running. These checks are not synchronization:
immediately after `run` dispatch, the previous verdict and page can remain readable. Wait for
a fresh `startedAt` and matching `spec` first.

A terminal verdict does not prove that the live frame still shows useful app content. Before
concluding that expected content is absent, check a known app anchor or inspect `body`. If the
frame is blank after a trailing pending/skipped test, pin a snapshot from the last executed
test, read it, then clear the pin.

## `dom`: HTML and text

```bash
npx cypress tap dom
npx cypress tap dom --selector 'form'
npx cypress tap dom --selector html
npx cypress tap dom --selector '#app' --max-chars 80000
```

The default selector is `body`; `html` includes the document head. Output is the selected
element's `outerHTML`. The default 30,000-character cap is browser-side, and truncated output
is marked. Cypress instrumentation in `<head>` can consume that entire cap for `html` before
app markup appears. Never infer absence from truncated output: narrow the selector or raise the
cap.

`outerHTML` reports attributes, not current DOM properties. For example, after typing into an
input, `dom` may still show its original `value="1"` attribute even though its live value is
`3`. Use `aria` to read the current value of an accessible form control.

## `aria`: semantic structure

```bash
npx cypress tap aria
npx cypress tap aria --selector '[role=dialog]'
npx cypress tap aria --max-nodes 500
```

`aria` returns a compact accessibility tree of roles, accessible names, values, and states.
The default root is `body` and the default cap is 200 nodes. Use it to understand controls and
semantic structure; use `dom` for exact body text.

The tree is filtered to semantic roles, but the filter is incomplete: Chromium's internal
`ListMarker` and `LabelText` roles leak through as bare, nameless rows and count against
`--max-nodes`. Ignore them when reading structure, and expect the effective budget to run out
sooner than 200 nodes on list- or form-heavy pages.

Accessibility nodes do not necessarily expose descendant text. In particular, a live region
such as `role="status"` may appear as a bare node even while it contains a toast or status
message, and a `<label>` may surface as a bare `LabelText` row instead of its text. Use `dom`
when verifying live-region, toast, status, or label text. For form-control values, use `aria`.

## `inspect`: one element in detail

```bash
npx cypress tap inspect --selector '[data-cy=submit]'
```

`--selector` is required. Output includes attributes, curated computed styles, and box geometry.
It includes an accessibility section when the browser exposes a node; hidden or ignored elements
may omit that section. It helps explain visibility and interaction problems, but does not
reproduce Cypress's full actionability algorithm.

The element's tag name is not in the human output, even though `inspect --help` lists it and
`--json` carries it as `tag`. Read the tag from `dom`, or from `inspect --json`. The
accessibility section here is also unfiltered, so a semantically empty element reports
`role generic` where `aria` drops the node entirely — a `role` from `inspect` is not evidence of
meaningful semantics.

Do not use `inspect` to read a form control's live value. Its attributes can retain the initial
HTML value, and its accessibility section may omit a value that `aria` exposes. Use `aria` for
the current value.

## Selectors must identify one element

All three readers require exactly one selected element:

- One match: return the read.
- Multiple matches without `--at`: exit `1` and list candidate selectors.
- Multiple matches with `--at <index>`: read the 0-based match.
- No matches: `dom` and `inspect` return `found:false` with exit `0`; `aria` returns an empty
  tree.

Because an empty `aria` result can mean either no match or no accessibility node, use `dom` or
`inspect` to establish element absence. In JSON, only `inspect` echoes the missed selector;
`dom` returns only `{"found":false}`.

The ambiguity response derives candidate selectors for only a bounded number of matches, but
`--at` can select any valid index. A missing candidate means no unique selector was derived;
prefer a semantic query or add a stable `data-*` hook.

## `pin`: inspect a command snapshot

The live frame shows the run's final state. `pin` replaces it with a DOM snapshot captured on
a reporter command:

```bash
npx cypress tap command --test-id r4 --command-id 5
npx cypress tap pin --test-id r4 --command-id 5
npx cypress tap pin -t r4 -c 5 --at before
npx cypress tap pin -t r4 -c 5 --at 1
npx cypress tap pin --clear
```

Get test and command ids from `reporter`. For `pin`, `--at` is a snapshot name or 1-based
index; this differs from the readers' 0-based element index. With no `--at`, `pin` chooses the
last snapshot.

While pinned, `dom`, `aria`, and `inspect` read the snapshot. Always run `pin --clear` when
finished. Clearing when nothing is pinned—or while a spec is running—still exits `0` and
reports `cleared:false`. Human output may call this `FAILED TO CLEAR PIN`; despite that wording,
it is a benign no-op, so do not retry it as an error. Pins created manually in the Cypress
reporter are visible to `status` and can also be cleared by `tap`.

Snapshots are available only for retained open-mode tests. If a row has no snapshots or its
details were evicted according to `numTestsKeptInMemory`, rerun the spec or choose another row.
A snapshot is static DOM: scripts and network activity do not run.
An interacted element may carry `data-cypress-el="true"` in a snapshot; that is Cypress
instrumentation, not application markup.
