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

namespace Netzmacht\Workflow\Contao\Backend\Helper;

use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Manager\Manager;

/**
 * Class Operations helper.
 *
 * @package Netzmacht\Workflow\Contao\Backend\Helper
 */
class Operations
{
    const GLOBAL_SCOPE = 'global_operations';
    const MODEL_SCOPE  = 'operations';

    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Construct.
     *
     * @param Manager $manager Workflow manager.
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Add transitions as operations.
     *
     * @param Transition[]   $transitions    Workflow transitions.
     * @param string         $providerName   Provider name.
     * @param string         $scope          Operation scope.
     * @param \Callable|null $callback       Filter callback.
     * @param string|null    $entityProvider Optional different entity provider name.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function addTransitions(
        $transitions,
        $providerName,
        $scope = self::MODEL_SCOPE,
        $callback = null,
        $entityProvider = null
    ) {
        foreach ($transitions as $transition) {
            if (!$transition->getConfigValue('addIcon')) {
                continue;
            }

            $buttonName     = 'transition_' . $transition->getName();
            $entityProvider = $entityProvider ?: $providerName;
            $config         = array(
                'label' => array(
                    $transition->getLabel(),
                    $transition->getConfigValue('description') ?: $transition->getLabel()
                ),
                'icon'  => static::getTransitionIcon($transition),
                'href'  => sprintf(
                    'table=%s&amp;key=workflow&amp;transition=%s',
                    $entityProvider,
                    $transition->getName()
                )
            );

            if ($callback) {
                $config = $callback($transition, $config);
            }

            $GLOBALS['TL_DCA'][$providerName]['list'][$scope][$buttonName] = $config;
        }
    }

    /**
     * Get a transition icon.
     *
     * Try to get the transition icon from the config. Use the default one if none is defined.
     *
     * @param Transition $transition The workflow transition.
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
