<?php

namespace Whisky\Script;

use Whisky\RunError;
use Whisky\Runtime;
use Whisky\Script;

class BasicScript implements Script
{
    private string $code;
    private string $runTimeName;
    private string $runTimeCode;
    private Runtime $runTime;

    public function __construct(string $code, string $runTimeName, string $runTimeCode)
    {
        $this->code = $code;
        $this->runTimeName = $runTimeName;
        $this->runTimeCode = $runTimeCode;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRunTimeName(): string
    {
        return $this->runTimeName;
    }

    public function getRunTimeCode(): string
    {
        return $this->runTimeCode;
    }

    public function getRunTime(): Runtime
    {
        if (isset($this->runTime)) {
            return $this->runTime;
        }
        $runTimeName = $this->getRunTimeName();
        if (!class_exists($runTimeName)) {
            eval($this->getRunTimeCode());
        }
        $runTime = new $runTimeName();
        if (!($runTime instanceof Runtime)) {
            throw new RunError('Runtime class must implement Runtime');
        }

        return $this->runTime = $runTime;
    }
}
