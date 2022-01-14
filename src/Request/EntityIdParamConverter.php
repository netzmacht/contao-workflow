<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Request;

use Netzmacht\Workflow\Data\EntityId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EntityIdParamConverter converts given EntityId raw value to the EntityId class.
 */
final class EntityIdParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $attribute = $configuration->getName();

        if (! $request->attributes->has($attribute)) {
            return false;
        }

        $entityId = EntityId::fromString($request->attributes->get($attribute));
        $request->attributes->set($attribute, $entityId);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === EntityId::class;
    }
}
