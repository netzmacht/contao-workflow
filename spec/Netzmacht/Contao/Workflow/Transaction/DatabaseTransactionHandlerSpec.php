<?php

namespace spec\Netzmacht\Contao\Workflow\Transaction;

use Contao\Database;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DatabaseTransactionHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Workflow\Transaction\DatabaseTransactionHandler');
    }

    function let(Database $database)
    {
        $this->beConstructedWith($database);
    }

    function it_begins_a_transaction(Database $database)
    {
        $database->beginTransaction()->shouldBeCalled();
        $this->begin();
    }

    function it_commits_a_transaction(Database $database)
    {
        $database->commitTransaction()->shouldBeCalled();
        $this->commit();
    }

    function it_rollbacks_a_transaction(Database $database)
    {
        $database->rollbackTransaction()->shouldBeCalled();
        $this->rollback();
    }
}
