<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Exception\Flow;

use Exception;

/**
 * Class InvalidTransitionException is thrown when transition could not be validated.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Exception
 */
class InvalidTransitionException extends \Exception
{
    /**
     * Construct.
     *
     * @param string    $transitionName The transition name.
     * @param int       $code           The error code.
     * @param Exception $previous       Previous exception.
     */
    public function __construct($transitionName, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Transition "%s" is not validated', $transitionName);
        parent::__construct($message, $code, $previous);
    }
}
