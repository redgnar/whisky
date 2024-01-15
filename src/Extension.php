<?php

declare(strict_types=1);

namespace Whisky;

use Whisky\Parser\ParseResult;

interface Extension
{
    public function parse(string $code): string;

    public function build(string $code, ParseResult $parseResult): string;
}
