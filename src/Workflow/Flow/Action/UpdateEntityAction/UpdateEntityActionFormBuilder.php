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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\UpdateEntityAction;

use Contao\StringUtil;
use Netzmacht\ContaoFormBundle\Form\DcaFormType;
use Netzmacht\ContaoWorkflowBundle\Form\Builder\DataAwareActionFormBuilder;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use function assert;

/**
 * Class builds the form for the update entity action which is based on an dca
 */
final class UpdateEntityActionFormBuilder implements DataAwareActionFormBuilder
{
    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * UpdateEntityActionFormBuilder constructor.
     *
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     */
    public function __construct(PropertyAccessManager $propertyAccessManager)
    {
        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Action $action): bool
    {
        return $action instanceof UpdateEntityAction;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
        assert($action instanceof UpdateEntityAction);

        $formBuilder->add(
            $action->getName(),
            DcaFormType::class,
            [
                'dataContainer' => $transition->getWorkflow()->getProviderName(),
                'fields'        => StringUtil::deserialize($action->getConfigValue('update_entity_properties', true)),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function buildFormData(
        Action $action,
        Transition $transition,
        Context $context,
        Item $item,
        array $data
    ): array {
        assert($action instanceof UpdateEntityAction);

        $fields     = StringUtil::deserialize($action->getConfigValue('update_entity_properties', true));
        $accessor   = $this->propertyAccessManager->provideAccess($item->getEntity());
        $actionData = [];

        foreach ($fields as $field) {
            $actionData[$field] = $accessor->get($field);
        }

        $data[$action->getName()] = $actionData;

        return $data;
    }
}
