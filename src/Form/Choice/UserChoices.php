<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2020 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Form\Choice;

use Doctrine\DBAL\Connection;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Security\Permission;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class provides all user choices depending on a permission
 */
final class UserChoices
{
    // @codingStandardsIgnoreStart
    // FIXME: Ignore disabled users
    // @codingStandardsIgnoreEnd
    private const MEMBERS_BY_PERMISSION = <<<'SQL'
SELECT DISTINCT m.id, m.username, m.firstname, m.lastname
           FROM tl_member m 
      LEFT JOIN tl_member_to_group t ON t.member_id = m.id
      LEFT JOIN tl_member_group g ON g.id = t.group_id
      LEFT JOIN tl_workflow_permission p ON p.source='tl_member_group' AND p.source_id=g.id
          WHERE p.permission = :permission
       ORDER BY m.firstname, m.lastname
SQL;

    // @codingStandardsIgnoreStart
    // FIXME: Ignore disabled users
    // @codingStandardsIgnoreEnd
    private const MEMBERS_QUERY = <<<'SQL'
SELECT DISTINCT m.id, m.username, m.firstname, m.lastname
           FROM tl_member m
       ORDER BY m.firstname, m.lastname
SQL;

    // @codingStandardsIgnoreStart
    // FIXME: Ignore disabled users
    // @codingStandardsIgnoreEnd
    private const USERS_BY_PERMISSION_QUERY = <<<'SQL'
SELECT DISTINCT u.id, u.username, u.name
           FROM tl_user u 
      LEFT JOIN tl_workflow_permission up ON up.source='tl_user' AND up.source_id=u.id
      LEFT JOIN tl_user_to_group t ON t.user_id = u.id 
      LEFT JOIN tl_user_group g ON g.id = t.group_id
      LEFT JOIN tl_workflow_permission gp ON gp.source='tl_user_group' AND gp.source_id=g.id
          WHERE 
            ((u.inherit = 'extend' OR u.inherit = 'custom') AND up.permission = :permission) OR 
            ((u.inherit = 'extend' OR u.inherit = 'group') AND gp.permission = :permission)
       ORDER BY u.name
SQL;

    // @codingStandardsIgnoreStart
    // FIXME: Ignore disabled users
    // @codingStandardsIgnoreEnd
    private const USERS_QUERY = <<<'SQL'
SELECT DISTINCT u.id, u.username, u.name
           FROM tl_user u 
       ORDER BY u.name
SQL;

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UserChoices constructor.
     *
     * @param Connection          $connection Database connection.
     * @param TranslatorInterface $translator Translator.
     */
    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    /**
     * Fetch user options by permission.
     *
     * @param Permission $permission The required permission.
     *
     * @return array
     */
    public function fetchByPermission(Permission $permission): array
    {
        $userGroup   = $this->translator->trans('MOD.user.0', [], 'contao_modules');
        $memberGroup = $this->translator->trans('MOD.member.0', [], 'contao_modules');

        return [
            $userGroup   => $this->fetchUsersByPermissions($permission),
            $memberGroup => $this->fetchMembersByPermissions($permission),
        ];
    }

    /**
     * Fetch all users grouped by members and users.
     *
     * @return array
     */
    public function findAll(): array
    {
        $userGroup   = $this->translator->trans('MOD.user.0', [], 'contao_modules');
        $memberGroup = $this->translator->trans('MOD.member.0', [], 'contao_modules');

        return [
            $userGroup   => $this->fetchAllUsers(),
            $memberGroup => $this->fetchAllMembers(),
        ];
    }

    /**
     * Fetch backend user options by permission.
     *
     * @param Permission $permission The required permission.
     *
     * @return array
     */
    private function fetchUsersByPermissions(Permission $permission): array
    {
        $statement = $this->connection->executeQuery(
            self::USERS_BY_PERMISSION_QUERY,
            ['permission' => (string) $permission]
        );

        $options = [];
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $label           = sprintf('%s (%s)', $row['name'], $row['username']);
            $options[$label] = (string) EntityId::fromProviderNameAndId('tl_user', $row['id']);
        }

        return $options;
    }

    /**
     * Fetch members options by permission.
     *
     * @param Permission $permission The required permission.
     *
     * @return array
     */
    private function fetchMembersByPermissions(Permission $permission): array
    {
        $statement = $this->connection->executeQuery(
            self::MEMBERS_BY_PERMISSION,
            ['permission' => (string) $permission]
        );

        $options = [];
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $label           = sprintf('%s %s (%s)', $row['firstname'], $row['lastname'], $row['username']);
            $options[$label] = (string) EntityId::fromProviderNameAndId('tl_member', $row['id']);
        }

        return $options;
    }

    /**
     * Fetch all backend users grouped.
     *
     * @return array
     */
    private function fetchAllUsers(): array
    {
        $statement = $this->connection->executeQuery(self::USERS_QUERY);
        $options   = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $label           = sprintf('%s (%s)', $row['name'], $row['username']);
            $options[$label] = (string) EntityId::fromProviderNameAndId('tl_user', $row['id']);
        }

        return $options;
    }

    /**
     * Fetch all frontend members.
     *
     * @return array
     */
    private function fetchAllMembers(): array
    {
        $statement = $this->connection->executeQuery(self::MEMBERS_QUERY);
        $options   = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $label           = sprintf('%s %s (%s)', $row['firstname'], $row['lastname'], $row['username']);
            $options[$label] = (string) EntityId::fromProviderNameAndId('tl_member', $row['id']);
        }

        return $options;
    }
}
