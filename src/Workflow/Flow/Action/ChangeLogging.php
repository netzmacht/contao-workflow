<?php

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
     */
    public function isLoggingEnabled(): bool
    {
        return $this->logChanges;
    }

    /**
     * Enable logging.
     */
    public function enableLogging(): void
    {
        $this->logChanges = true;
    }

    /**
     * Disable logging.
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
     */
    protected function propertyChanged(string $property, $value, Context $context): void
    {
        if (! ($this instanceof Base) || ! $this->isLoggingEnabled()) {
            return;
        }

        $context->getProperties()->set($this->getName() . '_' . $property, $value);
    }

    /**
     * Log multiple changes.
     *
     * @param array<string,mixed> $values  Changes properties as associated array['name' => 'val'].
     * @param Context             $context Transition context.
     */
    protected function propertiesChanged(array $values, Context $context): void
    {
        if (! ($this instanceof Base) || ! $this->isLoggingEnabled()) {
            return;
        }

        $properties = $context->getProperties();

        foreach ($values as $name => $value) {
            $properties->set($this->getName() . '_' . $name, $value);
        }
    }
}
