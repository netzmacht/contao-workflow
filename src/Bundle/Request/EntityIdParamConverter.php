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

namespace Netzmacht\Contao\Workflow\Bundle\Request;

use Netzmacht\Workflow\Data\EntityId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EntityIdParamConverter
 *
 * @package Netzmacht\Contao\Workflow\Bundle\Request
 */
class EntityIdParamConverter implements ParamConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $attribute = $configuration->getName();

        if (!$request->attributes->has($attribute)) {
            return false;
        }

        $entityId = EntityId::fromString($request->attributes->get($attribute));
        $request->attributes->set($attribute, $entityId);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === EntityId::class;
    }
}
