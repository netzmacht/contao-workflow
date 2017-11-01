<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Data;

use ContaoCommunityAlliance\DcGeneral\Data\ConfigInterface;
use Netzmacht\Workflow\Data\Specification;

/**
 * Interface QuerySpecification.
 *
 * @package Netzmacht\Contao\Workflow\Data
 */
interface QuerySpecification extends Specification
{
    /**
     * Prepare the configuration.
     *
     * @param ConfigInterface $config The query config.
     *
     * @return void
     */
    public function prepare(ConfigInterface $config);
}
