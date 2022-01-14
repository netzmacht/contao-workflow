<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Transition;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

final class TransitionRepository extends ContaoRepository
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct(TransitionModel::class);
    }

    /**
     * Find by workflow id.
     *
     * @param int                 $workflowId The workflow id.
     * @param array<string,mixed> $options    Query options.
     *
     * @return Collection|TransitionModel[]|null
     */
    public function findByWorkflow(int $workflowId, array $options = ['order' => '.sorting'])
    {
        return $this->findBy(
            ['.pid=?'],
            [$workflowId],
            $options
        );
    }

    /**
     * Find active by workflow id.
     *
     * @param int                 $workflowId The workflow id.
     * @param array<string,mixed> $options    Query options.
     *
     * @return Collection|TransitionModel[]|null
     */
    public function findActiveByTransition(int $workflowId, array $options = ['order' => '.sorting'])
    {
        return $this->findBy(
            ['.active=1', '.pid=?'],
            [$workflowId],
            $options
        );
    }
}
