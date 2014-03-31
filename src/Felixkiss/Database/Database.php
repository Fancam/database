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

    /**
     * Executes a SQL select command.
     * Returns the statement object.
     *
     * @param  string $query
     * @param  array  $parameters
     * @return PDOStatement
     */
    public function select($query, $parameters = [])
    {
        $statement = $this->pdo->prepare($query);

        foreach ($parameters as $key => $value)
        {
            if (is_numeric($key))
            {
                $key += 1;
            }

            $type = $this->getDataType($value);

            $statement->bindValue($key, $value, $type);
        }

        $statement->execute();

        return $statement;
    }

    /**
     * Determine the correct PDO data type for a value.
     * PDO::PARAM_*
     *
     * @param  mixed $value
     * @return integer
     */
    protected function getDataType($value)
    {
        if (is_bool($value))
        {
            return PDO::PARAM_BOOL;
        }
        if (is_null($value))
        {
            return PDO::PARAM_NULL;
        }
        if (is_integer($value))
        {
            return PDO::PARAM_INT;
        }
        return PDO::PARAM_STR;
    }
}
