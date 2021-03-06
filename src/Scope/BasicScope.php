<?php

namespace Whisky\Scope;

use Whisky\Scope;

/**
 * @extends \ArrayObject<string, mixed>
 */
class BasicScope extends \ArrayObject implements Scope
{
    public function has(string $name): bool
    {
        return $this->offsetExists($name);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name)
    {
        return $this->offsetGet($name);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, $value): void
    {
        $this->offsetSet($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function unset(string $name): void
    {
        $this->offsetUnset($name);
    }
}
