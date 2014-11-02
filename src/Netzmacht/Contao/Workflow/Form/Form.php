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
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\DefaultContainer;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\DefaultPropertiesDefinition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;
use ContaoCommunityAlliance\DcGeneral\DefaultEnvironment;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use ContaoCommunityAlliance\DcGeneral\Event\EventPropagator;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;

/**
 * Class Form describes a form instance.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
class Form
{
    /**
     * @var ContaoWidgetManager
     */
    private $widgetManager;

    /**
     * Legends.
     *
     * @var array
     */
    private $legends = array();

    /**
     * Field configurations.
     *
     * @var array
     */
    private $fields = array();

    /**
     * @var array|\Widget[]
     */
    private $widgets = array();

    /**
     * @var EnvironmentInterface
     */
    private $environment;

    public function __construct()
    {
        $container = new DefaultContainer('workflow_data');
        $container->setPropertiesDefinition(new DefaultPropertiesDefinition());

        $this->model = new DefaultModel();

        $this->environment   = new DefaultEnvironment();
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
        if (\Input::post('FORM_SUBMIT') != 'workflow_transition') {
            return false;
        }

        $this->buildWidgets();

        $propertyValues = $this->getPropertyValues();
        $this->validateWidgets($propertyValues);

        return $propertyValues->hasNoInvalidPropertyValues();
    }

    /**
     * @param       $fieldName
     * @param array $config
     * @param       $legend
     */
    public function addField($fieldName, array $config, $legend)
    {
        $property = new DefaultProperty($fieldName);
        $property->setWidgetType($config['inputType']);
        $property->setExtra($config['eval']);

        if (isset($config['options'])) {
            $property->setOptions($config['options']);
        }

        $this->environment->getDataDefinition()->getPropertiesDefinition()->addProperty($property);

        $this->fields[]           = $fieldName;
        $this->legends[$legend][] = $fieldName;
    }

    /**
     *
     */
    private function buildWidgets()
    {
        foreach ($this->fields as $fieldName) {
            $this->widgets[$fieldName] = $this->widgetManager->getWidget($fieldName);
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        $rendered = array();

        foreach ($this->widgets as $name => $widget) {
            $rendered[] = $this->widgetManager->renderWidget($name, false);
        }

        return implode('', $rendered);
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
        foreach ($this->fields as $property) {
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

        $_POST = $post;
        \Input::resetCache();
    }

    /**
     * @return PropertyValueBag
     */
    private function getPropertyValues()
    {
        $propertyValues = new PropertyValueBag();

        foreach ($this->fields as $fieldName) {
            $propertyValues->setPropertyValue(
                $fieldName,
                $this->environment->getInputProvider()->getValue($fieldName, true)
            );
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
}
