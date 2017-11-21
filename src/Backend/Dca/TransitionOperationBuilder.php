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

namespace Netzmacht\Contao\Workflow\Backend\Dca;

use Contao\FilesModel;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Manager as DcaManager;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Manager\Manager;

/**
 * Class Operations helper
 */
class TransitionOperationBuilder
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
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Construct.
     *
     * @param Manager           $manager           Workflow manager.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param DcaManager        $dcaManager        Data container manager.
     */
    public function __construct(Manager $manager, RepositoryManager $repositoryManager, DcaManager $dcaManager)
    {
        $this->manager           = $manager;
        $this->repositoryManager = $repositoryManager;
        $this->dcaManager        = $dcaManager;
    }

    /**
     * Add transitions as operations.
     *
     * @param Transition[]|array $transitions    Workflow transitions.
     * @param string             $providerName   Provider name.
     * @param string             $scope          Operation scope.
     * @param callable|null      $callback       Filter callback.
     * @param string|null        $entityProvider Optional different entity provider name.
     *
     * @return void
     */
    public function addTransitions(
        array $transitions,
        string $providerName,
        string $scope = self::MODEL_SCOPE,
        ?callable $callback = null,
        ?string $entityProvider = null
    ): void {
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
                'icon'  => $this->getTransitionIcon($transition),
                'href'  => sprintf(
                    'table=%s&amp;key=workflow&amp;transition=%s',
                    $entityProvider,
                    $transition->getName()
                )
            );

            if ($callback) {
                $config = $callback($transition, $config);
            }

            $this->dcaManager->getDefinition($providerName)->set(['list', $scope, $buttonName], $config);
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
    public function getTransitionIcon(Transition $transition): string
    {
        $icon = $transition->getConfigValue('icon');

        if ($icon) {
            /** @var ContaoRepository $repository */
            $repository = $this->repositoryManager->getRepository(FilesModel::class);
            $model      = $repository->findByUuid($icon);

            if ($model) {
                return $model->path;
            }
        }

        return 'bundles/netzmachtcontaoworkflow/img/transition.png';
    }
}
