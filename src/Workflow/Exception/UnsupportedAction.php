<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Exception;

use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Action;
use RuntimeException;
use Throwable;

use function get_class;
use function sprintf;

final class UnsupportedAction extends RuntimeException implements WorkflowException
{
    /**
     * Create exception for an action type.
     *
     * @param string         $type     The action type.
     * @param int            $code     The error code.
     * @param Throwable|null $previous A previous exception.
     */
    public static function withType(string $type, int $code = 0, ?Throwable $previous = null): UnsupportedAction
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
     */
    public static function withUnexpectedClass(
        Action $action,
        string $expectedClass,
        int $code = 0,
        ?Throwable $previous = null
    ): UnsupportedAction {
        return new static(
            sprintf('Unexpected action "%s". "%s" expected', get_class($action), $expectedClass),
            $code,
            $previous
        );
    }
}
