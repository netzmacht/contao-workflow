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
use Netzmacht\Workflow\Contao\Form\Contao\ContaoFormField;

class FormType
{
    /**
     * Form name.
     *
     * @var string
     */
    private $name;

    /**
     * Form name prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * Form label.
     *
     * @var string
     */
    private $label;

    /**
     * Form description.
     *
     * @var string
     */
    private $description;

    /**
     * Form data.
     *
     * @var array
     */
    private $data = array();

    /**
     * @var FormField[]
     */
    private $fields = array();

    /**
     * Bind entity values to form values.
     *
     * @var array
     */
    protected $bindValues = array();

    /**
     * @param string $name   Form type name.
     * @param null   $prefix Name prefix.
     */
    function __construct($name, $prefix = null)
    {
        $this->name   = $name;
        $this->prefix = $prefix;

        $this->initialize();
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
     * Set name prefix.
     *
     * @param string $prefix Prefix of the name.
     *
     * @return $this
     */
    public function setNamePrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get Name prefix.
     *
     * @return string
     */
    public function getNamePrefix()
    {
        return $this->prefix;
    }

    /**
     * Get full name.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->prefix . $this->getName();
    }

    /**
     * Get form description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set form description.
     *
     * @param string $description New description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get form label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set form label.
     *
     * @param string $label New Form label.
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Fill the form with data.
     *
     * @param array $data Form data.
     *
     * @return $this
     */
    public function fill($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Bind entity property to form field.
     *
     * @param string      $property  Entity property name.
     * @param string|null $fieldName Optional fieldname if property and fieldname are not the same.
     *
     * @return $this
     */
    public function bind($property, $fieldName = null)
    {
        $fieldName = $fieldName ?: $property;

        $this->bindValues[$fieldName] = $property;
    }

    /**
     * Get form data of an validated form.
     *
     * @param Entity $entity Entity.
     *
     * @return array
     */
    public function getData(Entity $entity = null)
    {
        if (!$entity) {
            return $this->data;
        }

        $data = $this->data;

        foreach ($this->bindValues as $fieldName => $property) {
            $data[$fieldName] = $entity->getProperty($property);
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addField($name, $type, array $extra = null, array $options = null)
    {
        $name  = $this->getFullName() . '_' . $name;
        $field = new ContaoFormField($name, $type);

        if ($extra) {
            $field->setExtra($extra);
        }

        if ($options) {
            $field->setOptions($options);
        }

        $this->fields[] = $field;

        return $field;
    }

    /**
     * Get form field.
     *
     * @param $name
     *
     * @return FormField
     *
     * @throws \InvalidArgumentException If form field is not found.
     */
    public function getField($name)
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === ($this->getName() . '_' . $name)) {
                return $field;
            }
        }

        throw new \InvalidArgumentException(sprintf('Form fied "%s" not found.', $name));
    }

    /**
     * @return FormField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Consider if type has fields.
     * @return bool
     */
    public function hasFields()
    {
        return !empty($this->fields);
    }

    /**
     * Initialize form type.
     *
     * Override this method to create form fields.
     *
     * @return void
     */
    protected function initialize()
    {
    }
}
