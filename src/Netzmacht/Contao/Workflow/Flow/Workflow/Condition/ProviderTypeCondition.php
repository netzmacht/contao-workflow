<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Flow\Workflow\Condition;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;
use Netzmacht\Contao\Workflow\Flow\Workflow;
use Netzmacht\Contao\Workflow\Flow\Workflow\Condition;

/**
 * Class ProviderTypeCondition check if entity matches a specific provider.
 *
 * @package Netzmacht\Contao\Workflow\Flow\Workflow\Condition
 */
class ProviderTypeCondition implements Condition
{
    /**
     * Provider name to check against.
     *
     * @var string
     */
    private $providerName;

    /**
     * Get the provider name to check against.
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Set a new provider name.
     *
     * @param string $providerName New provider name.
     *
     * @return $this
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;

        return $this;
    }

    /**
     * Consider if workflow matches to the entity.
     *
     * @param Workflow $workflow
     * @param Entity   $entity   The entity.
     *
     * @return bool
     */
    public function match(Workflow $workflow, Entity $entity)
    {
        if (!$this->providerName) {
            return $entity->getProviderName() == $workflow->getProviderName();
        }

        return $entity->getProviderName() == $this->providerName;
    }
}
