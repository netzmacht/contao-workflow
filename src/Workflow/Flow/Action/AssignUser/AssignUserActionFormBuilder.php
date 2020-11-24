<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\AssignUser;

use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;
use Netzmacht\ContaoWorkflowBundle\Form\Builder\ActionFormBuilder;
use Netzmacht\ContaoWorkflowBundle\Form\Choice\UserChoices;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Transition;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class AssignUserActionFormBuilder creates the form for the assign user action.
 */
final class AssignUserActionFormBuilder implements ActionFormBuilder
{
    /**
     * The user choices.
     *
     * @var UserChoices
     */
    private $userChoices;

    /**
     * AssignUserActionFormBuilder constructor.
     *
     * @param UserChoices $userChoices The user choices.
     */
    public function __construct(UserChoices $userChoices)
    {
        $this->userChoices = $userChoices;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Action $action): bool
    {
        return $action instanceof AssignUserAction && !$action->isCurrentUserAssigned();
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
        assert($action instanceof AssignUserAction);

        $permission = $action->getConfigValue('assign_user_permission');
        if ($permission) {
            $choices = $this->userChoices->fetchByPermission(Permission::fromString($permission));
        } else {
            $choices = $this->userChoices->findAll();
        }

        $formBuilder->add(
            'action_' . $action->getConfigValue('id'),
            FieldsetType::class,
            [
                'legend' => $action->getLabel(),
                'fields' => [
                    [
                        'name' => $action->getName() . '_user',
                        'type' => ChoiceType::class,
                        'attr' => [
                            'label'   => $action->getConfigValue('description'),
                            'choices' => $choices,
                        ],
                    ],
                ],
            ]
        );
    }
}
