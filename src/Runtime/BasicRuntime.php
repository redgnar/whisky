<?php

namespace Whisky\Runtime;

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

    public function offsetExists(mixed $offset): bool
    {
        return $this->scope->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->scope->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->scope->set($offset ?? '', $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->scope->unset($offset);
    }
}
