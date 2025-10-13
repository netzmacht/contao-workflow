<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use Contao\Model;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel\ContaoModelRelatedModelChangeTracker;
use Override;

use function assert;

/**
 * Factory creates an instance of the contao model property accessor
 */
final class ContaoModelPropertyAccessorFactory implements PropertyAccessorFactory
{
    /**
     * Related changes registry.
     */
    private ContaoModelRelatedModelChangeTracker $changesRegistry;

    /** @param ContaoModelRelatedModelChangeTracker $changesRegistry Related changes registry. */
    public function __construct(ContaoModelRelatedModelChangeTracker $changesRegistry)
    {
        $this->changesRegistry = $changesRegistry;
    }

     /** {@inheritDoc} */
    #[Override]
    public function supports(mixed $object): bool
    {
        return $object instanceof Model;
    }

     /** {@inheritDoc} */
    #[Override]
    public function create(mixed $object): PropertyAccessor
    {
        assert($object instanceof Model);

        return new ContaoModelPropertyAccessor($object, $this->changesRegistry);
    }
}
