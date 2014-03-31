<?php namespace spec\Felixkiss\Database;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PDO;
use PDOStatement;

class DatabaseSpec extends ObjectBehavior
{
    function let($pdo)
    {
        $pdo->beADoubleOf('PDO');
        $this->beConstructedWith($pdo);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Felixkiss\Database\Database');
    }

    function it_should_execute_sql_statements($pdo)
    {
        $pdo->exec('SOME SQL foo')
            ->shouldBeCalled();

        // when
        $this->execute('SOME SQL foo');
    }

    function its_execute_should_return_the_number_of_affected_rows($pdo)
    {
        $pdo->exec('SOME SQL foo')
            ->willReturn(123);

        // when
        $this->execute('SOME SQL foo')
             ->shouldReturn(123);
    }

    function it_should_select_records_using_prepared_statements($pdo, PDOStatement $statement)
    {
        $pdo->prepare('SELECT * FROM foo')
            ->shouldBeCalled()
            ->willReturn($statement);
        $statement->execute()
                  ->shouldBeCalled();

        // when
        $this->select('SELECT * FROM foo')
             ->shouldReturnAnInstanceOf('PDOStatement');
    }

    function its_select_should_use_numbered_parameters_if_given($pdo, PDOStatement $statement)
    {
        $pdo->prepare('SELECT * FROM foo WHERE bar = ?')
            ->willReturn($statement);
        $statement->bindValue(1, 'baz', PDO::PARAM_STR)
                  ->shouldBeCalled();
        $statement->execute()
                  ->shouldBeCalled();

        // when
        $this->select('SELECT * FROM foo WHERE bar = ?', ['baz']);
    }

    function its_select_should_use_named_parameters_if_given($pdo, PDOStatement $statement)
    {
        $pdo->prepare('SELECT * FROM foo WHERE bar = :bar')
            ->willReturn($statement);
        $statement->bindValue(':bar', 'baz', PDO::PARAM_STR)
                  ->shouldBeCalled();
        $statement->execute()
                  ->shouldBeCalled();

        // when
        $this->select('SELECT * FROM foo WHERE bar = :bar', [':bar' => 'baz']);
    }

    function its_select_should_use_correct_value_type_if_binding_parameters($pdo, PDOStatement $statement)
    {
        $pdo->prepare('SELECT * FROM foo WHERE int_field = ? AND bool_field = ? AND null_field IS ? AND other_field = ?')
            ->willReturn($statement);
        $statement->bindValue(1, 123, PDO::PARAM_INT)
                  ->shouldBeCalled();
        $statement->bindValue(2, true, PDO::PARAM_BOOL)
                  ->shouldBeCalled();
        $statement->bindValue(3, null, PDO::PARAM_NULL)
                  ->shouldBeCalled();
        $statement->bindValue(4, 'baz', PDO::PARAM_STR)
                  ->shouldBeCalled();
        $statement->execute()
                  ->shouldBeCalled();

        // when
        $this->select('SELECT * FROM foo WHERE int_field = ? AND bool_field = ? AND null_field IS ? AND other_field = ?', [
            123,
            true,
            null,
            'baz'
        ]);
    }

    function it_should_insert_records_using_prepared_statements($pdo, PDOStatement $statement)
    {
        $pdo->prepare('INSERT INTO users (username, city) VALUES (:username, :city)')
            ->shouldBeCalled()
            ->willReturn($statement);
        $statement->bindValue(Argument::cetera())
                  ->shouldBeCalled();
        $statement->execute()
                  ->shouldBeCalled();
        $statement->closeCursor()
                  ->shouldBeCalled();

        $this->insert('users', [
            'username' => 'felixkiss',
            'city' => 'Vienna, Austria',
        ]);
    }
}
