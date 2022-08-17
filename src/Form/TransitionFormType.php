<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form;

use Netzmacht\ContaoWorkflowBundle\Form\Builder\TransitionFormBuilder;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function assert;

final class TransitionFormType extends AbstractType
{
    /**
     * The transition form builder.
     *
     * @var TransitionFormBuilder
     */
    private $formBuilder;

    /**
     * @param TransitionFormBuilder $formBuilder The transition form builder.
     */
    public function __construct(TransitionFormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['item' => null])
            ->setRequired(['handler'])
            ->setAllowedTypes('handler', TransitionHandler::class)
            ->setAllowedTypes('item', Item::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilder $builder, array $options): void
    {
        $transitionHandler = $options['handler'];
        assert($transitionHandler instanceof TransitionHandler);
        $transition = $transitionHandler->getTransition();
        $item       = $transitionHandler->getItem();
        $context    = $transitionHandler->getContext();

        if ($this->formBuilder->supports($transition)) {
            $this->formBuilder->buildForm($transition, $item, $context, $builder);
        }

        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => $transition->getLabel(),
            ]
        );
    }
}
