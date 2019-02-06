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

use Assert\Assert;
use Netzmacht\ContaoWorkflowBundle\Exception\PropertyAccessFailed;
use function array_key_exists;
use function call_user_func;
use function is_object;
use function spl_object_hash;

/**
 * Class PropertyAccessManager
 */
final class PropertyAccessManager
{
    /**
     * Property access classes.
     *
     * @var string[]
     */
    private $accessors;

    /**
     * Cache of mapped property accessors.
     *
     * @var PropertyAccessor[]
     */
    private $mapping = [];

    /**
     * PropertyAccessManager constructor.
     *
     * @param string[] $accessors Property access class names.
     */
    public function __construct(array $accessors)
    {
        Assert::thatAll($accessors)->subclassOf(PropertyAccessor::class);

        $this->accessors = $accessors;
    }

    /**
     * Determine if property access is supported for given entity.
     *
     * @param mixed $data Given data structure.
     *
     * @return bool
     */
    public function supports($data): bool
    {
        foreach ($this->accessors as $accessor) {
            if (call_user_func([$accessor, 'supports'], $data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Provide access to a given data structure.
     *
     * @param mixed $data Data structure.
     *
     * @return PropertyAccessor
     *
     * @throws PropertyAccessFailed If no supported data structure is given.
     */
    public function provideAccess($data): PropertyAccessor
    {
        $hash = is_object($data) ? spl_object_hash($data) : null;

        if ($hash && array_key_exists($hash, $this->mapping)) {
            return $this->mapping[$hash];
        }

        foreach ($this->accessors as $accessor) {
            if (call_user_func([$accessor, 'supports'], $data)) {
                $accessor = call_user_func([$accessor, 'create'], $data);

                if ($hash) {
                    $this->mapping[$hash] = $accessor;
                }

                return $accessor;
            }
        }

        throw new PropertyAccessFailed('Could not determine property accessor for given data.');
    }
}
