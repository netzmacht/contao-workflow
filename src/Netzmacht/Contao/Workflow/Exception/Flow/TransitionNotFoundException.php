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
 * Class TransitionNotFoundException is thrown then transition was not found.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Exception
 */
class TransitionNotFoundException extends \Exception
{
    /**
     * Construct.
     *
     * @param string $transitionName The not found transition name.
     */
    public function __construct($transitionName)
    {
        parent::__construct(sprintf('Transition "%s" not found', $transitionName));
    }
}
