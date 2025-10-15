<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Request\ValueResolver;

use Netzmacht\Workflow\Data\EntityId;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

use function is_string;

/**
 * Class EntityIdParamConverter converts given EntityId raw value to the EntityId class.
 */
final class EntityIdValueResolver implements ValueResolverInterface
{
    /** {@inheritDoc} */
    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== EntityId::class) {
            return [];
        }

        $attributeName = $argument->getName();
        if (! $request->attributes->has($attributeName)) {
            return [];
        }

        $value = $request->attributes->get($attributeName);

        if (! is_string($value)) {
            return [];
        }

        yield EntityId::fromString($value);
    }
}
