#!/usr/bin/env bash
# The repo's checks. Exit 0 only when the branch is fit to review.
#
# The gate runs this script from the main checkout with the job's worktree as
# the working directory. Keep it fast, deterministic and non-interactive: no
# watch modes, no prompts, nothing that rewrites files.
#
# The unit tests need the framework of a Joomla installation (see
# tests/README.md). Give its folder in KUNENA_TEST_JOOMLA, either in the
# environment or in factory/checks.env (gitignored, one line):
#
#   KUNENA_TEST_JOOMLA=/path/to/joomla
#
# Not run here: the Integration suite (it needs a MySQL database of its own,
# which jobs running side by side would share) and Cypress (it needs a Joomla
# site linked to one checkout).
set -euo pipefail

FACTORY="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [ -f "$FACTORY/checks.env" ]; then
  # shellcheck disable=SC1091
  . "$FACTORY/checks.env"
fi

if [ -z "${KUNENA_TEST_JOOMLA:-}" ] || [ ! -f "$KUNENA_TEST_JOOMLA/libraries/loader.php" ]; then
  echo "checks.sh: set KUNENA_TEST_JOOMLA to a Joomla installation (in the environment or in factory/checks.env)." >&2
  exit 1
fi
export KUNENA_TEST_JOOMLA

base="$(sed -n 's/.*"base_branch": *"\([^"]*\)".*/\1/p' "$FACTORY/config.json")"

echo "== composer validate"
composer validate --no-check-all --strict

# A fresh worktree has no vendor/ (it is gitignored). Install only when it is
# missing or older than the lock file, so a checkout that is in sync is left alone.
if [ ! -f vendor/autoload.php ] || [ composer.lock -nt vendor/composer/installed.json ]; then
  echo "== composer install"
  composer install --no-progress --no-interaction
fi

# The PHP files the branch changed.
changed=()
while IFS= read -r file; do
  [ -f "$file" ] && changed+=("$file")
done < <(git diff --name-only --diff-filter=ACMR "origin/$base...HEAD" -- '*.php')

echo "== php -l (${#changed[@]} changed files)"
for file in "${changed[@]}"; do
  if ! out="$(php -l "$file" 2>&1)"; then
    echo "$out"
    exit 1
  fi
done

echo "== phpunit (Unit)"
vendor/bin/phpunit --testsuite Unit --no-progress --colors=never

# The code style is reported, not enforced: the CI does the same
# (continue-on-error), and files on the base branch do not all pass yet.
if [ "${#changed[@]}" -gt 0 ]; then
  echo "== php-cs-fixer (dry run on the changed files, not blocking)"
  vendor/bin/php-cs-fixer fix --dry-run --diff --using-cache=no --config=.php-cs-fixer.dist.php \
    --path-mode=intersection -- "${changed[@]}" || echo "php-cs-fixer: the files above differ from the code style."
fi

echo "== checks passed"
