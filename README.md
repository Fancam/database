# felixkiss/database

This project is a thin wrapper around the PDO class to allow cleaner and more
concise code when working with databases in PHP.

It is not intended to be an ORM, but rather a better way to work with
hand-written SQL statements.

# Usage

To instantiate a `Database` instance:

```php
require 'vendor/autoload.php';

use Felixkiss\Database;

$pdo = new PDO('mysql:dbname=foo;host=127.0.0.1', 'foo', 'bar');
$db = new Database($pdo);
```

## Executing SQL Statements

```php
$db->execute('TRUNCATE some_table');
```

The `execute` method will return the number of affected rows. See
[PDO#exec](http://www.php.net/manual/en/pdo.exec.php) for more information.

# License

MIT, see LICENSE.md
