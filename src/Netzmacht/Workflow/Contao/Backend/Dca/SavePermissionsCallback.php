<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Contao\Backend\Dca;

use Netzmacht\Workflow\Contao\Model\PermissionModel;

/**
 * Class SavePermissionsCallback stores the permission in the association group table.
 *
 * @package Netzmacht\Workflow\Contao\Backend\Dca
 */
class SavePermissionsCallback
{
    /**
     * Database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * The source table.
     *
     * @var string
     */
    private $source;

    /**
     * The loaded permissions.
     *
     * @var array
     */
    private $permissions = array();

    /**
     * Construct.
     *
     * @param string $source The source table.
     */
    public function __construct($source)
    {
        $this->database = \Database::getInstance();
        $this->source   = $source;
    }

    /**
     * Invoke the callback.
     *
     * @param mixed          $value         The value.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return mixed
     */
    public function __invoke($value, $dataContainer)
    {
        $this->loadPermissions($dataContainer->id);
        $this->createNewPermissions($value, $dataContainer);
        $this->deleteRemovedPermissions();

        return $value;
    }

    /**
     * Load permissions for the given row id.
     *
     * @param int $rowId The road id.
     *
     * @return void
     */
    private function loadPermissions($rowId)
    {
        $permissions = array();
        $result      = $this->database
            ->prepare('SELECT * FROM tl_workflow_permission WHERE source=? AND source_id=?')
            ->execute($this->source, $rowId);

        while ($result->next()) {
            $permissions[$result->permission] = $result->id;
        }

        $this->permissions = $permissions;
    }

    /**
     * Delete permissions which where removed.
     *
     * @return void
     */
    private function deleteRemovedPermissions()
    {
        if (!$this->permissions) {
            return;
        }

        $query = sprintf(
            'DELETE FROM tl_workflow_permission where id IN(\'%s\')',
            implode('\',\'', $this->permissions)
        );

        $this->database->query($query);
    }

    /**
     * Create new permissions.
     *
     * @param mixed          $value         The serialized permissions.
     * @param \DataContainer $dataContainer The data container.
     *
     * @return void
     */
    private function createNewPermissions($value, $dataContainer)
    {
        foreach (deserialize($value, true) as $permission) {
            if (isset($this->permissions[$permission])) {
                unset($this->permissions[$permission]);
            } else {
                $model = new PermissionModel();

                $model->tstamp     = time();
                $model->source     = $this->source;
                $model->source_id  = $dataContainer->id;
                $model->permission = $permission;

                $model->save();
            }
        }
    }
}
