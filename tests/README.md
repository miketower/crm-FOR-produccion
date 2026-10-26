# Running application tests

The test suite supports PHP 8.3 and 8.4 and uses PHPUnit 11.5.
Install PHP's `intl`, `mbstring`, `dom`, `xml`, `xmlwriter`, and `sqlite3`
extensions, along with the other extensions checked by Composer.

From the repository root:

```sh
composer install
composer check-platform-reqs
composer test
```

Composer resolves dependencies against PHP 8.3 via `config.platform.php`, so
updating the lockfile on PHP 8.4 does not introduce dependencies that require
PHP 8.4. Commit both `composer.json` and `composer.lock` when updating packages.
`composer check-platform-reqs` checks the actual PHP runtime and extensions.

## Test configuration

`phpunit.xml.dist` selects the `testing` environment, a local base URL, and the
`tests` database group with SQLite in memory. The database tests migrate and
seed their fixtures under `tests/_support`; they do not use the CRM database.
No MySQL service, application user, production credentials, or `.env` file is
needed to run the suite.

The suite covers database reads and soft deletion, session storage, and basic
application configuration. Run a subset with:

```sh
composer test -- --filter ExampleDatabaseTest
```

PHPUnit caches are written under `writable/cache/phpunit` and the JUnit report
under `writable/logs/phpunit/junit.xml`. Both paths are ignored by Git.
Warnings, risky tests, and an empty test suite fail the command.

## Optional code coverage

Coverage is not enabled for a normal test run. With Xdebug installed:

```sh
XDEBUG_MODE=coverage composer test -- --coverage-text --coverage-html writable/logs/phpunit/coverage
```

See the [CodeIgniter testing guide](https://codeigniter.com/user_guide/testing/index.html)
for creating tests and database fixtures.
