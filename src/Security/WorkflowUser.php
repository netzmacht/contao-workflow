<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Security;

use Contao\BackendUser;
use Contao\FrontendUser;
use Contao\StringUtil;
use Contao\User as ContaoUser;
use Doctrine\DBAL\Connection;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Security\Permission;
use PDO;
use Symfony\Component\Security\Core\Security;
use function array_flip;
use function array_key_exists;
use function array_map;
use function get_class;

/**
 * Class WorkflowUser contains all granted permission for the current user.
 *
 * A current user may be a backend user, frontend user or a guest. Unknown users authenticated by Symfony security
 * are ignored.
 */
final class WorkflowUser implements User
{
    /**
     * Internal cache of user permissions.
     *
     * @var array|array<string,array<string,int>>
     */
    private $permissions = [];

    /**
     * Symfony security component.
     *
     * @var Security
     */
    private $security;

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * WorkflowUser constructor.
     *
     * @param Security   $security   Security framework.
     * @param Connection $connection Database connection.
     */
    public function __construct(Security $security, Connection $connection)
    {
        $this->security   = $security;
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserId(): ?EntityId
    {
        $user = $this->security->getUser();
        if ($user instanceof FrontendUser) {
            return EntityId::fromProviderNameAndId('tl_member', $user->id);
        }
        if ($user instanceof BackendUser) {
            return EntityId::fromProviderNameAndId('tl_user', $user->id);
        }

        return null;
    }

    /**
     * Check if user as a given permission.
     *
     * @param Permission $permission The permission to check.
     *
     * @return bool
     */
    public function hasPermission(Permission $permission): bool
    {
        return array_key_exists($permission->__toString(), $this->getUserPermissions());
    }

    /**
     * {@inheritDoc}
     */
    public function getPermissions(): array
    {
        return array_map(
            // @codingStandardsIgnoreStart
            static function (string $permission): Permission {
                return Permission::fromString($permission);
            },
            // @codingStandardsIgnoreEnd
            array_keys($this->getUserPermissions())
        );
    }

    /**
     * Load all user permissions.
     *
     * @return array
     */
    private function getUserPermissions(): array
    {
        $user = $this->security->getUser();
        $key  = '__GUEST__';

        if ($user instanceof ContaoUser) {
            $key = get_class($user);
        } elseif ($user !== null) {
            return [];
        }

        if (isset($this->permissions[$key])) {
            return $this->permissions[$key];
        }

        switch ($key) {
            case '__GUEST__':
                $permissions = $this->loadGuestPermissions();
                break;

            case FrontendUser::class:
                $permissions = $this->loadFrontendUserPermissions($user);
                break;

            case BackendUser::class:
                $permissions = $this->loadBackendUserPermissions($user);
                break;

            default:
                return [];
        }

        return $this->permissions[$key] = array_flip($permissions);
    }

    /**
     * Load permissions of a guest.
     *
     * @return array
     */
    private function loadGuestPermissions(): array
    {
        $permissions = [];
        $statement   = $this->connection->executeQuery('SELECT id, permissions FROM tl_workflow');

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            foreach (StringUtil::deserialize($row['permissions'], true) as $permission) {
                if (! $permission['guest']) {
                    continue;
                }

                $permissions[] = 'workflow_' . $row['id'] . ':' . $permission['name'];
            }
        }

        return $permissions;
    }

    /**
     * Load permissions for an authenticated frontend user.
     *
     * @param FrontendUser $user The authenticated frontend user.
     *
     * @return array
     */
    private function loadFrontendUserPermissions(FrontendUser $user): array
    {
        $sql = <<<'SQL'
SELECT DISTINCT permission 
           FROM tl_workflow_permission
          WHERE source = :source
            AND source_id IN (:sourceIds)
SQL;

        $statement = $this->connection->executeQuery(
            $sql,
            ['source' => 'tl_member_group', 'sourceIds' => $user->groups],
            ['source' => PDO::PARAM_STR, 'sourceIds' => Connection::PARAM_STR_ARRAY]
        );

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Load permissions for an authenticated backend user.
     *
     * @param BackendUser $user The authenticated backend user.
     *
     * @return array
     */
    private function loadBackendUserPermissions(BackendUser $user): array
    {
        return (array) $user->workflow;
    }
}
