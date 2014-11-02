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
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\EncodePropertyValueFromWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Data\DefaultModel;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\DefaultContainer;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\DefaultPropertiesDefinition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\PropertiesDefinitionInterface;
use ContaoCommunityAlliance\DcGeneral\DefaultEnvironment;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use ContaoCommunityAlliance\DcGeneral\Event\EventPropagator;

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
     * Construct.
     */
    public function __construct()
    {
        // We fake being in an dc general environment by providing all required components for the DcGeneral widget
        // manager.

        $container = new DefaultContainer('workflow_data');
        $container->setPropertiesDefinition(new DefaultPropertiesDefinition());

        $this->model = new DefaultModel();

        $this->environment = new DefaultEnvironment();
        $this->environment->setDataDefinition($container);
        $this->environment->setInputProvider(new InputProvider());
        $this->environment->setEventPropagator(new EventPropagator($GLOBALS['container']['event-dispatcher']));

        $this->widgetManager = new ContaoWidgetManager($this->environment, $this->model);
    }

    /**
     * Validate the form.
     *
     * @return bool
     */
    public function validate()
    {
        if (\Input::post('FORM_SUBMIT') != $this->formName) {
            return false;
        }

        $this->buildWidgets();

        $propertyValues = $this->getPropertyValues();
        $this->validateWidgets($propertyValues);

        return $propertyValues->hasNoInvalidPropertyValues();
    }

    /**
     * Add a field to the form.
     *
     * @param string $name     Name of the field
     * @param string $type     Widget type name.
     * @param mixed  $default  Optional default value.
     * @param array  $config   Widget configuration.
     * @param array  $options  Optional Widget options.
     * @param string $fieldset Legend to which the widget should be grouped.
     *
     * @return $this
     */
    public function addField(
        $name,
        $type,
        $default = null,
        array $config = array(),
        array $options = array(),
        $fieldset = 'default'
    ) {
        $this->createPropertyForField($name, $type, $default, $config, $options);

        $this->fields[$fieldset][] = $name;

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
    public function addFieldset($name, $label, $description = null, $class = null)
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

        $template->submitLabel = $GLOBALS['TL_LANG']['MSC']['workflowSubmitLabel'];
        $template->name        = $this->templateName;
        $template->fieldsets   = $this->renderFieldSets();

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
                $this->widgets[$fieldName] = $this->widgetManager->getWidget($fieldName);
            }
        }
    }

    /**
     * Validate widgets.
     *
     * @copyright The MetaModels team./ DcGeneral
     * @see       Taken from ContaoCommunityAlliance/DcGeneral/Contao/View/Contao2BackendView/ContaoWidgetManager
     *
     * @param PropertyValueBag $propertyValues
     */
    private function validateWidgets(PropertyValueBag $propertyValues)
    {
        // @codingStandardsIgnoreStart - Remember current POST data and clear it.
        $post  = $_POST;
        $_POST = array();
        // @codingStandardsIgnoreEnd
        \Input::resetCache();

        // Set all POST data, these get used within the Widget::validate() method.
        foreach ($propertyValues as $property => $propertyValue) {
            $_POST[$property] = $propertyValue;
        }

        // Now get and validate the widgets.
        foreach ($this->fields as $fieldset => $fields) {
            foreach ($fields as $property) {
                // NOTE: the passed input values are RAW DATA from the input provider - aka widget known values and not
                // native data as in the model.
                // Therefore we do not need to decode them but MUST encode them.
                $widget = $this->widgets[$property];
                $widget->validate();

                if ($widget->hasErrors()) {
                    foreach ($widget->getErrors() as $error) {
                        $propertyValues->markPropertyValueAsInvalid($property, $error);
                    }
                } elseif ($widget->submitInput()) {
                    try {
                        $propertyValues->setPropertyValue(
                            $property,
                            $this->encodeValue($property, $widget->value, $propertyValues)
                        );
                    } catch (\Exception $e) {
                        $widget->addError($e->getMessage());
                        $propertyValues->markPropertyValueAsInvalid($property, $e->getMessage());
                    }
                }
            }
        }

        $_POST = $post;
        \Input::resetCache();
    }

    /**
     * @return PropertyValueBag
     */
    private function getPropertyValues()
    {
        $propertyValues = new PropertyValueBag();

        foreach ($this->fields as $fields) {
            foreach ($fields as $fieldName) {
                $propertyValues->setPropertyValue(
                    $fieldName,
                    $this->environment->getInputProvider()->getValue($fieldName, true)
                );
            }
        }

        return $propertyValues;
    }

    private function encodeValue($property, $value, $propertyValues)
    {
        $environment = $this->getEnvironment();

        $event = new EncodePropertyValueFromWidgetEvent($environment, $this->model, $propertyValues);
        $event
            ->setProperty($property)
            ->setValue($value);

        $environment->getEventDispatcher()->dispatch(
            sprintf('%s[%s][%s]', $event::NAME, $environment->getDataDefinition()->getName(), $property),
            $event
        );
        $environment->getEventDispatcher()->dispatch(
            sprintf('%s[%s]', $event::NAME, $environment->getDataDefinition()->getName()),
            $event
        );
        $environment->getEventDispatcher()->dispatch($event::NAME, $event);

        return $event->getValue();
    }

    /**
     * @return EnvironmentInterface
     */
    private function getEnvironment()
    {
        return $this->environment;
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
                $fields[] = $this->widgetManager->renderWidget($fieldName, false);
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
     * @param       $name
     * @param       $type
     * @param       $default
     * @param array $config
     * @param array $options
     *
     * @return void
     */
    private function createPropertyForField($name, $type, $default, array $config, array $options)
    {
        $property = new DefaultProperty($name);
        $property->setWidgetType($type);
        $property->setExtra($config);

        if ($default) {
            $this->model->setProperty($name, $default);
        }

        if ($options) {
            $property->setOptions($options);
        }

        $this->getPropertiesDefinition()->addProperty($property);
    }
}
