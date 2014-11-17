<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Dca\Event;

use Netzmacht\Workflow\Contao\Model\WorkflowModel;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetWorkflowTypesEvent is dispatched when collection all available workflow types.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca\Event
 */
class GetWorkflowActionsEvent extends Event
{
    const NAME = 'workflow.backend.get-workflow-types';

    /**
     * Collected workflow actions.
     *
     * @var array
     */
    private $actions = array();

    /**
     * Construct.
     *
     * @param WorkflowModel $workflowModel Current workflow model.
     */
    public function __construct(WorkflowModel $workflowModel)
    {
        $this->workflowModel = $workflowModel;
    }

    /**
     * Get workflow model.
     *
     * @return WorkflowModel
     */
    public function getWorkflowModel()
    {
        return $this->workflowModel;
    }

    /**
     * Add a new action.
     *
     * @param string $category Category.
     * @param string $name     Action name.
     *
     * @return $this
     */
    public function addAction($category, $name)
    {
        if (!isset($this->actions[$category])) {
            $this->actions[$category] = array();
        }

        $name = sprintf('%s_%s', $category, $name);

        if (!in_array($name, $this->actions[$category])) {
            $this->actions[$category][] = $name;
        }

        return $this;
    }

    /**
     * Add new actions.
     *
     * @param string $category Category name.
     * @param array  $actions  Set of actions.
     *
     * @return $this
     */
    public function addActions($category, array $actions)
    {
        foreach ($actions as $type) {
            $this->addAction($category, $type);
        }

        return $this;
    }

    /**
     * Get all actions.
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }
}
