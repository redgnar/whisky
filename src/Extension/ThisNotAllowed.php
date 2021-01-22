<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\ParseError;

class ThisNotAllowed implements Extension
{
    public function parse(string $code): void
    {
        if (1 === preg_match('/\$this([\W])/', $code)) {
            throw new ParseError('Using $this is not allowed');
        }
    }

    public function normalize(string $code): string
    {
        return $code;
    }
}
