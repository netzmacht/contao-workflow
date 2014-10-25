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

class WorkflowDoesNotMatchException extends \Exception
{
    /**
     * @param string $workflowName
     * @param int $providerName
     * @param Exception $workflowId
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($workflowName, $providerName, $workflowId, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Workflow %s does not match entity "%s::%s"', $workflowName, $providerName, $workflowId);
        parent::__construct($message, $code, $previous);
    }
}
