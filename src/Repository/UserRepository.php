<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Workflow\Repository;

use Database as Connection;
use Model\Registry;

/**
 * Class UserRepository encapsulates user queries in the workflow context.
 *
 * @package Netzmacht\Contao\Workflow\Repository
 */
class UserRepository
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Model registry.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Construct.
     *
     * @param Connection $connection Database connection.
     * @param Registry   $registry   The model registry.
     */
    public function __construct(Connection $connection, Registry $registry)
    {
        $this->connection = $connection;
        $this->registry   = $registry;
    }

    /**
     * Find users with a specific permission.
     *
     * @param string $permission The permission as string.
     *
     * @return \UserModel[]
     */
    public function findUsersWithPermission($permission)
    {
        $users  = array();
        $result = $this->prepareStatement()->execute($permission, $permission);

        while ($result->next()) {
            $model = $this->registry->fetch('tl_user', $result->id);

            if (!$model) {
                $model = new \UserModel($result);
            }

            $users[] = $model;
        }

        return $users;
    }

    /**
     * Get all users.
     *
     * @return \UserModel[]
     */
    public function findAllUsers()
    {
        $collection = \UserModel::findAll(array('order' => 'name'));

        if (!$collection) {
            return array();
        }

        $users = array();

        foreach ($collection as $user) {
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Prepare database query statement.
     *
     * @return \Database\Statement
     */
    private function prepareStatement()
    {
        $query = <<<SQL
SELECT u.* FROM tl_user u
LEFT JOIN tl_workflow_permission p ON p.source = 'tl_user' AND p.source_id=u.id
LEFT JOIN tl_user_to_group g ON g.user_id = u.id
LEFT JOIN tl_workflow_permission z ON z.source = 'tl_user_group' AND z.source_id=g.group_id
WHERE p.permission=? OR z.permission=?
GROUP BY u.id
SQL;

        return $this->connection->prepare($query);
    }
}
