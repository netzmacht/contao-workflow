<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedAction;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use function array_map;
use function sort;

/**
 * Class ActionFactory.
 */
final class ActionFactory
{
    /**
     * Action factories.
     *
     * @var iterable|ActionTypeFactory[]
     */
    private $factories;

    /**
     * ActionFactory constructor.
     *
     * @param iterable|ActionTypeFactory[] $factories Action factories.
     */
    public function __construct(iterable $factories)
    {
        Assertion::allImplementsInterface($factories, ActionTypeFactory::class);

        $this->factories = $factories;
    }

    /**
     * Get the action names.
     *
     * @return array
     */
    public function getTypeNames(): array
    {
        $names = [];

        foreach ($this->factories as $factory) {
            $names[] = $factory->getName();
        }

        return $names;
    }

    /**
     * Get the supported type names.
     *
     * @param Workflow $workflow The workflow.
     *
     * @return array
     */
    public function getSupportedTypeNames(Workflow $workflow): array
    {
        return array_map(
            function (ActionTypeFactory $factory) {
                return $factory->getName();
            },
            $this->getSupportedTypes($workflow)
        );
    }

    /**
     * Get the supported type names categorized.
     *
     * @param Workflow $workflow The workflow.
     *
     * @return array
     */
    public function getSupportedTypeNamesCategorized(Workflow $workflow): array
    {
        $names = [];

        foreach ($this->getSupportedTypes($workflow) as $factory) {
            $names[$factory->getCategory()][] = $factory->getName();
        }

        foreach ($names as &$actions) {
            sort($actions);
        }

        return $names;
    }

    /**
     * Get all supported workflow types.
     *
     * @param Workflow $workflow The workflow.
     *
     * @return array|ActionTypeFactory[]
     */
    public function getSupportedTypes(Workflow $workflow): array
    {
        $supported = [];

        foreach ($this->factories as $factory) {
            if ($factory->supports($workflow)) {
                $supported[] = $factory;
            }
        }

        return $supported;
    }

    /**
     * Create an action.
     *
     * @param string     $type       The action type.
     * @param array      $config     The action config.
     * @param Transition $transition Transition to which the action belongs.
     *
     * @return Action
     *
     * @throws UnsupportedAction When no action could be created.
     */
    public function create(string $type, array $config, Transition $transition): Action
    {
        foreach ($this->factories as $factory) {
            if ($factory->getName() === $type) {
                return $factory->create($config, $transition);
            }
        }

        throw UnsupportedAction::withType($type);
    }

    /**
     * Check if an action type is a post action.
     *
     * Returns also false if action type is unknown.
     *
     * @param string $type The action type.
     *
     * @return bool
     */
    public function isPostAction(string $type): bool
    {
        foreach ($this->factories as $factory) {
            if ($factory->getName() === $type) {
                return $factory->isPostAction();
            }
        }

        return false;
    }
}
