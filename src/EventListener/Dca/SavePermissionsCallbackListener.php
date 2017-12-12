<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\ContaoWorkflowBundle\Model\Permission\PermissionModel;

/**
 * Class SavePermissionsCallback stores the permission in the association group table.
 *
 * @package Netzmacht\ContaoWorkflowBundle\Backend\Dca
 */
class SavePermissionsCallbackListener
{
    /**
     * The loaded permissions.
     *
     * @var array
     */
    private $permissions = array();

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
     * @param mixed          $value         The value.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return mixed
     */
    public function onSaveCallback($value, $dataContainer)
    {
        $this->loadPermissions($dataContainer->table, (int) $dataContainer->id);
        $this->createNewPermissions($value, $dataContainer);
        $this->deleteRemovedPermissions();

        return $value;
    }

    /**
     * Load permissions for the given row id.
     *
     * @param string $source The source of the permissions.
     * @param int    $rowId  The road id.
     *
     * @return void
     */
    private function loadPermissions(string $source, int $rowId): void
    {
        $permissions = array();
        $query       = 'SELECT * FROM tl_workflow_permission WHERE source=:source AND source_id=:source_id';
        $statement   = $this->repositoryManager->getConnection()->prepare($query);
        $statement->execute(['source' => $source, 'source_id' => $rowId]);

        while ($result = $statement->fetch(\PDO::FETCH_OBJ)) {
            $permissions[$result->permission] = $result->id;
        }

        $this->permissions = $permissions;
    }

    /**
     * Delete permissions which where removed.
     *
     * @return void
     */
    private function deleteRemovedPermissions(): void
    {
        if (!$this->permissions) {
            return;
        }

        $statement = $this->repositoryManager
            ->getConnection()
            ->prepare('DELETE FROM tl_workflow_permission WHERE id IN (:ids)');

        $statement->bindValue('ids', $this->permissions, Connection::PARAM_INT_ARRAY);
        $statement->execute();
    }

    /**
     * Create new permissions.
     *
     * @param mixed          $value         The serialized permissions.
     * @param \DataContainer $dataContainer The data container.
     *
     * @return void
     */
    private function createNewPermissions($value, $dataContainer): void
    {
        foreach (deserialize($value, true) as $permission) {
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
