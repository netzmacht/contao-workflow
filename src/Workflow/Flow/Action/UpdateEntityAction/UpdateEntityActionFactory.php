<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\UpdateEntityAction;

use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\ContaoWorkflowBundle\PropertyAccess\PropertyAccessManager;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Factory creates update entity action
 */
final class UpdateEntityActionFactory implements ActionTypeFactory
{
    /**
     * Data container definition manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Property access manager.
     *
     * @var PropertyAccessManager
     */
    private $propertyAccessManager;

    /**
     * UpdateEntityActionFactory constructor.
     *
     * @param DcaManager $dcaManager Data container definition manager.
     */
    public function __construct(DcaManager $dcaManager, PropertyAccessManager $propertyAccessManager)
    {
        $this->dcaManager            = $dcaManager;
        $this->propertyAccessManager = $propertyAccessManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getCategory(): string
    {
        return 'default';
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'update_entity';
    }

    /**
     * {@inheritDoc}
     */
    public function isPostAction(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Workflow $workflow): bool
    {
        try {
            $definition = $this->dcaManager->getDefinition($workflow->getProviderName());

            return (bool) $definition->get(['config', 'useRawRequestData']);
        } catch (AssertionFailed $exception) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Transition $transition): Action
    {
        return new UpdateEntityAction(
            $this->propertyAccessManager,
            'action_' . $config['id'],
            $config['label'],
            $config
        );
    }
}
