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

namespace Netzmacht\Contao\Workflow\Exception;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Exception\WorkflowException;
use RuntimeException;
use Throwable;

/**
 * Class ActionNotFound
 */
class UnsupportedEntity extends RuntimeException implements WorkflowException
{
    /**
     * Create exception for an entity.
     *
     * @param EntityId       $entityId The entity id.
     * @param int            $code     The error code.
     * @param Throwable|null $previous Previous exception.
     *
     * @return UnsupportedEntity
     */
    public static function forEntity(EntityId $entityId, int $code = 0, Throwable $previous = null): UnsupportedEntity
    {
        return new static(
            sprintf('Could not create entity for given data of entity "%s" not found.', (string) $entityId),
            $code,
            $previous
        );
    }
}
