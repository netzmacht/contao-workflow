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
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;
use Traversable;

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
        $this->model->$name = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name)
    {
        if (strpos($name, '.') > -1) {
            $relationParts = explode('.', $name, 2);

            //foreach ($thi)

            $childModel = $this->model->getRelated($relationParts[0]);
            if (is_a($childModel, '\Contao\Model\Collection')) {
                $childModel = $childModel->first();
            }
            if ($childModel == null) {
                return null;
            }
            $childPropertyAccessor = new ContaoModelPropertyAccessor($childModel);
            return $childPropertyAccessor->get($relationParts[1]);
        }

        return $this->model->$name;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        return isset($this->model->$name);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->model->row());
    }
}
