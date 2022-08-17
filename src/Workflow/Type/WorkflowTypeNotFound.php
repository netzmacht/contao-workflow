<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

use Exception;

use function sprintf;

final class WorkflowTypeNotFound extends Exception
{
    /**
     * Create exception for a workflow type name.
     *
     * @param string $name Workflow type name.
     *
     * @return WorkflowTypeNotFound
     */
    public static function withName(string $name): self
    {
        return new self(sprintf('Workflow type "%s" not found', $name));
    }
}
