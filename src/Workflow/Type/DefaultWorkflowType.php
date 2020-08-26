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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Integration\UpdateEntityAction;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow\PropertyCondition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\Manager;
use function array_keys;

/**
 * Class DefaultWorkflowType.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Type
 */
final class DefaultWorkflowType extends AbstractWorkflowType
{
    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $workflowManager;

    /**
     * Default workflow type configuration.
     *
     * @var array
     */
    private $defaultWorkflowTypes;

    /**
     * DefaultWorkflowType constructor.
     *
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param Manager               $workflowManager       Workflow manager.
     * @param array                 $configuration         Default workflow type configuration.
     */
    public function __construct(
        PropertyAccessManager $propertyAccessManager,
        Manager $workflowManager,
        array $configuration = []
    ) {
        parent::__construct('default_type', array_keys($configuration));

        $this->propertyAccessManager = $propertyAccessManager;
        $this->workflowManager       = $workflowManager;
        $this->defaultWorkflowTypes  = $configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Workflow $workflow, callable $next): void
    {
        parent::configure($workflow, $next);

        $workflow->addCondition(
            new PropertyCondition($this->propertyAccessManager, 'workflow', $workflow->getName())
        );

        $permission = ($this->defaultWorkflowTypes[$workflow->getProviderName()]['step_permission'] ?? false);
        $action     = new UpdateEntityAction($this->propertyAccessManager, $this->workflowManager, $permission);
        foreach ($workflow->getTransitions() as $transition) {
            $transition->addPostAction($action);
        }
    }
}
