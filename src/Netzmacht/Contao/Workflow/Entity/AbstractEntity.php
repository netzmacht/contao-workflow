<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Entity;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface as Entity;

/**
 * Class AbstractEntity implements base Entity functionality.
 *
 * @package Netzmacht\Contao\Workflow\Entity
 */
abstract class AbstractEntity implements Entity
{
    /**
     * Entity metadata.
     *
     * @var array
     */
    private $metaData = array();

    /**
     * {@inheritdoc}
     */
    public function getMeta($metaName)
    {
        if (isset($this->metaData[$metaName])) {
            return $this->metaData[$metaName];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMeta($strMetaName, $varValue)
    {
        $this->metaData[$strMetaName] = $varValue;
    }
}
