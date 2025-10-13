<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Action;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;
use Netzmacht\ContaoWorkflowBundle\Model\Transition\TransitionModel;
use Netzmacht\ContaoWorkflowBundle\Model\Workflow\WorkflowModel;

/** @extends ContaoRepository<ActionModel> */
final class ActionRepository extends ContaoRepository
{
    public function __construct()
    {
        parent::__construct(ActionModel::class);
    }

    /**
     * Find by transition.
     *
     * @param int                 $transitionId The transition id.
     * @param array<string,mixed> $options      Query options.
     *
     * @return Collection<ActionModel>|null
     */
    public function findByTransition(int $transitionId, array $options = ['order' => '.sorting']): Collection|null
    {
        return $this->findBy(['.ptable=?', '.pid=?'], [TransitionModel::getTable(), $transitionId], $options);
    }

    /**
     * Find active by transition.
     *
     * @param int    $transitionId The transition id.
     * @param string $orderField   The order field.
     * @param string $direction    The direction.
     *
     * @return Collection<ActionModel>|null
     */
    public function findActiveByTransition(
        int $transitionId,
        string $orderField = '.sorting',
        string $direction = 'ASC',
    ): Collection|null {
        return $this->findBy(
            ['.ptable=?', '.pid=?', '.active=?'],
            [TransitionModel::getTable(), $transitionId, '1'],
            ['order' => $orderField . ' ' . $direction],
        );
    }

    /**
     * Find global actions defined for a workflow.
     *
     * @param int                 $workflowId The workflow id.
     * @param array<string,mixed> $options    Query options.
     *
     * @return Collection<ActionModel>|null
     */
    public function findByWorkflow(int $workflowId, array $options = ['order' => '.sorting']): Collection|null
    {
        return $this->findBy(['.ptable=?', '.pid=?'], [WorkflowModel::getTable(), $workflowId], $options);
    }
}
