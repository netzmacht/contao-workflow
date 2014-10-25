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

class TransitionNotFoundException extends \Exception
{
    /**
     * @param string $transitionName
     */
    public function __construct($transitionName)
    {
        parent::__construct(sprintf('Transition "%s" not found', $transitionName));
    }
}
