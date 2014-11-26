<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Backend\Helper;

use Netzmacht\Workflow\Flow\Transition;

class Operations
{
    const GLOBAL_SCOPE = 'global_operations';
    const MODEL_SCOPE  = 'operations';


    /**
     * @param Transition[] $transitions
     * @param string       $providerName
     * @param string       $scope
     * @param null         $entityProvider
     */
    public static function addTransitions(
        array $transitions,
        $providerName,
        $scope = Operations::MODEL_SCOPE,
        $entityProvider = null
    )
    {
        foreach ($transitions as $transition) {
            if (!$transition->getConfigValue('addIcon')) {
                continue;
            }

            $buttonName     = 'transition_' . $transition->getName();
            $entityProvider = $entityProvider ?: $providerName;

            $GLOBALS['TL_DCA'][$providerName]['list'][$scope][$buttonName] = array(
                'label' => array($transition->getLabel(), $transition->getConfigValue('description')),
                'icon'  => static::getTransitionIcon($transition),
                'href'  => sprintf('table=%s&amp;key=workflow&amp;transition=%s', $entityProvider, $transition->getName())
            );
        }
    }

    /**
     * @param $transition
     *
     * @return string
     */
    public static function getTransitionIcon(Transition $transition)
    {
        $icon = $transition->getConfigValue('icon');

        if ($icon) {
            $model = \FilesModel::findByUuid($icon);

            if ($model) {
                return $model->path;
            }
        }

        return 'system/modules/workflow/assets/img/transition.png';
    }

}
