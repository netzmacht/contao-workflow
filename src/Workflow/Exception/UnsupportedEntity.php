<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Exception;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Exception\WorkflowException;
use RuntimeException;
use Throwable;

use function sprintf;

final class UnsupportedEntity extends RuntimeException implements WorkflowException
{
    /**
     * Create exception for an entity.
     *
     * @param EntityId       $entityId The entity id.
     * @param int            $code     The error code.
     * @param Throwable|null $previous Previous exception.
     */
    public static function forEntity(EntityId $entityId, int $code = 0, ?Throwable $previous = null): UnsupportedEntity
    {
        return new static(
            sprintf('Could not create entity for given data of entity "%s" not found.', (string) $entityId),
            $code,
            $previous
        );
    }

    /**
     * Create exception for an entity.
     *
     * @param string         $providerName The provider name.
     * @param int            $code         The error code.
     * @param Throwable|null $previous     Previous exception.
     */
    public static function withProviderName(
        string $providerName,
        int $code = 0,
        ?Throwable $previous = null
    ): UnsupportedEntity {
        return new static(
            sprintf('Entity of type "%s" is not supported.', $providerName),
            $code,
            $previous
        );
    }
}
