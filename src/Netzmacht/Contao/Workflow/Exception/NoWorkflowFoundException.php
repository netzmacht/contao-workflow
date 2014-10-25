<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Exception;

use Exception;

class NoWorkflowFoundException extends Exception
{
    public function __construct($providerName, $entityId, $code = 0, Exception $previous = null)
    {
        $message = sprintf('No workflow found for entity "%s::%s"', $providerName, $entityId);
        parent::__construct($message, $code, $previous);
    }
} 