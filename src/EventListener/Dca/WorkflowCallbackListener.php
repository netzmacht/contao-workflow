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

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\DataContainer;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Model\Step\StepModel;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\DatabaseDrivenWorkflowLoader;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeNotFound;
use Netzmacht\ContaoWorkflowBundle\Workflow\Type\WorkflowTypeRegistry;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Manager\Manager as WorkflowManager;
use Symfony\Component\Translation\TranslatorInterface;
use function sprintf;

/**
 * Class Workflow stores callback being used by the tl_workflow table.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Contao\Dca
 */
final class WorkflowCallbackListener
{
    /**
     * Workflow type registry.
     *
     * @var WorkflowTypeRegistry
     */
    private $typeRegistry;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Database driven workflow loader.
     *
     * @var DatabaseDrivenWorkflowLoader
     */
    private $workflowLoader;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Workflow constructor.
     *
     * @param WorkflowTypeRegistry         $typeRegistry      Workflow type registry.
     * @param RepositoryManager            $repositoryManager Repository manager.
     * @param DatabaseDrivenWorkflowLoader $workflowLoader    Database driven workflow loader.
     * @param WorkflowManager              $workflowManager   The workflow manager.
     * @param TranslatorInterface          $translator        Translator.
     */
    public function __construct(
        WorkflowTypeRegistry $typeRegistry,
        RepositoryManager $repositoryManager,
        DatabaseDrivenWorkflowLoader $workflowLoader,
        WorkflowManager $workflowManager,
        TranslatorInterface $translator
    ) {
        $this->typeRegistry      = $typeRegistry;
        $this->repositoryManager = $repositoryManager;
        $this->workflowLoader    = $workflowLoader;
        $this->workflowManager   = $workflowManager;
        $this->translator        = $translator;
    }

    /**
     * Override the provider name if only one provider name is supported.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return void
     */
    public function saveProviderName($dataContainer): void
    {
        try {
            $repository    = $this->repositoryManager->getRepository(WorkflowModel::class);
            $workflowModel = $repository->find((int) $dataContainer->id);
            if (!$workflowModel instanceof WorkflowModel) {
                return;
            }

            $providerNames = $this->typeRegistry->getType($workflowModel->type)->getProviderNames();

            if (count($providerNames) !== 1 || $workflowModel->providerName === $providerNames[0]) {
                return;
            }

            $workflowModel->providerName = $providerNames[0];
            $repository->save($workflowModel);

            // @codingStandardsIgnoreStart
        } catch (WorkflowTypeNotFound $e) {
            // Do nothing
        }
        // @codingStandardsIgnoreEnd
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
        $label = sprintf(
            '<strong>%s</strong><br><span class="tl_gray">%s</span>',
            $row['label'],
            $row['description']
        );

        try {
            $this->workflowLoader->loadWorkflowById((int) $row['id']);
        } catch (\Exception $e) {
            $label .= sprintf(
                '<p class="workflow-definition-error">%s</p>',
                $this->translator->trans('invalid-workflow-definition', [$e->getMessage()], 'contao_workflow')
            );
        }

        return $label;
    }

    /**
     * Get names of workflow types.
     *
     * @return array
     */
    public function getTypes(): array
    {
        return $this->typeRegistry->getTypeNames();
    }

    /**
     * Get all provider names.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     *
     * @throws WorkflowTypeNotFound If workflow type is not defined.
     */
    public function getProviderNames($dataContainer): array
    {
        if (!$dataContainer->activeRecord || !$dataContainer->activeRecord->type) {
            return [];
        }

        if (!$this->typeRegistry->hasType($dataContainer->activeRecord->type)) {
            return [];
        }

        return $this->typeRegistry->getType($dataContainer->activeRecord->type)->getProviderNames();
    }

    /**
     * Get all start steps.
     *
     * @param DataContainer $dataContainer The data container driver.
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
     * @param DataContainer $dataContainer The data container driver.
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
     * @param DataContainer $dataContainer The data container.
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
                    $label  = sprintf('%s [ID %s]', $collection->label, $collection->id);

                    switch ($collection->type) {
                        case 'actions':
                            $label .= sprintf(' --> %s [ID %s]', $stepTo->label, $stepTo->id);
                            break;

                        case 'workflow':
                            try {
                                $workflowLabel = $this->workflowManager
                                    ->getWorkflowByName((string) $collection->workflow)
                                    ->getLabel();

                                $workflowLabel .= sprintf(' [%s]', $collection->workflow);
                            } catch (WorkflowNotFound $exception) {
                                $workflowLabel = $collection->workflow;
                            }

                            $label = sprintf('%s -->  %s', $label, $workflowLabel);
                            break;

                        default:
                            // Do nothing
                    }

                    $options[$collection->id] = $label;
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
        $value = StringUtil::deserialize($value, true);
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
        $value     = StringUtil::deserialize($value, true);
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
            $collection = $repository->findBy(['.pid=?', '.final=?'], [$parentId, ''], ['order' => '.label']);
        } else {
            $collection = $repository->findBy(['.pid=?'], [$parentId], ['order' => '.label']);
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
            if ($definition['step'] === 'start') {
                $count++;
            }
        }

        if (!$count) {
            throw new \Exception('Start transition is required.');
        }

        if ($count > 1) {
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
    private function guardValidPermissionName(array $row, array $names): void
    {
        $reserved = ['contao-admin', 'contao-guest'];

        if (in_array($row['name'], $names, true)) {
            throw new \InvalidArgumentException(sprintf('Permission name "%s" is not unique.', $row['name']));
        }

        if (in_array($row['name'], $reserved, true)) {
            throw new \InvalidArgumentException(sprintf('Permission name "%s" is reserved.', $row['name']));
        }
    }
}
