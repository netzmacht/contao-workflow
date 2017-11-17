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

namespace Netzmacht\Contao\Workflow\Action\Property;

use Netzmacht\Contao\Workflow\Action\ActionTypeFactory;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

/**
 * Class PropertyActionFactory
 *
 * @package Netzmacht\Contao\Workflow\Action\Property
 */
class PropertyActionFactory implements ActionTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'default_property';
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config, Workflow $workflow, Transition $transition): Action
    {
        $action = new PropertyAction($config['name'] ?: $config['id'],
            $config['label'],
            $config
        );

        if (isset($config['value'])) {
            $action->setValue($config['value']);
        }

        if (isset($config['logChanges']) === 'active'
            || (isset($config['logChanges']) === 'inherit' && $transition->getConfigValue('logChanges'))
        ) {
            $action->setLogChanges(true);
        }

        return $action;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilder $formBuilder, array $config): void
    {
        if (!empty($config['value'])) {
            return;
        }

        $formBuilder->add(
            'action_' . $config['name'],
            TextType::class,
            [
                'required'  => true,
                'label'     => $config['label'],
                'help'      => $config['description']
            ]
        );
    }
}
