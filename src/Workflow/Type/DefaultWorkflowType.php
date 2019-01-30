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

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\DefaultType\UpdateEntityAction;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Condition\Workflow\PropertyCondition;
use Netzmacht\Workflow\Flow\Workflow;
use function array_keys;

/**
 * Class DefaultWorkflowType.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Type
 */
final class DefaultWorkflowType extends AbstractWorkflowType
{
    /**
     * DefaultWorkflowType constructor.
     *
     * @param array $configuration Default workflow type configuration.
     */
    public function __construct(array $configuration = [])
    {
        parent::__construct('default_type', array_keys($configuration));
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Workflow $workflow, callable $next): void
    {
        parent::configure($workflow, $next);

        $workflow->addCondition(new PropertyCondition('workflowDefault', $workflow->getName()));
        $action = new UpdateEntityAction();

        foreach ($workflow->getTransitions() as $transition) {
            $transition->addPostAction($action);
        }
    }
}
