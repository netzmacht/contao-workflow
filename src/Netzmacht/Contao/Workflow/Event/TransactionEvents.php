<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Event;

/**
 * Class Events stores the transaction events names.
 *
 * @package Netzmacht\Contao\Workflow\Transaction
 */
class TransactionEvents
{
    const TRANSACTION_BEGIN = 'workflow.transaction.begin';

    const TRANSACTION_COMMIT = 'workflow.transaction.commit';

    const TRANSACTION_ROLLBACK = 'workflow.transaction.rollback';
}
