<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Form;

use ContaoCommunityAlliance\DcGeneral\Contao\InputProvider;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\ContaoWidgetManager;
use ContaoCommunityAlliance\DcGeneral\Controller\DefaultController;
use ContaoCommunityAlliance\DcGeneral\Data\DefaultModel;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\DefaultContainer;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\DefaultBasicDefinition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\DefaultPropertiesDefinition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\PropertyInterface;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\PropertiesDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\DefaultEnvironment;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use ContaoCommunityAlliance\DcGeneral\Event\EventPropagator;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;

/**
 * Form implementation for a backend form.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
class BackendForm implements Form
{
    /**
     * Name of the form.
     *
     * @var string
     */
    private $formName = 'workflow_transition';

    /**
     * Template name.
     *
     * @var string
     */
    private $templateName = 'be_workflow_transition_form';

    /**
     * Instance for the widget manager.
     *
     * @var ContaoWidgetManager
     */
    private $widgetManager;

    /**
     * Legends.
     *
     * @var array
     */
    private $fieldsets = array();

    /**
     * Field configurations.
     *
     * @var array
     */
    private $fields = array();

    /**
     * Created form widgets.
     *
     * @var array|\Widget[]
     */
    private $widgets = array();

    /**
     * DcGeneral environment.
     *
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * The model is used for storing form data.
     *
     * @var ModelInterface
     */
    private $model;

    /**
     * Property values.
     *
     * @var PropertyValueBag
     */
    private $propertyValues;

    /**
     * Form error collection.
     *
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * Construct.
     */
    public function __construct()
    {
        // We fake being in an dc general environment by providing all required components for the DcGeneral widget
        // manager.
        $this->createEnvironment();

        $this->model           = new DefaultModel();
        $this->widgetManager   = new ContaoWidgetManager($this->environment, $this->model);
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
     * Validate the form.
     *
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function validate(Context $context)
    {
        if (\Input::post('FORM_SUBMIT') != $this->formName) {
            return false;
        }

        $this->loadPropertyValues();
        $this->buildWidgets();
        $this->validateWidgets();
        $this->updateErrorCollection();

        $context->setParams($this->propertyValues->getArrayCopy());

        return $this->propertyValues->hasNoInvalidPropertyValues();
    }

    /**
     * Create a form field.
     *
     * @param string      $name     The name of the form field.
     * @param string      $type     The type of the form field.
     * @param array       $extra    Extra informations.
     * @param string|null $fieldset Optional a fieldset where the field is added. If null form field ist just created.
     *
     * @return FormField
     */
    public function createField($name, $type = 'text', array $extra = array(), $fieldset = null)
    {
        $formField = new BackendFormField($name, $type);
        $formField->setExtra($extra);

        if ($fieldset) {
            $this->addField($formField, $fieldset);
        }

        return $formField;
    }

    /**
     * Add a field to the form.
     *
     * @param FormField $formField The form field to add.
     * @param string    $fieldset  The fieldset being used.
     *
     * @return $this
     */
    public function addField(FormField $formField, $fieldset = 'default')
    {
        $property = $this->convertToProperty($formField);

        $this->getPropertiesDefinition()->addProperty($property);
        $this->fields[$fieldset][] = $formField->getName();

        return $this;
    }

    /**
     * Add label and description for a fieldset.
     *
     * @param string $name        Name of the fieldset.
     * @param string $label       Label of the fieldset.
     * @param string $description Description in the fieldset.
     * @param string $class       Optional css class.
     *
     * @return $this
     */
    public function setFieldsetDetails($name, $label, $description = null, $class = null)
    {
        $this->fieldsets[$name] = array(
            'legend'      => $label,
            'description' => $description
        );

        if ($class) {
            $this->fieldsets[$name]['class'] = $class;
        }

        return $this;
    }

    /**
     * Render the form.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function render()
    {
        $template = new \BackendTemplate($this->templateName);

        $template->submitLabel  = $GLOBALS['TL_LANG']['MSC']['workflowSubmitLabel'];
        $template->name         = $this->formName;
        $template->fieldsets    = $this->renderFieldSets();
        $template->requestToken = \RequestToken::get();

        return $template->parse();
    }

    /**
     * Get form data of an validated form.
     *
     * @return array
     */
    public function getData()
    {
        return $this->model->getPropertiesAsArray();
    }

    /**
     * Build all widgets.
     *
     * @return void
     */
    private function buildWidgets()
    {
        foreach ($this->fields as $fields) {
            foreach ($fields as $fieldName) {
                $this->widgets[$fieldName] = $this->widgetManager->getWidget($fieldName, $this->propertyValues);
            }
        }
    }

    /**
     * Validate widgets.
     *
     * @return void
     */
    private function validateWidgets()
    {
        $this->widgetManager->processInput($this->propertyValues);
    }

    /**
     * Load property values.
     *
     * @return void
     */
    private function loadPropertyValues()
    {
        $this->propertyValues = new PropertyValueBag();

        foreach ($this->fields as $fields) {
            foreach ($fields as $fieldName) {
                $value = $this->environment->getInputProvider()->getValue($fieldName, true);

                // Set value to property values and to model. If validation failed, the widget manager loads data
                // from the model.
                $this->model->setProperty($fieldName, $value);
                $this->propertyValues->setPropertyValue($fieldName, $value);
            }
        }
    }

    /**
     * Get the properties definition.
     *
     * @return PropertiesDefinitionInterface
     */
    private function getPropertiesDefinition()
    {
        return $this->environment->getDataDefinition()->getPropertiesDefinition();
    }

    /**
     * Render all fieldsets.
     *
     * @return array
     */
    private function renderFieldSets()
    {
        $fieldSets = array();
        $first     = true;

        foreach ($this->fields as $name => $widgets) {
            $fields = array();

            foreach ($widgets as $fieldName) {
                $fields[] = $this->widgetManager->renderWidget($fieldName, false, $this->propertyValues);
            }

            $fieldset = array(
                'label'   => $name,
                'palette' => implode('', $fields),
                'legend'  => $name
            );

            if (isset($this->fieldsets[$name])) {
                $fieldset = array_merge($fieldset, $this->fieldsets[$name]);
            }

            if (isset($fieldset['class'])) {
                $fieldset['class'] .= ' ';
                $fieldset['class'] .= ($first) ? 'tl_tbox' : 'tl_box';
            } else {
                $fieldset['class'] = ($first) ? 'tl_tbox' : 'tl_box';
            }

            $fieldSets[] = $fieldset;
            $first       = false;
        }

        return $fieldSets;
    }

    /**
     * Convert a form field to the internal used property.
     *
     * @param FormField $formField The form field.
     *
     * @return PropertyInterface
     */
    private function convertToProperty(FormField $formField)
    {
        // Backend form field is a facade for the property.
        if ($formField instanceof PropertyInterface) {
            return $formField;
        }

        $property = new BackendFormField($formField->getName(), $formField->getWidgetType());
        $property
            ->setLabel($formField->getLabel())
            ->setDescription($formField->getDescription())
            ->setOptions($formField->getOptions())
            ->setExtra($formField->getExtra())
            ->setExplanation($formField->getExplanation());

        return $property;
    }

    /**
     * Update the error collection.
     *
     * @return void
     */
    private function updateErrorCollection()
    {
        $this->errorCollection->reset();

        // copy error messages
        foreach ($this->propertyValues->getInvalidPropertyErrors() as $field => $errors) {
            foreach ($errors as $error) {
                $this->errorCollection->addError(
                    'form.validation.error',
                    array(
                        'field'   => $field,
                        'message' => $error
                    )
                );
            }
        }
    }

    /**
     * Create the event propagator.
     *
     * @return EventPropagator
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getEventPropagator()
    {
        return new EventPropagator($GLOBALS['container']['event-dispatcher']);
    }

    /**
     * Create the environment.
     *
     * @return void
     */
    private function createEnvironment()
    {
        $container = new DefaultContainer('workflow_data');
        $container->setPropertiesDefinition(new DefaultPropertiesDefinition());
        $container->setBasicDefinition(new DefaultBasicDefinition());

        $this->environment = new DefaultEnvironment();
        $this->environment->setDataDefinition($container);
        $this->environment->setInputProvider(new InputProvider());
        $this->environment->setEventPropagator($this->getEventPropagator());

        $controller = new DefaultController();
        $controller->setEnvironment($this->environment);

        $this->environment->setController($controller);
    }
}
