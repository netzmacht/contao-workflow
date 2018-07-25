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
use Netzmacht\ContaoWorkflowBundle\Workflow\Exception\UnsupportedActionType;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class ActionFactory.
 */
final class ActionFactory
{
    /**
     * Action factories.
     *
     * @var array|ActionTypeFactory[]
     */
    private $factories;

    /**
     * ActionFactory constructor.
     *
     * @param array|ActionTypeFactory[] $factories Action factories.
     */
    public function __construct(array $factories)
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
        return array_map(
            function (ActionTypeFactory $factory) {
                return $factory->getName();
            },
            $this->factories
        );
    }

    /**
     * Get the supported type names.
     *
     * @param Workflow $workflow The workflow.
     *
     * @return array|ActionTypeFactory[]
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
     * @return array|ActionTypeFactory[][]
     */
    public function getSupportedTypeNamesCategorized(Workflow $workflow): array
    {
        $names = [];

        foreach ($this->getSupportedTypes($workflow) as $factory) {
            $names[$factory->getCategory()][] = $factory->getName();
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
        return array_filter(
            $this->factories,
            function (ActionTypeFactory $factory) use ($workflow) {
                return $factory->supports($workflow);
            }
        );
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
     * @throws UnsupportedActionType When no action could be created.
     */
    public function create(string $type, array $config, Transition $transition): Action
    {
        foreach ($this->factories as $factory) {
            if ($factory->getName() === $type) {
                return $factory->create($config, $transition);
            }
        }

        throw UnsupportedActionType::withType($type);
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

    /**
     * Build the form for an action type.
     *
     * @param Action      $action      The action.
     * @param Transition  $transition  Workflow transition.
     * @param FormBuilder $formBuilder The form builder.
     *
     * @return void
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
        foreach ($this->factories as $factory) {
            if ($factory->match($action)) {
                $factory->buildForm($action, $transition, $formBuilder);
            }
        }
    }
}
