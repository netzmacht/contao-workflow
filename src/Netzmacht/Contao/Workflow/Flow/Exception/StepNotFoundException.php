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

class StepNotFoundException extends \Exception
{
    /**
     * @param string $stepName
     */
    function __construct($stepName)
    {
        parent::__construct(sprintf('Step "%s" not found', $stepName));
    }
}