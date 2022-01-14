<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Contao\Model;

use function array_unique;

/**
 * ContaoModelRelatedModelChangesTracker tracks changes made on related models of a contao model.
 */
final class ContaoModelRelatedModelChangeTracker
{
    /**
     * Tracked models.
     *
     * @var       Model[][][]
     * @psalm-var array<string,array<int,array<int,Model>>>
     */
    private $changes = [];

    /**
     * Track a change of a related model.
     *
     * @param Model $baseModel           The base model which is the base model being used then changing a related
     *                                   model.
     * @param Model $changedRelatedModel The model being changed.
     */
    public function track(Model $baseModel, Model $changedRelatedModel): void
    {
        $this->changes[$baseModel::getTable()][$baseModel->id][] = $changedRelatedModel;
    }

    /**
     * Release the tracked changes of a model.
     *
     * @param Model $model The base model.
     *
     * @return Model[]
     */
    public function release(Model $model): array
    {
        $models = ($this->changes[$model::getTable()][$model->id] ?? []);
        unset($this->changes[$model::getTable()][$model->id]);

        return array_unique($models);
    }
}
