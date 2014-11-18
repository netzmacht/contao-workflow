<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Dca\Table;

use Netzmacht\Workflow\Contao\Model\StepModel;
use Netzmacht\Workflow\Contao\Model\TransitionModel;

/**
 * Class Workflow stores callback being used by the tl_workflow table.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class Workflow
{
    /**
     * Get names of workflow types.
     *
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getTypes()
    {
        return array_keys($GLOBALS['WORKFLOW_TYPES']);
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
     * @param mixed $value The raw permissions value
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
     * @param bool $filterFinal If true only steps which are not final are loaded
     *
     * @return array
     */
    private function getSteps($filterFinal = false)
    {
        $steps      = array();

        if ($filterFinal) {
            $collection = StepModel::findBy('final', '', array('order' => 'name'));
        }
        else {
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
     * @param $row
     * @param $names
     */
    protected function guardValidPermissionName($row, $names)
    {
        $reserved  = array('contao-admin', 'contao-guest');

        if (in_array($row['name'], $names)) {
            throw new \InvalidArgumentException(sprintf('Permission name "%s" is not unique.', $row['name']));
        } elseif (in_array($row['name'], $reserved)) {
            throw new \InvalidArgumentException(sprintf('Permission name "%s" is reserved.', $row['name']));
        }
    }
}
