<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action;

use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedAction;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

use function array_map;
use function sort;

final class ActionFactory
{
    /**
     * Action factories.
     *
     * @var iterable|ActionTypeFactory[]
     */
    private $factories;

    /**
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
     * @return list<string>
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
     * @return list<string>
     */
    public function getSupportedTypeNames(Workflow $workflow): array
    {
        return array_map(
            static function (ActionTypeFactory $factory) {
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
     * @return array<string,list<string>>
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
            if (! $factory->supports($workflow)) {
                continue;
            }

            $supported[] = $factory;
        }

        return $supported;
    }

    /**
     * Create an action.
     *
     * @param string              $type       The action type.
     * @param array<string,mixed> $config     The action config.
     * @param Transition          $transition Transition to which the action belongs.
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
