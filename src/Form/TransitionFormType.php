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
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

/**
 * Class TransitionFormType
 */
final class TransitionFormType extends AbstractType
{
    /**
     * The router.
     *
     * @var Router
     */
    private $router;

    /**
     * The transition form builders.
     *
     * @var iterable|TransitionFormBuilder[]
     */
    private $formBuilders;

    /**
     * TransitionFormType constructor.
     *
     * @param Router   $router       The router.
     * @param iterable $formBuilders The transition form builders.
     */
    public function __construct(Router $router, iterable $formBuilders)
    {
        $this->router       = $router;
        $this->formBuilders = $formBuilders;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['transition'])
            ->setAllowedTypes('transition', Transition::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilder $formBuilder, array $options)
    {
        /** @var Transition $transition */
        $transition = $options['transition'];

        foreach ($this->formBuilders as $builder) {
            if ($builder->supports($transition)) {
                $builder->buildForm($transition, $formBuilder);
            }
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
