# felixkiss/database

This project is a thin wrapper around the PDO class to allow cleaner and more
concise code when working with databases in PHP.

It is not intended to be an ORM, but rather a better way to work with
hand-written SQL statements.

# Usage

To instantiate a `Database` instance:

```php
require 'vendor/autoload.php';

use Felixkiss\Database\Database;

$pdo = new PDO('mysql:dbname=foo;host=127.0.0.1', 'foo', 'bar');
$db = new Database($pdo);
```

## Executing SQL Statements

```php
$db->execute('TRUNCATE some_table');
```

The `execute` method will return the number of affected rows. See
[PDO#exec](http://www.php.net/manual/en/pdo.exec.php) for more information.

## SELECT

```php
$user = $db->select('SELECT * FROM user');
foreach ($users as $user)
{
    // Do something ...
}
```

### With Numbered Parameters

```php
$users = $db->select('SELECT * FROM user WHERE age BETWEEN ? AND ?', [20, 40]);
```

### With Named Parameters

```php
$users = $db->select(
  'SELECT * FROM user WHERE age BETWEEN :young AND :old LIMIT 0, :limit', [
  ':young' => 20,
  ':old'   => 40,
  ':limit' => 10,
]);
```

## Inserting Records

```php
$db->insert('users', [
    'username' => 'felixkiss',
    'location' => 'Vienna, Austria',
]);
```

# License

MIT, see LICENSE.md
