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

namespace Netzmacht\ContaoWorkflowBundle\Form\Builder;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * The transition action builder creates the forms for the actions which requires user data.
 */
final class TransitionActionsFormBuilder implements TransitionFormBuilder
{
    /**
     * The action form builders.
     *
     * @var ActionFormBuilder[]|iterable
     */
    private $builders;

    /**
     * TransitionActionsFormBuilder constructor.
     *
     * @param iterable|ActionFormBuilder[] $builders The action form builders.
     */
    public function __construct(iterable $builders)
    {
        $this->builders = $builders;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Transition $transition): bool
    {
        if (count($this->builders) === 0) {
            return false;
        }

        if (count($transition->getActions()) > 0) {
            return true;
        }

        if (count($transition->getPostActions()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(Transition $transition, Item $item, Context $context, FormBuilder $formBuilder): void
    {
        foreach ($transition->getActions() as $action) {
            foreach ($this->builders as $builder) {
                if ($builder->supports($action)) {
                    $builder->buildForm($action, $transition, $formBuilder);
                }
            }
        }

        foreach ($transition->getPostActions() as $action) {
            foreach ($this->builders as $builder) {
                if ($builder->supports($action)) {
                    $builder->buildForm($action, $transition, $formBuilder);
                }
            }
        }
    }
}
