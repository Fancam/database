# felixkiss/database

This project is a thin wrapper around the PDO class to allow cleaner and more
concise code when working with databases in PHP.

It is not intended to be an ORM, but rather a better way to work with
hand-written SQL statements.

# Installation

Install through composer:

```
$ composer require felixkiss/database:0.*
```

or edit `composer.json` directly:

```json
{
  "require": {
    "felixkiss/database": "0.*"
  }
}
```

and run `composer update` afterwards.

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

### Get An Array Of One Column

```php
$users = $db->lists('SELECT username FROM users');
```

This will return a flattened array like:

```
['felixkiss', 'foobar', ...]
```

### Get A Single Value

```php
$count = $db->pluck('SELECT COUNT(*) FROM users');
```

## Inserting Records

```php
$db->insert('users', [
    'username' => 'felixkiss',
    'location' => 'Vienna, Austria',
]);
```

## Different Connections For Read And Write Operations

Sometimes it can be useful, to specify separate connections for reads (SELECT)
and writes (INSERT, UPDATE, DELETE), e.g. in a replicated environment.

```php
$read = new PDO('mysql:dbname=foo;host=127.0.0.1', 'foo', 'bar');
$write = new PDO('mysql:dbname=foo;host=mirror.example.com', 'foo', 'bar');
$db = new Database($read, $write);
```

Other SQL statements (via `execute()`) will be called on the write connection by
default, unless `$readOnly = true` is specified as the second parameter:

```php
$db->execute('TRUNCATE users'); // runs on write connection
$db->execute('LOCK TABLE users WRITE', true); // runs on read connection
```

# License

MIT, see LICENSE.md
