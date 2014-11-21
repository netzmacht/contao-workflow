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
use Netzmacht\Workflow\Contao\Form\FormBuilder;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Form\Form;

/**
 * Class AbstractAction which uses an form builder to create user input form data.
 *
 * @package Netzmacht\Workflow\Contao\Action
 */
abstract class AbstractAction implements Action
{
    /**
     * Optional form builder.
     *
     * @var FormBuilder
     */
    private $formBuilder;

    /**
     * Construct.
     *
     * @param FormBuilder $formBuilder Optional form builder.
     */
    public function __construct(FormBuilder $formBuilder = null)
    {
        $this->formBuilder = $formBuilder;
    }


    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function requiresInputData()
    {
        if ($this->formBuilder) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form $form, Item $item)
    {
        if ($this->formBuilder) {
            $this->buildForm($form, $item);
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
