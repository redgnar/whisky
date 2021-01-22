<?php

declare(strict_types=1);

namespace Whisky;

interface Extension
{
    public function parse(string $code): void;

    public function normalize(string $code): string;
}
