<?php

namespace Whisky\Script;

use Whisky\Parser\ParseResult;
use Whisky\Runtime;
use Whisky\Script;

class BasicScript implements Script
{
    //    private Runtime $runTime;

    private string $code;
    private string $resultCode;
    private ParseResult $parseResult;

    public function __construct(
        string $code,
        string $resultCode,
        ParseResult $parseResult
    ) {
        $this->code = $code;
        $this->resultCode = $resultCode;
        $this->parseResult = $parseResult;
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
