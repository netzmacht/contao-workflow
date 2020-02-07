<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use ArrayIterator;
use Contao\Model;
use Contao\Model\Collection;
use Exception;
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;
use Traversable;
use function array_pop;
use function explode;

/**
 * Class ContaoModelPropertyAccessor
 */
final class ContaoModelPropertyAccessor implements PropertyAccessor
{
    /**
     * Wrapped model.
     *
     * @var Model
     */
    private $model;

    /**
     * ContaoModelPropertyAccessor constructor.
     *
     * @param Model $model Contao model.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public static function supports($object): bool
    {
        return $object instanceof Model;
    }

    /**
     * {@inheritDoc}
     *
     * @throws PropertyAccessFailed When data structure is not supported.
     */
    public static function create($object): PropertyAccessor
    {
        if (self::supports($object)) {
            return new self($object);
        }

        throw new PropertyAccessFailed('Unsupported data structure.');
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, $value): void
    {
        $path     = explode('.', $name);
        $property = array_pop($path);
        $model    = $this->determineModel($path);

        if ($model) {
            $model->$property = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name)
    {
        $path     = explode('.', $name);
        $property = array_pop($path);
        $model    = $this->determineModel($path);

        if ($model === null) {
            return null;
        }

        return $model->$property;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        $path     = explode('.', $name);
        $property = array_pop($path);
        $model    = $this->determineModel($path);

        if ($model === null) {
            return false;

        }

        return isset($model->$property);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->model->row());
    }

    /**
     * Determine the model from a given path.
     *
     * @param array $path Property path checking releations
     *
     * @return Model|null
     *
     * @throws Exception When property is not relational.
     */
    private function determineModel(array $path) :? Model
    {
        if (count($path) === 0) {
            return $this->model;
        }

        $model = $this->model;

        foreach ($path as $part) {
            $model = $model->getRelated($part);
            if ($model === null) {
                return null;
            }

            // Only n:1 relations are supported.
            if ($model instanceof Collection) {
                return null;
            }
        }

        return $model;
    }
}
