<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Acl;

use Netzmacht\Contao\Workflow\Contao\Model\RoleModel;
use Netzmacht\Contao\Workflow\Flow\Workflow;

/**
 * Class FrontendAclManager implements the acl manager for frontend members.
 *
 * @package Netzmacht\Contao\Workflow\Acl
 */
class FrontendAclManager extends AbstractContaoAclManager
{
    /**
     * {@inheritdoc}
     */
    public function hasAdminPermissions()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function matchUser(RoleModel $model)
    {
        $members = deserialize($model->members, true);

        return in_array($this->user->id, $members);
    }
}
