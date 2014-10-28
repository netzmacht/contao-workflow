<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Exception;

use Exception;

/**
 * Class ProcessNotStartedException is thrown when transition is executed on a not started workflow.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Exception
 */
class ProcessNotStartedException extends Exception
{

}
