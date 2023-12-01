<?php

declare(strict_types=1);

namespace Whisky;

interface Scope
{
    public function has(string $name): bool;

    public function get(string $name): mixed;

    public function set(string $name, mixed $value): void;

    public function unset(string $name): void;
}
