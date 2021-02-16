<?php

declare(strict_types=1);

namespace Whisky;

use Whisky\Parser\ParseResult;

interface Extension
{
    public function transformCode(string $code): string;

    public function secure(ParseResult $parseResult): void;
}
