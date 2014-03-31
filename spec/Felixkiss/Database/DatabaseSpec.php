<?php namespace spec\Felixkiss\Database;

use PhpSpec\ObjectBehavior;
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
}
