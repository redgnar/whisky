<?php

namespace Whisky\Script;

use Whisky\Parser\ParseResult;
use Whisky\Runtime;
use Whisky\Script;

class BasicScript implements Script
{
    //    private Runtime $runTime;

    public function __construct(
        private readonly string $code,
        private readonly string $resultCode,
        private readonly ParseResult $parseResult
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getResultCode(): string
    {
        return $this->resultCode;
    }

    public function getParseResult(): ParseResult
    {
        return $this->parseResult;
    }
}
