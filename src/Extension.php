<?php

declare(strict_types=1);

namespace Whisky;

use Whisky\Parser\ParseResult;

interface Extension
{
    public function build(string $code, ParseResult $parseResult, Scope $environment): string;
}
