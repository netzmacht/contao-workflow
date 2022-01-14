<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Model\Permission\PermissionModel;

use function time;

/**
 * Class SavePermissionsCallback stores the permission in the association group table.
 */
final class SavePermissionsCallbackListener
{
    /**
     * The loaded permissions.
     *
     * @var array<string,string>
     */
    private $permissions = [];

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Construct.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Invoke the callback.
     *
     * @param mixed         $permissions   The value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return mixed
     */
    public function onSaveCallback($permissions, DataContainer $dataContainer)
    {
        $permissions = StringUtil::deserialize($permissions, true);

        $this->loadPermissions($dataContainer->table, (int) $dataContainer->id);
        $this->createNewPermissions($permissions, $dataContainer);
        $this->deleteRemovedPermissions($permissions, $dataContainer->table, (int) $dataContainer->id);

        return $permissions;
    }

    /**
     * Load permissions for the given row id.
     *
     * @param string $source The source of the permissions.
     * @param int    $rowId  The road id.
     */
    private function loadPermissions(string $source, int $rowId): void
    {
        $permissions = [];
        $query       = 'SELECT * FROM tl_workflow_permission WHERE source=:source AND source_id=:source_id';
        $statement   = $this->repositoryManager->getConnection()->prepare($query);
        $results     = $statement->executeQuery(['source' => $source, 'source_id' => $rowId]);

        while ($result = (object) $results->fetchAssociative()) {
            $permissions[$result->permission] = $result->id;
        }

        $this->permissions = $permissions;
    }

    /**
     * Delete permissions which where removed.
     *
     * @param list<string> $values   List of active permissions.
     * @param string       $source   Source table.
     * @param int          $sourceId Source id.
     */
    private function deleteRemovedPermissions(array $values, string $source, int $sourceId): void
    {
        $queryBuilder = $this->repositoryManager->getConnection()->createQueryBuilder()
            ->delete('tl_workflow_permission')
            ->where('source = :source')
            ->andWhere('source_id = :source_id')
            ->setParameter('source', $source)
            ->setParameter('source_id', $sourceId);

        if ($values) {
            $queryBuilder->andWhere('permission NOT IN (:permissions)');
            $queryBuilder->setParameter('permissions', $values, Connection::PARAM_STR_ARRAY);
        }

        $queryBuilder->execute();
    }

    /**
     * Create new permissions.
     *
     * @param list<string>  $permissions   The permissions as string representations.
     * @param DataContainer $dataContainer The data container.
     */
    private function createNewPermissions(array $permissions, DataContainer $dataContainer): void
    {
        foreach ($permissions as $permission) {
            if (isset($this->permissions[$permission])) {
                unset($this->permissions[$permission]);
            } else {
                $model = new PermissionModel();

                $model->tstamp     = time();
                $model->source     = $dataContainer->table;
                $model->source_id  = $dataContainer->id;
                $model->permission = $permission;

                $this->repositoryManager->getRepository(PermissionModel::class)->save($model);
            }
        }
    }
}
