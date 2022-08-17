<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Integration;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractPropertyAccessAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Manager\Manager;

/**
 * Class UpdateEntityAction updates the entity of a default workflow item
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class UpdateEntityAction extends AbstractPropertyAccessAction
{
    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $workflowManager;

    /**
     * Flag to store current step permission.
     *
     * @var bool
     */
    private $storePermission;

    /**
     * Construct.
     *
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param Manager               $workflowManager       Workflow manager.
     * @param bool                  $storePermission       Store current step permission.
     */
    public function __construct(
        PropertyAccessManager $propertyAccessManager,
        Manager $workflowManager,
        bool $storePermission
    ) {
        parent::__construct($propertyAccessManager, 'Update entity action');

        $this->workflowManager       = $workflowManager;
        $this->storePermission       = $storePermission;
        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [];
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function validate(Item $item, Context $context): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ActionFailedException When no property access is given.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $entity = $item->getEntity();
        if (! $this->propertyAccessManager->supports($entity)) {
            throw new ActionFailedException('No property access to entity');
        }

        $accessor = $this->propertyAccessManager->provideAccess($entity);
        $accessor->set('workflow', $item->getWorkflowName());
        $accessor->set('workflowStep', $item->getCurrentStepName());

        if (! $this->storePermission) {
            return;
        }

        $workflowName = $item->getWorkflowName();
        if ($workflowName === null) {
            return;
        }

        $workflow = $this->workflowManager->getWorkflowByName($workflowName);
        $stepName = $item->getCurrentStepName();
        if ($stepName === null) {
            return;
        }

        $permission = $workflow->getStep($stepName)->getPermission();
        $accessor->set('workflowStepPermission', $permission ? $permission->__toString() : null);
    }
}
