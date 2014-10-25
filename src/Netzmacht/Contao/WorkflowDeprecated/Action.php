<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\WorkflowDeprecated;


use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBag;
use Netzmacht\Contao\WorkflowDeprecated\Entity;
use Netzmacht\Contao\WorkflowDeprecated\Flow\Transition;
use Netzmacht\Contao\WorkflowDeprecated\View\FormFactory;

interface Action
{
    /**
     * @param Transition $transition
     * @param Entity $entity
     * @param PropertyValueBag $data
     * @return mixed
     */
    public function execute(Transition $transition, Entity $entity, PropertyValueBag $data);

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @return bool
     */
    public function validate(Transition $transition, Entity $entity);

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @return bool
     */
    public function match(Transition $transition, Entity $entity);

    /**
     * @param FormFactory $formFactory
     * @return mixed
     */
    public function buildForm(FormFactory $formFactory);

    /**
     * @return bool
     */
    public function hasForm();

} 