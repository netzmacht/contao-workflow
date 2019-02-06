<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowExampleBundle\EventListener;

use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\ContaoWorkflowBundle\EventListener\Integration\OperationListener;
use Symfony\Component\Translation\TranslatorInterface;

final class ExampleDcaListener extends AbstractListener
{
    protected static $name = 'tl_example';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(Manager $dcaManager, TranslatorInterface $translator)
    {
        parent::__construct($dcaManager);

        $this->translator = $translator;
    }

    public function onLoad(): void
    {
        $configuration = [
            'label'           => [
                $this->translator->trans('workflow.integration.operation.0'),
                $this->translator->trans('workflow.integration.operation.1'),
            ],
            'href'            => 'key=workflow',
            'icon'            => 'bundles/netzmachtcontaoworkflow/img/workflow.png',
            'button_callback' => [
                OperationListener::class,
                'workflowOperationButton',
            ],
        ];

        $definition = $this->getDefinition();
        $operations = $definition->get(['list', 'operations'], []);
        array_unshift($operations, $configuration);
        $definition->set(['list', 'operations'], $operations);
    }
}
