<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Data;

use ContaoCommunityAlliance\DcGeneral\Data\ConfigInterface;
use Netzmacht\Workflow\Data\Specification;

/**
 * Interface QuerySpecification.
 *
 * @package Netzmacht\Workflow\Contao\Data
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
