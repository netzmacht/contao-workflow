<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Exception\DataContainer;

use Netzmacht\ContaoWorkflowBundle\Exception\RuntimeException;

/**
 * Class FieldAlreadyExists
 */
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
