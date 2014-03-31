<?php namespace Felixkiss\Database;

use PDO;

class Database
{
    /**
     * The PDO connection to run sql commands against.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Executes any SQL command (DROP, DELETE, TRUNCATE, etc.)
     * Returns the number of affected rows by the command.
     *
     * @param  string $sql
     * @return integer
     */
    public function execute($sql)
    {
        return $this->pdo->exec($sql);
    }
}
