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

class FrontendAclManager extends AbstractContaoAclManager
{
    /**
     * @param \FrontendUser $user
     */
    public function __construct(\FrontendUser $user)
    {
        parent::__construct($user);
    }

    /**
     * @return bool
     */
    public function hasAdminPermissions()
    {
        return false;
    }

    /**
     * @param $model
     *
     * @return bool
     */
    protected function matchUser(RoleModel $model)
    {
        $members = deserialize($model->members, true);

        return in_array($this->user->id, $members);
    }
}
