<?php

namespace Whisky\Script;

use Whisky\Runtime;
use Whisky\Script;

class BasicScript implements Script
{
    //    private Runtime $runTime;

    public function __construct(
        private readonly string $code,
        private readonly string $resultCode,
        private readonly \Closure $codeRunner
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

    public function getCodeRunner(): \Closure
    {
        return $this->codeRunner;
    }
}
