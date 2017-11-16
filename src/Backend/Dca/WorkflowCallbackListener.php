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

use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Workflow\Backend\Event\GetProviderNamesEvent;
use Netzmacht\Contao\Workflow\Model\StepModel;
use Netzmacht\Contao\Workflow\Model\TransitionModel;
use Netzmacht\Contao\Workflow\Type\WorkflowTypeProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Workflow stores callback being used by the tl_workflow table.
 *
 * @package Netzmacht\Contao\Workflow\Contao\Dca
 */
class WorkflowCallbackListener
{
    /**
     * Workflow type provider.
     *
     * @var WorkflowTypeProvider
     */
    private $typeProvider;

    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Workflow constructor.
     *
     * @param WorkflowTypeProvider     $typeProvider      Workflow type provider.
     * @param EventDispatcherInterface $eventDispatcher   Event dispatcher.
     * @param RepositoryManager        $repositoryManager Repository manager.
     */
    public function __construct(
        WorkflowTypeProvider $typeProvider,
        EventDispatcherInterface $eventDispatcher,
        RepositoryManager $repositoryManager
    ) {
        $this->typeProvider      = $typeProvider;
        $this->eventDispatcher   = $eventDispatcher;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Generate a row view.
     *
     * @param array $row Current data row.
     *
     * @return string
     */
    public function generateRow(array $row): string
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
     */
    public function getTypes(): array
    {
        return $this->typeProvider->getTypeNames();
    }

    /**
     * Get all provider names.
     *
     * @param \DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function getProviderNames($dataContainer): array
    {
        if (!$dataContainer->activeRecord || !$dataContainer->activeRecord->type) {
            return [];
        }

        $event = new GetProviderNamesEvent($dataContainer->activeRecord->type);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return array_merge($event->getProviderNames(), ['tl_test']);
    }

    /**
     * Get all start steps.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     */
    public function getStartSteps($dataContainer): array
    {
        return [
            'process' => ['start'],
            'steps'   => $this->getSteps((int) $dataContainer->activeRecord->id, true),
        ];
    }

    /**
     * Get all end steps.
     *
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     */
    public function getEndSteps($dataContainer): array
    {
        return $this->getSteps((int) $dataContainer->activeRecord->id);
    }

    /**
     * Get all transitions.
     *
     * @param \DataContainer $dataContainer The data container.
     *
     * @return array
     */
    public function getTransitions($dataContainer): array
    {
        $options = [];

        if ($dataContainer->activeRecord) {
            $repository = $this->repositoryManager->getRepository(TransitionModel::class);
            $collection = $repository->findBy(['.pid=?'], [$dataContainer->activeRecord->id]);

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
    public function validatePermissions($value): array
    {
        $value     = deserialize($value, true);
        $names     = [];
        $validated = [];

        foreach ($value as $row) {
            if (!$row['name']) {
                if (!$row['label']) {
                    continue;
                }

                $row['name'] = StringUtil::standardize($row['label']);
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
     * @param int  $parentId    The parent id.
     * @param bool $filterFinal If true only steps which are not final are loaded.
     *
     * @return array
     */
    private function getSteps(int $parentId, bool $filterFinal = false): array
    {
        $steps      = [];
        $repository = $this->repositoryManager->getRepository(StepModel::class);

        if ($filterFinal) {
            $collection = $repository->findBy(['pid=?', 'final=?'], [$parentId, ''], ['order' => 'name']);
        } else {
            $collection = $repository->findBy(['pid=?'], [$parentId], ['order' => 'name']);
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
    private function guardStartStepDefined(array $process): void
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
    protected function guardValidPermissionName(array $row, array $names): void
    {
        $reserved = ['contao-admin', 'contao-guest'];

        if (in_array($row['name'], $names)) {
            throw new \InvalidArgumentException(sprintf('Permission name "%s" is not unique.', $row['name']));
        } elseif (in_array($row['name'], $reserved)) {
            throw new \InvalidArgumentException(sprintf('Permission name "%s" is reserved.', $row['name']));
        }
    }
}
