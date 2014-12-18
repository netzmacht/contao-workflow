<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Workflow\Contao\Backend\Dca;

use Netzmacht\Workflow\Contao\Backend\Event\GetProviderNamesEvent;
use Netzmacht\Workflow\Contao\Model\StepModel;
use Netzmacht\Workflow\Contao\Model\TransitionModel;
use Netzmacht\Workflow\Contao\ServiceContainerTrait;

/**
 * Class Workflow stores callback being used by the tl_workflow table.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class Workflow
{
    use ServiceContainerTrait;

    /**
     * Generate a row view.
     *
     * @param array $row Current data row.
     *
     * @return string
     */
    public function generateRow(array $row)
    {
        return sprintf(
            '<strong>%s</strong> <span class="tl_gray">[%s: %s]</span><br>%s',
            $row['label'],
            $row['name'],
            $row['description']
        );
    }

    /**
     * Get names of workflow types.
     *
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getTypes()
    {
        return $GLOBALS['WORKFLOW_TYPES'];
    }

    /**
     * Get all provider names.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getProviderNames($dataContainer)
    {
        if (!$dataContainer->activeRecord || !$dataContainer->activeRecord->type) {
            return array();
        }

        $event = new GetProviderNamesEvent($dataContainer->activeRecord->type);
        $this->getService('event-dispatcher')->dispatch($event::NAME, $event);

        return $event->getProviderNames();
    }

    /**
     * Get all start steps.
     *
     * @return array
     */
    public function getStartSteps()
    {
        return array(
            'process' => array('start'),
            'steps' => $this->getSteps(true)
        );
    }

    /**
     * Get all end steps.
     *
     * @return array
     */
    public function getEndSteps()
    {
        return $this->getSteps();
    }

    /**
     * Get all transitions.
     *
     * @param \DataContainer $dataContainer The data container.
     *
     * @return array
     */
    public function getTransitions($dataContainer)
    {
        $options = array();

        if ($dataContainer->activeRecord) {
            $collection = TransitionModel::findBy('pid', $dataContainer->activeRecord->id);

            if ($collection) {
                while ($collection->next()) {
                    $stepTo = $collection->getRelated('stepTo');

                    $options[$collection->id] = sprintf(
                        '%s [%s] --> %s [%s]',
                        $collection->label,
                        $collection->name,
                        $stepTo->label,
                        $stepTo->name
                    );
                }
            }
        }

        return $options;
    }

    /**
     * Validate given process data.
     *
     * @param mixed $value Raw process vlaue.
     *
     * @return array|mixed
     *
     * @throws \Exception If Invalid data given.
     */
    public function validateProcess($value)
    {
        $value = deserialize($value, true);
        $value = array_filter(
            $value,
            function ($item) {
                return $item['step'] && $item['transition'];
            }
        );

        $this->guardStartStepDefined($value);

        return $value;
    }

    /**
     * Filer and validate permission values.
     *
     * @param mixed $value The raw permissions value.
     *
     * @return array
     */
    public function validatePermissions($value)
    {
        $value     = deserialize($value, true);
        $names     = array();
        $validated = array();

        foreach ($value as $row) {
            if (!$row['name']) {
                if (!$row['label']) {
                    continue;
                }

                $row['name'] = standardize($row['label']);
            }

            $this->guardValidPermissionName($row, $names);

            $names[]     = $row['name'];
            $validated[] = $row;
        }

        return $validated;
    }

    /**
     * Get steps form database.
     *
     * @param bool $filterFinal If true only steps which are not final are loaded.
     *
     * @return array
     */
    private function getSteps($filterFinal = false)
    {
        $steps = array();

        if ($filterFinal) {
            $collection = StepModel::findBy('final', '', array('order' => 'name'));
        } else {
            $collection = StepModel::findAll(array('order' => 'name'));
        }

        if ($collection) {
            while ($collection->next()) {
                $steps[$collection->id] = $collection->label;

                if ($collection->final) {
                    $steps[$collection->id] .= ' [final]';
                }
            }
        }

        return $steps;
    }

    /**
     * Guard that start step is defined.
     *
     * @param array $process Process information.
     *
     * @throws \Exception If no start step is given.
     *
     * @return void
     */
    private function guardStartStepDefined($process)
    {
        if (!$process) {
            return;
        }

        $count = 0;

        foreach ($process as $definition) {
            if ($definition['step'] == 'start') {
                $count++;
            }
        }

        if (!$count) {
            throw new \Exception('Start transition is required.');
        } elseif ($count > 1) {
            throw new \Exception('There must be exactly one start transition.');
        }

    }

    /**
     * Guard that a valid permission name is given.
     *
     * @param array $row   Current permission definition row.
     * @param array $names All permission names so far.
     *
     * @throws \InvalidArgumentException If a invalid permission name is given.
     *
     * @return void
     */
    protected function guardValidPermissionName($row, $names)
    {
        $reserved = array('contao-admin', 'contao-guest');

        if (in_array($row['name'], $names)) {
            throw new \InvalidArgumentException(sprintf('Permission name "%s" is not unique.', $row['name']));
        } elseif (in_array($row['name'], $reserved)) {
            throw new \InvalidArgumentException(sprintf('Permission name "%s" is reserved.', $row['name']));
        }
    }
}
