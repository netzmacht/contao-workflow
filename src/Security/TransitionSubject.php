<?php

/**
 * contao-workflow.
 *
 * @package    contao-workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Workflow\Security;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class TransitionSubject
 *
 * @package Netzmacht\Contao\Workflow\Security
 */
class TransitionSubject
{
    /**
     * @var Item
     */
    private $item;

    /**
     * @var Transition
     */
    private $transition;

    /**
     * TransitionSubject constructor.
     *
     * @param Item       $item
     * @param Transition $transition
     */
    public function __construct(Item $item, Transition $transition)
    {
        $this->item       = $item;
        $this->transition = $transition;
    }

    /**
     * Get item.
     *
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * Get transition.
     *
     * @return Transition
     */
    public function getTransition(): Transition
    {
        return $this->transition;
    }
}
