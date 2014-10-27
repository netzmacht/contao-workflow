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

class BackendAclManager extends AbstractContaoAclManager
{
    /**
     * @param \BackendUser $user
     */
    public function __construct(\BackendUser $user)
    {
        parent::__construct($user);
    }

    /**
     * @return bool
     */
    public function hasAdminPermissions()
    {
        return $this->user->isAdmin;
    }

    /**
     * @param $model
     *
     * @return bool
     */
    protected function matchUser(RoleModel $model)
    {
        $users = deserialize($model->users, true);

        return in_array($this->user->id, $users);
    }
}
