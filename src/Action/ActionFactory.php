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

namespace Netzmacht\Contao\Workflow\Action;

use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Workflow\Exception\UnsupportedActionType;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class ActionFactory.
 */
class ActionFactory
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
     * Create an action.
     *
     * @param string     $type       The action type.
     * @param array      $config     The action config.
     * @param Workflow   $workflow   Workflow in which the action occurs.
     * @param Transition $transition Transition to which the action belongs.
     *
     * @return Action
     */
    public function create(string $type, array $config, Workflow $workflow, Transition $transition): Action
    {
        foreach ($this->factories as $factory) {
            if ($factory->getName() === $type) {
                return $factory->create($config, $workflow, $transition);
            }
        }

        throw UnsupportedActionType::withType($type);
    }

    /**
     * Build the form for an action type.
     *
     * @param FormBuilder $formBuilder The form builder.
     * @param string      $type        The action type.
     * @param array       $config      The action config.
     */
    public function buildForm(FormBuilder $formBuilder, string $type, array $config): void
    {
        foreach ($this->factories as $factory) {
            if ($factory->getName() === $type) {
                $factory->buildForm($formBuilder, $config);

                break;
            }
        }
    }
}
