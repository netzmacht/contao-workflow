<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\View;


use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;

abstract class AbstractItemView implements View
{
    /**
     * @var Workflow
     */
    protected $workflow;

    /**
     * @var Item
     */
    protected $item;

    /**
     * @var string
     */
    protected $template;

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @param Workflow $workflow
     */
    public function setWorkflow($workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @param $params
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
