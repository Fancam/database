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

    function its_select_should_use_numbered_parameters_if_given($pdo)
    {
        $statement = $this->mockStatementFor($pdo, 'SELECT * FROM foo WHERE bar = ?');

        $statement->bindValue(1, 'baz', PDO::PARAM_STR)
                  ->shouldBeCalled();

        // when
        $this->select('SELECT * FROM foo WHERE bar = ?', ['baz']);
    }

    function its_select_should_use_named_parameters_if_given($pdo)
    {
        $statement = $this->mockStatementFor($pdo, 'SELECT * FROM foo WHERE bar = :bar');

        $statement->bindValue(':bar', 'baz', PDO::PARAM_STR)
                  ->shouldBeCalled();

        // when
        $this->select('SELECT * FROM foo WHERE bar = :bar', [':bar' => 'baz']);
    }

    function its_select_should_use_correct_value_type_if_binding_parameters($pdo)
    {
        $statement = $this->mockStatementFor($pdo,
            'SELECT * FROM foo WHERE int_field = ? AND bool_field = ? AND null_field IS ? AND other_field = ?'
        );

        $statement->bindValue(1, 123, PDO::PARAM_INT)
                  ->shouldBeCalled();
        $statement->bindValue(2, true, PDO::PARAM_BOOL)
                  ->shouldBeCalled();
        $statement->bindValue(3, null, PDO::PARAM_NULL)
                  ->shouldBeCalled();
        $statement->bindValue(4, 'baz', PDO::PARAM_STR)
                  ->shouldBeCalled();

        // when
        $this->select('SELECT * FROM foo WHERE int_field = ? AND bool_field = ? AND null_field IS ? AND other_field = ?', [
            123,
            true,
            null,
            'baz'
        ]);
    }

    function its_lists_should_provide_a_list_of_a_given_column($pdo)
    {
        $statement = $this->mockStatementFor($pdo, 'SELECT username FROM users');

        $statement->fetchAll(PDO::FETCH_COLUMN, 0)
                  ->willReturn(['felixkiss'])
                  ->shouldBeCalled();

        // when
        $this->lists('SELECT username FROM users')
             ->shouldReturn(['felixkiss']);
    }

    function its_pluck_should_give_a_single_value($pdo)
    {
        $statement = $this->mockStatementFor($pdo, 'SELECT COUNT(*) FROM users');

        $statement->fetchColumn(0)
                  ->willReturn(2)
                  ->shouldBeCalled();
        // when
        $this->pluck('SELECT COUNT(*) FROM users')
             ->shouldReturn(2);
    }

    function it_should_insert_records_using_prepared_statements($pdo)
    {
        $statement = $this->mockStatementFor($pdo, 'INSERT INTO users (username, city) VALUES (:username, :city)');

        $statement->bindValue(':username', Argument::cetera())
                  ->shouldBeCalled();
        $statement->bindValue(':city', Argument::cetera())
                  ->shouldBeCalled();

        // when
        $this->insert('users', [
            'username' => 'felixkiss',
            'city' => 'Vienna, Austria',
        ]);
    }

    private function mockStatementFor($pdo, $query)
    {
        $prophet = new \Prophecy\Prophet;

        $statement = $prophet->prophesize();
        $statement->willExtend('PDOStatement');
        $statement->execute()
                  ->willReturn(null);
        $statement->closeCursor()
                  ->willReturn(null);

        $pdo->prepare($query)
            ->shouldBeCalled()
            ->willReturn($statement);

        return $statement;
    }
}
