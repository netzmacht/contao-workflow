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

namespace Netzmacht\Contao\Workflow\Flow\Action\Form;

use Netzmacht\ContaoFormBundle\Form\FormGeneratorType;
use Netzmacht\Contao\Workflow\Flow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class FormActionFactory
 *
 * @package Netzmacht\Contao\Workflow\Flow\Action\Form
 */
class FormActionFactory implements ActionTypeFactory
{
    /**
     * @inheritDoc
     */
    public function getCategory(): string
    {
        return 'default';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'form';
    }

    /**
     * @inheritDoc
     */
    public function supports(Workflow $workflow): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function match(Action $action): bool
    {
        return $action instanceof FormAction;
    }

    /**
     * @inheritDoc
     */
    public function create(array $config, Transition $transition): Action
    {
        return new FormAction('action_' . $config['id'], $config['label'], $config);
    }

    /**
     * @inheritDoc
     */
    public function buildForm(Action $action, Transition $transition, FormBuilder $formBuilder): void
    {
        if (!$action instanceof FormAction) {
            throw new \RuntimeException();
        }

        $formBuilder->add(
            $action->getName(),
            FormGeneratorType::class,
            [
                'formId' => $action->getConfigValue('formId'),
                'ignore' => ['submit']
            ]
        );
    }
}
