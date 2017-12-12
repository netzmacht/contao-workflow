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

namespace Netzmacht\ContaoWorkflowBundle\Model\Step;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

/**
 * Class StepRepository
 */
class StepRepository extends ContaoRepository
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
     * @param int   $workflowId The workflow id.
     * @param array $options    Query options.
     *
     * @return Collection|StepModel[]|null
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
