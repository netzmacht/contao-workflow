<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Form\Contao;

use ContaoCommunityAlliance\DcGeneral\Contao\InputProvider;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\ContaoWidgetManager;
use ContaoCommunityAlliance\DcGeneral\Controller\DefaultController;
use ContaoCommunityAlliance\DcGeneral\Data\DefaultModel;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\DefaultContainer;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\DefaultBasicDefinition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\DefaultPropertiesDefinition;
use ContaoCommunityAlliance\DcGeneral\DefaultEnvironment;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use Netzmacht\Contao\Workflow\Form\AbstractForm;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;

/**
 * Form implementation for a backend form.
 *
 * @package Netzmacht\Contao\Workflow\Form
 */
class BackendForm extends AbstractForm
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
     * Construct.
     *
     * @param string          $name            The form name.
     * @param ErrorCollection $errorCollection The error collection.
     * @param string|null     $prefix          The form field prefix.
     */
    public function __construct($name, ErrorCollection $errorCollection = null, $prefix = null)
    {
        parent::__construct($name, $errorCollection, $prefix);

        // Fake being in an dc general environment by providing all required components for the
        // DcGeneral widget manager.
        $this->createEnvironment();

        $this->model         = new DefaultModel();
        $this->widgetManager = new ContaoWidgetManager($this->environment, $this->model);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(Item $item, Context $context)
    {
        parent::prepare($item, $context);

        $this->loadPropertyValues();
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        if (!$this->isSubmit()) {
            return false;
        }

        $this->buildWidgets();
        $this->validateWidgets();
        $this->updateErrorCollection();

        foreach ($this->getForms() as $form) {
            foreach ($form->getFields() as $field) {
                $this->context->setParam(
                    substr($field->getName(), (strlen($form->getName()) + 1)),
                    $this->propertyValues->getPropertyValue($field->getName()),
                    $form->getName()
                );
            }
        }
        return $this->propertyValues->hasNoInvalidPropertyValues();
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
        $template->fieldsets    = $this->renderSubForms();
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
        $values = array();

        foreach ($this->getForms() as $form) {
            $values[$form->getName()] = $form->getData($this->getEntity());
        }

        return $values;
    }

    /**
     * Build all widgets.
     *
     * @return void
     */
    private function buildWidgets()
    {
        foreach ($this->getForms() as $form) {
            foreach ($form->getFields() as $field) {
                $this->widgets[$field->getName()] = $this->widgetManager->getWidget(
                    $field->getName(),
                    $this->propertyValues
                );
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
        $flatten              = $this->flatten($this->getData());
        $this->propertyValues = new PropertyValueBag($flatten);
        $this->model->setPropertiesAsArray($flatten);

        foreach ($this->getForms() as $form) {
            foreach ($form->getFields() as $field) {
                $defintion = $this->environment->getDataDefinition()->getPropertiesDefinition();
                $defintion->addProperty($field);

                if (!$this->isSubmit()) {
                    continue;
                }

                $value = $this->environment->getInputProvider()->getValue($field->getName(), true);

                // Set value to property values and to model. If validation failed, the widget manager loads data
                // from the model.
                $this->model->setProperty($field->getName(), $value);
                $this->propertyValues->setPropertyValue($field->getName(), $value);
            }
        }
    }

    /**
     * Render all fieldsets.
     *
     * @return array
     */
    private function renderSubForms()
    {
        $fieldSets = array();
        $first     = true;

        foreach ($this->getForms() as $form) {
            $fields = array();

            foreach ($form->getFields() as $field) {
                $fields[] = $this->widgetManager->renderWidget($field->getName(), false, $this->propertyValues);
            }

            $fieldset = array(
                'label'       => $form->getLabel(),
                'name'        => $form->getFullName(),
                'palette'     => implode('', $fields),
                'legend'      => $form->getLabel(),
                'description' => $form->getDescription(),
            );

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
     * Update the error collection.
     *
     * @return void
     */
    private function updateErrorCollection()
    {
        $this->getErrorCollection()->reset();

        // copy error messages
        foreach ($this->propertyValues->getInvalidPropertyErrors() as $field => $errors) {
            foreach ($errors as $error) {
                $this->getErrorCollection()->addError(
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

        $controller = new DefaultController();
        $controller->setEnvironment($this->environment);

        $this->environment->setController($controller);
    }

    /**
     * Flatten the form data.
     *
     * @param array $data Raw form data.
     *
     * @return array
     */
    private function flatten($data)
    {
        $flatten = array();

        foreach ($data as $name => $values) {
            foreach ($values as $field => $value) {
                $flatten[$name . '_' . $field] = $value;
            }
        }

        return $flatten;
    }

    /**
     * Check if form was submitted.
     *
     * @return bool
     */
    public function isSubmit()
    {
        return \Input::post('FORM_SUBMIT') === $this->formName;
    }
}
