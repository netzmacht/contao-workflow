<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Contao\Dca;

use Netzmacht\Contao\Workflow\Contao\Model\StepModel;
use Netzmacht\Contao\Workflow\Contao\Model\TransitionModel;

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
            'steps' => $this->getSteps()
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
                    $options[$collection->id] = sprintf('%s [%s]', $collection->label, $collection->name);
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
                return $item['step'] && $item['stepTo'] && $item['transition'];
            }
        );

        $this->guardStartStepDefined($value);

        return $value;
    }

    /**
     * Get steps form database.
     *
     * @return array
     */
    private function getSteps()
    {
        $steps      = array();
        $collection = StepModel::findAll(array('order' => 'name'));

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
}
