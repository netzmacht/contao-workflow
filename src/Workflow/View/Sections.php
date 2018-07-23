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

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View;

/**
 * Class Sections
 */
final class Sections implements \IteratorAggregate
{
    /**
     * The generated sections.
     *
     * @var array|string[]
     */
    private $sections;

    /**
     * The section templates.
     *
     * @var array
     */
    private $templates;

    /**
     * Sections constructor.
     *
     * @param array $sections  The generated sections.
     * @param array $templates The section templates.
     */
    public function __construct(array $sections, array $templates)
    {
        $this->sections  = $sections;
        $this->templates = $templates;
    }

    /**
     * Render the sections.
     *
     * @param string $name
     * @param bool   $remove
     *
     * @return array
     */
    public function get(string $name, bool $remove = true): array
    {
        if (!isset($this->sections[$name])) {
            return [];
        }

        $return = $this->sections[$name];

        if ($remove) {
            unset($this->sections[$name]);
        }

        return $return;
    }

    /**
     * Get the template of a section.
     *
     * @param string $name The section name.
     *
     * @return null|string
     */
    public function getTemplate(string $name): ?string
    {
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->sections);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->sections[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException Overriding sections is not allowed.
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Overriding sections is not allowed.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->sections[$offset]);
    }

    /**
     * Allow to iterate over the sections.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->sections);
    }
}
