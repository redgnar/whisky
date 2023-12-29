<?php

declare(strict_types=1);

namespace Whisky;

interface Provider
{
    public function has(string $name): bool;

    public function get(string $name): mixed;
}
