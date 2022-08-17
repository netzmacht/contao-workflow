<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Exception\DataContainer;

use Netzmacht\ContaoWorkflowBundle\Exception\RuntimeException;

use function sprintf;

final class FieldAlreadyExists extends RuntimeException
{
    /**
     * Create exception with default message from field and data container name.
     *
     * @param string $field         Field name.
     * @param string $dataContainer Data container name.
     *
     * @return FieldAlreadyExists
     */
    public static function namedInDataContainer(string $field, string $dataContainer): self
    {
        return new self(
            sprintf('Field "%s" already exists in data container "%s" configuration.', $field, $dataContainer)
        );
    }
}
