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

namespace Netzmacht\Contao\Workflow\Action;

use Assert\Assertion;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Workflow\Base;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;

/**
 * Class AbstractAction which uses an form builder to create user input form data.
 *
 * @package Netzmacht\Contao\Workflow\Action
 */
abstract class AbstractAction extends Base implements Action
{
    /**
     * Log changed values as workflow data.
     *
     * @var bool
     */
    private $logChanges = true;

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
            $context->getProperties()->set($property, $value, $this->getName());
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
            foreach ($values as $name => $value) {
                $context->getProperties()->set($name, $value, $this->getName());
            }
        }

        return $this;
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

        Assertion::isInstanceOf($entity, Entity::class, 'Invalid entity given');

        return $entity;
    }
}
