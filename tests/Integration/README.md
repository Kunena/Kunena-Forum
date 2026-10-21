# Integration tests

These tests run on a real MySQL / MariaDB database, like the integration tests of Joomla.

## The database

The tables are made from `src/admin/sql/install/schema/install.xml` (see `SchemaGenerator.php`), so the tests always have the
tables of the code they test. The prefix is `ktest_`. Before a test only the tables which an earlier test put rows in are emptied.

Where the database comes from, first match wins:

1. the constants `KTEST_DB_HOST`, `KTEST_DB_NAME`, `KTEST_DB_USER`, `KTEST_DB_PASSWORD` in your `phpunit.xml`
   (copy `phpunit.xml.dist`, see the comment in it);
2. the environment variables `KUNENA_TEST_DB_HOST`, `KUNENA_TEST_DB_NAME`, `KUNENA_TEST_DB_USER`, `KUNENA_TEST_DB_PASSWORD`
   (and `KUNENA_TEST_DB_PORT`);
3. the server and the user of the Joomla installation in `KUNENA_TEST_JOOMLA`, with the database `<name of its database>_phpunit`.

The database is made when it is not there. **It must be a database of its own**: the tests empty tables, and the tests refuse
to run on the database of the Joomla site. Without a database the tests are skipped, not failed.

## Writing a test

```php
class MyTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    public function testSomething(): void
    {
        $this->insertRow('#__kunena_topics', ['id' => 1, 'subject' => 'Hello']);

        $this->assertSame(1, $this->countRows('#__kunena_topics'));
    }
}
```

`getSchemasToLoad()` says which datasets a test needs: `kunena` (default, the tables of Kunena) and files of
`datasets/mysqli/` for tables of Joomla itself (`joomla` has `#__users` and `#__mail_templates`). Add a table of Joomla there when
a test needs it.
