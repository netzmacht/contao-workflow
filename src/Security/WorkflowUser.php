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
use Symfony\Component\Security\Core\User\UserInterface;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
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
    public function getUserId(?UserInterface $user = null): ?EntityId
    {
        $user = $user ?: $this->security->getUser();
        if ($user instanceof FrontendUser) {
            return EntityId::fromProviderNameAndId('tl_member', $user->id);
        }
        if ($user instanceof BackendUser) {
            return EntityId::fromProviderNameAndId('tl_user', $user->id);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasPermission(Permission $permission, ?UserInterface $user = null): bool
    {
        return array_key_exists($permission->__toString(), $this->getUserPermissions($user));
    }

    /**
     * {@inheritDoc}
     */
    public function getPermissions(?UserInterface $user = null): array
    {
        return array_map(
        // @codingStandardsIgnoreStart
            static function (string $permission): Permission {
                return Permission::fromString($permission);
            },
            // @codingStandardsIgnoreEnd
            array_keys($this->getUserPermissions($user))
        );
    }

    /**
     * Load all user permissions.
     *
     * @param UserInterface|null $user The user to check. If empty the user the current security user is used.
     *
     * @return array
     */
    private function getUserPermissions(?UserInterface $user = null): array
    {
        $user = $user ?: $this->security->getUser();
        $key  = '__GUEST__';

        if ($user instanceof ContaoUser) {
            $key = get_class($user);
        } elseif ($user !== null) {
            return [];
        }

        switch ($key) {
            case '__GUEST__':
                $permissions = $this->loadPermissionsFilteredBy('guest');
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

        return array_flip($permissions);
    }

    /**
     * Load permissions of a guest.
     *
     * @param string $key The filter key. Valid values are guest or admin.
     *
     * @return array
     */
    private function loadPermissionsFilteredBy(string $key): array
    {
        $permissions = [];
        $statement   = $this->connection->executeQuery('SELECT id, permissions FROM tl_workflow');

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            foreach (StringUtil::deserialize($row['permissions'], true) as $permission) {
                if (! $permission[$key]) {
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
        if (! $user->isAdmin) {
            return $user->workflow;
        }

        $sql = <<<'SQL'
SELECT DISTINCT permission 
           FROM tl_workflow_permission
          WHERE source = :source
            AND source_id = :sourceId
SQL;

        $statement = $this->connection->executeQuery($sql, ['source' => 'tl_user', 'sourceId' => $user->id]);

        return array_values(
            array_unique(
                array_merge($statement->fetchAll(PDO::FETCH_COLUMN), $this->loadPermissionsFilteredBy('admin'))
            )
        );
    }
}
