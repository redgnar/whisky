<?php

declare(strict_types=1);

namespace Whisky;

interface Scope extends Provider
{
    public function set(string $name, mixed $value): void;

    public function unset(string $name): void;
}
