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

abstract class AbstractForm implements ContaoForm
{
    /**
     * Form name.
     *
     * @var string
     */
    private $name;

    /**
     * @var FormType[]
     */
    private $forms = array();

    /**
     * @var Entity
     */
    private $entity;

    /**
     * Form error collection.
     *
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * @param string $name
     */
    function __construct($name)
    {
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
            $data[$form->getName()] = $form->getData($this->entity);
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
        return $this->entity;
    }

    /**
     * Set entity.
     *
     * @param Entity $entity Entity.
     *
     * @return $this
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;

        return $this;
    }
}
