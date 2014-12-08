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
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Form\Form;
use Verraes\ClassFunctions\ClassFunctions;

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
     * Log changed values as workflow data
     *
     * @var bool
     */
    private $logChanges = true;

    /**
     * Log namespace is used as property namespace if logging in enabled.
     *
     * @var string
     */
    private $logNamespace;

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

        $this->formType     = $formType;
        $this->logNamespace = $this->createDefaultLogNamespace();
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
     * Consider if changes are logged.
     *
     * @return boolean
     */
    public function isLogChanges()
    {
        return $this->logChanges;
    }

    /**
     * Set log changes.
     *
     * @param boolean $logChanges Log changes.
     *
     * @return $this
     */
    public function setLogChanges($logChanges)
    {
        $this->logChanges = (bool) $logChanges;

        return $this;
    }

    /**
     * Get log namespace.
     *
     * @return string
     */
    public function getLogNamespace()
    {
        return $this->logNamespace;
    }

    /**
     * Set new log namespace.
     *
     * @param string $logNamespace New log namespace.
     */
    public function setLogNamespace($logNamespace)
    {
        $this->logNamespace = $logNamespace;
    }

    /**
     * Log changes if enabled.
     *
     * @param string  $property Property name.
     * @param mixed   $value    Property value.
     * @param Context $context  Transition context.
     *
     * @return $this
     */
    protected function logChanges($property, $value, Context $context)
    {
        if ($this->isLogChanges()) {
            $context->setProperty($property, $value, $this->getLogNamespace());
        }

        return $this;
    }

    /**
     * Log multiple changes.
     *
     * @param array   $values  Changes propertys as associated array['name' => 'val'].
     * @param Context $context Transition context.
     *
     * @return $this
     */
    protected function logMultipleChanges(array $values, Context $context)
    {
        if ($this->isLogChanges()) {
            $namespace = $this->getLogNamespace();

            foreach ($values as $name => $value) {
                $context->setProperty($name, $values, $namespace);
            }
        }

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

    /**
     * Create default log namespace.
     *
     * @return string
     */
    private function createDefaultLogNamespace()
    {
        $className = ClassFunctions::underscore(ClassFunctions::short($this));

        return preg_replace('/_action$/', '', $className, 1);
    }
}
