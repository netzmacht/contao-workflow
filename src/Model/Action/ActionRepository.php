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

namespace Netzmacht\Contao\Workflow\Model\Action;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;

/**
 * Class ActionRepository
 */
class ActionRepository extends ContaoRepository
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct(ActionModel::class);
    }

    /**
     * Find by transition.
     *
     * @param int   $transitionId The transition id.
     * @param array $options      Query options.
     *
     * @return ActionModel[]|Collection|null
     */
    public function findByTransition(int $transitionId, array $options = ['order' => '.sorting'])
    {
        return $this->findBy(['.pid=?'], [$transitionId], $options);
    }
}
