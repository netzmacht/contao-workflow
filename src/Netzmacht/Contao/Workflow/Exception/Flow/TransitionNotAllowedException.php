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
 * Class TransitionNotAllowedException is thrown when transition was not allowed.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Exception
 */
class TransitionNotAllowedException extends \Exception
{
    /**
     * Construct.
     *
     * @param string    $currentStep    The current step name.
     * @param string    $transitionName The transition name.
     * @param int       $code           Error code.
     * @param Exception $previous       Previous exception.
     */
    public function __construct($currentStep, $transitionName, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Transiton "%s" is not allowed at step "%s"', $transitionName, $currentStep);
        parent::__construct($message, $code, $previous);
    }
}
