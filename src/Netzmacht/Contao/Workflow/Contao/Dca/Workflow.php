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

class Workflow
{
    /**
     * Get names of workflow types
     *
     * @return array
     */
    public function getTypes()
    {
        return array_keys($GLOBALS['WORKFLOW_TYPES']);
    }

    /**
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
     * @return array
     */
    public function getEndSteps()
    {
        return $this->getSteps();
    }

    /**
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
     * @param $value
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function validateProcess($value)
    {
        $value    = deserialize($value, true);
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
     * @return array
     */
    private function getSteps()
    {
        $steps = array();
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
     * @param $process
     *
     * @throws \Exception
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
