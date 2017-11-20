<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Form;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;

/**
 * AbstractForm is the base form implementation of the contao form.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
abstract class AbstractForm implements ContaoForm
{
    /**
     * Form name.
     *
     * @var string
     */
    protected $name;

    /**
     * Sub form types.
     *
     * @var FormType[]
     */
    protected $forms = array();

    /**
     * Workflow item.
     *
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
     * Construct.
     *
     * @param string          $name            Form name.
     * @param ErrorCollection $errorCollection Error collection.
     */
    public function __construct($name, ErrorCollection $errorCollection = null)
    {
        $this->name            = $name;
        $this->errorCollection = $errorCollection ?: new ErrorCollection();
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
     * Get a sub form.
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
