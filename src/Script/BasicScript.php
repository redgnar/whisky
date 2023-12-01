<?php

namespace Whisky\Script;

use Whisky\Runtime;
use Whisky\Script;

class BasicScript implements Script
{
    //    private Runtime $runTime;

    public function __construct(
        private string $code,
        private \Closure $codeRunner
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCodeRunner(): \Closure
    {
        return $this->codeRunner;
    }
}
