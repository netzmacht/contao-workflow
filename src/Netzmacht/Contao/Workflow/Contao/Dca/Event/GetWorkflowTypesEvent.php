<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Contao\Dca\Event;


use Netzmacht\Contao\Workflow\Contao\Model\WorkflowModel;
use Symfony\Component\EventDispatcher\Event;

class GetWorkflowTypesEvent extends Event
{
    const NAME = 'workflow.backend.get-workflow-types';

    /**
     * @var array
     */
    private $types = array();

    /**
     * @param WorkflowModel $workflowModel
     */
    public function __construct(WorkflowModel $workflowModel)
    {
        $this->workflowModel = $workflowModel;
    }

    /**
     * @return WorkflowModel
     */
    public function getWorkflowModel()
    {
        return $this->workflowModel;
    }

    /**
     * @param $category
     * @param $name
     *
     * @return $this
     */
    public function addType($category, $name)
    {
        if (!isset($this->types[$category])) {
            $this->types[$category] = array();
        }

        $name = sprintf('%s_%s', $category, $name);;

        if (!in_array($name, $this->types[$category])) {
            $this->types[$category][] = $name;
        }

        return $this;
    }

    /**
     * @param       $category
     * @param array $types
     *
     * @return $this
     */
    public function addTypes($category, array $types)
    {
        foreach ($types as $type) {
            $this->addType($category, $type);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }
}
