# Running application tests

The compatibility baseline is PHP 8.0.3 (MAMP), using PHPUnit 9.6.
Run the suite with PHP 8.0.3 to match the local MAMP environment.
PHP 8.3/8.4 requires a different dependency resolution.
Install PHP's `intl`, `mbstring`, `dom`, `xml`, `xmlwriter`, and `sqlite3`
extensions, along with the other extensions checked by Composer.

From the repository root:

```sh
composer install
composer check-platform-reqs
composer test
```

Composer resolves dependencies against PHP 8.0.3 via `config.platform.php`, so
updating the lockfile on a newer PHP does not introduce dependencies that require
a newer runtime. Commit both `composer.json` and `composer.lock` when updating packages.
`composer check-platform-reqs` checks the actual PHP runtime and extensions.

## Test configuration

`phpunit.xml.dist` selects the `testing` environment, a local base URL, and the
`tests` database group with SQLite in memory. The database tests migrate and
seed their fixtures under `tests/_support`; they do not use the CRM database.
No MySQL service, application user, production credentials, or `.env` file is
needed to run the suite.

The quotation regression tests cover creation, editing, and persistence for
Servicio Especializado and Mixto, including the gross salary, net salary, and surplus.
The browser calculations and PDF option data have a separate dependency-free
Node.js suite (Node 18+):

```sh
node --test tests/js/cotizador.test.js
```

It checks that Total FOR includes the gross FOR salary, surplus, social cost, and
FOR commission, while preserving the existing PROEM, surplus, and commission formulas.

The suite covers database reads and soft deletion, session storage, and basic
application configuration. Run a subset with:

```sh
composer test -- --filter ExampleDatabaseTest
```

The PHPUnit result cache is written to `writable/cache/phpunit.result.cache` and the JUnit report
under `writable/logs/phpunit/junit.xml`. Both paths are ignored by Git.
Warnings, risky tests, and an empty test suite fail the command.

## Optional code coverage

Coverage is not enabled for a normal test run. With Xdebug installed:

```sh
XDEBUG_MODE=coverage composer test -- --coverage-text --coverage-html writable/logs/phpunit/coverage
```

See the [CodeIgniter testing guide](https://codeigniter.com/user_guide/testing/index.html)
for creating tests and database fixtures.
