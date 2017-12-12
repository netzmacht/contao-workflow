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

use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionFactory;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

/**
 * Class TransitionFormType
 */
class TransitionFormType extends AbstractType
{
    /**
     * The action factory.
     *
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * The router.
     *
     * @var Router
     */
    private $router;

    /**
     * TransitionFormType constructor.
     *
     * @param ActionFactory $actionFactory The action factory.
     * @param Router        $router        The router.
     */
    public function __construct(ActionFactory $actionFactory, Router $router)
    {
        $this->actionFactory = $actionFactory;
        $this->router        = $router;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Transition $transition */
        $transition = $options['transition'];

        foreach ($transition->getActions() as $action) {
            $this->actionFactory->buildForm($action, $transition, $builder);
        }

        foreach ($transition->getPostActions() as $action) {
            $this->actionFactory->buildForm($action, $transition, $builder);
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
