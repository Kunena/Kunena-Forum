---
name: cypress-cloud-cli
description: Runs the cy-cloud CLI to inspect Cypress Cloud organizations, projects, runs, specs, tests, failure screenshots, and Test Replay data. Use when the user mentions Cypress Cloud, cy-cloud, cypress-cloud-cli, a Cloud run or test URL, failing or flaky Cypress tests, Test Replay, run triage, Cloud artifacts, or asks why a recorded test failed.
metadata:
  version: 1.1.0
---

# Cypress Cloud CLI

Use `cy-cloud` directly to investigate Cypress Cloud. Execute the commands, inspect their JSON, and
report conclusions supported by the returned data. Do not send the user to the Cloud UI when the CLI
can answer the question.

## Start safely

The published binary requires Node.js 22.21.0 or newer.

Run these checks only on first use, when the environment is unknown, or after an installation or
authentication error. If `cy-cloud` already worked in this session, invoke the investigation command
directly instead of spending another turn rechecking the setup.

```bash
node --version
cy-cloud status
cy-cloud --help
```

Assume `cy-cloud` is already installed and available on `PATH`. Run it from the user's current
workspace; do not locate, clone, build, or depend on the cloud-cli source repository. If the command
is unavailable, stop and report the missing prerequisite. The user can install it with:

```bash
npm install -g @cypress/cloud
```

If unauthenticated, ask the user to choose an authentication method:

```bash
cy-cloud login                 # interactive OAuth
cy-cloud login --token "$PAT"  # persist a Personal Access Token
```

For CI or an existing environment, `CYPRESS_CLOUD_TOKEN` takes precedence over stored credentials
and writes no credential file.

Never print, echo, read back, or commit tokens. Never inspect
`~/.config/cy-cloud/auth.json` or `cypress.env.json`; use `cy-cloud status`.

The organization must have the Cypress Cloud CLI integration enabled. A response saying it is not
enabled requires action from an organization administrator; it cannot be fixed with another CLI command.

## Discover instead of guessing

Every command supports:

```bash
cy-cloud --help
cy-cloud <command> --help
cy-cloud <command> --schema
```

Treat `cy-cloud --help` as authoritative for available commands, per-command `--help` as
authoritative for arguments, and `--schema` as the intended response contract. Use them before
constructing a query when a command, flag, or field is uncertain. Do not memorize a command
inventory from this skill; the installed CLI may be newer or older than these instructions.
Validate the actual response too: `run get` can return a fractional `duration`, and
`replay info.failedAt` can be a fractional epoch even when the schema declares an integer. Treat
those values as numbers and report the schema mismatch if it matters; do not discard or round the
data.

Successful data commands write JSON to stdout. Parse that JSON directly; do not install a JSON
tool to run this skill. Replay download progress is written to stderr, so never combine stderr with
stdout before parsing JSON:

```bash
cy-cloud replay timeline --testId <uuid> --attempt <failedAttemptNumber> 2>/dev/null
```

If a command fails, rerun without suppressing stderr. Argument-validation errors can appear on
stdout; command execution errors appear on stderr. Both exit with status 1.

Cloud-backed fetches may be blocked by security sandboxes even when local commands such as `status`
succeed. Run Cloud data commands with network access allowed. If a fetch fails with a sandbox or
network denial, retry that same command with broader network permission before diagnosing a CLI,
authentication, or Cloud problem.

Replay downloads are cached on disk. Confirm the location with `cy-cloud cache info` (`.location`)
and run Cloud commands so that directory can be written and still exists later in this session. An
ephemeral sandbox that cannot persist it will re-download replay data or fail cache validation,
including on otherwise local commands. Do not relocate the cache into the user's project.

Cloud API calls are rate limited per user. Prefer considered, methodical requests: reuse
identifiers already in a URL or prior response, skip org and project discovery when an ID is
known, and do not page further than the question requires. After the first replay download for a
test, `replay info` and `replay timeline` run against the local cache and do not count against
that limit, so those follow-up queries can be chatty. If a command reports an exceeded rate
limit, stop issuing Cloud-backed requests and report the limit; do not retry in a tight loop.

## Identifiers and pagination

For `projectId`, use the project identifier already present in a Cloud URL; both the short slug and
the project UUID are accepted by project/run lookups and run-scoped spec/test lists. Do not perform
organization and project discovery merely to replace a UUID with a slug. `runNumber` is the
per-project run number. `specId` and `testId` are UUIDs. Attempt numbers are 1-indexed, matching
Cypress Cloud.

Cloud list responses have `.pagination`; request more pages when the question is not limited to the
first page.

## Route by task

Read only the references required for the current task:

- **Find orgs, projects, runs, specs, or tests, or open a Cloud URL:** read
  [investigation.md](references/investigation.md).
- **Inspect attempts, flakes, or failure screenshots:** read
  [investigation.md](references/investigation.md).
- **Query Test Replay:** read [investigation.md](references/investigation.md) and
  [test-replay.md](references/test-replay.md).
- **Inspect the app itself (DOM, ARIA, computed styles, a live screenshot) at a moment in the test, or
  find which command changed an element:** read [replay-view.md](references/replay-view.md).
- **Diagnose why a recorded test failed:** read [investigation.md](references/investigation.md),
  [test-replay.md](references/test-replay.md), [replay-view.md](references/replay-view.md) when the
  timeline alone does not explain the symptom, and [diagnosis.md](references/diagnosis.md).

## Cache and common recovery

```bash
cy-cloud cache info
cy-cloud cache cleanup
cy-cloud cache clear
```

Use `cache cleanup` for normal maintenance. Use `cache clear` only when replay resolution appears
stale or corrupted because it forces future replay downloads. If replay re-downloads every
command, the cache directory is not persisting; retry with a sandbox that can write to the
location reported by `cache info`.

Common outcomes:

- Missing expected fields: inspect that command's `--schema`.
- JSON parse failure: ensure stderr was not merged into stdout.
- Cache validation or write errors from the sandbox: retry so the cache directory is writable
  and persistent; do not copy the cache into the project.
- `cy-cloud version` reporting `"status": "Unknown"` does not by itself mean the installation is
  broken or outdated. Compare `version` and `latestVersion`; upgrade only when those values or an
  explicit compatibility error indicate it.
- Exceeded rate limit: stop Cloud-backed requests and report the limit; do not retry in a loop.
  Cached replay queries are exempt once the download has succeeded.
- Replay-specific errors: read [test-replay.md](references/test-replay.md).
