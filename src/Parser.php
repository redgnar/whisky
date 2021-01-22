<?php

declare(strict_types=1);

namespace Whisky;

use Whisky\Parser\ParseResult;

interface Parser
{
    public function parse(string $code): ParseResult;
}
