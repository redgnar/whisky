<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\Parser\ParseResult;

class SimplePhp implements Extension
{
    public function transformCode(string $code): string
    {
        return $code;
    }

    public function secure(ParseResult $parseResult): void
    {
    }
}
