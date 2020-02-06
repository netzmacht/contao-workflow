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

namespace Netzmacht\ContaoWorkflowBundle\Form;

use Netzmacht\ContaoWorkflowBundle\Form\Builder\TransitionFormBuilder;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TransitionFormType
 */
final class TransitionFormType extends AbstractType
{
    /**
     * The transition form builders.
     *
     * @var iterable|TransitionFormBuilder[]
     */
    private $formBuilders;

    /**
     * TransitionFormType constructor.
     *
     * @param iterable $formBuilders The transition form builders.
     */
    public function __construct(iterable $formBuilders)
    {
        $this->formBuilders = $formBuilders;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['handler'])
            ->setAllowedTypes('handler', TransitionHandler::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilder $formBuilder, array $options): void
    {
        /** @var TransitionHandler $transitionHandler */
        $transitionHandler = $options['handler'];
        $transition        = $transitionHandler->getTransition();
        $item              = $transitionHandler->getItem();
        $context           = $transitionHandler->getContext();

        foreach ($this->formBuilders as $builder) {
            if (! $builder->supports($transition)) {
                continue;
            }

            $builder->buildForm($transition, $item, $context, $formBuilder);
        }

        $formBuilder->add(
            'submit',
            SubmitType::class,
            [
                'label' => $transition->getLabel(),
            ]
        );
    }
}
