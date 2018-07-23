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

namespace Netzmacht\ContaoWorkflowBundle\Model\Workflow;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

/**
 * Class WorkflowRepository
 */
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
     * @param string $providerName The provider name.
     * @param string $workflowType The workflow type.
     * @param array  $options      Query options.
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
     * @param string $providerName The provider name.
     * @param array  $options      Query options.
     *
     * @return Collection|WorkflowModel[]|null
     */
    public function findByProvider(string $providerName, array $options = [])
    {
        return $this->findBy(['.providerName=?'], [$providerName], $options);
    }
}
