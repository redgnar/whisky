<?php

declare(strict_types=1);

namespace Whisky\Functions;

/**
 * @extends \ArrayObject<string, \Closure>
 */
class FunctionRepository extends \ArrayObject
{
    /**
     * @var Provider[]
     */
    private array $providers = [];

    public function has(string $name): bool
    {
        $result = $this->offsetExists($name);
        if ($result) {
            return true;
        }
        foreach ($this->providers as $provider) {
            if ($provider->has($name)) {
                return true;
            }
        }

        return false;
    }

    public function get(string $name): ?\Closure
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        }
        foreach ($this->providers as $provider) {
            if ($provider->has($name)) {
                $this->offsetSet($name, $provider->get($name));
                break;
            }
        }

        return $this->offsetGet($name);
    }

    public function addProvider(Provider $provider): void
    {
        $this->providers[] = $provider;
    }

    public function set(string $name, \Closure $value): void
    {
        $this->offsetSet($name, $value);
    }

    public function unset(string $name): void
    {
        $this->offsetUnset($name);
    }
}
