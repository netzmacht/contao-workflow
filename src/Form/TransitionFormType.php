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
     * The transition form builder.
     *
     * @var TransitionFormBuilder
     */
    private $formBuilder;

    /**
     * TransitionFormType constructor.
     *
     * @param TransitionFormBuilder $formBuilder The transition form builder.
     */
    public function __construct(TransitionFormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
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

        if ($this->formBuilder->supports($transition)) {
            $this->formBuilder->buildForm($transition, $item, $context, $formBuilder);
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
