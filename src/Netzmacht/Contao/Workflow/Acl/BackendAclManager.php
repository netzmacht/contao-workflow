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
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class BackendAclManager implements the acl manager for backend users.
 *
 * @package Netzmacht\Contao\Workflow\Acl
 */
class BackendAclManager extends AbstractContaoAclManager
{
    /**
     * {@inheritdoc}
     */
    public function hasAdminPermissions()
    {
        return $this->user->isAdmin;
    }

    /**
     * {@inheritdoc}
     */
    protected function matchUser(RoleModel $model)
    {
        $users = deserialize($model->users, true);

        return in_array($this->user->id, $users);
    }
}
