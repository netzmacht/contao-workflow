<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Form;

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
use Netzmacht\Contao\Workflow\ErrorCollection;
use Netzmacht\Contao\Workflow\Flow\Context;

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
     * Created widgets.
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
     * @var PropertyValueBag
     */
    private $propertyValues;

    /**
     * Construct.
     */
    public function __construct()
    {
        // We fake being in an dc general environment by providing all required components for the DcGeneral widget
        // manager.

        $container = new DefaultContainer('workflow_data');
        $container->setPropertiesDefinition(new DefaultPropertiesDefinition());
        $container->setBasicDefinition(new DefaultBasicDefinition());

        $this->model = new DefaultModel();

        $this->environment = new DefaultEnvironment();
        $this->environment->setDataDefinition($container);
        $this->environment->setInputProvider(new InputProvider());
        $this->environment->setEventPropagator(new EventPropagator($GLOBALS['container']['event-dispatcher']));

        $controller = new DefaultController();
        $controller->setEnvironment($this->environment);

        $this->environment->setController($controller);

        $this->widgetManager = new ContaoWidgetManager($this->environment, $this->model);
    }

    /**
     * Validate the form.
     *
     * @param Context $context
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
        $this->updateContext($context);

        return $this->propertyValues->hasNoInvalidPropertyValues();
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $extra
     *
     * @return FormField
     */
    public function createField($name, $type = 'text', array $extra = array())
    {
        $formField = new BackendFormField($name, $type);
        $formField->setExtra($extra);

        return $formField;
    }

    /**
     * Add a field to the form.
     *
     * @param FormField $formField
     * @param string    $fieldset
     *
     * @return $this
     */
    public function addField(FormField $formField, $fieldset = 'default') {
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
     * @return string
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
     *
     */
    private function buildWidgets()
    {
        foreach ($this->fields as $fieldset => $fields) {
            foreach ($fields as $fieldName) {
                $this->widgets[$fieldName] = $this->widgetManager->getWidget($fieldName, $this->propertyValues);
            }
        }
    }

    /**
     * Validate widgets.
     */
    private function validateWidgets()
    {
        $this->widgetManager->processInput($this->propertyValues);
    }

    /**
     * @return PropertyValueBag
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
     * @return array
     */
    private function renderFieldSets()
    {
        $fieldSets = array();
        $first = true;

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
            }
            else {
                $fieldset['class'] = ($first) ? 'tl_tbox' : 'tl_box';
            }

            $fieldSets[] = $fieldset;
            $first       = false;
        }

        return $fieldSets;
    }

    /**
     * @param $formField
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
     * @param Context $context
     */
    private function updateContext(Context $context)
    {
        // set form input as params
        $context->setParams($this->propertyValues->getArrayCopy());

        // copy error messages
        $errorCollection = $context->getErrorCollection();
        foreach ($this->propertyValues->getInvalidPropertyErrors() as $field => $errors) {
            foreach ($errors as $error) {
                $errorCollection->addError(
                    'form.validation.error',
                    array(
                        'field'   => $field,
                        'message' => $error
                    )
                );
            }
        }
    }
}
