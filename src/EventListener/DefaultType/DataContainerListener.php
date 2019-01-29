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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\DefaultType;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use RuntimeException;

final class DataContainerListener
{
    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Configuration of the default workflow types.
     *
     * @var array
     */
    private $defaultConfiguration;

    /**
     * DefaultWorkflowTypeIntegrationListener constructor.
     *
     * @param DcaManager               $dcaManager
     * @param array                    $defaultConfiguration
     */
    public function __construct(
        DcaManager $dcaManager,
        array $defaultConfiguration
    ) {
        $this->dcaManager               = $dcaManager;
        $this->defaultConfiguration     = $defaultConfiguration;
    }

    public function onLoadDataContainer(string $dataContainerName): void
    {
        if (!isset($this->defaultConfiguration[$dataContainerName])) {
            return;
        }

        $definition = $this->dcaManager->getDefinition($dataContainerName);

        $this->addWorkflowFieldToDefinition($definition);
        $this->addWorkflowStateFieldToDefinition($definition);
        $this->addWorkflowFieldToPalettes($definition);
    }

    private function addWorkflowFieldToDefinition(\Netzmacht\Contao\Toolkit\Dca\Definition $definition): void
    {
        if ($definition->has(['fields', 'workflow'])) {
            throw new RuntimeException();
        }

        $definition->set(
            ['fields', 'workflow'],
            [
                'label'            => ['Workflow'],
                'inputType'        => 'select',
                'exclude'          => true,
                'filter'           => true,
                'default'          => $this->defaultConfiguration[$definition->getName()]['default_workflow'] ?? 0,
                'eval'             => [
                    'includeBlankOption' => true,
                    'chosen'             => true,
                    'tl_class'           => 'w50',
                ],
                'options_callback' => [OptionsListener::class, 'workflowOptions'],
                'sql'              => 'varchar(64) NOT NULL default \'\'',
            ]
        );
    }

    private function addWorkflowStateFieldToDefinition(\Netzmacht\Contao\Toolkit\Dca\Definition $definition): void
    {
        if ($definition->has(['fields', 'workflowCurrentStep'])) {
            throw new RuntimeException();
        }

        $definition->set(
            ['fields', 'workflowCurrentStep'],
            [
                'label'            => ['Current Step'],
                'inputType'        => 'select',
                'exclude'          => true,
                'filter'           => true,
                'default'          => '',
                'eval'             => [
                    'includeBlankOption' => true,
                    'chosen'             => true,
                    'tl_class'           => 'w50'
                ],
                'options_callback' => [OptionsListener::class, 'stepOptions'],
                'sql'              => 'varchar(64) NOT NULL default \'\'',
            ]
        );
    }

    private function addWorkflowFieldToPalettes(\Netzmacht\Contao\Toolkit\Dca\Definition $definition): void
    {
        $palettes = ($this->defaultConfiguration[$definition->getName()]['palettes'] ?? []);

        foreach ($palettes as $palette) {
            PaletteManipulator::create()
                ->addLegend('workflow_legend', '')
                ->addField(['workflow', 'workflowCurrentStep'], 'workflow_legend', PaletteManipulator::POSITION_APPEND)
                ->applyToPalette($palette, $definition->getName());
        }
    }
}
