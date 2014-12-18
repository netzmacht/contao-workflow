<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Workflow\Contao\View;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class AbstractItemView for views displaying an item.
 *
 * @package Netzmacht\Workflow\Contao\View
 */
abstract class AbstractItemView implements View
{
    /**
     * Current workflow.
     *
     * @var Workflow
     */
    protected $workflow;

    /**
     * The workflow item.
     *
     * @var Item
     */
    protected $item;

    /**
     * The template name.
     *
     * @var string
     */
    protected $template;

    /**
     * Get the item.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set the item.
     *
     * @param Item $item The workflow item.
     *
     * @return $this
     */
    public function setItem(Item $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Set the workflow.
     *
     * @param Workflow $workflow The workflow definition.
     *
     * @return $this
     */
    public function setWorkflow(Workflow $workflow)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get the template name.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the template name.
     *
     * @param string $template The template name.
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Render the template.
     *
     * @param array $params Optional params being rendered.
     *
     * @return string
     */
    protected function renderTemplate($params = array())
    {
        $template = new \BackendTemplate();
        $template->setData($params);

        $template->item     = $this->item;
        $template->workflow = $this->workflow;

        return $template->parse();
    }
}
