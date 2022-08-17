<?php

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
     * @param ContaoModelRelatedModelChangeTracker $changesRegistry Related changes registry.
     */
    public function __construct(ContaoModelRelatedModelChangeTracker $changesRegistry)
    {
        $this->changesRegistry = $changesRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($object): bool
    {
        return $object instanceof Model;
    }

    /**
     * {@inheritDoc}
     */
    public function create($object): PropertyAccessor
    {
        assert($object instanceof Model);

        return new ContaoModelPropertyAccessor($object, $this->changesRegistry);
    }
}
