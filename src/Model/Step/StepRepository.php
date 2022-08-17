<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Step;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

final class StepRepository extends ContaoRepository
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct(StepModel::class);
    }

    /**
     * Find by workflow id.
     *
     * @param int                 $workflowId The workflow id.
     * @param array<string,mixed> $options    Query options.
     *
     * @return Collection|StepModel[]|null
     * @psalm-return Collection|null
     */
    public function findByWorkflow(int $workflowId, array $options = ['order' => 'label'])
    {
        return $this->findBy(
            ['.pid=?'],
            [$workflowId],
            $options
        );
    }
}
