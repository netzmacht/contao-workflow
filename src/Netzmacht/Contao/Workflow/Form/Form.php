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

use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;

/**
 * Class Form describes a form instance.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
class Form
{
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
     * @var InputProviderInterface
     */
    private $inputProvider;

    /**
     * Validate the form.
     *
     * @return bool
     */
    public function validate()
    {
        $this->buildWidgets();

        $propertyValues = $this->getPropertyValues();
        $this->validateWidgets($propertyValues);

        return $propertyValues->hasNoInvalidPropertyValues();
    }

    /**
     * @param       $fieldName
     * @param array $fieldConfiguration
     * @param       $legend
     */
    public function addField($fieldName, array $fieldConfiguration, $legend)
    {
        $this->fields[$fieldName] = $fieldConfiguration;
        $this->legends[$legend][] = $fieldName;
    }

    private function buildWidgets()
    {
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
        foreach (array_keys($this->fields) as $property) {
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

        foreach (array_keys($this->fields) as $fieldName) {
            $propertyValues->setPropertyValue($fieldName, $this->inputProvider->getValue($propertyValues, true));
        }

        return $propertyValues;
    }
}
