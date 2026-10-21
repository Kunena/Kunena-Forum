# Tests of Kunena

The tests follow the layout of the tests of Joomla (`joomla/tests`): three layers, each in its own folder.

| Folder | What | Needs |
| --- | --- | --- |
| `Unit/` | PHPUnit tests of one class or file, no database. The folders mirror `src/` (`Libraries/Kunena/...` for `src/libraries/kunena/src`, `Component/Site` and `Component/Administrator` for the component, `Plugin/...`). `Codebase/`, `Features/` and `Language/` hold tests which look at all the files. | PHP (Joomla is optional) |
| `Integration/` | PHPUnit tests on a real MySQL database, with the tables made from the installer schema. | PHP, MySQL |
| `System/` | Cypress tests of the running forum (all pages, as guest, user and admin, and the features). | A Joomla site, Cypress |

## Run

```bash
composer install
composer test               # both PHPUnit suites
composer test:unit          # only the unit tests
composer test:integration   # only the integration tests
```

Without any setting the unit tests run with a small stand-in for the Joomla `Registry`, and the integration tests are skipped
(they say why). To run everything, point the tests at a Joomla installation:

```bash
KUNENA_TEST_JOOMLA=/path/to/joomla composer test
```

That loads the real framework of Joomla (as `tests/Unit/bootstrap.php` of Joomla does), and the integration tests use the
database server of that installation with the database `<its database>_phpunit` (made when it is not there; the database of the
site itself is never used). See `Integration/README.md` for other ways to give a database, and `System/README.md` for Cypress.

## Where a new test goes

- A class of `src/libraries/kunena/src/Forum/Category/KunenaCategory.php` gets
  `tests/Unit/Libraries/Kunena/Forum/Category/KunenaCategoryTest.php`, namespace `Kunena\Tests\Unit\Libraries\Kunena\Forum\Category`.
- Extend `UnitTestCase` (helpers: `requireJoomla()`, `invoke()`, `bare()`); use `IntegrationTestCase` with `DBTestInterface` and
  `DBTestTrait` when the test needs the database.
- A bug which only shows in the browser gets a Cypress spec in `System/integration/...`.

## Cypress against the checkout

The Cypress tests (`System/`) need a Joomla site of their own with Kunena installed (the settings are in `cypress.config.js`:
site `localhost/tests`, database `tests`, the administrator `ci-admin`). Keep that site on the code of this checkout:

- Link the Kunena folders of the site to `src/` (as a development site does: `components/com_kunena` → `src/site`,
  `administrator/components/com_kunena` → `src/admin`, `libraries/kunena`, `media/kunena/core`, the plugins). Copy new files of
  `src/media/kunena/emoticons` (and the other media folders) into the site: they are not linked.
- Upgrade the database of the site after a new version of the schema (new tables and columns); a site on an older version fails in
  places which have nothing to do with the test.
- Opcache can keep an old compiled copy of a file which is reached through a link: reset it when a change seems to have no effect.
- Kunena keeps its options in its own cache (`cache/com_kunena_configuration`). The commands `cy.kunena_setOption()` and
  `cy.kunena_getOption()` change options in the database and clear that cache; Cypress must know the folder of the site:

```bash
KUNENA_CMS_PATH=E:/www/apache/htdocs/tests npx cypress run --config-file cypress.config.js --spec "tests/System/integration/**/com_kunena/**"
```
