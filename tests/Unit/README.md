# Unit tests

Fast PHPUnit tests without a database. `bootstrap.php` loads the Composer autoloader and maps the Kunena namespaces to
this checkout:

- `Kunena\Forum\Libraries\` → `src/libraries/kunena/src/`
- `Kunena\Forum\Site\` → `src/site/src/`
- `Kunena\Forum\Administrator\` → `src/admin/src/`

With `KUNENA_TEST_JOOMLA=/path/to/joomla` the real Joomla framework is loaded first (as Joomla does in its own bootstrap), so
code which uses `Joomla\CMS\...` works. Without it a small stand-in for `Joomla\Registry\Registry` is used
(`Support/RegistryStub.php`) and tests which need more call `$this->requireJoomla()`, which skips them.

The folders mirror the source. Some tests look at all the files of the project and sit in their own folders:

- `Codebase/` – every PHP file parses, every page has a controller and a test.
- `Features/` – the files of a feature are wired together (language strings, scripts, options).
- `Language/` – the language files have the same keys and valid syntax.
