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

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Exception;

use function get_class;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Action;
use RuntimeException;
use Throwable;

/**
 * Class ActionNotFound
 */
final class UnsupportedAction extends RuntimeException implements WorkflowException
{
    /**
     * Create exception for an action type.
     *
     * @param string         $type     The action type.
     * @param int            $code     The error code.
     * @param Throwable|null $previous A previous exception.
     *
     * @return UnsupportedAction
     */
    public static function withType(string $type, int $code = 0, Throwable $previous = null): UnsupportedAction
    {
        return new static(
            sprintf('Action "%s" not found.', $type),
            $code,
            $previous
        );
    }

    /**
     * Create exception for an action type.
     *
     * @param Action         $action        The given action.
     * @param string         $expectedClass The expected action.
     * @param int            $code          The error code.
     * @param Throwable|null $previous      A previous exception.
     *
     * @return UnsupportedAction
     */
    public static function withUnexpectedClass(
        Action $action,
        string $expectedClass,
        int $code = 0,
        Throwable $previous = null
    ): UnsupportedAction {
        return new static(
            sprintf('Unexpected action "%s". "%s" expected', get_class($action), $expectedClass),
            $code,
            $previous
        );
    }
}
