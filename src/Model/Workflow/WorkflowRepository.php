<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Model\Workflow;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

final class WorkflowRepository extends ContaoRepository
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct(WorkflowModel::class);
    }

    /**
     * Find workflows by the provider name and type.
     *
     * @param string              $providerName The provider name.
     * @param string              $workflowType The workflow type.
     * @param array<string,mixed> $options      Query options.
     *
     * @return Collection|WorkflowModel[]|null
     */
    public function findByProviderAndType(string $providerName, string $workflowType, array $options = [])
    {
        return $this->findBy(
            ['.providerName=?', '.type=?'],
            [$providerName, $workflowType],
            $options
        );
    }

    /**
     * Find workflow definitions by the provider name.
     *
     * @param string              $providerName The provider name.
     * @param array<string,mixed> $options      Query options.
     *
     * @return Collection|WorkflowModel[]|null
     */
    public function findByProvider(string $providerName, array $options = [])
    {
        return $this->findBy(['.providerName=?'], [$providerName], $options);
    }

    /**
     * Find active workflow definitions.
     *
     * @param array<string,mixed> $options Query options.
     *
     * @return Collection|WorkflowModel[]|null
     */
    public function findActive(array $options = [])
    {
        return $this->findBy(['.active=?'], ['1'], $options);
    }
}
