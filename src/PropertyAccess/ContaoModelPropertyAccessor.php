<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\PropertyAccess;

use ArrayIterator;
use Contao\Model;
use Contao\Model\Collection;
use Exception;
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel\ContaoModelRelatedModelChangeTracker;
use Traversable;

use function array_pop;
use function count;
use function explode;

final class ContaoModelPropertyAccessor implements PropertyAccessor
{
    /**
     * Wrapped model.
     *
     * @var Model
     */
    private $model;

    /**
     * Related model changes registry.
     *
     * @var ContaoModelRelatedModelChangeTracker|null
     */
    private $changesRegistry;

    /**
     * @param Model                                     $model           Contao model.
     * @param ContaoModelRelatedModelChangeTracker|null $changesRegistry Related model changes registry.
     */
    public function __construct(Model $model, ?ContaoModelRelatedModelChangeTracker $changesRegistry = null)
    {
        $this->model           = $model;
        $this->changesRegistry = $changesRegistry;
    }

    /**
     * @deprecated Will be removed in the next major release. use PropertyAccessorFactory instead.
     *
     * @param mixed $object
     */
    public static function supports($object): bool
    {
        return $object instanceof Model;
    }

    /**
     * @deprecated Will be removed in the next major release. use PropertyAccessorFactory instead.
     *
     * @param mixed $object
     *
     * @throws PropertyAccessFailed When data structure is not supported.
     */
    public static function create($object): PropertyAccessor
    {
        /** @psalm-suppress DeprecatedMethod */
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

        if ($model === null) {
            return;
        }

        $model->$property = $value;
        if (! $this->changesRegistry || $this->model === $model) {
            return;
        }

        $this->changesRegistry->track($this->model, $model);
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

    /** @return ArrayIterator<string,mixed> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->model->row());
    }

    /**
     * Determine the model from a given path.
     *
     * @param list<string> $path Property path checking relations.
     *
     * @throws Exception When property is not relational.
     */
    private function determineModel(array $path): ?Model
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
