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

class InValidTransitionException extends \Exception
{
    public function __construct($transitionName, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Transition "%s" is not validated', $transitionName);
        parent::__construct($message, $code, $previous);
    }
}
