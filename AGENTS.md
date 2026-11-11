# Kunena

Kunena is a forum component for Joomla, written in PHP. This branch (`K8.0`) is Kunena 8.0: Joomla 6, PHP 8.4 or later.

## Layout

- `src/site`: the frontend component (`src/Controllers`, `src/Models`, ...) and the templates (`template/aurelia` is the default,
  on Bootstrap 5; `template/system` holds the fallback layouts; `template/agora` is a second template).
- `src/admin`: the backend component, the database schema (`sql/`, `install/`), the forms and the language files.
- `src/libraries/kunena/src`: the library, namespace `Kunena\Forum\Libraries` (forum objects, access, BBCode, uploads, email...).
- `src/plugins`: the plugins (`plg_kunena_*`, `plg_system_kunena`, ...).
- `tests`: `Unit` and `Integration` (PHPUnit), `System` (Cypress). Read `tests/README.md` before adding a test.
- `docs`: the documentation of the users and of the developers.

## Set up a fresh checkout

```bash
composer install
```

`vendor/` is not in git, so a new checkout or worktree needs this once. There is no `.env` file to copy and nothing to build for
the PHP tests.

## Run it

Kunena has no dev server of its own. It runs inside a Joomla site whose Kunena folders are links to `src/` of one checkout
(`tests/README.md`, "Cypress against the checkout"). A git worktree is therefore not served by any site: a change in a worktree
is checked with the unit tests and by reading the code, not in a browser.

## Validate

```bash
composer validate --no-check-all --strict
KUNENA_TEST_JOOMLA=/path/to/joomla vendor/bin/phpunit --testsuite Unit
vendor/bin/php-cs-fixer fix --dry-run --diff path/to/changed/File.php
```

- The unit tests need the framework of a Joomla installation in `KUNENA_TEST_JOOMLA`; without it some tests fail.
- `bash factory/checks.sh` runs these checks the way the software factory does (it reads `KUNENA_TEST_JOOMLA` from the
  environment or from `factory/checks.env`), plus `php -l` on the PHP files the branch changed and the `Integration` suite
  on the database `<database of that Joomla>_phpunit` (a skipped integration test fails the checks). The code style is
  reported but does not fail the checks, as in the CI.
- The Cypress tests need a Joomla site. `bash factory/checks.sh` does not run them; the gate of the software factory does
  (`FACTORY_CYPRESS=1`): all specs except `install/`, through `python3 factory/bin/site-cypress`, which points the links of
  the site in `KUNENA_TEST_SITE` (`factory/checks.env`) at the job's worktree for the run and back afterwards. A test which also fails on the main checkout (the base branch) is listed and
  ignored: only a test the branch breaks fails the gate. The gate before the merge runs Cypress again only when the sync with
  the base branch changed the branch. Outside the
  gate, run Cypress only when asked, and never the install specs.
- Never run `php-cs-fixer fix` without `--dry-run` on files you did not change.

## Conventions

- New behaviour of a library class gets a unit test which mirrors its path (`tests/README.md`, "Where a new test goes").
- Every visible text is a language key (`Text::_('COM_KUNENA_...')`) with its English text in the `en-GB` `.ini` files.
- A change in a layout of one template usually needs the same change in the other templates.
- Commit messages are one line and start with the branch: `[K8.0] Keep recent uploads in the clean-up of pending attachments`.

## Software factory

`/factory run` builds every open GitHub issue labeled `factory` through
intake → spec → build → gate → review → approve, one PR per issue. State is in
`factory/jobs/` (gitignored); `factory/bin/job` is its only writer. Checks:
`factory/checks.sh`. Dashboard: `cd factory && python3 -m http.server`, then
open http://localhost:8000/dashboard.html.

Each job works in its own git worktree, a full checkout of the repo at
`.worktrees/<job-id>` (gitignored). A tool run from the repo root that doesn't
read `.gitignore` sees those copies too; exclude `.worktrees/` in its config.

The scripts in `factory/bin` are Python and run on Windows and Linux; start them
as `python3 factory/bin/job ...`. On Windows the checks run in Git Bash.
