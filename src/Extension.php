<?php

declare(strict_types=1);

namespace Whisky;

use Whisky\Parser\ParseResult;

interface Extension
{
    public function parse(string $code): void;

    public function normalize(string $code): string;

    public function secure(ParseResult $parseResult): void;
}
