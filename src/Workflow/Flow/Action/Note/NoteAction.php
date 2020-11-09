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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\Note;

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AbstractAction;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Patchwork\Utf8;

/**
 * Class NoteAction
 */
final class NoteAction extends AbstractAction
{
    /**
     * Flag to determine if note is required.
     *
     * @var bool
     */
    private $required;

    /**
     * Min number of characters which are required if note is given.
     *
     * @var int
     */
    private $minLength;

    /**
     * Construct.
     *
     * @param string $name      Name of the element.
     * @param string $label     Label of the element.
     * @param bool   $required  Flag to determine if note is required.
     * @param int    $minLength Min number of characters which are required if note is given.
     * @param array  $config    Configuration values.
     */
    public function __construct(string $name, string $label, bool $required, int $minLength, array $config = [])
    {
        parent::__construct($name, $label, $config);

        $this->required  = $required;
        $this->minLength = $minLength;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [$this->payloadName()];
    }

    /**
     * Check if note is required.
     *
     * @return bool
     */
    public function required(): bool
    {
        return $this->required;
    }

    /**
     * Min number of characters which are required if note is given.
     *
     * @return int
     */
    public function minLength(): int
    {
        if ($this->required) {
            return $this->minLength;
        }

        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(Item $item, Context $context): bool
    {
        $name = $this->payloadName();

        if (!$this->required) {
            return true;
        }

        $payload = $context->getPayload();
        if (!$payload->has($name)) {
            $context->addError('action.note.validate.required', [$this->getLabel()]);

            return false;
        }

        $length = Utf8::strlen($payload->get($name));
        if ($length >= $this->minLength) {
            return true;
        }

        $context->addError(
            'action.note.validate.minlength',
            [$this->getLabel(), $this->minLength, $length]
        );

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function transit(Transition $transition, Item $item, Context $context): void
    {
        $name = $this->payloadName();

        $context->getProperties()->set($name, $context->getPayload()->get($name));
    }

    /**
     * Get the payload name.
     *
     * @return string
     */
    public function payloadName(): string
    {
        return $this->getConfigValue('playload_name') ?: ($this->getName() . '_note');
    }
}
