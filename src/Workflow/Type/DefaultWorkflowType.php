<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Type;

use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Integration\UpdateEntityAction;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow\PropertyCondition;
use Netzmacht\Workflow\Flow\Condition\Workflow\OrCondition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\Manager;

use function array_keys;

/** @SuppressWarnings(PHPMD.LongVariable) */
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
     * @var array<string,mixed>
     */
    private $defaultWorkflowTypes;

    /**
     * @param PropertyAccessManager $propertyAccessManager Property access manager.
     * @param Manager               $workflowManager       Workflow manager.
     * @param array<string,mixed>   $configuration         Default workflow type configuration.
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

    public function configure(Workflow $workflow, callable $next): void
    {
        parent::configure($workflow, $next);

        $condition = new PropertyCondition($this->propertyAccessManager, 'workflow', $workflow->getName());
        if ($workflow->getConfigValue('autoAssign', false)) {
            $condition = new OrCondition(
                [
                    $condition,
                    new PropertyCondition($this->propertyAccessManager, 'workflow', ''),
                ]
            );
        }

        $workflow->addCondition($condition);

        $permission = ($this->defaultWorkflowTypes[$workflow->getProviderName()]['step_permission'] ?? false);
        $action     = new UpdateEntityAction($this->propertyAccessManager, $this->workflowManager, $permission);
        foreach ($workflow->getTransitions() as $transition) {
            $transition->addPostAction($action);
        }
    }
}
