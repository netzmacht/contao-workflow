<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Exception;

use Netzmacht\Workflow\Exception\WorkflowException;
use RuntimeException;
use Throwable;

/**
 * Class ActionNotFound
 */
class UnsupportedActionType extends RuntimeException implements WorkflowException
{
    /**
     * Create exception for an action type.
     *
     * @param string         $type     The action type.
     * @param int            $code     The error code.
     * @param Throwable|null $previous A previous exception.
     *
     * @return UnsupportedActionType
     */
    public static function withType(string $type, int $code = 0, Throwable $previous = null): UnsupportedActionType
    {
        return new static(
            sprintf('Action "%s" not found.', $type),
            $code,
            $previous
        );
    }
}
