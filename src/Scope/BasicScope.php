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

    public function get(string $name)
    {
        return $this->offsetGet($name);
    }

    public function set(string $name, $value): void
    {
        $this->offsetSet($name, $value);
    }

    public function unset(string $name): void
    {
        $this->offsetUnset($name);
    }
}
