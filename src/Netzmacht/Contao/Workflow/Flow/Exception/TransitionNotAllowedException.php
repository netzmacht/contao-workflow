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

class TransitionNotAllowedException extends \Exception
{
    /**
     * @param string $currentStep
     * @param int $transitionName
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($currentStep, $transitionName, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Transiton "%s" is not allowed at step "%s"', $transitionName, $currentStep);
        parent::__construct($message, $code, $previous);
    }
}
