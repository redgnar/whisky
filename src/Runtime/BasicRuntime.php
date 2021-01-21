<?php

namespace Whisky\Runtime;

use ArrayAccess;
use Whisky\Runtime;
use Whisky\Scope;

abstract class BasicRuntime implements Runtime
{
    private Scope $scope;

    public function setScope(Scope $scope): void
    {
        $this->scope = $scope;
    }

    public function getScope(): Scope
    {
        return $this->scope;
    }

    public function offsetExists($offset)
    {
        return $this->scope->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->scope->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->scope->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->scope->unset($offset);
    }
}
