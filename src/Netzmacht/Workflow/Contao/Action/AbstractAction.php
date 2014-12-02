<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Action;

use Assert\Assertion;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Workflow\Base;
use Netzmacht\Workflow\Contao\Form\ContaoForm;
use Netzmacht\Workflow\Contao\Form\FormType;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Form\Form;

/**
 * Class AbstractAction which uses an form builder to create user input form data.
 *
 * @package Netzmacht\Workflow\Contao\Action
 */
abstract class AbstractAction extends Base implements Action
{
    /**
     * Optional form builder.
     *
     * @var FormType
     */
    protected $formType;

    /**
     * Construct.
     *
     * @param string   $name     Name of the action.
     * @param null     $label    abel of the action.
     * @param array    $config   Iotional config.
     * @param FormType $formType Optional form type.
     */
    public function __construct($name, $label = null, array $config = array(), FormType $formType = null)
    {
        parent::__construct($name, $label, $config);

        $this->formType = $formType;
    }

    /**
     * Get the form builder.
     *
     * @return FormType
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * Set the form builder.
     *
     * @param FormType $formType The form type.
     *
     * @return $this
     */
    public function setFormType(FormType $formType)
    {
        $this->formType = $formType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isInputRequired(Item $item)
    {
        if ($this->formType) {
            return $this->formType->hasFields();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form $form, Item $item)
    {
        if ($this->formType && $form instanceof ContaoForm) {
            $form->addForm($this->formType);
        }
    }

    /**
     * Get the entity of the item and protect entity type.
     *
     * @param Item $item Workflow item.
     *
     * @return Entity
     *
     * @hrows AssertionException If entity is not an Instance of
     */
    protected function getEntity(Item $item)
    {
        $entity = $item->getEntity();

        Assertion::isInstanceOf(
            $entity,
            'ContaoCommunityAlliance\DcGeneral\Data\ModelInterface',
            'Invalid entity given'
        );

        return $entity;
    }
}
