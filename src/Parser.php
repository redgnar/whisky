<?php

declare(strict_types=1);

namespace Whisky;

interface Parser
{
    public function parse(string $code): string;
}
