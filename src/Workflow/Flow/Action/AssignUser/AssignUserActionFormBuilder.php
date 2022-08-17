<?php

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

use function assert;

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
     * @param UserChoices $userChoices The user choices.
     */
    public function __construct(UserChoices $userChoices)
    {
        $this->userChoices = $userChoices;
    }

    public function supports(Action $action): bool
    {
        return $action instanceof AssignUserAction && ! $action->isCurrentUserAssigned();
    }

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
