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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Integration;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\ContaoWorkflowBundle\Exception\DataContainer\FieldAlreadyExists;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use function array_unshift;

/**
 * Data container listener handles the integration in the configured data containers for the default workflow type
 */
final class DataContainerListener
{
    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Configuration of the default workflow types.
     *
     * @var array
     */
    private $defaultConfiguration;

    /**
     * Data container provider names.
     *
     * @var array
     */
    private $dcaProviders;

    /**
     * DefaultWorkflowTypeIntegrationListener constructor.
     *
     * @param DcaManager $dcaManager           Data container manager.
     * @param Translator $translator           Translator.
     * @param array      $dcaProviders         Data container provider names.
     * @param array      $defaultConfiguration Configuration of the default workflow types.
     */
    public function __construct(
        DcaManager $dcaManager,
        Translator $translator,
        array $dcaProviders,
        array $defaultConfiguration
    ) {
        $this->dcaManager           = $dcaManager;
        $this->defaultConfiguration = $defaultConfiguration;
        $this->translator           = $translator;
        $this->dcaProviders         = $dcaProviders;
    }

    /**
     * Data container integration is triggered by the onLoadDataContainer hook.
     *
     * @param string $dataContainerName The data container name.
     *
     * @return void
     */
    public function onLoadDataContainer(string $dataContainerName): void
    {
        if (!isset($this->dcaProviders[$dataContainerName])) {
            return;
        }

        $definition = $this->dcaManager->getDefinition($dataContainerName);

        $this->addTranslations($definition->getName());

        if ($this->dcaProviders[$dataContainerName]['workflow']) {
            $this->addWorkflowFieldToDefinition($definition);
        }

        if ($this->dcaProviders[$dataContainerName]['step']) {
            $this->addWorkflowStepFieldToDefinition($definition);
        }

        if ($this->dcaProviders[$dataContainerName]['step_permission']) {
            $this->addWorkflowStepPermissionFieldToDefinition($definition);
        }

        if (!isset($this->defaultConfiguration[$dataContainerName])) {
            return;
        }

        $this->adjustPalettes($definition);
        $this->addWorkflowOperation($definition);
        $this->addButtonsCallback($definition);
    }

    /**
     * Add the workflow field to the data container definition.
     *
     * @param Definition $definition The data container definition.
     *
     * @return void
     *
     * @throws FieldAlreadyExists When workflow is already configured in data container.
     */
    private function addWorkflowFieldToDefinition(Definition $definition): void
    {
        if ($definition->has(['fields', 'workflow'])) {
            throw FieldAlreadyExists::namedInDataContainer('workflow', $definition->getName());
        }

        $definition->set(
            ['fields', 'workflow'],
            [
                'label'            => [
                    $this->translator->trans('integration.workflow.0', [], 'netzmacht_workflow'),
                    $this->translator->trans('integration.workflow.1', [], 'netzmacht_workflow'),
                ],
                'inputType'        => 'select',
                'exclude'          => true,
                'filter'           => true,
                'default'          => ($this->defaultConfiguration[$definition->getName()]['default_workflow'] ?? ''),
                'eval'             => [
                    'includeBlankOption' => true,
                    'chosen'             => true,
                    'submitOnChange'     => true,
                    'tl_class'           => 'w50',
                ],
                'options_callback' => [OptionsListener::class, 'workflowOptions'],
                'sql'              => 'varchar(64) NOT NULL default \'\'',
            ]
        );
    }

    /**
     * Add the workflow step field to the data container definition.
     *
     * @param Definition $definition The data container definition.
     *
     * @return void
     *
     * @throws FieldAlreadyExists When workflowStep is already configured in data container.
     */
    private function addWorkflowStepFieldToDefinition(Definition $definition): void
    {
        if ($definition->has(['fields', 'workflowStep'])) {
            throw FieldAlreadyExists::namedInDataContainer('workflowStep', $definition->getName());
        }

        $definition->set(
            ['fields', 'workflowStep'],
            [
                'label'            => [
                    $this->translator->trans('integration.current_step.0', [], 'netzmacht_workflow'),
                    $this->translator->trans('integration.current_step.1', [], 'netzmacht_workflow'),
                ],
                'inputType'        => 'select',
                'exclude'          => true,
                'filter'           => true,
                'default'          => '',
                'eval'             => [
                    'includeBlankOption' => true,
                    'tl_class'           => 'w50',
                    'disabled'           => true,
                ],
                'options_callback' => [OptionsListener::class, 'stepOptions'],
                'sql'              => 'varchar(64) NOT NULL default \'\'',
            ]
        );
    }

    /**
     * Add the workflow step permission field to the data container definition.
     *
     * @param Definition $definition The data container definition.
     *
     * @return void
     *
     * @throws FieldAlreadyExists When workflowStepPermission is already configured in data container.
     */
    private function addWorkflowStepPermissionFieldToDefinition(Definition $definition): void
    {
        if ($definition->has(['fields', 'workflowStepPermission'])) {
            throw FieldAlreadyExists::namedInDataContainer('workflowStepPermission', $definition->getName());
        }

        $definition->set(
            ['fields', 'workflowStepPermission'],
            [
                'label'            => [
                    $this->translator->trans('integration.current_step_permission.0', [], 'netzmacht_workflow'),
                    $this->translator->trans('integration.current_step_permission.1', [], 'netzmacht_workflow'),
                ],
                'inputType'        => 'select',
                'exclude'          => true,
                'filter'           => true,
                'default'          => '',
                'eval'             => [
                    'includeBlankOption' => true,
                    'tl_class'           => 'w50',
                    'disabled'           => true,
                ],
                'options_callback' => [OptionsListener::class, 'stepPermissionOptions'],
                'sql'              => 'varchar(64) NULL default NULL',
            ]
        );
    }

    /**
     * Adjust the palettes.
     *
     * @param Definition $definition The data container definition.
     *
     * @return void
     */
    private function adjustPalettes(Definition $definition): void
    {
        $palettes = ($this->defaultConfiguration[$definition->getName()]['palettes'] ?? []);

        foreach ($palettes as $palette) {
            PaletteManipulator::create()
                ->addLegend('workflow_legend', '', PaletteManipulator::POSITION_AFTER, true)
                ->addField(
                    ['workflow'],
                    'workflow_legend',
                    PaletteManipulator::POSITION_APPEND
                )
                ->applyToPalette($palette, $definition->getName());
        }

        $definition->set(['metasubselectpalettes', 'workflow'], ['!' => ['workflowStep']]);
        $definition->set(['metasubselectpalettes', 'workflowStep'], ['!' => ['workflowStepPermission']]);
    }

    /**
     * Add the workflow operation button.
     *
     * @param Definition $definition The data container definition.
     *
     * @return void
     */
    private function addWorkflowOperation(Definition $definition): void
    {
        $position = ($this->defaultConfiguration[$definition->getName()]['operation'] ?? false);
        if ($position === false) {
            return;
        }

        $configuration = [
            'label'           => [
                $this->translator->trans('integration.operation.0', [], 'netzmacht_workflow'),
                $this->translator->trans('integration.operation.1', [], 'netzmacht_workflow'),
            ],
            'href'            => '',
            'icon'            => 'bundles/netzmachtcontaoworkflow/img/workflow.png',
            'button_callback' => [
                OperationListener::class,
                'workflowOperationButton',
            ],
        ];

        if ($position === 'first') {
            $operations = $definition->get(['list', 'operations'], []);
            array_unshift($operations, $configuration);
            $definition->set(['list', 'operations'], $operations);

            return;
        }

        $definition->set(['list', 'operations', 'workflow'], $configuration);
    }

    /**
     * Add translations which could not be added to the element directly.
     *
     * @param string $providerName The provider name.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function addTranslations(string $providerName): void
    {
        $GLOBALS['TL_LANG'][$providerName]['workflow_legend'] = $this->translator->trans(
            'integration.legend',
            [],
            'netzmacht_workflow'
        );
    }

    /**
     * Add the buttons callback to add the transitions to the edit buttons.
     *
     * @param Definition $definition Data container definition.
     *
     * @return void
     */
    private function addButtonsCallback(Definition $definition): void
    {
        if (($this->defaultConfiguration[$definition->getName()]['submit_buttons'] ?? false) === false) {
            return;
        }

        $buttonCallbacks   = $definition->get(['edit', 'buttons_callback'], []);
        $buttonCallbacks[] = [SubmitButtonsListener::class, 'addTransitionButtons'];
        $definition->set(['edit', 'buttons_callback'], $buttonCallbacks);

        $submitCallbacks   = $definition->get(['config', 'onsubmit_callback'], []);
        $submitCallbacks[] = [SubmitButtonsListener::class, 'redirectToTransition'];
        $definition->set(['config', 'onsubmit_callback'], $submitCallbacks);
    }
}
