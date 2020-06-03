<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use Contao\Model;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel\ContaoModelRelatedModelChangeTracker;
use function assert;

/**
 * Factory creates an instance of the contao model property accessor
 */
final class ContaoModelPropertyAccessorFactory implements PropertyAccessorFactory
{
    /**
     * Related changes registry.
     *
     * @var ContaoModelRelatedModelChangeTracker
     */
    private $changesRegistry;

    /**
     * ContaoModelPropertyAccessorFactory constructor.
     *
     * @param ContaoModelRelatedModelChangeTracker $changesRegistry Related changes registry.
     */
    public function __construct(ContaoModelRelatedModelChangeTracker $changesRegistry)
    {
        $this->changesRegistry = $changesRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($object) : bool
    {
        return $object instanceof Model;
    }

    /**
     * {@inheritDoc}
     */
    public function create($object) : PropertyAccessor
    {
        assert($object instanceof Model);

        return new ContaoModelPropertyAccessor($object, $this->changesRegistry);
    }
}
