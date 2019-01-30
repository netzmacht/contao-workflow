<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Netzmacht\Workflow\Flow\Base;
use Netzmacht\Workflow\Flow\Context;

/**
 * Trait ChangeLogging provides logging for property changes.
 */
trait ChangeLogging
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
     * @return bool
     */
    public function isLoggingEnabled(): bool
    {
        return $this->logChanges;
    }

    /**
     * Enable logging.
     *
     * @return void
     */
    public function enableLogging(): void
    {
        $this->logChanges = true;
    }

    /**
     * Disable logging.
     *
     * @return void
     */
    public function disableLogging(): void
    {
        $this->logChanges = false;
    }

    /**
     * Log changes if enabled.
     *
     * @param string  $property Property name.
     * @param mixed   $value    Property value.
     * @param Context $context  Transition context.
     *
     * @return void
     */
    protected function propertyChanged(string $property, $value, Context $context): void
    {
        if ($this instanceof Base && $this->isLoggingEnabled()) {
            $context->getProperties()->set($this->getName() . '_' . $property, $value);
        }
    }

    /**
     * Log multiple changes.
     *
     * @param array   $values  Changes properties as associated array['name' => 'val'].
     * @param Context $context Transition context.
     *
     * @return void
     */
    protected function propertiesChanged(array $values, Context $context): void
    {
        if ($this instanceof Base && $this->isLoggingEnabled()) {
            $properties = $context->getProperties();

            foreach ($values as $name => $value) {
                $properties->set($this->getName() . '_' . $name, $value);
            }
        }
    }
}
