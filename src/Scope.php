<?php
declare(strict_types=1);

namespace Whisky;


interface Scope
{

    public function has(string $name): bool;

    /**
     * @return mixed
     */
    public function get(string $name);

    /**
     * @param mixed $value
     */
    public function set(string $name, $value): void;

    public function unset(string $name): void;
}
