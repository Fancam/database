<?php namespace Felixkiss\Database;

use PDO;
use PDOStatement;

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

        $this->bindParameters($statement, $parameters);
        $statement->execute();

        return $statement;
    }

    /**
     * Get an array of values from a single database column.
     *
     * @param  string $query
     * @param  array  $parameters
     * @return array
     */
    public function lists($query, $parameters = [])
    {
        $statement = $this->select($query, $parameters);
        $list = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

        $statement->closeCursor();
        return $list;
    }

    /**
     * Inserts a record into the given table.
     *
     * @param string $table
     * @param array  $values
     */
    public function insert($table, $values = [])
    {
        $sql = $this->buildInsertQuery($table, $values);
        $statement = $this->pdo->prepare($sql);

        $parameters = [];
        foreach ($values as $key => $value)
        {
            $parameters[':' . $key] = $value;
        }

        $this->bindParameters($statement, $parameters);
        $statement->execute();

        $statement->closeCursor();
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

    /**
     * Binds the given parameters to the PDOStatement.
     *
     * @param PDOStatement $statement
     * @param array        $parameters
     */
    protected function bindParameters(PDOStatement $statement, array $parameters)
    {
        foreach ($parameters as $key => $value)
        {
            // PDO uses 1-indexed numbered parameters
            if (is_numeric($key))
            {
                $key += 1;
            }

            // Find the correct data type for the current value.
            // Without this, you can't use parameters in clauses beside WHERE
            // (e.g. LIMIT)
            $type = $this->getDataType($value);

            $statement->bindValue($key, $value, $type);
        }
    }

    /**
     * Create INSERT INTO query.
     *
     * @param  string $table
     * @param  array  $values
     * @return string
     */
    protected function buildInsertQuery($table, $values)
    {
        $fields = array_keys($values);
        $parameters = array_map(function($name)
        {
            return ':' . $name;
        }, $fields);

        return 'INSERT INTO ' . $table . ' (' .
               implode(', ', $fields) . ') VALUES (' .
               implode(', ', $parameters) . ')';
    }
}
