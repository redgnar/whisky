<?php

declare(strict_types=1);

namespace Whisky;

use Whisky\Parser\ParseResult;

interface Script
{
    public function getCode(): string;

    public function getResultCode(): string;

    public function getParseResult(): ParseResult;

    public function getCodeRunner(): \Closure;
}
