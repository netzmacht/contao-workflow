<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Form;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;

abstract class AbstractForm implements ContaoForm
{
    /**
     * Form name.
     *
     * @var string
     */
    protected $name;

    /**
     * @var FormType[]
     */
    protected $forms = array();

    /**
     * @var Item
     */
    protected $item;

    /**
     * Form error collection.
     *
     * @var ErrorCollection
     */
    protected $errorCollection;

    /**
     * Transition context.
     *
     * @var Context
     */
    protected $context;

    /**
     * @param string $name
     */
    function __construct($name)
    {
        $this->name            = $name;
        $this->errorCollection = new ErrorCollection();
    }

    /**
     * Get errors of form.
     *
     * @return ErrorCollection
     */
    public function getErrorCollection()
    {
        return $this->errorCollection;
    }

    /**
     * Set form name.
     *
     * @param string $name Form name.
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get form name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add form.
     *
     * @param FormType $form Child form.
     *
     * @return $this
     */
    public function addForm(FormType $form)
    {
        $this->forms[] = $form;

        return $this;
    }

    /**
     *
     * @param string $name Form name.
     *
     * @return FormType
     *
     * @throws \InvalidArgumentException If form does not exists.
     */
    public function getForm($name)
    {
        foreach ($this->forms as $form) {
            if ($form->getName() === $name) {
                return $form;
            }
        }

        throw new \InvalidArgumentException(sprintf('Form "%s" not found', $name));
    }

    /**
     * Get all sub forms.
     *
     * @return FormType[]
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Get form data of an validated form.
     *
     * @return array
     */
    public function getData()
    {
        $data = array();

        foreach ($this->forms as $form) {
            $data[$form->getName()] = $form->getData($this->item->getEntity());
        }

        return $data;
    }

    /**
     * Get entity.
     *
     * @return Entity
     */
    public function getEntity()
    {
        return $this->item->getEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(Item $item, Context $context)
    {
        $this->item    = $item;
        $this->context = $context;
    }
}
