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

    /**
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->scope->has($offset);
    }

    /**
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->scope->get($offset);
    }

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->scope->set($offset, $value);
    }

    /**
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->scope->unset($offset);
    }
}
